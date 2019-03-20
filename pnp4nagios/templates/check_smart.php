<?php

$color = array(
	'#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D',
	'#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF',
	'#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

function ResolveDS_Array($id, $DS, $NAME) {
        $index = array();
        for($i=1; $i <= sizeof($DS); $i++) {
                if (stristr($NAME[$i], $id) !== false) {
                        $index[] = $i;
                }
        }
        return $index;
}

$graph = 0;
$index = ResolveDS_Array("temp" ,$DS, $NAME);
if (!empty($index)) {
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - temperatures \" --lower-limit 0 --vertical-label °C   ";
	$ds_name[$graph] = "hard drive temperatures";
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_', $label);
		if (sizeof($tmp) >= 3) {
			$label = $tmp[0] . ' ambient temperature';
		} else {
			$label = $tmp[0] . ' temperature';
		}
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),23));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf°C");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("raw_read_error_rate" ,$DS, $NAME);
if (!empty($index)) {
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - RAW Read Error Rate \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = "RAW Read Error Rate";
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' RAW Read Error Rate';
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),23));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("spinup_time" ,$DS, $NAME);
if (!empty($index)) {
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - Spinup Time \" --lower-limit 0 --vertical-label ms   ";
	$ds_name[$graph] = "Spinup Time";
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' Spinup Time';
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),23));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}
$graph++;

$index = ResolveDS_Array("realloc_sector_count" ,$DS, $NAME);
if (!empty($index)) {
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - Reallocated Sector Count \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = "Reallocated Sectore Count";
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' Reallocated Sector Count';
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("seek_error_rate" ,$DS, $NAME);
if (!empty($index)) {
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - Seek Error Rate \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = "Seek Error Rate";
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' Seek Error Rate';
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("load_cycle_count" ,$DS, $NAME);
if (!empty($index)) {
	$text = 'Load Cycle Count';
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - $text \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = $text;
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' ' . $text;
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("hardware_ecc_recovered" ,$DS, $NAME);
if (!empty($index)) {
	$text = 'Hardware ECC Recovered';
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - $text \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = $text;
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' ' . $text;
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("ssd_lifetime_remaining" ,$DS, $NAME);
if (!empty($index)) {
	$text = 'SSD Lifetime Remaining';
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - $text \" --lower-limit 0 --vertical-label %   ";
	$ds_name[$graph] = $text;
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' ' . $text;
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
$index = ResolveDS_Array("pending_remaps" ,$DS, $NAME);
if (!empty($index)) {
	$text = 'Pending unreadable Sector remaps';
	$opt[$graph] = "TEXTALIGN:right --title \"SMART Status - $text \" --lower-limit 0 --vertical-label count   ";
	$ds_name[$graph] = $text;
	$def[$graph] = '';
	for($i=0; $i < sizeof($index); $i++) {
		$label = $NAME[$index[$i]];
		$tmp = explode('_',$label);
		$label = $tmp[0] . ' ' . $text;
		$id = $index[$i];
		$var = str_replace(' ', '_', $label);
		$def[$graph] .= rrd::def($var, $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2($var, $color[$i], rrd::cut(ucfirst($label),32));
		$def[$graph] .= rrd::gprint($var, array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}
$graph++;
