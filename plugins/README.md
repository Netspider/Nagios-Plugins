# plugins

## installation

The \*.cfg and \*.php files are needed if you use pnp4nagios. The files belong in these directories:
```perl
plugins
└── check_example
    ├── check_example # /usr/lib/nagios/plugins/
    ├── check_example.cfg # /etc/pnp4nagios/check_commands/
    └── check_example.php # /etc/pnp4nagios/templates.d/
```
Pull requests for missing functionality are welcome!

## pyhelper

This library is needed by some of the plugins used here. It provides easy access to the Nagios plugin api.

Rename the library to `pyhelper.py`  and copy it in the site-packages directory:

```python
# for all users:
python -c 'import site; print(site.getsitepackages()[0])'
# OR for the monitoring user:
sudo -u nagios python -c 'import site; print(site.getusersitepackages())'
```

## check_connections

This plugin displays the number of TCP/UDP connections in their states.

*Written in Python, uses pyhelper*

```
define service {
    host_name               server
    service_description     Connection Status
    check_command           check_by_ssh_1arg!check_connections
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_connections=/usr/lib/nagios/plugins/check_connections
```

## check_cpu-usage

This plugin displays the cpu and IO usage.

*Written in Python, uses pyhelper*

```
define service {
    host_name               server
    service_description     CPU usage
    check_command           check_by_ssh!check_cpu-usage!"--iowait_warn 20"
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_cpu-usage=/usr/lib/nagios/plugins/check_cpu-usage $ARG1$
```

## check_diskstats

*Written in Python, uses pyhelper*

```
define service {
    host_name               server
    service_description     Diskstats
    check_command           check_by_ssh_1arg!check_diskstats
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_diskstats=/usr/lib/nagios/plugins/check_diskstats
```

## check_host-info

*Written in Python, uses pyhelper*

```
define service{
    host_name               server
    service_description     Host Info
    check_command           check_by_ssh_1arg!check_host-info
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_host-info=/usr/lib/nagios/plugins/check_host-info
```

## check_logstash

This plugin connects to the logstash management port and checks if logstash can deliver documents to its output.

*Written in Python 3, uses pyhelper*

```
define service {
    host_name               server
    service_description     Logstash Status
    check_command           check_by_ssh!check_logstash!"--suppress-geoip"
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_logstash=/usr/lib/nagios/plugins/check_logstash $ARG1$
```

## check_memory

This plugin gathers ram/swap related statistics.

*Written in Python, uses pyhelper*

```
define service {
    host_name               server
    service_description     Memory Usage
    check_command           check_by_ssh!check_memory!"-w 90,50 -c 95,75"
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_memory=/usr/lib/nagios/plugins/check_memory $ARG1$
```

## check_nf_conntrack

This plugin warns if the connection tracking table is nearly full. This table is used to track related/established packages.

*Written in Bash*

```
define service {
    host_name               server
    service_description     Conntrack Table
    check_command           check_by_ssh_1arg!check_nf_conntrack
    use                     template-service
}
```

```
# NRPE / ssh-agent
check_nf_conntrack=/usr/lib/nagios/plugins/check_nf_conntrack 80 90
```

## check\_redis

This plugin returns many metrics and checks if a random key can be saved in redis and if it has the same value after reading it again.

*Written in Go*

```
define service {
    host_name               server
    service_description     Redis Stats
    check_command           check_by_ssh!check_redis!"-p 6379 -P $USER2$"
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_redis=/usr/lib/nagios/plugins/check_redis $ARG1$
```

## check\_rrdcached
If you are using pnp4nagios with many metrics, you might already use rrdcached. It reduces disk IO by writing rrd files in bulk. This plugin gathers some statistics about the caching daemon.

*Written in Python &ge; 2.6*

```
define service {
    host_name               server
    service_description     rrdcached
    check_command           check_by_ssh_1arg!check_rrdcached
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_rrdcached=/usr/lib/nagios/plugins/check_rrdcached
```

## check\_smart
Gathers S.M.A.R.T diagnostic data from hard drives, warns if a parameter hints at a failing device and returns many metrics. Hardware raids are not supported.

*Written in Bash*

```
define service {
    host_name               server
    service_description     SMART HDD Check
    check_command           check_by_ssh_1arg!check_smart
    check_interval          60
    max_check_attempts      1
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_smart=/usr/lib/nagios/plugins/check_smart
```

## check\_squid
Collects a ton of statistics from Squid. Warns if median service time (time to deliver pages) reaches a threshold.

*Written in Python 2, uses pyhelper*

```
define service {
    host_name               server
    service_description     Squid Statistics
    check_command           check_by_ssh!check_squid!"-p 8080 -Uusername -P$USER2$"
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_squid=/usr/lib/nagios/plugins/check_squid $ARG1$
```

## check\_switch-status
Reads switch metrics using SNMP.

*Written in Python 2, uses pyhelper*

```
define service{
    host_name               server
    service_description     Switch Status
    check_command           check_switch-status!--mem-warn 75 --mem-crit 95
    use                     template-service
}
```

```
# strong encryption
define command {
        command_name    check_switch-status
        command_line    $USER1$/check_switch-status --host $HOSTADDRESS$ --snmp_secName $USER3$ --snmp_authPassword $USER4$ --snmp_privPassword $USER5$ --snmp_authProtocol sha --snmp_secLevel AuthPriv --snmp_privProtocol aes $ARG1$
}
# weak encryption
define command {
        command_name    check_switch-status-v2
        command_line    $USER1$/check_switch-status --host $HOSTADDRESS$ --community $USER2$ $ARG1$
}
```

## check_temp_sensors
This plugin reads the hardware temperature sensors built into the system. There is no warning mechanism for high temperatures, this plugin is just for the metrics.

*Written in Perl 5*

```
define service {
    host_name               server
    service_description     Sensor Temperature
    check_command           check_by_ssh_1arg!check_temp_sensors
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_temp_sensors=/usr/lib/nagios/plugins/check_temp_sensors
```

## check\_traffic
Checks network traffic and warns about too many retransmits and network errors. Provides many metrics.

*Written in Python &ge; 2.7, uses pyhelper*

```
define service {
    host_name               server
    service_description     Traffic
    check_command           check_by_ssh!check_traffic!"--disable_alarm tap,bond,tun --ignore_interfaces dummy"
    use                     template-service
}
```
```dosini
# NRPE / ssh-agent
check_traffic=/usr/lib/nagios/plugins/check_traffic $ARG1$
```
