<?php

$color = array('#00CC00','#0066B3','#FF8000','#FFCC00','#330099','#990099','#CCFF00','#FF0000','#808080','#008F00','#00487D','#B35A00','#B38F00','#6B006B','#8FB300','#B30000','#BEBEBE','#80FF80','#80C9FF','#FFC080','#FFE680','#AA80FF','#EE00CC','#FF8080','#666600','#FFBFFF','#00FFCC','#CC6699','#999900');

$slab_cache = array_search('slab_cache', $NAME);
$swap_cache = array_search('swap_cache', $NAME);
$page_tables = array_search('page_tables', $NAME);
$vmalloc_used = array_search('vmalloc_used', $NAME);
$mem_free = array_search('mem_free', $NAME);
$mem_free_simple = array_search('mem_free_simple', $NAME);
$mem_used_simple = array_search('mem_used_simple', $NAME);
$mem_total = array_search('mem_total', $NAME);
$buffers = array_search('buffers', $NAME);
$cached = array_search('cached', $NAME);
$committed = array_search('committed', $NAME);
$mapped = array_search('mapped', $NAME);
$active = array_search('active', $NAME);
$active_anon = array_search('active_anon', $NAME);
$active_cache = array_search('active_cache', $NAME);
$inactive = array_search('inactive', $NAME);
$inactive_dirty = array_search('inactive_dirty', $NAME);
$inactive_laundry = array_search('inactive_laundry', $NAME);
$inactive_clean = array_search('inactive_clean', $NAME);
$swap_total = array_search('swap_total', $NAME);
$swap_free = array_search('swap_free', $NAME);
$swap_used = array_search('swap_used', $NAME);
$apps = array_search('apps', $NAME);

$opt[1] = "--vertical-label 'Bytes' --base 1024 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60  --title '$hostname / $servicedesc (simple)'   ";
$ds_name[1] = 'Memory Usage (simple)';
$def[1] = '';

#$def[1] .= rrd::def("mem_total", $RRDFILE[$mem_total], $DS[$mem_total] , "AVERAGE") ;
#$def[1] .= rrd::LINE1("mem_total", "#000000", rrd::cut("RAM total",12));
#$def[1] .= rrd::cdef("mem_total_MB",'mem_total,1024,/,1024,/');
#$def[1] .= rrd::gprint("mem_total_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
#
#$def[1] .= rrd::def("mem_used_simple", $RRDFILE[$mem_used_simple], $DS[$mem_used_simple] , "AVERAGE") ;
#$def[1] .= rrd::AREA("mem_used_simple", "#008000", rrd::cut("used",12));
#$def[1] .= rrd::cdef('mem_used_simple_MB','mem_used_simple,1024,/,1024,/');
#$def[1] .= rrd::gprint("mem_used_simple_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
#
#$def[1] .= rrd::def("mem_free_simple", $RRDFILE[$mem_free_simple], $DS[$mem_free_simple] , "AVERAGE") ;
#$def[1] .= rrd::AREA("mem_free_simple", "#adff2f", rrd::cut("free",12),true);
#$def[1] .= rrd::cdef('mem_free_simple_MB','mem_free_simple,1024,/,1024,/');
#$def[1] .= rrd::gprint("mem_free_simple_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
#
#$def[1] .= rrd::def("swap_used", $RRDFILE[$swap_used], $DS[$swap_used] , "AVERAGE");
#$def[1] .= rrd::AREA("swap_used", "#ff0000", rrd::cut("swap used",12),true);
#$def[1] .= rrd::cdef('swap_used_MB','swap_used,1024,/,1024,/');
#$def[1] .= rrd::gprint("swap_used_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");

##################

$def[1] .= rrd::def("apps", $RRDFILE[$apps], $DS[$apps] , "AVERAGE") ;
$def[1] .= rrd::def("page_tables", $RRDFILE[$page_tables], $DS[$page_tables] , "AVERAGE") ;
$def[1] .= rrd::def("swap_cache", $RRDFILE[$swap_cache], $DS[$swap_cache] , "AVERAGE") ;
$def[1] .= rrd::def("slab_cache", $RRDFILE[$slab_cache], $DS[$slab_cache] , "AVERAGE") ;
$def[1] .= rrd::def("buffers", $RRDFILE[$buffers], $DS[$buffers] , "AVERAGE") ;
$def[1] .= rrd::def("cached", $RRDFILE[$cached], $DS[$cached] , "AVERAGE") ;
$def[1] .= rrd::def("mem_free", $RRDFILE[$mem_free], $DS[$mem_free] , "AVERAGE") ;

