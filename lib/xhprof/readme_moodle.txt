Description of XHProf 2.2.3 library/viewer import into Moodle

Removed (commit #1):
 * examples - examples dir removed completely
 * extension - extension dir removed completely
 * xhprof_html/docs - documentation dir removed completely

Added (commit #2 - always taken from current moodle.git master):
 * index.html - prevent directory browsing on misconfigured servers
 * xhprof_moodle.php - containing all the stuff needed to run the xhprof profiler within Moodle
 * readme_moodle.txt - this file ;-)

Our changes:  Look for "moodle" in code (commit #3 - always mimic from current moodle.git master):
 * xhprof_html/index.php  ----|
 * xhprof_html/callgraph.php -|=> Changed to use own DB iXHProfRuns implementation (moodle_xhprofrun)
 * xhprof_html/typeahead.php -|
 * xhprof_html/css/xhprof.css: Minor tweaks to report styles
 * xhprof_lib/utils/callgraph_utils.php: Modified to use $CFG->pathtodot

TODO:
 * improvements to the listing mode: various commodity details like:
       - allow to filter by various criteria
       - inline (and ajax) editing of reference/comment and deleting
       - whatever daily usage discovers ;-)
 * add new settings to control if we want to profile things like:
       - php functions
       - memory
       - cpu times
       (all them are right now enabled for everybody by default)
 * allow multiple runs to be exported together (right now only ONE can be
   exported at a time). Note it is only an UI restriction, backend supports multiple.

20101122 - MDL-24600 - Eloy Lafuente (stronk7): Original import of 0.9.2 release
20110318 - MDL-26891 - Eloy Lafuente (stronk7): Implemented earlier profiling runs
20130621 - MDL-39733 - Eloy Lafuente (stronk7): Export & import of profiling runs
20160721 - MDL-55292 - Russell Smith (mr-russ): Add support for tideways profiler collection for PHP7
20171002 - MDL-60313 - Marina Glancy (marinaglancy): Upgrade to 0.9.4 release; patched for PHP7.2
20190314 - MDL-64543 - Brendan Heywood (brendanheywood): Add support for conditional slow profiling
20191016 - MDL-65349 - Brendan Heywood (brendanheywood): Improved url matching behaviour
20201012 - MDL-67081 - Brendan Heywood (brendanheywood): Support selective profiles from CLI
20201210 - MDL-70297 - Ilya Tregubov (ilyatregubov): Upgrade to 2.2.3 release;
20210209 - MDL-70525 - Tomo Tsuyuki (tomotsuyuki): Allow huge number of values

