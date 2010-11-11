<?php
/** 
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @version prepared for 2.0
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* General function library
*
* This file must not contain any PHP 5, because it is used to test for PHP 5
* itself, and needs to be able to be executed on PHP 4 installations.
*
*/

/**
* Constants
*/
define('SEARCH_INDEX_PATH', $CFG->dataroot.'/search');
define('SEARCH_DATABASE_TABLE', 'block_search_documents');

// get document types
include_once $CFG->dirroot.'/search/searchtypes.php';

/**
* collects all searchable items identities
* @param boolean $namelist if true, only returns list of names of searchable items
* @param boolean $verbose if true, prints a discovering status
* @return an array of names or an array of type descriptors
*/
function search_collect_searchables($namelist=false, $verbose=true){
    global $CFG, $DB;
    
    $searchables = array();
    $searchables_names = array();
    
/// get all installed modules
    if ($mods = $DB->get_records('modules', null, 'name', 'id,name')){

        $searchabletypes = array_values(search_get_document_types());

        foreach($mods as $mod){
            $plugin = new StdClass();
            $plugin->name = $mod->name;
            $plugin->type = 'mod';
            if (in_array($mod->name, $searchabletypes)){
                $plugin->location = 'internal';
                $searchables[$plugin->name] = $plugin;
                $searchables_names[] = $mod->name;
            } else {
                $documentfile = $CFG->dirroot."/mod/{$mod->name}/search_document.php";
                $plugin->location = 'mod';
                if (file_exists($documentfile)){
                    $searchables[$plugin->name] = $plugin;
                    $searchables_names[] = $mod->name;
                }
            }        
        }    
        if ($verbose) mtrace(count($searchables).' modules to search in / '.count($mods).' modules found.');
    }
      
/// collects blocks as indexable information may be found in blocks either
    if ($blocks = $DB->get_records('block', null, 'name', 'id,name')) {
        $blocks_searchables = array();
        // prepend the "block_" prefix to discriminate document type plugins
        foreach($blocks as $block){
            $plugin = new StdClass();
            $plugin->dirname = $block->name;
            $plugin->name = 'block_'.$block->name;
            if (in_array('SEARCH_TYPE_'.strtoupper($block->name), $searchabletypes)){
                $plugin->location = 'internal';
                $plugin->type = 'block';
                $blocks_searchables[$plugin->name] = $plugin;
                $searchables_names[] = $plugin->name;
            } else {
                $documentfile = $CFG->dirroot."/blocks/{$plugin->dirname}/search_document.php";
                if (file_exists($documentfile)){
                    $plugin->location = 'blocks';
                    $plugin->type = 'block';
                    $blocks_searchables[$plugin->name] = $plugin;
                    $searchables_names[] = $plugin->name;
                }
            }        
        }    
        if ($verbose) mtrace(count($blocks_searchables).' blocks to search in / '.count($blocks).' blocks found.');
        $searchables = array_merge($searchables, $blocks_searchables);
    }
      
/// add virtual modules onto the back of the array

    $additional = search_get_additional_modules($searchables_names);
    if (!empty($additional)){
        if ($verbose) mtrace(count($additional).' additional to search in.');
        $searchables = array_merge($searchables, $additional);
    }
    
    if ($namelist)
        return $searchables_names;
    return $searchables;
}

/**
* returns all the document type constants that are known in core implementation
* @param prefix a pattern for recognizing constants
* @return an array of type labels
*/
function search_get_document_types($prefix = 'SEARCH_TYPE_') {
    $ret = array();
    foreach (get_defined_constants() as $key => $value) {
        if (preg_match("/^{$prefix}/", $key)){
            $ret[$key] = $value;
        } 
    } 
    sort($ret);
    return $ret;
}

/**
* additional virtual modules to index
*
* By adding 'moo' to the extras array, an additional document type
* documents/moo_document.php will be indexed - this allows for
* virtual modules to be added to the index, i.e. non-module specific
* information.
*/
function search_get_additional_modules(&$searchables_names) {
    $extras = array(/* additional keywords go here */);
    if (defined('SEARCH_EXTRAS')){
        $extras = explode(',', SEARCH_EXTRAS);
    }

    $ret = array();
    $temp = new StdClass;
    foreach($extras as $extra) {
        $plugin = new StdClass();
        $plugin->name = $extra;
        $plugin->location = 'internal';
        eval('$plugin->type = TYPE_FOR_SEARCH_TYPE_'.strtoupper($extra).';');
        $ret[$plugin->name] = $plugin;
        $searchables_names[] = $extra;
    } 

    return $ret;
}

/**
* shortens a url so it can fit on the results page
* @param url the url
* @param length the size limit we want
*/
function search_shorten_url($url, $length=30) {
    return substr($url, 0, $length)."...";
}

/**
* simple timer function, on first call, records a current microtime stamp, outputs result on 2nd call
* @param cli an output formatting switch
* @return void
*/
function search_stopwatch($cli = false) {
    if (!empty($GLOBALS['search_script_start_time'])) {
        if (!$cli) print '<em>';
        print round(microtime(true) - $GLOBALS['search_script_start_time'], 6).' '.get_string('seconds', 'search');
        if (!$cli) print '</em>';
        unset($GLOBALS['search_script_start_time']);
    } else {
        $GLOBALS['search_script_start_time'] = microtime(true);
    } 
}

/**
* print and exit (for debugging)
* @param str a variable to explore
* @return void
*/
function search_pexit($str = "") {
    if (is_array($str) or is_object($str)) {
        print_r($str);
    } else if ($str) {
        print $str."<br/>";
    }
    exit(0);
}

function search_updatedcallback($name) {
    global $CFG, $DB;
    // set block to hidden when global search is disabled.
    if ($CFG->enableglobalsearch != 1) {
        $DB->set_field('block', 'visible', 0, array('name'=>'search'));     // Hide block
    }
}

?>
