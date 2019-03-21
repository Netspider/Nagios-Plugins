<?php

$color = array('#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D','#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF','#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

$graph = 1;

############################################################################
# Median Service Times
############################################################################
$opt[$graph] = "--vertical-label 'median response times (s)' --lower-limit 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Median Service Times '";
$ds_name[$graph] = 'Squid Median Services Times';
$def[$graph] = '';
$index = array_search("Http", $NAME);
$def[$graph] .= rrd::def("var1", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var1", $color[0], rrd::cut("http",20));
$def[$graph] .= rrd::gprint("var1", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Cache_misses", $NAME);
$def[$graph] .= rrd::def("var2", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var2", $color[1], rrd::cut("cache misses",20));
$def[$graph] .= rrd::gprint("var2", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Cache_hits", $NAME);
$def[$graph] .= rrd::def("var3", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var3", $color[2], rrd::cut("cache hits",20));
$def[$graph] .= rrd::gprint("var3", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Near_hits", $NAME);
$def[$graph] .= rrd::def("var4", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var4", $color[3], rrd::cut("near hits",20));
$def[$graph] .= rrd::gprint("var4", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Not-modified_replies", $NAME);
$def[$graph] .= rrd::def("var5", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var5", $color[4], rrd::cut("not-modified replies",20));
$def[$graph] .= rrd::gprint("var5", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Dns_lookups", $NAME);
$def[$graph] .= rrd::def("var6", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var6", $color[5], rrd::cut("dns lookups",20));
$def[$graph] .= rrd::gprint("var6", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("Icp_queries", $NAME);
$def[$graph] .= rrd::def("var7", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var7", $color[6], rrd::cut("icp queries",20));
$def[$graph] .= rrd::gprint("var7", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
$graph++;

############################################################################
# Cache Status
############################################################################
$opt[$graph] = "--vertical-label 'bytes' -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Cache Status '";
$ds_name[$graph] = 'Squid cache status';
$def[$graph] = '';
$index = array_search("cache_size", $NAME);
$def[$graph] .= rrd::def("var1_cache_size", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var1_cache_size", $color[0], rrd::cut("cache size",10));
$def[$graph] .= rrd::cdef("cache_size","var1_cache_size,1024,/,1024,/,1024,/") ;
$def[$graph] .= rrd::gprint("cache_size", array("LAST", "AVERAGE", "MAX"), "%6.3lf GB");
$index = array_search("cache_used", $NAME);
$def[$graph] .= rrd::def("var2_cache_used", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var2_cache_used", $color[1], rrd::cut("cache used",10));
$def[$graph] .= rrd::cdef("cache_used","var2_cache_used,1024,/,1024,/,1024,/") ;
$def[$graph] .= rrd::gprint("cache_used", array("LAST", "AVERAGE", "MAX"), "%6.3lf GB");
$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;

############################################################################
# Client Requests
############################################################################
$opt[$graph] = "--vertical-label 'requests' --base 1000 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Client Requests '";
$ds_name[$graph] = 'Squid client requests';
$def[$graph] = '';
$index = array_search("hits", $NAME);
$def[$graph] .= rrd::def("var2_hits", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var2_hits", $color[0], rrd::cut("hits",6));
$def[$graph] .= rrd::gprint("var2_hits", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("errors", $NAME);
$def[$graph] .= rrd::def("var3_errors", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var3_errors", $color[1], rrd::cut("errors",6), true);
$def[$graph] .= rrd::gprint("var3_errors", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("misses", $NAME);
$def[$graph] .= rrd::def("var1_misses", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var1_misses", $color[2], rrd::cut("misses",6), true);
$def[$graph] .= rrd::gprint("var1_misses", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$index = array_search("total", $NAME);
$def[$graph] .= rrd::def("var4_total", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var4_total",'#000000', rrd::cut("total",6));
$def[$graph] .= rrd::gprint("var4_total", array("LAST", "AVERAGE", "MAX"), "%6.2lf$UNIT[$index]");
$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
$graph++;

############################################################################
# Object Size
############################################################################
$opt[$graph] = "--vertical-label 'bytes' -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Object Size '";
$ds_name[$graph] = 'Squid object size';
$def[$graph] = '';
$index = array_search("Object_size", $NAME);
$def[$graph] .= rrd::def("var1", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var1", $color[0], rrd::cut("object size",11));
$def[$graph] .= rrd::cdef("var1_custom", "var1,1024,/");
$def[$graph] .= rrd::gprint("var1_custom", array("LAST", "AVERAGE", "MAX"), "%6.3lf kByte");
$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Traffic Status
############################################################################
$opt[$graph] = "--vertical-label 'bits per second' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Traffic Status '";
$ds_name[$graph] = 'Squid traffic status';
$def[$graph] = '';
$index = array_search("sent", $NAME);
$def[$graph] .= rrd::def("var1_sent", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::AREA("var1_sent", $color[1], rrd::cut("sent",10));
$def[$graph] .= rrd::cdef("custom_sent", "var1_sent,1024,/");
$def[$graph] .= rrd::gprint("custom_sent", array("LAST", "AVERAGE", "MAX"), "%6.3lf kbit");
$index = array_search("from_cache", $NAME);
$def[$graph] .= rrd::def("var1_from_cache", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var1_from_cache", $color[2], rrd::cut("from cache",10));
$def[$graph] .= rrd::cdef("from_cache", "var1_from_cache,1024,/");
$def[$graph] .= rrd::gprint("from_cache", array("LAST", "AVERAGE", "MAX"), "%6.3lf kbit");
$index = array_search("received", $NAME);
$def[$graph] .= rrd::def("var1_rec", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("var1_rec", $color[0], rrd::cut("received",10));
$def[$graph] .= rrd::cdef("custom_rec", "var1_rec,1024,/");
$def[$graph] .= rrd::gprint("custom_rec", array("LAST", "AVERAGE", "MAX"), "%6.3lf kbit");
$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Objects
#############################################################################
$opt[$graph] = "--vertical-label 'count' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60   --title '$hostname / Squid Objects '";
$ds_name[$graph] = 'Squid Objects';
$def[$graph] = '';
$index = array_search("StoreEntries", $NAME);
$def[$graph] .= rrd::def("StoreEntries", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("StoreEntries", $color[1], rrd::cut("StoreEntries",28));
$def[$graph] .= rrd::gprint("StoreEntries", array("LAST", "AVERAGE", "MAX"), "%8.0lf");
$index = array_search("StoreEntries_with_MemObjects", $NAME);
$def[$graph] .= rrd::def("StoreEntries_with_MemObjects", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("StoreEntries_with_MemObjects", $color[2], rrd::cut("StoreEntries with MemObjects",28));
$def[$graph] .= rrd::gprint("StoreEntries_with_MemObjects", array("LAST", "AVERAGE", "MAX"), "%8.0lf");
$index = array_search("Hot_Object_Cache_Items", $NAME);
$def[$graph] .= rrd::def("Hot_Object_Cache_Items", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("Hot_Object_Cache_Items", $color[0], rrd::cut("Hot Object Cache Items",28));
$def[$graph] .= rrd::gprint("Hot_Object_Cache_Items", array("LAST", "AVERAGE", "MAX"), "%8.0lf");
$index = array_search("on-disk_objects", $NAME);
$def[$graph] .= rrd::def("on-disk_objects", $RRDFILE[$index], $DS[$index] , "AVERAGE") ;
$def[$graph] .= rrd::LINE2("on-disk_objects", $color[5], rrd::cut("on-disk objects",28));
$def[$graph] .= rrd::gprint("on-disk_objects", array("LAST", "AVERAGE", "MAX"), "%8.0lf");
$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$index]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Storage Mem Cache
#############################################################################
$opt[$graph] = "--vertical-label 'Bytes' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Storage Mem Cache' --lower-limit 0";
$ds_name[$graph] = 'Storage Mem Cache';
$def[$graph] = '';


$storage_mem_size = array_search("Storage_Mem_size", $NAME);
$storage_mem_used_pct = array_search("Storage_Mem_used", $NAME);
$def[$graph] .= rrd::def("storage_mem_total", $RRDFILE[$storage_mem_size], $DS[$storage_mem_size] , "AVERAGE");
$def[$graph] .= rrd::def("storage_mem_used_pct", $RRDFILE[$storage_mem_used_pct], $DS[$storage_mem_used_pct] , "AVERAGE");

$def[$graph] .= rrd::cdef("storage_mem_used","storage_mem_total,storage_mem_used_pct,*,100,/");
$def[$graph] .= rrd::cdef("storage_mem_used_mb","storage_mem_used,1000,/,1000,/");
$def[$graph] .= rrd::AREA("storage_mem_used", "#55F", rrd::cut("Mem used",28));
$def[$graph] .= rrd::gprint("storage_mem_used_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfMB");

$def[$graph] .= rrd::cdef("storage_mem_free","storage_mem_total,100,storage_mem_used_pct,-,*,100,/");
$def[$graph] .= rrd::cdef("storage_mem_free_mb","storage_mem_free,1000,/,1000,/");
$def[$graph] .= rrd::AREA("storage_mem_free", "#ADA", rrd::cut("Mem free",28), true);
$def[$graph] .= rrd::gprint("storage_mem_free_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfM$UNIT[$storage_mem_size]");

$def[$graph] .= rrd::cdef("storage_mem_total_mb","storage_mem_total,1000,/,1000,/");
$def[$graph] .= rrd::LINE2("storage_mem_total", "#000", rrd::cut("Mem total",28));
$def[$graph] .= rrd::gprint("storage_mem_total_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfM$UNIT[$storage_mem_size]");


$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$storage_mem_size]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Storage Swap Cache
#############################################################################
$opt[$graph] = "--vertical-label 'Bytes' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Storage Swap Cache' --lower-limit 0";
$ds_name[$graph] = 'Storage Swap Cache';
$def[$graph] = '';


$storage_swap_size = array_search("Storage_Swap_size", $NAME);
$storage_swap_used_pct = array_search("Storage_Swap_used", $NAME);
$def[$graph] .= rrd::def("storage_swap_total", $RRDFILE[$storage_swap_size], $DS[$storage_swap_size] , "AVERAGE");
$def[$graph] .= rrd::def("storage_swap_used_pct", $RRDFILE[$storage_swap_used_pct], $DS[$storage_swap_used_pct] , "AVERAGE");

$def[$graph] .= rrd::cdef("storage_swap_used","storage_swap_total,storage_swap_used_pct,*,100,/");
$def[$graph] .= rrd::cdef("storage_swap_used_mb","storage_swap_used,1000,/,1000,/");
$def[$graph] .= rrd::AREA("storage_swap_used", "#55F", rrd::cut("Swap used",28), true);
$def[$graph] .= rrd::gprint("storage_swap_used_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfM$UNIT[$storage_swap_size]");

$def[$graph] .= rrd::cdef("storage_swap_free","storage_swap_total,100,storage_swap_used_pct,-,*,100,/");
$def[$graph] .= rrd::cdef("storage_swap_free_mb","storage_swap_free,1000,/,1000,/");
$def[$graph] .= rrd::AREA("storage_swap_free", "#ADA", rrd::cut("Swap free",28), true);
$def[$graph] .= rrd::gprint("storage_swap_free_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfM$UNIT[$storage_swap_size]");

$def[$graph] .= rrd::cdef("storage_swap_total_mb","storage_swap_total,1000,/,1000,/");
$def[$graph] .= rrd::LINE2("storage_swap_total", "#000", rrd::cut("Swap total",28));
$def[$graph] .= rrd::gprint("storage_swap_total_mb", array("LAST", "AVERAGE", "MAX"), "%8.0lfMB");

$graph++;


############################################################################
# CPU Usage
#############################################################################
$cpu_usage = array_search("CPU_usage", $NAME);

$opt[$graph] = "--vertical-label '%' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / CPU Usage' --lower-limit 0";
$ds_name[$graph] = 'CPU Usage';
$def[$graph] = '';


$def[$graph] .= rrd::def("cpu_usage", $RRDFILE[$cpu_usage], $DS[$cpu_usage] , "AVERAGE");
$def[$graph] .= rrd::LINE2("cpu_usage", "#55F", rrd::cut("CPU usage",28), true);
$def[$graph] .= rrd::gprint("cpu_usage", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$cpu_usage]");

$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$cpu_usage]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Clients
#############################################################################
$num_clients = array_search("num_clients", $NAME);

$opt[$graph] = "--vertical-label '' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Clients' --lower-limit 0";
$ds_name[$graph] = 'Clients';
$def[$graph] = '';


$def[$graph] .= rrd::def("num_clients", $RRDFILE[$num_clients], $DS[$num_clients] , "AVERAGE");
$def[$graph] .= rrd::LINE2("num_clients", "#55F", rrd::cut("num clients",28), true);
$def[$graph] .= rrd::gprint("num_clients", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$num_clients]");

$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$num_clients]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# Requests
#############################################################################
##
## average http requests since start -> je länger der Start her ist, desto ungenauer wird der Graph
##
#$average_http_requests = array_search("average_http_requests", $NAME);
#
#$opt[$graph] = "--vertical-label '/ min' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Requests' --lower-limit 0";
#$ds_name[$graph] = 'Requests (average since start)';
#$def[$graph] = '';
#
#
#$def[$graph] .= rrd::def("average_http_requests", $RRDFILE[$average_http_requests], $DS[$average_http_requests] , "AVERAGE");
#$def[$graph] .= rrd::LINE2("average_http_requests", "#55F", rrd::cut("HTTP requests / Minute",28), true);
#$def[$graph] .= rrd::gprint("average_http_requests", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$average_http_requests]");
#
#$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$average_http_requests]));
#$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";
#
#$graph++;


############################################################################
# Memory Usage
#############################################################################
$memory_free = array_search("memory_free", $NAME);
$memory_size = array_search("memory_size", $NAME);

$mem_accounted_total = array_search("mem_accounted_total", $NAME);
$mem_accounted = array_search("mem_accounted", $NAME);
$mem_unaccounted = array_search("mem_unaccounted", $NAME);

$opt[$graph] = "--vertical-label '$UNIT[$memory_free]' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / Memory Usage' --lower-limit 0";
$ds_name[$graph] = 'Memory Usage';
$def[$graph] = '';


#$def[$graph] .= rrd::def("memory_used", $RRDFILE[$memory_used], $DS[$memory_used] , "AVERAGE"); # bug in squid 3.4.8 (spätestens in 3.5.7 gefixt): used==free
$def[$graph] .= rrd::def("memory_free", $RRDFILE[$memory_free], $DS[$memory_free] , "AVERAGE");
$def[$graph] .= rrd::def("memory_size", $RRDFILE[$memory_size], $DS[$memory_size] , "AVERAGE");
$def[$graph] .= rrd::cdef("memory_used","memory_size,memory_free,-");

$def[$graph] .= rrd::cdef("memory_free_mb","memory_free,1000,/,1000,/");
$def[$graph] .= rrd::cdef("memory_used_mb","memory_used,1000,/,1000,/");
$def[$graph] .= rrd::cdef("memory_size_mb","memory_size,1000,/,1000,/");

$def[$graph] .= rrd::def("mem_accounted_total", $RRDFILE[$mem_accounted_total], $DS[$mem_accounted_total] , "AVERAGE");
$def[$graph] .= rrd::def("mem_accounted", $RRDFILE[$mem_accounted], $DS[$mem_accounted] , "AVERAGE");
$def[$graph] .= rrd::def("mem_unaccounted", $RRDFILE[$mem_unaccounted], $DS[$mem_unaccounted] , "AVERAGE");

$def[$graph] .= rrd::cdef("mem_accounted_total_mb","mem_accounted_total,1000,/,1000,/");
$def[$graph] .= rrd::cdef("mem_accounted_mb","mem_accounted,1000,/,1000,/");
$def[$graph] .= rrd::cdef("mem_unaccounted_mb","mem_unaccounted,1000,/,1000,/");

$def[$graph] .= rrd::AREA("memory_used", "#55F", rrd::cut("Memory used",28));
$def[$graph] .= rrd::gprint("memory_used_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$memory_free]");

$def[$graph] .= rrd::AREA("memory_free", "#ADA", rrd::cut("Memory free",28), true);
$def[$graph] .= rrd::gprint("memory_free_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$memory_free]");

$def[$graph] .= rrd::LINE2("memory_size", "#000", rrd::cut("Memory total",28));
$def[$graph] .= rrd::gprint("memory_size_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$memory_size]");


#$def[$graph] .= rrd::LINE2("mem_accounted_total", "#000", rrd::cut("Accounted Memory total",28));
#$def[$graph] .= rrd::gprint("mem_accounted_total_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$mem_accounted_total]");

$def[$graph] .= rrd::LINE2("mem_accounted", "#FD0", rrd::cut("Memory accounted",28));
$def[$graph] .= rrd::gprint("mem_accounted_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$mem_accounted]");

#$def[$graph] .= rrd::LINE2("mem_unaccounted", "#000", rrd::cut("Unaccounted Memory",28));
#$def[$graph] .= rrd::gprint("mem_unaccounted_mb", array("LAST", "AVERAGE", "MAX"), "%8.2lfM$UNIT[$mem_unaccounted]");

$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$memory_size]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;


############################################################################
# FILE DESCRIPTORS
#############################################################################
$max_file_descriptors = array_search("max_file_descriptors", $NAME);
$largest_file_descriptor = array_search("largest_file_descriptor", $NAME);
$file_descriptors_used = array_search("file_descriptors_used", $NAME);
$num_files_queued = array_search("num_files_queued", $NAME);
$file_descriptors_free = array_search("file_descriptors_free", $NAME);

$opt[$graph] = "--vertical-label '' --base 1000 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60 --title '$hostname / File Descriptors' --lower-limit 0 --upper-limit 65535";
$ds_name[$graph] = 'File Descriptors';
$def[$graph] = '';


$def[$graph] .= rrd::def("file_descriptors_used", $RRDFILE[$file_descriptors_used], $DS[$file_descriptors_used] , "AVERAGE");
$def[$graph] .= rrd::AREA("file_descriptors_used", "#55F", rrd::cut("descriptors used",28));
$def[$graph] .= rrd::gprint("file_descriptors_used", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$file_descriptors_used]");

$def[$graph] .= rrd::def("file_descriptors_free", $RRDFILE[$file_descriptors_free], $DS[$file_descriptors_free] , "AVERAGE");
$def[$graph] .= rrd::AREA("file_descriptors_free", "#ADA", rrd::cut("descriptors free",28), true);
$def[$graph] .= rrd::gprint("file_descriptors_free", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$file_descriptors_free]");

$def[$graph] .= rrd::def("max_file_descriptors", $RRDFILE[$max_file_descriptors], $DS[$max_file_descriptors] , "AVERAGE");
$def[$graph] .= rrd::LINE2("max_file_descriptors", "#000", rrd::cut("descriptors max",28));
$def[$graph] .= rrd::gprint("max_file_descriptors", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$max_file_descriptors]");

$def[$graph] .= rrd::def("largest_file_descriptor", $RRDFILE[$largest_file_descriptor], $DS[$largest_file_descriptor] , "AVERAGE");
$def[$graph] .= rrd::LINE2("largest_file_descriptor", "#FD0", rrd::cut("largest descriptor",28));
$def[$graph] .= rrd::gprint("largest_file_descriptor", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$largest_file_descriptor]");

$def[$graph] .= rrd::def("num_files_queued", $RRDFILE[$num_files_queued], $DS[$num_files_queued] , "AVERAGE");
$def[$graph] .= rrd::LINE1("num_files_queued", "#F33", rrd::cut("files queued for open",28));
$def[$graph] .= rrd::gprint("num_files_queued", array("LAST", "AVERAGE", "MAX"), "%8.2lf$UNIT[$num_files_queued]");

$lastupdate = date("D M d H\\\\:i\\\\:s Y", filemtime($RRDFILE[$cpu_usage]));
$def[$graph] .= "COMMENT:'Last update\: $lastupdate'";

$graph++;

