<?php

$hdds = array();

for ($i=1; $i <= sizeof($DS); $i++) {

        if (strpos($NAME[$i],'-read_sectors') != false) {
                $hdd = explode('-',$NAME[$i]);
                $hdds[] = $hdd[0];
        }
}

$graph = 0;

for ($k=0; $k < sizeof($hdds); $k++) {

        $read_ios = array_search($hdds[$k].'-read_ios', $NAME);
        $write_ios = array_search($hdds[$k].'-write_ios', $NAME);

	$opt[$graph] = "TEXTALIGN:right --title \"$hdds[$k] - IOPS\" --vertical-label \"IOPS\"   ";
	$ds_name[$graph] = "Diskstats";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("read_ios$k", $RRDFILE[$read_ios], $DS[$read_ios] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("read_ios$k", "#0000FF", rrd::cut(ucfirst("$hdds[$k] - read ops"),24));
	$def[$graph] .= rrd::gprint("read_ios$k", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("write_ios$k", $RRDFILE[$write_ios], $DS[$write_ios] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("write_ios$k", "#00FF00", rrd::cut(ucfirst("$hdds[$k] - write ops"),24), true);
	$def[$graph] .= rrd::gprint("write_ios$k", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::cdef("io$k", "write_ios$k,read_ios$k,+");
	$def[$graph] .= rrd::LINE2("io$k", "#000000", rrd::cut(ucfirst("$hdds[$k] - total IOPS"),24));
	$def[$graph] .= rrd::gprint("io$k", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$write_ios]));
        $def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;

}

#$graph = 0;

for ($j=0; $j < sizeof($hdds); $j++) {

	$read_sectors = array_search($hdds[$j].'-read_sectors', $NAME);
	$write_sectors = array_search($hdds[$j].'-write_sectors', $NAME);


	$opt[$graph] = "TEXTALIGN:right --title \"$hdds[$j] - Disk Sectors Read/Written\" --vertical-label \"per second\"   ";
	$ds_name[$graph] = "Diskstats";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("read_sectors$j", $RRDFILE[$read_sectors], $DS[$read_sectors] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("read_sectors$j", "#542437", rrd::cut(ucfirst("$hdds[$j] - Sectors Read"),24));
	$def[$graph] .= rrd::cdef("read_sectors_custom$j", "read_sectors$j,1000,/");
	$def[$graph] .= rrd::gprint("read_sectors_custom$j", array("LAST", "AVERAGE", "MAX"), "%.2lfk");
	$def[$graph] .= rrd::def("write_sectors$j", $RRDFILE[$write_sectors], $DS[$write_sectors] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef("write_sectors_custom$j", "write_sectors$j,-1,*");
	$def[$graph] .= rrd::AREA("write_sectors_custom$j", "#53777A", rrd::cut(ucfirst("$hdds[$j] - Sectors Written"),24));
	$def[$graph] .= rrd::cdef("write_sectors_custom2$j", "write_sectors$j,1000,/");
	$def[$graph] .= rrd::gprint("write_sectors_custom2$j", array("LAST", "AVERAGE", "MAX"), "%.2lfk");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$write_sectors]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph++;

	################################################

        $read_merges = array_search($hdds[$j].'-read_merges', $NAME);
        $write_merges = array_search($hdds[$j].'-write_merges', $NAME);
        $read_ios = array_search($hdds[$j].'-read_ios', $NAME);
        $write_ios = array_search($hdds[$j].'-write_ios', $NAME);


	$opt[$graph] = "TEXTALIGN:right --title \"$hdds[$j] - Disk Operations\" --vertical-label \"per second\"   ";
	$ds_name[$graph] = "Diskstats";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("read_ios$j", $RRDFILE[$read_ios], $DS[$read_ios] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("read_ios$j", "#2A2829", rrd::cut(ucfirst("$hdds[$j] - Reads"),24));
	$def[$graph] .= rrd::gprint("read_ios$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("read_merges$j", $RRDFILE[$read_merges], $DS[$read_merges] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("read_merges$j", "#668284", rrd::cut(ucfirst("$hdds[$j] - Reads Merged"),24), true);
	$def[$graph] .= rrd::gprint("read_merges$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("write_ios$j", $RRDFILE[$write_ios], $DS[$write_ios] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef("write_ios_custom$j", "write_ios$j,-1,*");
	$def[$graph] .= rrd::AREA("write_ios_custom$j", "#493736", rrd::cut(ucfirst("$hdds[$j] - Writes"),24));
	$def[$graph] .= rrd::gprint("write_ios$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("write_merges$j", $RRDFILE[$write_merges], $DS[$write_merges] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef("write_merges_custom$j", "write_merges$j,-1,*");
	$def[$graph] .= rrd::AREA("write_merges_custom$j", "#7B3B3B", rrd::cut(ucfirst("$hdds[$j] - Writes Merged"),24), true);
	$def[$graph] .= rrd::gprint("write_merges$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$write_merges]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph++;

	$io_ticks = array_search($hdds[$j].'-io_ticks', $NAME);
	$io_queue = array_search($hdds[$j].'-time_in_queue', $NAME);

	$opt[$graph] = "TEXTALIGN:right --title \"$hdds[$j] - Disk Elapsed IO Time\" --vertical-label \"ms\"   ";
	$ds_name[$graph] = "Diskstats";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("io_ticks$j", $RRDFILE[$io_ticks], $DS[$io_ticks] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE1("io_ticks$j", "#4E3F30", rrd::cut(ucfirst("$hdds[$j] - IO Time"),24));
	$def[$graph] .= rrd::gprint("io_ticks$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("io_queue$j", $RRDFILE[$io_queue], $DS[$io_queue] , "AVERAGE") ;
	$def[$graph] .= rrd::LINE1("io_queue$j", "#2C5043", rrd::cut(ucfirst("$hdds[$j] - IO Time weighted"),24));
	$def[$graph] .= rrd::gprint("io_queue$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$io_queue]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph++;

	$read_ticks = array_search($hdds[$j].'-read_ticks', $NAME);
	$write_ticks = array_search($hdds[$j].'-write_ticks', $NAME);

	$opt[$graph] = "TEXTALIGN:right --title \"$hdds[$j] - Disk Read/Write Time\" --vertical-label \"ms\"   ";
	$ds_name[$graph] = "Diskstats";
	$def[$graph] = '';
	$def[$graph] .= rrd::def("read_ticks$j", $RRDFILE[$read_ticks], $DS[$read_ticks] , "AVERAGE") ;
	$def[$graph] .= rrd::AREA("read_ticks$j", "#755E5E", rrd::cut(ucfirst("$hdds[$j] - Time Spent Reading"),24));
	$def[$graph] .= rrd::gprint("read_ticks$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$def[$graph] .= rrd::def("write_ticks$j", $RRDFILE[$write_ticks], $DS[$write_ticks] , "AVERAGE") ;
	$def[$graph] .= rrd::cdef("write_ticks_custom$j", "write_ticks$j,-1,*");
	$def[$graph] .= rrd::AREA("write_ticks_custom$j", "#C02942", rrd::cut(ucfirst("$hdds[$j] - Time Spent Writing"),24));
	$def[$graph] .= rrd::gprint("write_ticks$j", array("LAST", "AVERAGE", "MAX"), "%.2lf");
	$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$read_ticks]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph++;
}

