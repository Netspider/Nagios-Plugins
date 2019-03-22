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

## nagioslib2
This library is needed by some of the plugins used here. It provides easy access to the Nagios plugin api.

## check\_redis
This plugin return many metrics and checks if a random key can be saved in redis and if it has the same value after reading it again.

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

*Written in Python 2*

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

*Written in Python 2*<br>
*uses nagioslib2*

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

*Written in Python &ge; 2.7*<br>
*uses nagioslib2*

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
