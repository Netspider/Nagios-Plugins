#!/usr/bin/python2
# -*- coding: utf-8 -*-

import os
import sys
import subprocess
import pickle
import pwd
import socket
import time
import signal
# "typing" ab Python3.5 (Python2 mit python-typing)
try:
    # noinspection PyUnresolvedReferences
    from typing import Union, List, Dict, Callable, Tuple, Optional, NoReturn
except ImportError:
    pass


# noinspection PyPep8Naming
class external(object):
    @staticmethod
    def run(cmd, shell=True):
        # type: (List[str], bool) -> Tuple[str, str, int]
        tmp = subprocess.Popen(cmd, shell=shell, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        data = tmp.communicate()
        return data[0], data[1], tmp.returncode


# noinspection PyPep8Naming
class snmp(object):
    def __init__(self, host, version, community=None, port=161, secName=None, authPassword=None, authProtocol=None,
                 snmpbindir='/usr/bin/', engineID=None, secLevel=None, contextName=None, privProtocol=None,
                 privPassword=None):
        # type: (str, str, str, int, str, str, str, str, str, str, str, str, str) -> None
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

        snmpargs = {
            'authProtocol': {'option': '-a', 'value': self.authProtocol},
            'authPassword': {'option': '-A', 'value': self.authPassword},
            'community': {'option': '-c', 'value': self.community},
            'engineID': {'option': '-e', 'value': self.engineID},
            'secLevel': {'option': '-l', 'value': self.secLevel},
            'contextName': {'option': '-n', 'value': self.contextName},
            'secName': {'option': '-u', 'value': self.secName},
            'privProtocol': {'option': '-x', 'value': self.privProtocol},
            'privPassword': {'option': '-X', 'value': self.privPassword},
            'version': {'option': '-v', 'value': self.version},
        }  # type: Dict[str, Dict]

        self.snmpcmd = []
        for arg in snmpargs.keys():
            if snmpargs[arg]['value']:
                self.snmpcmd.extend([snmpargs[arg]['option'], snmpargs[arg]['value']])

    def walk(self, oid, bulk=True, dictionary=True):
        # type: (str, bool, bool) -> Union[List[List[str]], Dict[str, str]]
        if bulk:
            snmpbin = self.snmpbindir + 'snmpbulkwalk'
        else:
            snmpbin = self.snmpbindir + 'snmpwalk'

        snmpcmd = [snmpbin]
        snmpcmd.extend(self.snmpcmd)
        snmpcmd.extend(['-Oq', '-On', '%s:%s' % (self.host, self.port), oid])
        stdout, stderr, ret = external.run(snmpcmd, shell=False)

        if ret != 0:
            raise OSError(stderr)
        else:
            snmpdata = stdout.splitlines()
            if dictionary:
                ddata = {}
                for line in snmpdata:
                    if line.strip() != '':
                        tmp = line.split(' ', 1)
                        ddata[tmp[0]] = tmp[1]
                return ddata
            else:
                ldata = []
                for line in snmpdata:
                    if line.strip() != '':
                        tmp = line.split(' ', 1)
                        ldata.append(tmp)
                return ldata

    def get(self, oid):
        # type: (str) -> str
        snmpcmd = [self.snmpbindir + 'snmpget']
        snmpcmd.extend(self.snmpcmd)
        snmpcmd.extend(['-Oq', '-On', '%s:%s' % (self.host, self.port), oid])
        stdout, stderr, ret = external.run(snmpcmd, shell=False)

        if ret != 0:
            raise OSError(stderr)
        else:
            if 'No Such' in stdout and 'at this OID' in stdout:
                raise OSError(stdout)
            else:
                return stdout.splitlines()[0].split(' ', 1)[1]

    def identify_vendor(self):
        # type: () -> Tuple[Optional[str], Optional[str], Optional[str]]
        try:
            # DELL Powerconnect
            return (
                'DELL',
                self.get('.1.3.6.1.4.1.674.10895.3000.1.2.100.3.0'),
                self.get('.1.3.6.1.4.1.674.10895.3000.1.2.100.1.0')
            )
        except OSError:
            pass

        try:
            # HP ProCurve
            return (
                'HP',
                self.get('.1.3.6.1.4.1.11.2.36.1.1.5.1.1.7.1'),
                self.get('.1.3.6.1.4.1.11.2.36.1.1.5.1.1.8.1')
            )
        except OSError:
            pass

        try:
            # Cisco
            return (
                'CISCO',
                self.get('.1.3.6.1.2.1.47.1.1.1.1.2.1'),
                self.get('.1.3.6.1.2.1.47.1.1.1.1.2.1')
            )
        except OSError:
            pass

        try:
            # DLink DGS 1210
            _ = self.get('.1.3.6.1.4.1.171.10.76.10.1.1.0')
            return 'DLINK DGS', 'DLINK', 'DGS 1210'
        except OSError:
            pass

        try:
            # 3Com
            _ = self.get('.1.3.6.1.4.1.43.47.1.1.5.2.0')
            return '3COM', '3COM', '3COM'
        except OSError:
            pass

        return None, None, None


# noinspection PyPep8Naming
class rate(object):
    @staticmethod
    def calc(path, data):
        # type: (str, dict) -> Optional[dict]

        rate_data = {}  # type: Dict[str, Union[str, float]]

        if type(data) is not dict:
            return None

        new_time = int(time.time())
        new_data = data

        try:
            tmp = files.pickle_load(path)
        # tmp = [12312313, {'value1':4234234, 'value2':4343}]
        finally:
            # neue Daten schreiben
            files.pickle_save(path, [new_time, new_data])

        old_time = tmp[0]
        old_data = tmp[1]

        if old_time is None or old_data is None:
            # erster Durchlauf oder leeres file
            for value in new_data:
                rate_data[value] = ''
            return rate_data
        else:
            # Daten vorhanden
            if old_time > new_time:
                NagiosStatus.die(NagiosStatus.code["unknown"], "old_time > new_time. Das sollte nicht so sein!")

            if old_time == new_time:
                for value in new_data:
                    new_data[value] = ''
                return new_data

            delta_time = new_time - old_time
            for value in new_data:
                if value in old_data.keys():
                    old_value = float(old_data[value])
                    new_value = float(new_data[value])
                    if new_value < old_value:
                        # Counter übergelaufen
                        rate_data[value] = ''
                    else:

                        delta_value = new_value - old_value
                        rate_data[value] = (delta_value / delta_time)
                else:
                    rate_data[value] = ''
            return rate_data


# noinspection PyPep8Naming
class files(object):
    @staticmethod
    def change_stats(path, mod, uname, gname):
        # type: (str, int, str, str) -> None
        os.chmod(path, mod)
        uid = pwd.getpwnam(uname).pw_uid
        gid = pwd.getpwnam(gname).pw_gid
        os.chown(path, uid, gid)

    @staticmethod
    def pickle_save(path, data):
        # type: (str, object) -> None
        try:
            pickle.dump(data, open(path, 'wb'))
        except IOError:
            # "except IOError as e" does not work in Python2.5
            NagiosStatus.die(NagiosStatus.code["unknown"], repr(sys.exc_info()[1]))

    @staticmethod
    def pickle_load(path):
        # type: (str) -> Tuple[int, dict]
        return pickle.load(open(path, 'rb'))


# noinspection PyPep8Naming
class livestatus(object):
    @staticmethod
    def query(msg):
        # type: (str) -> Union[str, int, Tuple, List, Dict, bool, None]
        import ast
        socket_path = '/var/cache/naemon/live'
        s = socket.socket(socket.AF_UNIX, socket.SOCK_STREAM)
        s.connect(socket_path)
        s.send(msg)
        s.shutdown(socket.SHUT_WR)
        time.sleep(0.1)
        total_data = []  # type: List[str]
        while True:
            data = s.recv(8192)
            if not data:
                if not total_data:
                    total_data = ['[[]]']
                break
            total_data.append(data)

        s.close()
        return ast.literal_eval(''.join(total_data))

    @staticmethod
    def query_tcp(msg, ip, port=6557):
        # type: (str, str, int) -> Union[str, int, Tuple, List, Dict, bool, None]
        import ast
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect((ip, port))
        s.sendall(msg)
        s.shutdown(socket.SHUT_WR)
        total_data = []  # type: List[str]
        while True:
            data = s.recv(8192)
            if not data:
                if not total_data:
                    total_data = ['[[]]']
                break
            total_data.append(data)
        s.close()
        return ast.literal_eval(''.join(total_data))


# collects Nagios Status messages and code
class NagiosStatus(object):
    info = None  # type: Tuple[List[str],List[str],List[str],List[str]]
    performance_data = None  # type: List[str]
    message = None  # type: Tuple[List[str],List[str],List[str],List[str]]
    exitcode = None  # type: int
    code = {
        "unknown": 3,
        "critical": 2,
        "warning": 1,
        "ok": 0,
    }  # type: Dict[str, int]
    code_word = {
        3: "unknown",
        2: "critical",
        1: "warning",
        0: "ok",
    }  # type: Dict[int, str]

    # constructor
    # initialize class parameters
    # @param self object pointer
    # @param program_name name of the program at the beginning of the message
    # @param info_name name of info section for additional information
    def __init__(self, program_name, info_name=None):
        # type: (str, str) -> None
        self.exitcode = self.code["ok"]
        self.message = ([], [], [], [])
        self.performance_data = []
        self.program_name = program_name
        self.info_name = info_name
        self.info = ([], [], [], [])

    # add new message and check if exitcode is worse than before
    # @param self object pointer
    # @param code new exit code if it is worse than the last code
    # @param message user message to append
    # @param performance_data performance data to append
    # @param info info to append (multiline)
    def set_code(self, code, message=None, performance_data=None, info=None):
        # type: (Union[str, int], Optional[str], str, str) -> None
        if not isinstance(code, int):
            code = self.code[code]
        if code >= self.exitcode:
            self.exitcode = code
        if message:
            self.message[code].append(message)
        if performance_data:
            self.performance_data.append(performance_data)
        if info:
            self.info[code].append(info)

    # return message for specific exit code or nothing, if no message for this code exists
    # @param self object pointer
    # @param code exit code
    def _build_message(self, code):
        # type: (int) -> List[str]
        if self.message[code]:
            return ["%s:" % self.code_word[code], ", ".join(self.message[code])]
        else:
            return []

    # return info for specific exit code or nothing, if no message for this code exists
    # @param self object pointer
    # @param code exit code
    def _build_info(self, code):
        # type: (int) -> List[str]
        if self.info[code]:
            return ["\n%s:" % self.code_word[code], ", ".join(self.info[code])]
        else:
            return []

    # immediately exit the program ignoring previously set messages and exit codes
    # @param code exit code to use
    # @param message the message to print
    @staticmethod
    def die(code, message):
        # type: (Union[str, int], str) -> NoReturn
        print(message)
        if isinstance(code, int):
            sys.exit(code)
        else:
            sys.exit(NagiosStatus.code[code])

    # immediately exit the program using the previously set messages and exit code
    # @param self object pointer
    def exit(self):
        # type: () -> NoReturn
        sys.exit(self.show_info())

    # print the previously set messages and exit code
    # @param self object pointer
    def show_info(self):
        # type: () -> int
        out_message = [self.program_name]
        if self.info_name:
            out_info = ['\n' + self.info_name]
        else:
            out_info = []
        for code in [3, 2, 1, 0]:
            out_message.extend(self._build_message(code))
            out_info.extend(self._build_info(code))

        if len(out_info) > 0:
            print("%s | %s %s" % (" ".join(out_message), " ".join(self.performance_data), " ".join(out_info)))
        else:
            print("%s | %s" % (" ".join(out_message), " ".join(self.performance_data)))
        return self.exitcode

    # set exit code to unknown if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def unknown(self, message=None, performance_data=None, info=None):
        # type: (str, str, str) -> None
        self.set_code(self.code["unknown"], message, performance_data, info)

    # set exit code to critical if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def critical(self, message=None, performance_data=None, info=None):
        # type: (str, str, str) -> None
        self.set_code(self.code["critical"], message, performance_data, info)

    # set exit code to warning if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def warning(self, message=None, performance_data=None, info=None):
        # type: (str, str, str) -> None
        self.set_code(self.code["warning"], message, performance_data, info)

    # set exit code to ok if it is worse than the last status
    # @param self object pointer
    # @param message message to append to last messages
    # @param performance_data performance data to append to last data
    # @param info message to append to last info
    def ok(self, message=None, performance_data=None, info=None):
        # type: (str, str, str) -> None
        self.set_code(self.code["ok"], message, performance_data, info)

    @staticmethod
    def generate_performance_data(
            label,  # type: str
            value,  # type: Union[str, int, float]
            unit='',  # type: str
            warn='',  # type: Union[str, int, float]
            crit='',  # type: Union[str, int, float]
            minimum='',  # type: Union[str, int, float]
            maximum=''  # type: Union[str, int, float]
    ):
        return '%s=%s%s;%s;%s;%s;%s' % (label.replace(' ', '_'), value, unit, warn, crit, minimum, maximum)


# Plugin Timeout functionality (static class)
class Timeout(object):
    # (re)starts the countdown
    # @param self object pointer
    # @param callback function to call when time runs out. signature: func(signum, frame)
    # @param timeout number of seconds to wait from start, before callback is called
    @staticmethod
    def start(callback, timeout=10):
        # type: (Callable, int) -> None
        if isinstance(timeout, int) and timeout > 0:
            signal.signal(signal.SIGALRM, callback)
            signal.alarm(timeout)
        else:
            raise ValueError("timeout must be type int and > 0")

    # stops the countdown
    # @param self object pointer
    @staticmethod
    def stop():
        signal.alarm(0)