$def[1] .= rrd::def("mem_total", $RRDFILE[$mem_total], $DS[$mem_total] , "AVERAGE") ;
$def[1] .= rrd::cdef('mem_total_temp','mem_total,UN,apps,page_tables,+,swap_cache,+,slab_cache,+,buffers,+,cached,+,mem_free,+,mem_total,IF');
$def[1] .= rrd::LINE1("mem_total_temp", "#000000", rrd::cut("RAM total",12));
$def[1] .= rrd::cdef("mem_total_MB",'mem_total_temp,1024,/,1024,/');
$def[1] .= rrd::gprint("mem_total_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");

$def[1] .= rrd::def("mem_free_simple", $RRDFILE[$mem_free_simple], $DS[$mem_free_simple] , "AVERAGE") ;
$def[1] .= rrd::cdef('mem_free_simple_temp','mem_total_temp,UN,UNKN,mem_free_simple,UN,buffers,cached,+,mem_free,+,mem_free_simple,IF,IF');
$def[1] .= rrd::cdef('mem_free_simple_MB','mem_free_simple_temp,1024,/,1024,/');

$def[1] .= rrd::def("mem_used_simple", $RRDFILE[$mem_used_simple], $DS[$mem_used_simple] , "AVERAGE") ;
$def[1] .= rrd::cdef('mem_used_simple_temp','mem_total_temp,UN,UNKN,mem_total_temp,mem_free_simple_temp,-,IF');
$def[1] .= rrd::cdef('mem_used_simple_MB','mem_used_simple_temp,1024,/,1024,/');

$def[1] .= rrd::AREA("mem_used_simple_temp", "#008000", rrd::cut("used",12));
$def[1] .= rrd::gprint("mem_used_simple_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");

$def[1] .= rrd::AREA("mem_free_simple_temp", "#adff2f", rrd::cut("free",12),true);
$def[1] .= rrd::gprint("mem_free_simple_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");

$def[1] .= rrd::def("swap_used", $RRDFILE[$swap_used], $DS[$swap_used] , "AVERAGE");
$def[1] .= rrd::cdef('swap_used_temp','mem_total_temp,UN,UNKN,swap_used,IF');
$def[1] .= rrd::AREA("swap_used_temp", "#ff0000", rrd::cut("swap used",12),true);
$def[1] .= rrd::cdef('swap_used_MB','swap_used,1024,/,1024,/');
$def[1] .= rrd::gprint("swap_used_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");

##################

$def[1] .= "HRULE:$WARN[$mem_used_simple]#FFFF00:\"\" ";
$def[1] .= "HRULE:$CRIT[$mem_used_simple]#FF0000:\"\" ";

$swap_warn = ($WARN[$swap_used]+$MAX[$mem_used_simple]);
$swap_crit = ($CRIT[$swap_used]+$MAX[$mem_used_simple]);

$def[1] .= "HRULE:$swap_warn#FFFF00:\"\" ";
$def[1] .= "HRULE:$swap_crit#ffb6c1:\"\" ";


$opt[2] = "--vertical-label 'Bytes' --base 1024 -l 0 --font DEFAULT:0:DejaVuSans --font LEGEND:7:DejaVuSansMono -S 60  --title '$hostname / $servicedesc (complex)'   ";

$ds_name[2] = 'Memory Usage (complex)';
$def[2] = '';

