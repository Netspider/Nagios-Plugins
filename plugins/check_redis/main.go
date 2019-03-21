package main

import (
	"bufio"
	"encoding/base64"
	"fmt"
	"github.com/go-redis/redis"
	"github.com/olorin/nagiosplugin"
	"github.com/spf13/pflag"
	"math"
	"math/rand"
	"strconv"
	"strings"
	"time"
)

type options struct {
	hostname string
	port     uint16
	password string
	db       int
}

func init() {
	rand.Seed(time.Now().Unix())
}

func main() {
	opts := getOpts()

	DoCheck(opts)
}

func DoCheck(opts options) {
	check := nagiosplugin.NewCheck()
	defer check.Finish()

	//////////////////////////
	// Connect
	redisdb := redis.NewClient(&redis.Options{
		Addr:     fmt.Sprintf("%s:%d", opts.hostname, opts.port),
		Password: opts.password,
		DB:       opts.db,
	})
	defer redisdb.Close()

	//////////////////////////
	// PingPong
	pong, err := redisdb.Ping().Result()
	if pong != "PONG" || err != nil {
		check.AddResult(nagiosplugin.CRITICAL, "Connection to Redis failed")
		check.Finish()
	} else {
		check.AddResult(nagiosplugin.OK, "Got Pong")
	}

	if isMaster(redisdb, check) {
		//////////////////////////
		// random key
		key := "check_redis-" + randStr(16)
		wantedValue := randStr(16)

		// does key exist?
		keyExists, err := redisdb.Exists(key).Result()
		if err != nil {
			check.AddResultf(nagiosplugin.CRITICAL, "could not check if key %q exists: %v", key, err)
		} else if keyExists == 1 {
			check.AddResultf(nagiosplugin.UNKNOWN, "random key %q already exists", key)
		} else {

			// set
			err = redisdb.Set(key, wantedValue, 30*time.Second).Err()
			if err != nil {
				check.AddResultf(nagiosplugin.CRITICAL, "could not write key %q: %v", key, err)
			} else {
				// get
				gotValue, err := redisdb.Get(key).Result()
				if err != nil {
					check.AddResultf(nagiosplugin.CRITICAL, "could not read key %q: %v", key, err)
				} else if gotValue != wantedValue {
					check.AddResultf(nagiosplugin.CRITICAL, "value was not equal! Wanted: %q, got %q", wantedValue, gotValue)
				} else {
					check.AddResult(nagiosplugin.OK, "Writing and Reading works")
				}
			}

			// delete not needed, timeout is 30 seconds
		}
	} else {
		//////////////////////////
		// slave
		check.AddResult(nagiosplugin.OK, "R/W test skipped, readonly slave")
	}

	//////////////////////////
	// Info server
	keys := map[string]string {
		"uptime_in_seconds": "s",
	}
	parseInfo("server", keys, redisdb, check)

	// Info clients
	keys = map[string]string {
		"connected_clients": "",
		"blocked_clients": "",
	}
	parseInfo("clients", keys, redisdb, check)

	// Info memory
	keys = map[string]string {
		"used_memory": "B",
		"used_memory_rss": "B",
		"used_memory_overhead": "B",
		"used_memory_dataset": "B",
		"used_memory_lua": "B",
		"maxmemory": "B",
		"active_defrag_running": "",
	}
	parseInfo("memory", keys, redisdb, check)

	// Info stats
	keys = map[string]string {
		"total_connections_received": "c",
		"total_commands_processed": "c",
		"instantaneous_ops_per_sec": "",
		"total_net_input_bytes": "c",
		"total_net_output_bytes": "c",
		"rejected_connections": "c",
		"expired_keys": "c",
		"evicted_keys": "c",
		"keyspace_hits": "c",
		"keyspace_misses": "c",
		"pubsub_channels": "c",
		"pubsub_patterns": "c",
		"migrate_cached_sockets": "",
	}
	parseInfo("stats", keys, redisdb, check)

	// Info replication
	keys = map[string]string {
		"repl_backlog_active": "",
		"repl_backlog_size": "B",
		"repl_backlog_first_byte_offset": "B",
		"repl_backlog_histlen": "B",
		"master_sync_in_progress": "",
		"slave_repl_offset": "B",
		"master_link_down_since_seconds": "s",
		"connected_slaves": "",
		"min_slaves_good_slaves": "",
	}
	parseInfo("replication", keys, redisdb, check)

	// Info cpu
	keys = map[string]string {
		"used_cpu_sys": "c",
		"used_cpu_user": "c",
		"used_cpu_sys_children": "c",
		"used_cpu_user_children": "c",
	}
	parseInfo("cpu", keys, redisdb, check)
}

func parseInfo(section string, keys map[string]string, redisdb *redis.Client, check *nagiosplugin.Check) {
	infoData, err := redisdb.Info(section).Result()
	if err == nil {
		data := strToMap(infoData)
		for infoName, infoUnit := range keys {
			if val, ok := data[infoName]; ok {
				floatVal, err := strconv.ParseFloat(val, 64)
				if infoUnit == "c" {
					floatVal = math.Round(floatVal)
				}
				if err == nil {
					check.AddPerfDatum(infoName, infoUnit, floatVal)
				}
			}
		}
	} else {
		check.AddResultf(nagiosplugin.WARNING, "could not get Info for section %q", section)
	}
}

func isMaster(redisdb *redis.Client, check *nagiosplugin.Check) bool {
	infoData, err := redisdb.Info("replication").Result()
	if err == nil {
		if strings.Contains(infoData, "role:master") {
			return true
		} else if strings.Contains(infoData, "role:slave") {
			return false
		} else {
			check.Exitf(nagiosplugin.UNKNOWN, "role unknown")
			return false
		}
	} else {
		check.Exitf(nagiosplugin.UNKNOWN, "could not get Info for section %q", "replication")
		return false
	}
}

func strToMap(data string) map[string]string {
	m := make(map[string]string)
	scanner := bufio.NewScanner(strings.NewReader(data))
	for scanner.Scan() {
		if ! strings.HasPrefix(scanner.Text(), "#") {
			if strings.Contains(scanner.Text(), ":") {
				d := strings.SplitN(scanner.Text(), ":", 2)
				m[d[0]] = d[1]
			} else {
				println("something missing: " + scanner.Text())
			}
		}

	}
	return m
}

func randStr(len int) string {
	buff := make([]byte, len)
	rand.Read(buff)
	str := base64.StdEncoding.EncodeToString(buff)
	// Base 64 can be longer than len
	return str[:len]
}

func getOpts() options {
	opts := options{}
	pflag.StringVarP(&opts.hostname, "hostname", "n", "localhost", "hostname or ip address")
	pflag.Uint16VarP(&opts.port, "port", "p", 6379, "redis port")
	pflag.IntVarP(&opts.db, "db", "d", 0, "redis database")
	pflag.StringVarP(&opts.password, "password", "P", "", "password for authentication")

	pflag.Parse()
	return opts
}
