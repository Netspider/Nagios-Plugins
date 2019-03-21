<?php

$graph = 1;

####################################################################
#
# connected_clients
#
####################################################################

$connected_clients = array_search("connected_clients",$NAME);
$blocked_clients = array_search("blocked_clients",$NAME);

if (is_int($connected_clients) and is_int($blocked_clients))
{
	$opt[$graph] = "--vertical-label 'Connected Clients' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Connected Clients'";
	$ds_name[$graph] = 'Connected Clients';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("blocked_clients", $RRDFILE[$blocked_clients], $DS[$blocked_clients] , "AVERAGE");
	$def[$graph] .= rrd::AREA("blocked_clients", "#F00", rrd::cut("Blocked Clients",18), true);
	$def[$graph] .= rrd::gprint("blocked_clients", array("LAST", "AVERAGE", "MAX"), "%10.2lf");

	$def[$graph] .= rrd::def("connected_clients", $RRDFILE[$connected_clients], $DS[$connected_clients] , "AVERAGE");
	$def[$graph] .= rrd::AREA("connected_clients", "#88F", rrd::cut("Connected Clients",18), true);
	$def[$graph] .= rrd::gprint("connected_clients", array("LAST", "AVERAGE", "MAX"), "%10.2lf");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$connected_clients]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;
}

####################################################################
#
# memory
#
####################################################################

$maxmemory = array_search("maxmemory",$NAME);
$used_memory = array_search("used_memory",$NAME);
$used_memory_rss = array_search("used_memory_rss",$NAME);
$used_memory_lua = array_search("used_memory_lua",$NAME);

/*
 * used by redis (used-lua)
 * used by lua (lua)
 * fragmentation (rss-used)
 * unused (max-rss)
 * */
if (is_int($maxmemory) and is_int($used_memory) and is_int($used_memory_rss) and is_int($used_memory_lua))
{
	$opt[$graph] = "--vertical-label 'Bytes' --base 1024 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Memory Usage'";
	$ds_name[$graph] = 'Memory Usage';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("maxmemory", $RRDFILE[$maxmemory], $DS[$maxmemory] , "AVERAGE");
	$def[$graph] .= rrd::def("used_memory", $RRDFILE[$used_memory], $DS[$used_memory] , "AVERAGE");
	$def[$graph] .= rrd::def("used_memory_rss", $RRDFILE[$used_memory_rss], $DS[$used_memory_rss] , "AVERAGE");
	$def[$graph] .= rrd::def("used_memory_lua", $RRDFILE[$used_memory_lua], $DS[$used_memory_lua] , "AVERAGE");

	$def[$graph] .= rrd::cdef("used_memory_redis", "used_memory,used_memory_lua,-");
	$def[$graph] .= rrd::cdef("fragmentation", "used_memory_rss,used_memory,-");
	$def[$graph] .= rrd::cdef("unused_memory", "maxmemory,used_memory_rss,-");

	$def[$graph] .= rrd::cdef("used_memory_redis_mb", "used_memory_redis,1024,/,1024,/");
	$def[$graph] .= rrd::cdef("used_memory_lua_mb", "used_memory_lua,1024,/,1024,/");
	$def[$graph] .= rrd::cdef("fragmentation_mb", "fragmentation,1024,/,1024,/");
	$def[$graph] .= rrd::cdef("unused_memory_mb", "unused_memory,1024,/,1024,/");


	$def[$graph] .= rrd::AREA("used_memory_redis", "#2A2", rrd::cut("Used by Redis",18), true);
	$def[$graph] .= rrd::gprint("used_memory_redis_mb", array("LAST", "AVERAGE", "MAX"), "%10.2lf MB");

	$def[$graph] .= rrd::AREA("used_memory_lua", "#008", rrd::cut("Used by Lua",18), true);
	$def[$graph] .= rrd::gprint("used_memory_lua_mb", array("LAST", "AVERAGE", "MAX"), "%10.2lf MB");

	$def[$graph] .= rrd::AREA("fragmentation", "#800", rrd::cut("Fragmented Memory",18), true);
	$def[$graph] .= rrd::gprint("fragmentation_mb", array("LAST", "AVERAGE", "MAX"), "%10.2lf MB");

	$def[$graph] .= rrd::AREA("unused_memory", "#CCC", rrd::cut("Unused",18), true);
	$def[$graph] .= rrd::gprint("unused_memory_mb", array("LAST", "AVERAGE", "MAX"), "%10.2lf MB");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$maxmemory]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;
}

####################################################################
#
# traffic
#
####################################################################

$total_net_input_bytes = array_search("total_net_input_bytes",$NAME);
$total_net_output_bytes = array_search("total_net_output_bytes",$NAME);

if (is_int($total_net_input_bytes) and is_int($total_net_output_bytes))
{
	$opt[$graph] = "--vertical-label 'Bit/s' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Traffic'";
	$ds_name[$graph] = 'Traffic';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("input", $RRDFILE[$total_net_input_bytes], $DS[$total_net_input_bytes] , "AVERAGE");
	$def[$graph] .= rrd::def("output", $RRDFILE[$total_net_output_bytes], $DS[$total_net_output_bytes] , "AVERAGE");

	$def[$graph] .= rrd::cdef("input_bit_ps", "input,8,*");
	$def[$graph] .= rrd::cdef("output_bit_ps", "output,8,*");
	$def[$graph] .= rrd::cdef("input_mbit_ps", "input_bit_ps,1024,/,1024,/");
	$def[$graph] .= rrd::cdef("output_mbit_ps", "output_bit_ps,1024,/,1024,/");

	$def[$graph] .= rrd::AREA("input_bit_ps", "#0B0", rrd::cut("Herein",18), true);
	$def[$graph] .= rrd::gprint("input_mbit_ps", array("LAST", "AVERAGE", "MAX"), "%10.2lf Mbps");

	$def[$graph] .= rrd::LINE1("output_bit_ps", "#06B", rrd::cut("Hinaus",18), true);
	$def[$graph] .= rrd::gprint("output_mbit_ps", array("LAST", "AVERAGE", "MAX"), "%10.2lf Mbps");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$total_net_input_bytes]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;
}