# DEBUG: apps
$def[2] .= rrd::def("apps", $RRDFILE[$apps], $DS[$apps] , "AVERAGE") ;
$def[2] .= rrd::AREA("apps", $color[0], rrd::cut("apps",12));
$def[2] .= rrd::cdef('apps_MB','apps,1024,/,1024,/');
$def[2] .= rrd::gprint("apps_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: page_tables
$def[2] .= rrd::def("page_tables", $RRDFILE[$page_tables], $DS[$page_tables] , "AVERAGE") ;
$def[2] .= rrd::AREA("page_tables", $color[1], rrd::cut("page_tables",12), true);
$def[2] .= rrd::cdef('page_tables_MB','page_tables,1024,/,1024,/');
$def[2] .= rrd::gprint("page_tables_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: swap_cache
$def[2] .= rrd::def("swap_cache", $RRDFILE[$swap_cache], $DS[$swap_cache] , "AVERAGE") ;
$def[2] .= rrd::AREA("swap_cache", $color[2], rrd::cut("swap_cache",12), true);
$def[2] .= rrd::cdef('swap_cache_MB','swap_cache,1024,/,1024,/');
$def[2] .= rrd::gprint("swap_cache_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: slab_cache
$def[2] .= rrd::def("slab_cache", $RRDFILE[$slab_cache], $DS[$slab_cache] , "AVERAGE") ;
$def[2] .= rrd::AREA("slab_cache", $color[3], rrd::cut("slab_cache_cache",12), true);
$def[2] .= rrd::cdef('slab_cache_MB','slab_cache,1024,/,1024,/');
$def[2] .= rrd::gprint("slab_cache_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: cached
$def[2] .= rrd::def("cached", $RRDFILE[$cached], $DS[$cached] , "AVERAGE") ;
$def[2] .= rrd::AREA("cached", $color[4], rrd::cut("cache",12), true);
$def[2] .= rrd::cdef('cached_MB','cached,1024,/,1024,/');
$def[2] .= rrd::gprint("cached_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: buffers
$def[2] .= rrd::def("buffers", $RRDFILE[$buffers], $DS[$buffers] , "AVERAGE") ;
$def[2] .= rrd::AREA("buffers", $color[5], rrd::cut("buffers",12), true);
$def[2] .= rrd::cdef('buffers_MB','buffers,1024,/,1024,/');
$def[2] .= rrd::gprint("buffers_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: free
$def[2] .= rrd::def("mem_free", $RRDFILE[$mem_free], $DS[$mem_free] , "AVERAGE") ;
$def[2] .= rrd::AREA("mem_free", $color[6], rrd::cut("unused",12), true);
$def[2] .= rrd::cdef('mem_free_MB','mem_free,1024,/,1024,/');
$def[2] .= rrd::gprint("mem_free_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: swap_used
$def[2] .= rrd::def("swap_used", $RRDFILE[$swap_used], $DS[$swap_used] , "AVERAGE") ;
$def[2] .= rrd::AREA("swap_used", $color[7], rrd::cut(ucfirst("swap used"),12), true);
$def[2] .= rrd::cdef('swap_used_MB','swap_used,1024,/,1024,/');
$def[2] .= rrd::gprint("swap_used_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
## DEBUG: swap_free
#$def[2] .= rrd::def("swap_free", $RRDFILE[$swap_free], $DS[$swap_free] , "AVERAGE") ;
#$def[2] .= rrd::LINE2("swap_free", $color[8], rrd::cut(ucfirst("swap free"),12), true);
#$def[2] .= rrd::cdef('swap_free_MB','swap_used,1024,/,1024,/');
#$def[2] .= rrd::gprint("swap_free_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: inactive
$def[2] .= rrd::def("inactive", $RRDFILE[$inactive], $DS[$inactive] , "AVERAGE") ;
$def[2] .= rrd::LINE2("inactive", $color[8], rrd::cut("inactive",12));
$def[2] .= rrd::cdef('inactive_MB','inactive,1024,/,1024,/');
$def[2] .= rrd::gprint("inactive_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: committed
$def[2] .= rrd::def("committed", $RRDFILE[$committed], $DS[$committed] , "AVERAGE") ;
$def[2] .= rrd::LINE2("committed", $color[9], rrd::cut("committed",12));
$def[2] .= rrd::cdef('committed_MB','committed,1024,/,1024,/');
$def[2] .= rrd::gprint("committed_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: active
$def[2] .= rrd::def("active", $RRDFILE[$active], $DS[$active] , "AVERAGE") ;
$def[2] .= rrd::LINE2("active", $color[10], rrd::cut("active",12));
$def[2] .= rrd::cdef('active_MB','active,1024,/,1024,/');
$def[2] .= rrd::gprint("active_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: vmalloc_used
$def[2] .= rrd::def("vmalloc_used", $RRDFILE[$vmalloc_used], $DS[$vmalloc_used] , "AVERAGE") ;
$def[2] .= rrd::LINE2("vmalloc_used", $color[11], rrd::cut("vmalloc_used",12));
$def[2] .= rrd::cdef('vmalloc_used_MB','vmalloc_used,1024,/,1024,/');
$def[2] .= rrd::gprint("vmalloc_used_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
# DEBUG: mapped
$def[2] .= rrd::def("mapped", $RRDFILE[$mapped], $DS[$mapped] , "AVERAGE") ;
$def[2] .= rrd::LINE2("mapped", $color[12], rrd::cut("mapped",12));
$def[2] .= rrd::cdef('mapped_MB','mapped,1024,/,1024,/');
$def[2] .= rrd::gprint("mapped_MB", array("LAST", "AVERAGE", "MAX"), "%8.2lf MB");
$lastupdate = date('D M d H\\\\:i\\\\:s Y', filemtime($RRDFILE[$mapped]));
$def[2] .= "COMMENT:'Last update\: $lastupdate'";
?>
