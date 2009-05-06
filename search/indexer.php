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
* The indexer logic -
*
* Look through each installed module's or block's search document class file (/search/documents)
* for necessary search functions, and if they're present add the content to the index.
* Repeat this for blocks.
*
* Because the iterator/retrieval functions are now stored in /search/documents/<mod>_document.php,
* /mod/mod/lib.php doesn't have to be modified - and thus the search module becomes quite
* self-sufficient. URL's are now stored in the index, stopping us from needing to require
* the class files to generate a results page.
*
* Along with the index data, each document's summary gets stored in the database
* and synchronised to the index (flat file) via the primary key ('id') which is mapped
* to the 'dbid' field in the index
* */

//this'll take some time, set up the environment
@set_time_limit(0);
@ob_implicit_flush(true);
@ob_end_flush();

    /**
    * includes and requires
    */
    require_once('../config.php');
    require_once($CFG->dirroot.'/search/lib.php');

//require_once("debugging.php");

    ini_set('include_path', $CFG->dirroot.DIRECTORY_SEPARATOR.'search'.PATH_SEPARATOR.ini_get('include_path'));

/// only administrators can index the moodle installation, because access to all pages is required

    require_login();
    
    if (empty($CFG->enableglobalsearch)) {
        error(get_string('globalsearchdisabled', 'search'));
    }
    
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
        error(get_string('beadmin', 'search'), "$CFG->wwwroot/login/index.php");
    } 
    
/// confirmation flag to prevent accidental reindexing (indexersplash.php is the correct entry point)

    $sure = strtolower(optional_param('areyousure', '', PARAM_ALPHA));
    
    if ($sure != 'yes') {
        mtrace("<pre>Sorry, you need to confirm indexing via <a href='indexersplash.php'>indexersplash.php</a>"
              .". (<a href='index.php'>Back to query page</a>).</pre>");
    
        exit(0);
    }
    
/// check for php5 (lib.php)

    //php5 found, continue including php5-only files
    //require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");
    require_once("$CFG->dirroot/search/indexlib.php");
    
    mtrace('<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>');
    mtrace('<pre>Server Time: '.date('r',time())."\n");
    
    if (isset($CFG->search_indexer_busy) && $CFG->search_indexer_busy == '1') {
        //means indexing was not finished previously
        mtrace("Warning: Indexing was not successfully completed last time, restarting.\n");
    }
    
/// turn on busy flag

    set_config('search_indexer_busy', '1');
    
    //paths
    $index_path = SEARCH_INDEX_PATH;
    $index_db_file = "{$CFG->dirroot}/search/db/$CFG->dbtype.sql";
    $dbcontrol = new IndexDBControl();
    
/// setup directory in data root

    if (!file_exists($index_path)) {
        mtrace("Data directory ($index_path) does not exist, attempting to create.");
        if (!mkdir($index_path, $CFG->directorypermissions)) {
            search_pexit("Error creating data directory at: $index_path. Please correct.");
        } 
        else {
            mtrace("Directory successfully created.");
        } 
    } 
    else {
        mtrace("Using {$index_path} as data directory.");
    } 
    
    Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
    $index = new Zend_Search_Lucene($index_path, true);
    
    /*
    OBSOLETE REGENERATION - DB installs with search block by now
    if (!$dbcontrol->checkDB()) {
        search_pexit("Database error. Please check settings/files.");
    }
    */

/// New regeneration

    mtrace('Deleting old index entries.');
    delete_records(SEARCH_DATABASE_TABLE);
    
/// begin timer

    search_stopwatch();
    mtrace("Starting activity modules\n");
    
    //the presence of the required search functions -
    // * mod_iterator
    // * mod_get_content_for_index
    //are the sole basis for including a module in the index at the moment.

    $searchables = search_collect_searchables();
    
/// start indexation

    if ($searchables){
        foreach ($searchables as $mod) {
        
            echo "start {$mod->name}";
        
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
            
            /*
            if (!file_exists($class_file)){
                if (defined("PATH_FOR_SEARCH_TYPE_{$mod->name}")){
                    eval("\$pluginpath = PATH_FOR_SEARCH_TYPE_{$mod->name}");
                    $class_file = "{$CFG->dirroot}/{$pluginpath}/searchlib.php";
                } else {
                   mtrace ("No search document found for plugin {$mod->name}. Ignoring.");
                   continue;
                }
            }
            */
         
            if (file_exists($class_file)) {
                include_once($class_file);
    
                //build function names
                $iter_function = $mod->name.'_iterator';
                $index_function = $mod->name.'_get_content_for_index';
                $counter = 0;
                if (function_exists($index_function) && function_exists($iter_function)) {
                    mtrace("Processing module function $index_function ...");
                    $sources = $iter_function();
                    if ($sources){
                        foreach ($sources as $i) {
                            $documents = $index_function($i);
                  
                            //begin transaction
                            if ($documents){
                                foreach($documents as $document) {
                                    $counter++;
                                    
                                    //object to insert into db
                                    $dbid = $dbcontrol->addDocument($document);
                                    
                                    //synchronise db with index
                                    $document->addField(Zend_Search_Lucene_Field::Keyword('dbid', $dbid));
                                    
                                    //add document to index
                                    $index->addDocument($document);
                                    
                                    //commit every x new documents, and print a status message
                                    if (($counter % 2000) == 0) {
                                        $index->commit();
                                        mtrace(".. $counter");
                                    } 
                                }
                            }
                            //end transaction
                        }
                    }
            
                    //commit left over documents, and finish up
                    $index->commit();
          
                    mtrace("-- $counter documents indexed");
                    mtrace("done.\n");
                }
            } else {
               mtrace ("No search document found for plugin {$mod->name}. Ignoring.");
            }
        }
    }
      
/// finished modules

    mtrace('Finished activity modules');
    search_stopwatch();
        
    mtrace(".<br/><a href='index.php'>Back to query page</a>.");
    mtrace('</pre>');
    
/// finished, turn busy flag off

    set_config('search_indexer_busy', '0');
    
/// mark the time we last updated

    set_config('search_indexer_run_date', time());
    
/// and the index size

    set_config('search_index_size', (int)$index->count());

?>