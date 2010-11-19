<?php

    /**
    * Global Search Engine for Moodle
    *
    * Used to test if modules/blocks are ready to included in the search index.
    * Carries out some basic function/file existence tests - the search module
    * is expected to exist, along with the db schema files and the search data
    * directory.
    *
    * @package search
    * @category core
    * @subpackage search_engine
    * @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
    * @date 2008/03/31
    * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
    * @version Moodle 2.0
    **/

    define('NO_OUTPUT_BUFFERING', true);

    require_once('../../config.php');

    @set_time_limit(0);
/// makes inclusions of the Zend Engine more reliable
    ini_set('include_path', $CFG->dirroot.DIRECTORY_SEPARATOR.'search'.PATH_SEPARATOR.ini_get('include_path'));

    require_once($CFG->dirroot.'/search/lib.php');

    require_login();

    if (empty($CFG->enableglobalsearch)) {
      print_error('globalsearchdisabled', 'search');
    }

    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
      print_error('onlyadmins', 'error', get_login_url());
    }

    mtrace('<pre>Server Time: '.date('r',time()));
    mtrace("Testing global search capabilities:\n");

    //fix paths for testing
    set_include_path(get_include_path().":../");
    require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");

    mtrace("Checking activity modules:\n");

    //the presence of the required search functions -
    // * mod_iterator
    // * mod_get_content_for_index
    //are the sole basis for including a module in the index at the moment.

/// get all installed modules
    if ($mods = $DB->get_records('modules', null, 'name', 'id, name')){

        $searchabletypes = array_values(search_get_document_types());

        foreach($mods as $mod){
            if (in_array($mod->name, $searchabletypes)){
                $mod->location = 'internal';
                $searchables[] = $mod;
            } else {
                $documentfile = $CFG->dirroot."/mod/{$mod->name}/search_document.php";
                $mod->location = 'mod';
                if (file_exists($documentfile)){
                    $searchables[] = $mod;
                }
            }
        }
        mtrace(count($searchables).' modules to search in / '.count($mods).' modules found.');
    }

/// collects blocks as indexable information may be found in blocks either
    if ($blocks = $DB->get_records('block', null, 'name', 'id,name')) {
        $blocks_searchables = array();
        // prepend the "block_" prefix to discriminate document type plugins
        foreach($blocks as $block){
            $block->dirname = $block->name;
            $block->name = 'block_'.$block->name;
            if (in_array('SEARCH_TYPE_'.strtoupper($block->name), $searchabletypes)){
                $mod->location = 'internal';
                $blocks_searchables[] = $block;
            } else {
                $documentfile = $CFG->dirroot."/blocks/{$block->dirname}/search_document.php";
                if (file_exists($documentfile)){
                    $mod->location = 'blocks';
                    $blocks_searchables[] = $block;
                }
            }
        }
        mtrace(count($blocks_searchables).' blocks to search in / '.count($blocks).' blocks found.');
        $searchables = array_merge($searchables, $blocks_searchables);
    }

/// add virtual modules onto the back of the array

    $additional = search_get_additional_modules();
    mtrace(count($additional).' additional to search in.');
    $searchables = array_merge($searchables, $additional);

    foreach ($searchables as $mod) {

        $key = 'search_in_'.$mod->name;
        if (isset($CFG->$key) && !$CFG->$key) {
            mtrace("module $key has been administratively disabled. Skipping...\n");
            continue;
        }

        if ($mod->location == 'internal'){
            $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
        } else {
            $class_file = $CFG->dirroot.'/'.$mod->location.'/'.$mod->name.'/search_document.php';
        }

        if (file_exists($class_file)) {
            include_once($class_file);

            if ($mod->location != 'internal' && !defined('X_SEARCH_TYPE_'.strtoupper($mod->name))) {
                mtrace("ERROR: Constant 'X_SEARCH_TYPE_".strtoupper($mod->name)."' is not defined in search/searchtypes.php or in module");
                continue;
            }

            $iter_function = $mod->name.'_iterator';
            $index_function = $mod->name.'_get_content_for_index';

            if (function_exists($index_function) && function_exists($iter_function)) {
                $entries = $iter_function();
                if (!empty($entries)) {
                    $documents = $index_function(array_pop($entries));

                    if (is_array($documents)) {
                        mtrace("Success: '$mod->name' module seems to be ready for indexing.");
                    } else {
                        mtrace("ERROR: $index_function() doesn't seem to be returning an array.");
                    }
                } else {
                    mtrace("Success : '$mod->name' has nothing to index.");
                }
            } else {
                mtrace("ERROR: $iter_function() and/or $index_function() does not exist in $class_file");
            }
        } else {
            mtrace("Notice: $class_file does not exist, this module will not be indexed.");
        }
    }

    mtrace("\nFinished checking for searcheable items.");

    mtrace("<br/><a href='../index.php'>Back to query page</a> or <a href='../indexersplash.php'>Start indexing</a>.");
    mtrace('</pre>');
?>