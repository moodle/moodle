<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* This file serves as a splash-screen (entry page) to the indexer script -
* it is in place to prevent accidental reindexing which can lead to a loss
* of time, amongst other things.
*/

/**
* includes and requires
*/
require_once('../config.php');
require_once($CFG->dirroot.'/search/lib.php');

/// makes inclusions of the Zend Engine more reliable

    ini_set('include_path', $CFG->dirroot.DIRECTORY_SEPARATOR.'search'.PATH_SEPARATOR.ini_get('include_path'));

/// check global search is enabled 

    require_login();
    
    if (empty($CFG->enableglobalsearch)) {
        error(get_string('globalsearchdisabled', 'search'));
    }
    
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
        error(get_string('beadmin', 'search'), $CFG->wwwroot.'/login/index.php');
    } 
    
    require_once($CFG->dirroot.'/search/indexlib.php');
    $indexinfo = new IndexInfo();
    
    if ($indexinfo->valid()) {
        $strsearch = get_string('search', 'search');
        $strquery  = get_string('stats');
        
        $navlinks[] = array('name' => $strsearch, 'link' => "index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strquery, 'link' => "stats.php", 'type' => 'misc');
        $navlinks[] = array('name' => get_string('runindexer','search'), 'link' => null, 'type' => 'misc');
        // if ($CFG->version <= 2007021541){ // 1.8 branch stable timestamp NOT RELIABLE
        if (!function_exists('build_navigation')){ // 1.8 branch stable timestamp
            $navigation = '';
        } else {
            $navigation = build_navigation($navlinks);
        }
        $site = get_site();
        print_header("$strsearch", "$site->fullname" , $navigation, "", "", true, "&nbsp;", navmenu($site));
     
        mtrace("<pre>The data directory ($indexinfo->path) contains $indexinfo->filecount files, and\n"
              ."there are ".$indexinfo->dbcount." records in the <em>block_search_documents</em> table.\n"
              ."\n"
              ."This indicates that you have already succesfully indexed this site. Follow the link\n"
              ."if you are sure that you want to continue indexing - this will replace any existing\n"
              ."index data (no Moodle data is affected).\n"
              ."\n"
              ."You are encouraged to use the 'Test indexing' script before continuing onto\n"
              ."indexing - this will check if the modules are set up correctly. Please correct\n"
              ."any errors before proceeding.\n"
              ."\n"
              ."<a href='tests/index.php'>Test indexing</a> or "
              ."<a href='indexer.php?areyousure=yes'>Continue indexing</a> or <a href='index.php'>Back to query page</a>."
              ."</pre>");
        print_footer();
    } 
    else {
        header('Location: indexer.php?areyousure=yes');
    }
?>