<?php

  /* Used to test if modules/blocks are ready to included in the search index.
   * Carries out some basic function/file existence tests - the search module
   * is expected to exist, along with the db schema files and the search data
   * directory.
   * */

@set_time_limit(0);
@ob_implicit_flush(true);
@ob_end_flush();

require_once('../../config.php');
require_once("$CFG->dirroot/search/lib.php");

require_login();
  
$strsearch = get_string('search', 'search');
$strquery  = get_string('stats');
    
$navlinks[] = array('name' => $strsearch, 'link' => "../index.php", 'type' => 'misc');
$navlinks[] = array('name' => $strquery, 'link' => "../stats.php", 'type' => 'misc');
$navlinks[] = array('name' => get_string('runindexertest','search'), 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);
$site = get_site();
print_header("$strsearch", "$site->fullname" , $navigation, "", "", true, "&nbsp;", navmenu($site));
  
if (empty($CFG->enableglobalsearch)) {
    error('Global searching is not enabled.');
}

if (!isadmin()) {
    error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
} //if

mtrace('<pre>Server Time: '.date('r',time()));
mtrace("Testing global search capabilities:\n");

$phpversion = phpversion();

if (!search_check_php5()) {
    mtrace("ERROR: PHP 5.0.0 or later required (currently using version $phpversion).");
    exit(0);
} else {
    mtrace("Success: PHP 5.0.0 or later is installed ($phpversion).\n");
} //else

//fix paths for testing
set_include_path(get_include_path().":../");
require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");

mtrace("Checking activity modules:\n");

//the presence of the required search functions -
// * mod_iterator
// * mod_get_content_for_index
//are the sole basis for including a module in the index at the moment.

if ($mods = get_records_select('modules')) {
    $mods = array_merge($mods, search_get_additional_modules());

    foreach ($mods as $mod) {
        $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';

        if (file_exists($class_file)) {
            include_once($class_file);

            if (!defined('SEARCH_TYPE_'.strtoupper($mod->name))) {
                mtrace("ERROR: Constant 'SEARCH_TYPE_".strtoupper($mod->name)."' is not defined in /search/lib.php");
                continue;
            } //if

            $iter_function = $mod->name.'_iterator';
            $index_function = $mod->name.'_get_content_for_index';

            if (function_exists($index_function) && function_exists($iter_function)) {
                if (is_array($iter_function())) {
                    $documents = $index_function(array_pop($iter_function()));

                    if (is_array($documents)) {
                        mtrace("Success: '$mod->name' module seems to be ready for indexing.");
                    } else {
                        mtrace("ERROR: $index_function() doesn't seem to be returning an array.");
                    } //else
                } else {
                    mtrace("ERROR: $iter_function() doesn't seem to be returning an object array.");
                } //else
            } else {
                mtrace("ERROR: $iter_function() and/or $index_function() does not exist in $class_file");
            } //else
        } else {
            mtrace("Notice: $class_file does not exist, this module will not be indexed.");
        } //else
    } //foreach
} //if

//finished modules
mtrace("\nFinished checking activity modules.");

//now blocks...
//

mtrace("<br/><a href='../index.php'>Back to query page</a> or <a href='../indexersplash.php'>Start indexing</a>.");
mtrace('</pre>');
print_footer();
?>