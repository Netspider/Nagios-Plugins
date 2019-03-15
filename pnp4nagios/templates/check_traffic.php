<?php

# ethX first
if (!function_exists('prioritizeInterfaces')) {
    function prioritizeInterfaces($ifaces) {
        $ifaces_sorted_ethx = array();
        $ifaces_sorted_rest = array();

        foreach ($ifaces as $iface) {
            if (preg_match("/eth\d{1,}$/", $iface) == 1) {
                $ifaces_sorted_ethx[] = $iface;
            } else {
                $ifaces_sorted_rest[] = $iface;
            }
        }
        return array_merge($ifaces_sorted_ethx, $ifaces_sorted_rest);
    }
}


$graph = 1;
$ifaces = array();

for ($a=1; $a <= sizeof($DS); $a++) {
    if (substr($NAME[$a], -3) === "_TX") {
        $iface = explode('_', $NAME[$a]);
        $ifaces[] = $iface[0];
    }
}

$ifaces = array_unique($ifaces);
$ifaces = prioritizeInterfaces($ifaces);

####################################################################
#
# Traffic
#
####################################################################

foreach ($ifaces as $iface) {
    if ($iface != 'tun') {
        $iface_rx = array_search("{$iface}_RX", $NAME);
        $iface_tx = array_search("{$iface}_TX", $NAME);

        $opt[$graph]= "TEXTALIGN:right --vertical-label \"Mbit\" --title \"Traffic $iface\"  ";
        if ($CRIT[$iface_rx] > 0) {
            if ($ACT[$iface_rx] >= $WARN[$iface_rx] and $ACT[$iface_rx] < $CRIT[$iface_rx] or
                $ACT[$iface_tx] >= $WARN[$iface_tx] and $ACT[$iface_tx] < $CRIT[$iface_tx]
            ) {
                $opt[$graph] = "TEXTALIGN:right --color BACK#FFFF01 --color FONT#000000 --vertical-label \"bit/s\" --title \"Traffic\"  ";
            }
            elseif ($ACT[$iface_rx] >= $CRIT[$iface_rx] or $ACT[$iface_tx] >= $CRIT[$iface_tx]) {
                $opt[$graph] = "TEXTALIGN:right --color BACK#FE2E2E --color FONT#FFFFFF --vertical-label \"bit/s\" --title \"Traffic\"  ";
            }
        }

        $ds_name[$graph] = "Traffic $iface";
        $def[$graph] = '';

        $def[$graph] .= rrd::def("incoming", $RRDFILE[$iface_rx], $DS[$iface_rx] , "AVERAGE") ;
        $def[$graph] .= rrd::AREA("incoming", "#00b300", rrd::cut("Incoming ($iface)",18));
        $def[$graph] .= rrd::gprint("incoming", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$iface_rx]");

        $def[$graph] .= rrd::def("outgoing", $RRDFILE[$iface_tx], $DS[$iface_tx] , "AVERAGE") ;
        $def[$graph] .= rrd::line1("outgoing", "#0066B3", rrd::cut("Outgoing ($iface)",18));
        $def[$graph] .= rrd::gprint("outgoing", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$iface_tx]");

        $maximum = $MAX[$iface_tx];
        if ($maximum > 0) {
            $def[$graph] .= rrd::hrule( $maximum, "#FF0000", "maximum bandwidth\: $maximum MBit/s \\n");
        }

        $lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
        $def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

        $graph++;
    }
}

####################################################################
#
# Errors
#
####################################################################

foreach ($ifaces as $iface) {
    if ($iface != 'tun') {
        $iface_rx_error = array_search("{$iface}_RX_error", $NAME);
        $iface_tx_error = array_search("{$iface}_TX_error", $NAME);

        $opt[$graph]= "TEXTALIGN:right --vertical-label \"%\" --title \"Interface Errors (errors, dropped, overruns, frame) $iface\"  ";
        $ds_name[$graph] = "Interface Errors $iface";
        $def[$graph] = '';

        $def[$graph] .= rrd::def("incoming", $RRDFILE[$iface_rx_error], $DS[$iface_rx_error] , "AVERAGE") ;
        $def[$graph] .= rrd::AREA("incoming", "#00b300", rrd::cut("Errors In  ($iface) ",18));
        $def[$graph] .= rrd::gprint("incoming", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$iface_rx_error]");

        $def[$graph] .= rrd::def("outgoing", $RRDFILE[$iface_tx_error], $DS[$iface_tx_error] , "AVERAGE") ;
        $def[$graph] .= rrd::line1("outgoing", "#0066B3", rrd::cut("Errors Out ($iface)",18));
        $def[$graph] .= rrd::gprint("outgoing", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$iface_tx_error]");

        $lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$iface_rx_error]));
        $def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

        $graph++;
    }
}

####################################################################
#
# Retransmit
#
####################################################################

$retransmitted_rate = array_search("retransmitted_rate", $NAME);

$opt[$graph]= "TEXTALIGN:right --vertical-label \"%\" --title \"Retransmitted Rate\"  ";
$ds_name[$graph] = "Retransmitted Rate";
$def[$graph] = '';

$def[$graph] .= rrd::def("re", $RRDFILE[$retransmitted_rate], $DS[$retransmitted_rate] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("re", "#00b300", rrd::cut("Retransmitted Rate",18));
$def[$graph] .= rrd::gprint("re", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$retransmitted_rate]");

$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$retransmitted_rate]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;