#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys
import subprocess
import socket
import time
import signal


class external(object):
    @staticmethod
    def run(cmd, shell=True):
        tmp = subprocess.Popen(cmd, shell=shell, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        data = tmp.communicate()
        return data[0], data[1], tmp.returncode


class snmp(object):
    def __init__(self, host, version, community=False, port=161, secName=False, authPassword=False, authProtocol=False, snmpbindir='/usr/bin/', engineID=False, secLevel=False, contextName=False, privProtocol=False, privPassword=False):
        self.community = community
        self.host = host
        self.port = port
        self.version = version
        if not snmpbindir.endswith('/'):
            snmpbindir += '/'
        self.snmpbindir = snmpbindir
        self.secName = secName
        self.authPassword = authPassword
        self.authProtocol = authProtocol
        self.engineID = engineID
        self.secLevel = secLevel
        self.contextName = contextName
        self.privProtocol = privProtocol
        self.privPassword = privPassword

        snmpargs = {'authProtocol': {'option': '-a', 'value': self.authProtocol}, 'authPassword': {'option': '-A', 'value': self.authPassword}, 'community': {'option': '-c', 'value': self.community}, 'engineID': {'option': '-e', 'value': self.engineID}, 'secLevel': {'option': '-l', 'value': self.secLevel}, 'contextName': {'option': '-n', 'value': self.contextName}, 'secName': {'option': '-u', 'value': self.secName}, 'privProtocol': {'option': '-x', 'value': self.privProtocol}, 'privPassword': {'option': '-X', 'value': self.privPassword}, 'version': {'option': '-v', 'value': self.version}}

        snmpcmd = []
        for arg in snmpargs.keys():
            if snmpargs[arg]['value'] != False:
                snmpcmd.append('%s %s' % (snmpargs[arg]['option'], snmpargs[arg]['value']))
        self.snmpcmd = ' '.join(snmpcmd)

    def walk(self, oid, bulk=True, dictionary=True):
        if bulk == True:
            snmpbin = self.snmpbindir + 'snmpbulkwalk'
        else:
            snmpbin = self.snmpbindir + 'snmpwalk'

        snmpcmd = snmpbin + ' ' + self.snmpcmd + ' -Oq -On' + ' %s:%s ' % (self.host, self.port) + oid
        stdout, stderr, ret = external.run(snmpcmd)

        if '\n' in stdout: snmpdata = stdout.split('\n')
        else: snmpdata = [stdout]

        if ret != 0:
            return None, stderr
        else:
            data = None
            if dictionary is True:
                data = {}
                for line in snmpdata:
                    if line.strip() != '':
                        tmp = line.split(' ', 1)
                        data[tmp[0]] = tmp[1]

            else:
                data = []
                for line in snmpdata:
                    if line.strip() != '':
                        tmp = line.split(' ', 1)
                        data.append(tmp)
            return data

    def get(self, oid):
        snmpcmd = self.snmpbindir + 'snmpget ' + self.snmpcmd + ' -Oq -On' + ' %s:%s ' % (self.host, self.port) + oid
        stdout, stderr, ret = external.run(snmpcmd)

        if ret != 0:
            return None, stderr
        else:
            if 'No Such' in stdout and 'at this OID' in stdout:
                return None, stdout
            else:
                return stdout.split('\n')[0].split(' ', 1)[1]

    def identify_vendor(self):
        if type(self.get('.1.3.6.1.4.1.674.10895.3000.1.2.100.3.0')) is not tuple:
            # DELL Powerconnect
            return ['DELL', self.get('.1.3.6.1.4.1.674.10895.3000.1.2.100.3.0'), self.get('.1.3.6.1.4.1.674.10895.3000.1.2.100.1.0')]
        if type(self.get('.1.3.6.1.4.1.11.2.14.11.5.1.9.6.1.0')) is not tuple:
            # HP ProCurve
            return ['HP', self.get('.1.3.6.1.4.1.11.2.36.1.1.5.1.1.7.1'), self.get('.1.3.6.1.4.1.11.2.36.1.1.5.1.1.8.1')]
        if type(self.get('.1.3.6.1.4.1.9.2.1.5.0')) is not tuple:
            # Cisco
            return ['CISCO', self.get('.1.3.6.1.2.1.47.1.1.1.1.2.1'), self.get('.1.3.6.1.2.1.47.1.1.1.1.2.1')]
        if type(self.get('.1.3.6.1.4.1.171.10.76.10.1.1.0')) is not tuple:
            # DLink DGS 1210
            return ['DLINK DGS', 'DLINK', 'DGS 1210']
        if type(self.get('.1.3.6.1.4.1.43.47.1.1.5.2.0')) is not tuple:
            # 3Com
            return['3COM', '3COM', '3COM']
        else:
            return [None, None], None


class livestatus(object):
    @staticmethod
    def query(msg):
        import ast
        socket_path = '/var/cache/naemon/live'
        s = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
        s.connect(socket_path)
        s.send(msg)
        s.shutdown(socket.SHUT_WR)
        time.sleep(0.1)
        total_data = []
        while True:
            data = s.recv(8192)
            if not data:
                if total_data == []:
                    total_data = ['[[]]']
                break
            total_data.append(data)

        s.close()
        return ast.literal_eval(''.join(total_data))

    @staticmethod
    def query_tcp(msg):
        import ast
        ip = 'CHANGEME'
        port = 6557
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect((ip, port))
        s.sendall(msg.encode())
        s.shutdown(socket.SHUT_WR)
        total_data = []
        while True:
            data = s.recv(8192)
            if not data:
                if total_data == []:
                    total_data = ['[[]]']
                break
            total_data.append(data)
        s.close()
        return ast.literal_eval(''.join(total_data))

## collects Nagios Status messages and code
class NagiosStatus(object):
    code = {
        "unknown": 3, 3: "unknown",
        "critical": 2, 2: "critical",
        "warning": 1, 1: "warning",
        "ok": 0, 0: "ok"
    }

    ## constructor
    # initialize class parameters
    # @param self object pointer
    # @param program_name name of the program at the beginning of the message
    # @param info_name name of info section for additional information
    def __init__(self, program_name, info_name=None):
        self.exitcode = self.code["ok"]
        self.message = [[], [], [], []]
        self.performance_data = []
        self.program_name = program_name
        self.info_name = info_name
        self.info = [[], [], [], []]

    ## add new message and check if exitcode is worse than before
    # @param self object pointer
    # @param code new exit code if it is worse than the last code
    # @param message user message to append
    # @param performance_data performance data to append
    # @param info info to append (multiline)
    def set_code(self, code, message=None, performance_data=None, info=None):
        if code >= self.exitcode:
            self.exitcode = code
        if message:
            self.message[code].append(message)
        if performance_data:
            self.performance_data.append(performance_data)
        if info:
            self.info[code].append(info)

    ## return message for specific exit code or nothing, if no message for this code exists
    # @param self object pointer
    # @param code exit code
    def _build_message(self, code):
        if self.message[code]:
            return ["%s:" % self.code[code], ", ".join(self.message[code])]
        else:
            return []

    ## return info for specific exit code or nothing, if no message for this code exists
    # @param self object pointer
    # @param code exit code
    def _build_info(self, code):
        if self.info[code]:
            return ["\n%s:" % self.code[code], ", ".join(self.info[code])]
        else:
            return []

    ## immediately exit the program ignoring previously set messages and exit codes
    # @param code exit code to use
    # @param message the message to print
    def die(self, code, message):
        print message
        if isinstance(code, int):
            sys.exit(code)
        elif code in self.code:
            sys.exit(self.code[code])

    ## immediately exit the program using the previously set messages and exit code
    # @param self object pointer
    def exit(self):
        sys.exit(self.show_info())

    ## print the previously set messages and exit code
    # @param self object pointer
    def show_info(self):
        out_message = [self.program_name]
        if self.info_name:
            out_info = ['\n'+self.info_name]
        else:
            out_info = []
        for code in [3, 2, 1, 0]:
            out_message.extend(self._build_message(code))
            out_info.extend(self._build_info(code))

        if len(out_info) > 0:
            print "%s | %s %s" % (" ".join(out_message), " ".join(self.performance_data), " ".join(out_info))
        else:
            print "%s | %s" % (" ".join(out_message), " ".join(self.performance_data))
        return self.exitcode

    ## set exit code to unknown if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def unknown(self, message=None, performance_data=None, info=None):
        self.set_code(self.code["unknown"], message, performance_data, info)

    ## set exit code to critical if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def critical(self, message=None, performance_data=None, info=None):
        self.set_code(self.code["critical"], message, performance_data, info)

    ## set exit code to warning if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def warning(self, message=None, performance_data=None, info=None):
        self.set_code(self.code["warning"], message, performance_data, info)

    ## set exit code to ok if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def ok(self, message=None, performance_data=None, info=None):
        self.set_code(self.code["ok"], message, performance_data, info)

    def generate_performance_data(self, label, value, unit='', warn='', crit='', min='', max=''):
        performance_string = '%s=%s%s;%s;%s;%s;%s' % (label.replace(' ', '_'), value, unit, warn, crit, min, max)
        return performance_string

## Plugin Timeout functionality (static class)
class Timeout(object):
    ## (re)starts the countdown
    # @param self object pointer
    # @param callback function to call when time runs out. signature: func(signum, frame)
    # @param timeout number of seconds to wait from start, before callback is called
    @staticmethod
    def start(callback, timeout=10):
        if isinstance(timeout, int) and timeout > 0:
            signal.signal(signal.SIGALRM, callback)
            signal.alarm(timeout)
        else:
            raise ValueError("timeout must be type int and > 0")

    ## stops the countdown
    # @param self object pointer
    @staticmethod
    def stop():
        signal.alarm(0)