####################################################################
#
# hits/misses
#
####################################################################

$keyspace_hits = array_search("keyspace_hits",$NAME);
$keyspace_misses = array_search("keyspace_misses",$NAME);

if (is_int($keyspace_hits) and is_int($keyspace_misses))
{
	$opt[$graph] = "--vertical-label '1/s' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Hits/Misses'";
	$ds_name[$graph] = 'Hits/Misses';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("hits", $RRDFILE[$keyspace_hits], $DS[$keyspace_hits] , "AVERAGE");
	$def[$graph] .= rrd::def("misses", $RRDFILE[$keyspace_misses], $DS[$keyspace_misses] , "AVERAGE");

	$def[$graph] .= rrd::AREA("hits", "#2A2", rrd::cut("Hits",18), true);
	$def[$graph] .= rrd::gprint("hits", array("LAST", "AVERAGE", "MAX"), "%10.2lf/s");

	$def[$graph] .= rrd::AREA("misses", "#A22", rrd::cut("Misses",18), true);
	$def[$graph] .= rrd::gprint("misses", array("LAST", "AVERAGE", "MAX"), "%10.2lf/s");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$keyspace_hits]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;

	########################

	$opt[$graph] = "--vertical-label '%' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Hitrate'";
	$ds_name[$graph] = 'Hitrate';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("hits", $RRDFILE[$keyspace_hits], $DS[$keyspace_hits] , "AVERAGE");
	$def[$graph] .= rrd::def("misses", $RRDFILE[$keyspace_misses], $DS[$keyspace_misses] , "AVERAGE");

	$def[$graph] .= rrd::cdef("hit_pc", "hits,hits,misses,+,/,100,*");
	$def[$graph] .= rrd::LINE1("hit_pc", "#88F", rrd::cut("Hitrate",18), true);
	$def[$graph] .= rrd::gprint("hit_pc", array("LAST", "AVERAGE", "MAX"), "%10.2lf %%");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$keyspace_hits]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;
}

####################################################################
#
# cpu
#
####################################################################

$used_cpu_sys = array_search("used_cpu_sys",$NAME);
$used_cpu_user = array_search("used_cpu_user",$NAME);
$used_cpu_sys_children = array_search("used_cpu_sys_children",$NAME);
$used_cpu_user_children = array_search("used_cpu_user_children",$NAME);

if (is_int($used_cpu_sys) and is_int($used_cpu_user) and is_int($used_cpu_sys_children) and is_int($used_cpu_user_children))
{
	$opt[$graph] = "--vertical-label '%' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / CPU usage'";
	$ds_name[$graph] = 'CPU usage';
	$def[$graph] = '';

	$def[$graph] .= rrd::def("used_cpu_sys", $RRDFILE[$used_cpu_sys], $DS[$used_cpu_sys] , "AVERAGE");
	$def[$graph] .= rrd::def("used_cpu_user", $RRDFILE[$used_cpu_user], $DS[$used_cpu_user] , "AVERAGE");
	$def[$graph] .= rrd::def("used_cpu_sys_children", $RRDFILE[$used_cpu_sys_children], $DS[$used_cpu_sys_children] , "AVERAGE");
	$def[$graph] .= rrd::def("used_cpu_user_children", $RRDFILE[$used_cpu_user_children], $DS[$used_cpu_user_children] , "AVERAGE");

	$def[$graph] .= rrd::cdef("used_cpu_sys_pct", "used_cpu_sys,100,*");
	$def[$graph] .= rrd::cdef("used_cpu_user_pct", "used_cpu_user,100,*");
	$def[$graph] .= rrd::cdef("used_cpu_sys_children_pct", "used_cpu_sys_children,100,*");
	$def[$graph] .= rrd::cdef("used_cpu_user_children_pct", "used_cpu_user_children,100,*");

	$def[$graph] .= rrd::AREA("used_cpu_sys_pct", "#0C0", rrd::cut("System",18), true);
	$def[$graph] .= rrd::gprint("used_cpu_sys_pct", array("LAST", "AVERAGE", "MAX"), "%10.2lf%%");

	$def[$graph] .= rrd::AREA("used_cpu_sys_children_pct", "#060", rrd::cut("System Child",18), true);
	$def[$graph] .= rrd::gprint("used_cpu_sys_children_pct", array("LAST", "AVERAGE", "MAX"), "%10.2lf%%");

	$def[$graph] .= rrd::AREA("used_cpu_user_pct", "#06B", rrd::cut("User",18), true);
	$def[$graph] .= rrd::gprint("used_cpu_user_pct", array("LAST", "AVERAGE", "MAX"), "%10.2lf%%");

	$def[$graph] .= rrd::AREA("used_cpu_user_children_pct", "#025", rrd::cut("User Child",18), true);
	$def[$graph] .= rrd::gprint("used_cpu_user_children_pct", array("LAST", "AVERAGE", "MAX"), "%10.2lf%%");

	$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$used_cpu_sys]));
	$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

	$graph ++;
}


?>
