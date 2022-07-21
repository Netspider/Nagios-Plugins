<?php

$color = array('#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D','#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF','#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

$opt[1] = "--vertical-label '%' --base 1000 -r --lower-limit 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / $servicedesc '";

$ds_name[1] = 'CPU usage';
$def[1] = '';


# subtract guest from user
# subtract guest_nice from nice

# DEBUG: system
$def[1] .= rrd::def("var3", $RRDFILE[3], $DS[3] , "AVERAGE") ;
$def[1] .= rrd::AREA("var3", $color[0], rrd::cut(ucfirst("system"),11));
$def[1] .= rrd::gprint("var3", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[3]");
# subtract guest from user
$def[1] .= rrd::def("var9", $RRDFILE[9], $DS[9] , "AVERAGE") ;
$def[1] .= rrd::def("var1", $RRDFILE[1], $DS[1] , "AVERAGE") ;
$def[1] .= rrd::cdef("var9_custom","var9,UN,0,var9,IF");
# DEBUG: user
$def[1] .= rrd::cdef("user-guest","var1,var9_custom,-");
$def[1] .= rrd::AREA("user-guest", $color[1], rrd::cut(ucfirst("user"),11), true);
$def[1] .= rrd::gprint("user-guest", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[1]");
##DEBUG: guest
$def[1] .= rrd::AREA("var9_custom", $color[8], rrd::cut(ucfirst("guest"),11), true);
$def[1] .= rrd::gprint("var9_custom", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[9]");
# subtract guest_nice from nice
#DEBUG: guest_nice
$def[1] .= rrd::def("var2", $RRDFILE[2], $DS[2] , "AVERAGE") ;
$def[1] .= rrd::def("var10", $RRDFILE[10], $DS[10] , "AVERAGE") ;
$def[1] .= rrd::cdef("var10_custom","var10,UN,0,var10,IF");
$def[1] .= rrd::cdef("nice-guestnice","var2,var10_custom,-");
$def[1] .= rrd::AREA("nice-guestnice", $color[2], rrd::cut(ucfirst("nice"),11), true);
$def[1] .= rrd::gprint("nice-guestnice", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[2]");
$def[1] .= rrd::AREA("var10_custom", $color[9], rrd::cut(ucfirst("guest nice"),11), true);
$def[1] .= rrd::gprint("var10_custom", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[9]");
# DEBUG: idle
$def[1] .= rrd::def("var4", $RRDFILE[4], $DS[4] , "AVERAGE") ;
$def[1] .= rrd::AREA("var4", $color[3], rrd::cut(ucfirst("idle"),11), true);
$def[1] .= rrd::gprint("var4", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[4]");
# DEBUG: iowait
$def[1] .= rrd::def("var5", $RRDFILE[5], $DS[5] , "AVERAGE") ;
$def[1] .= rrd::AREA("var5", $color[4], rrd::cut(ucfirst("iowait"),11), true);
$def[1] .= rrd::gprint("var5", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[5]");
# DEBUG: irq
$def[1] .= rrd::def("var6", $RRDFILE[6], $DS[6] , "AVERAGE") ;
$def[1] .= rrd::AREA("var6", $color[5], rrd::cut(ucfirst("irq"),11), true);
$def[1] .= rrd::gprint("var6", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[6]");
# DEBUG: softirq
$def[1] .= rrd::def("var7", $RRDFILE[7], $DS[7] , "AVERAGE") ;
$def[1] .= rrd::AREA("var7", $color[6], rrd::cut(ucfirst("softirq"),11), true);
$def[1] .= rrd::gprint("var7", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[7]");
#DEBUG: steal
$def[1] .= rrd::def("var8", $RRDFILE[8], $DS[8] , "AVERAGE") ;
$def[1] .= rrd::AREA("var8", $color[7], rrd::cut(ucfirst("steal"),11), true);
$def[1] .= rrd::gprint("var8", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[8]");

$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[1]));
$def[1] .= "COMMENT:'Last update\: $lastupdate'";
?>

