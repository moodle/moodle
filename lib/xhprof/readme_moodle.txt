Description of XHProf 0.9.2 library/viewer import into Moodle

Removed:
 * examples - examples dir removed completely
 * extension - extension dir removed completely
 * package.xml - PECL package definition removed completely
 * xhprof_html/docs - documentation dir removed completely

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * xhprof_moodle.php - containing all the stuff needed to run the xhprof profiler within Moodle
 * readme_moodle.txt - this file ;-)

Our changes:  Look for "moodle" in code
 * xhprof_html/index.php  ----|
 * xhprof_html/callgraph.php -|=> Changed to use own DB iXHProfRuns implementation (moodle_xhprofrun)
 * xhprof_html/typeahead.php -|
 * xhprof_html/css/xhprof.css: Minor tweaks to report styles
 * xhprof_lib/utils/callgraph_utils.php: Modified to use $CFG->pathtodot

TODO:
 * with the 3 reports (index, callgraph and typeahead), close seesion asap,
       so user can continue working with moodle while the report (specially
       the graph is being generated).
 * export/import profiling runs: Allow to pick any profile record, encapsulate
       it into some serialized/encoded way and allow download/upload. It requires
       DB changes in order to be able to specify the source of each record (own/imported).
 * improvements to the listing mode: various commodity details like:
       - allow to filter by various criteria
       - inline (and ajax) editing of reference/comment and deleting
       - whatever daily usage discovers ;-)
 * add new settings to control if we want to profile things like:
       - php functions
       - memory
       - cpu times
       (all them are right now enabled for everybody by default)

20101122 - MDL-24600 - Eloy Lafuente (stronk7): Original import of 0.9.2 release
20110318 - MDL-26891 - Eloy Lafuente (stronk7): Implemented earlier profiling runs
