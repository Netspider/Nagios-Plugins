<?php

$opt[1] = "--vertical-label 'count' -u 100 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono TEXTALIGN:right -S 60   --title \"$hostname / $servicedesc \"";

$ds_name[1] = 'Conntrack Table';
$def[1] = '';

$def[1] .= rrd::def("var1", $RRDFILE[1], $DS[1] , "AVERAGE") ;
$def[1] .= rrd::AREA("var1", "#00CC00", rrd::cut(ucfirst("conntrack_count"),27));
$def[1] .= rrd::gprint("var1", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[1]");

$def[1] .= rrd::def("var2", $RRDFILE[2], $DS[2] , "AVERAGE") ;
$def[1] .= rrd::LINE1("var2", "#FF0000", rrd::cut(ucfirst("conntrack_max"),27));
$def[1] .= rrd::gprint("var2", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[2]");




$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
$def[1] .= "COMMENT:'Last update\: $lastupdate'";
?>
