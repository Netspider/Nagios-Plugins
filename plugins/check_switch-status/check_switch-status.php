<?php

$color = array('#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D','#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF','#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

function ResolveDS_Array($id, $NAME) {
        $index = array();
        for($i=1; $i <= sizeof($NAME); $i++) {
                if (strpos($NAME[$i], $id) === 0) {
                        $index[] = $i;
                }
        }
        return $index;
}

$graph = 0;

######################################################################
#
# HP
#
######################################################################
$hp_cpu = array_search("hp_cpu", $NAME);
if (is_numeric($hp_cpu)) {
	$opt[$graph] = "TEXTALIGN:right --title \"HP Switch CPU \" --lower-limit 0 --vertical-label % --upper-limit 100";
	$ds_name[$graph] = "HP Switch CPU";
	$def[$graph] = '';
	$def[$graph] .= rrd::def('hp_cpu', $RRDFILE[$hp_cpu], $DS[$hp_cpu] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('max','hp_cpu,hp_cpu,-,100,+' );
	$def[$graph] .= rrd::AREA('max', "#81F79F");
	$def[$graph] .= rrd::AREA('hp_cpu', $color[0], rrd::cut(ucfirst("CPU Auslastung"),23));
	$def[$graph] .= rrd::gprint('hp_cpu', array("LAST",'MAX','AVERAGE'), "%6.0lf%%");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;

$hp_memory_total = array_search("hp_memory_total", $NAME);
$hp_memory_alloc = array_search("hp_memory_alloc", $NAME);
$hp_memory_free = array_search("hp_memory_free", $NAME);

if (is_numeric($hp_memory_total)) {
	$opt[$graph] = "TEXTALIGN:right --title \"HP Switch Memory \" --lower-limit 0 --vertical-label Byte";
	$ds_name[$graph] = "HP Switch Memory";
	$def[$graph] = '';

	$def[$graph] .= rrd::def('hp_memory_total', $RRDFILE[$hp_memory_total], $DS[$hp_memory_total] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_total', 'hp_memory_total,1000,/,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_total', '#81F79F', rrd::cut(ucfirst("total"),23));
	$def[$graph] .= rrd::gprint('mb_hp_memory_total', array("LAST",'MAX','AVERAGE'), "%.2lf MB");

	$def[$graph] .= rrd::def('hp_memory_alloc', $RRDFILE[$hp_memory_alloc], $DS[$hp_memory_alloc] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_alloc', 'hp_memory_alloc,1000,/,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_alloc', '#008000', rrd::cut(ucfirst("allocated"),23));
	$def[$graph] .= rrd::gprint('mb_hp_memory_alloc', array("LAST",'MAX','AVERAGE'), "%.2lf MB");

	$def[$graph] .= rrd::def('hp_memory_free', $RRDFILE[$hp_memory_free], $DS[$hp_memory_free] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_free', 'hp_memory_free,1000,/,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_free', '#ADFF2F', rrd::cut(ucfirst("free"),23), true);
	$def[$graph] .= rrd::gprint('mb_hp_memory_free', array("LAST",'MAX','AVERAGE'), "%.2lf MB");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;

$hp_memory_slabcount = array_search("hp_memory_slabcount", $NAME);
$hp_memory_freesegcount = array_search("hp_memory_freesegcount", $NAME);
$hp_memory_allocsegcount = array_search("hp_memory_allocsegcount", $NAME);

if (is_numeric($hp_memory_slabcount)) {
	$opt[$graph] = "TEXTALIGN:right --title \"HP Switch Memory - Kernel \" --lower-limit 0 --vertical-label Byte";
	$ds_name[$graph] = "HP Switch Memory - Kernel";
	$def[$graph] = '';

	$def[$graph] .= rrd::def('hp_memory_slabcount', $RRDFILE[$hp_memory_slabcount], $DS[$hp_memory_slabcount] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_slabcount', 'hp_memory_slabcount,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_slabcount', '#81F79F', rrd::cut(ucfirst("Slabcount"),23));
	$def[$graph] .= rrd::gprint('mb_hp_memory_slabcount', array("LAST",'MAX','AVERAGE'), "%.2lf kB");

	$def[$graph] .= rrd::def('hp_memory_freesegcount', $RRDFILE[$hp_memory_freesegcount], $DS[$hp_memory_freesegcount] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_freesegcount', 'hp_memory_freesegcount,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_freesegcount', '#008000', rrd::cut(ucfirst("freesegcount"),23));
	$def[$graph] .= rrd::gprint('mb_hp_memory_freesegcount', array("LAST",'MAX','AVERAGE'), "%.2lf kB");

	$def[$graph] .= rrd::def('hp_memory_allocsegcount', $RRDFILE[$hp_memory_allocsegcount], $DS[$hp_memory_allocsegcount] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef('mb_hp_memory_allocsegcount', 'hp_memory_allocsegcount,1000,/');
	$def[$graph] .= rrd::AREA('hp_memory_allocsegcount', '#ADFF2F', rrd::cut(ucfirst("allocsegcount"),23), true);
	$def[$graph] .= rrd::gprint('mb_hp_memory_allocsegcount', array("LAST",'MAX','AVERAGE'), "%.2lf kB");

	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}


####################################################
#
# DELL
#
####################################################

$dell_cpu_5s = array_search("dell_cpu_5s", $NAME);
$dell_cpu_1min = array_search("dell_cpu_1min", $NAME);
$dell_cpu_5min = array_search("dell_cpu_5min", $NAME);

if (is_numeric($dell_cpu_5s)) {
	$opt[$graph] = "TEXTALIGN:right --title \"DELL Switch CPU \" --lower-limit 0 --vertical-label %";
	$ds_name[$graph] = "DELL Switch CPU";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("var5min", $RRDFILE[$dell_cpu_5min], $DS[$dell_cpu_5min] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var5min", '#FF0000', rrd::cut(ucfirst('CPU 5min'),23));
	$def[$graph] .= rrd::gprint("var5min", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$def[$graph] .= rrd::def("var1min", $RRDFILE[$dell_cpu_1min], $DS[$dell_cpu_1min] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var1min", '#EA8F00', rrd::cut(ucfirst('CPU 1min'),23));
	$def[$graph] .= rrd::gprint("var1min", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$def[$graph] .= rrd::def("var5s", $RRDFILE[$dell_cpu_5s], $DS[$dell_cpu_5s] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var5s", '#EACC00', rrd::cut(ucfirst('CPU 5s'),23));
	$def[$graph] .= rrd::gprint("var5s", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;


$dell_temp = array_search("dell_temp", $NAME);
if (is_numeric($dell_temp)) {
	$opt[$graph] = "TEXTALIGN:right --title \"DELL Switch Temperaturen \" --lower-limit 0 --vertical-label °C";
	$ds_name[$graph] = "DELL Switch Temperatur";
	$def[$graph] = '';
	$def[$graph] .= rrd::def('dell_temp', $RRDFILE[$dell_temp], $DS[$dell_temp] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA('dell_temp', $color[0], rrd::cut(ucfirst("Temperatur"),23));
	$def[$graph] .= rrd::gprint('dell_temp', array("LAST",'MAX','AVERAGE'), "%6.0lf°C");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$dell_temp]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;
########################################################
#
# CISCO
#
########################################################
$cisco_cpu_5s = array_search("cisco_cpu_id0_5s", $NAME);
$cisco_cpu_1min = array_search("cisco_cpu_id0_1min", $NAME);
$cisco_cpu_5min = array_search("cisco_cpu_id0_5min", $NAME);
if (is_numeric($cisco_cpu_5s)) {
	$opt[$graph] = "TEXTALIGN:right --title \"Cisco Switch CPU \" --lower-limit 0 --vertical-label %";
	$ds_name[$graph] = "Cisco Switch CPU";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("var5min", $RRDFILE[$cisco_cpu_5min], $DS[$cisco_cpu_5min] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var5min", '#FF0000', rrd::cut(ucfirst('CPU 5min'),23));
	$def[$graph] .= rrd::gprint("var5min", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$def[$graph] .= rrd::def("var1min", $RRDFILE[$cisco_cpu_1min], $DS[$cisco_cpu_1min] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var1min", '#EA8F00', rrd::cut(ucfirst('CPU 1min'),23));
	$def[$graph] .= rrd::gprint("var1min", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$def[$graph] .= rrd::def("var5s", $RRDFILE[$cisco_cpu_5s], $DS[$cisco_cpu_5s] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("var5s", '#EACC00', rrd::cut(ucfirst('CPU 5s'),23));
	$def[$graph] .= rrd::gprint("var5s", array("LAST",'MAX','AVERAGE'), "%6.2lf %%");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;

$cisco_temp = ResolveDS_Array("cisco_temp", $NAME);

if (sizeof($cisco_temp) >= 1) {
	$opt[$graph] = "TEXTALIGN:right --title \"Cisco Switch Temperaturen \" --lower-limit 0 --vertical-label %";
	$ds_name[$graph] = "Switch Temperatur";
	$def[$graph] = '';
	for($i=0; $i < sizeof($cisco_temp); $i++) {
		$id = $cisco_temp[$i];
		$def[$graph] .= rrd::def("ctempvar$i", $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
		$def[$graph] .= rrd::LINE2("ctempvar$i", $color[$i], rrd::cut(ucfirst($NAME[$id]),23));
		$def[$graph] .= rrd::gprint("ctempvar$i", array("LAST",'MAX','AVERAGE'), "%6.0lf");
	}
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;

$cisco_mem_used = array_search("cisco_mem_used", $NAME);
$cisco_mem_free = array_search("cisco_mem_free", $NAME);
$cisco_mem_kernel = array_search("cisco_mem_kernel", $NAME);
$cisco_mem_total = array_search("cisco_mem_total", $NAME);


if (is_numeric($cisco_mem_total) >= 1) {
	$opt[$graph] = "TEXTALIGN:right --title \"Cisco Switch Memory \" --lower-limit 0 --vertical-label %";
	$ds_name[$graph] = "Cisco Switch Memory";
	$def[$graph] = '';

	$def[$graph] .= rrd::def('cisco_mem_used', $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE2('cisco_mem_used', $color[$i], rrd::cut(ucfirst($NAME[$id]),23));
	$def[$graph] .= rrd::gprint('cisco_mem_used', array("LAST",'MAX','AVERAGE'), "%6.0lf");

	$def[$graph] .= rrd::def('cisco_mem_free', $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE2('cisco_mem_free', $color[$i], rrd::cut(ucfirst($NAME[$id]),23));
	$def[$graph] .= rrd::gprint('cisco_mem_free', array("LAST",'MAX','AVERAGE'), "%6.0lf");

	$def[$graph] .= rrd::def('cisco_mem_kernel', $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE2('cisco_mem_kernel', $color[$i], rrd::cut(ucfirst($NAME[$id]),23));
	$def[$graph] .= rrd::gprint('cisco_mem_kernel', array("LAST",'MAX','AVERAGE'), "%6.0lf");

	$def[$graph] .= rrd::def('cisco_mem_total', $RRDFILE[$id], $DS[$id] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE2('cisco_mem_total', $color[$i], rrd::cut(ucfirst($NAME[$id]),23));
	$def[$graph] .= rrd::gprint('cisco_mem_total', array("LAST",'MAX','AVERAGE'), "%6.0lf");

	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}

$graph++;

# Temperatures
$temp = false;

for ($i=1;$i<=sizeof($NAME);$i++) {
	if (preg_match("/^temp/", $NAME[$i]) == 1) {
		$temp = true;
		break;
	}
}

if ($temp) {
	$opt[$graph] = "TEXTALIGN:right --vertical-label \"°C\" --title '$hostname / $servicedesc ' --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60  ";

	$ds_name[$graph] = "Temperatures";
	$def[$graph] = '';

	for($i=1; $i <= sizeof($DS); $i++) {
		if (preg_match("/^temp/", $NAME[$i]) == 1) {
			$def[$graph] .= rrd::def("var$i", $RRDFILE[$i], $DS[$i] , "AVERAGE") ;
			$def[$graph] .= rrd::line1("var$i", $color[$i-1], rrd::cut(ucfirst($LABEL[$i]),25) );
			$def[$graph] .= rrd::gprint("var$i", array('LAST', 'MAX', 'AVERAGE'), "%8.2lf$UNIT[$i]");
		}
	}

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[1]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
}
