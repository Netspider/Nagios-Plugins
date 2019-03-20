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

## description

### nagioslib2
This library is needed by some of the plugins used here. It provides easy access to the Nagios plugin api.

### check\_rrdcached
If you are using pnp4nagios with many metrics, you might already use rrdcached. It reduces disk IO by writing rrd files in bulk. This plugin gathers some statistics about the caching daemon.

*Written in Python &ge; 2.6*

#### Nagios configuration
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

### check\_smart
Gathers S.M.A.R.T diagnostic data from hard drives, warns if a parameter hints at a failing device and returns many metrics. Hardware raids are not supported.

*Written in Bash*

#### Nagios configuration
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

### check\_traffic
Checks network traffic and warns about too many retransmits and network errors. Provides many metrics.

*Written in Python &ge; 2.6*<br>
*uses nagioslib2*

#### Nagios configuration
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