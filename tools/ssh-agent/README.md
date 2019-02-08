# ssh-agent

This is a replacement for NRPE using SSH.

## installation

* create an unprivileged user on all hosts. You can use the **nagios** account. It needs to have a valid login shell like **/bin/sh** or **/bin/bash**.
* create a ssh keypair without passphrase on the nagios server: `sudo -u nagios ssh-keygen -b 4096`
* add the contents of ~nagios/.ssh/id\_rsa.pub to authorized\_keys.example
* copy to all hosts:
	* nagios-ssh-wrapper -> /usr/lib/nagios/plugins/nagios-ssh-wrapper
	* authorized\_keys.example -> ~nagios/.ssh/authorized\_keys
	* checks.conf -> /etc/nagios\_commands.d/checks.conf
* copy to nagios server:
	* ssh\_config\_nagiosserver -> ~nagios/.ssh/config
* copy to pnp4nagios server:
	* check\_by\_ssh*.cfg -> /etc/pnp4nagios/check_commands/

## usage
```
define service {
	host_name			YOURHOST
	service_description	Local SMTP Status
	check_command		check_by_ssh_1arg!check_smtp
	use					template-service
}

define service {
	host_name			YOURHOST
	service_description	Apache Status
	check_command		check_by_ssh!check_http!"-H 127.0.0.1 -u /server-status"
	use					template-service
}
```

## pros

* secure encryption with the settings YOU want to use
* SSH is already present on most machines
* no `nasty_metachars` handling needed because the first argument will be called as a program and everything else is used as parameters
* plugins can have multlinie output
* no character length limitation in plugin output
* adaptive security for the connection via ssh server configuration

## cons

* Connection multiplexing is used to speed up the connections. This is done using Control\* in ~nagios/.ssh/config. By default there is a maximum of 10 Sessions per host, controlled by **MaxSessions** in /etc/ssh/sshd\_config
* You can not use quotes in your arguments because the command (check\_by\_ssh\*) uses single quotes when calling ssh and the service check uses double quotes to put all parameters in \$ARG2\$
* only publickey is supported for authentication
