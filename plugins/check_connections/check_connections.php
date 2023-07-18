<?php

$color = array('#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D','#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF','#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

$opt[1] = "TEXTALIGN:right --vertical-label 'Connectioncount' --title '$hostname / $servicedesc ' --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60  ";



$ds_name[1] = "Connections";
$def[1] = '';

for($i=1; $i <= sizeof($DS); $i++) {
$def[1] .=  rrd::def("var$i", $RRDFILE[$i], $DS[$i] , "AVERAGE") ;
$def[1] .= rrd::AREA("var$i", $color[$i-1], rrd::cut(ucfirst($LABEL[$i]),25), true );
$def[1] .= rrd::gprint("var$i", array('LAST', 'MAX', 'AVERAGE'), "%8.2lf$UNIT[$i]");
}

$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[1]));
$def[1] .= "COMMENT:'Last update\: $lastupdate'";
?>
