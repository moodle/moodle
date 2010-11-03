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
    * Index asynchronous updator
    *
    * Major chages in this review is passing the xxxx_db_names return to
    * multiple arity to handle multiple document types modules
    */
    
    /**
    * includes and requires
    */
    require_once('../config.php');

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from the cron script
    }

    global $DB;

/// makes inclusions of the Zend Engine more reliable                               
    ini_set('include_path', $CFG->dirroot.DIRECTORY_SEPARATOR.'search'.PATH_SEPARATOR.ini_get('include_path'));

    require_once($CFG->dirroot.'/search/lib.php');
    require_once($CFG->dirroot.'/search/indexlib.php');

/// checks global search activation

    // require_login();
    
    if (empty($CFG->enableglobalsearch)) {
        print_error('globalsearchdisabled', 'search');
    }
    
    /*
    Obsolete with the MOODLE INTERNAL check
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        print_error('beadmin', 'search', get_login_url());
    }
    */

    try {
        $index = new Zend_Search_Lucene(SEARCH_INDEX_PATH);
    } catch(LuceneException $e) {
        mtrace("Could not construct a valid index. Maybe the first indexation was never made, or files might be corrupted. Run complete indexation again.");
        return;
    }
    $dbcontrol = new IndexDBControl();
    $update_count = 0;
    $mainstartupdatedate = time();

/// indexing changed resources
    
    mtrace("Starting index update (updates)...\n");
    
    if ($mods = search_collect_searchables(false, true)){
        
        foreach ($mods as $mod) {
            $indexdate = 0;
            $indexdatestring = 'search_indexer_update_date_'.$mod->name;
            $startupdatedate = time();
            if (isset($CFG->$indexdatestring)) {
                $indexdate = $CFG->$indexdatestring;
            }

            $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
            $get_document_function = $mod->name.'_single_document';
            $delete_function = $mod->name.'_delete';
            $db_names_function = $mod->name.'_db_names';
            $updates = array();
            
            if (file_exists($class_file)) {
                require_once($class_file);
                
                //if both required functions exist
                if (function_exists($delete_function) and function_exists($db_names_function) and function_exists($get_document_function)) {
                    mtrace("Checking $mod->name module for updates.");
                    $valuesArray = $db_names_function();
                    if ($valuesArray){
                        foreach($valuesArray as $values){
                            $where = (isset($values[5]) and $values[5]!='') ? 'AND ('.$values[5].')' : '';
                            $itemtypes = ($values[4] != '*' && $values[4] != 'any') ? " AND itemtype = '{$values[4]}' " : '' ;
    
                            //TODO: check 'in' syntax with other RDBMS' (add and update.php as well)
                            $table = SEARCH_DATABASE_TABLE;
                            $query = "
                                SELECT 
                                    docid,
                                    itemtype
                                FROM 
                                    {{$table}}
                                WHERE
                                    doctype = ?
                                    $itemtypes
                            ";
                            $docIds = $DB->get_records_sql_menu($query, array($mod->name));
                            if (!empty($docIds)){
                                list($usql, $params) = $DB->get_in_or_equal(array_keys($docIds));
                                $query = "
                                    SELECT 
                                        id, 
                                        $values[0] as docid
                                    FROM 
                                        {{$values[1]}}
                                    WHERE 
                                        $values[3] > $indexdate AND 
                                        id $usql
                                    $where
                                ";
                                $records = $DB->get_records_sql($query, $params);
                            } else {
                                $records = array();
                            }

                            foreach($records as $record) {
                                $updates[] = $delete_function($record->docid, $docIds[$record->docid]);
                            } 
                        }

                        foreach ($updates as $update) {
                            ++$update_count;
                            $added_doc = false;

                            //get old document for deletion later
                            // change from default text only search to include numerals for this search.
                            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
                            $doc = $index->find("+docid:{$update->id} +doctype:{$mod->name} +itemtype:{$update->itemtype}");
                            
                            try {
                                //add new modified document back into index
                                $add = $get_document_function($update->id, $update->itemtype);

                                //object to insert into db
                                $dbid = $dbcontrol->addDocument($add);

                                //synchronise db with index
                                $add->addField(Zend_Search_Lucene_Field::Keyword('dbid', $dbid));
                                mtrace("  Add: $add->title (database id = $add->dbid, moodle instance id = $add->docid)");
                                $index->addDocument($add);
                                $added_doc = true;
                            }

                            catch (dml_write_exception $e) {
                                mtrace(" Add: FAILED adding '$add->title' ,  moodle instance id = $add->docid , Error: $e->error ");
                                mtrace($e);
                                $added_doc = false;
                            }

                            if ($added_doc) {
                                // ok we've successfully added the new document so far
                                // delete single previous old document
                                try {
                                    //get the record, should only be one
                                    foreach ($doc as $thisdoc) {
                                        mtrace("  Delete: $thisdoc->title (database id = $thisdoc->dbid, index id = $thisdoc->id, moodle instance id = $thisdoc->docid)");
                                        $dbcontrol->delDocument($thisdoc);
                                        $index->delete($thisdoc->id);
                                    }
                                }

                                catch (dml_write_exception $e) {
                                    mtrace(" Delete: FAILED deleting '$thisdoc->title' ,  moodle instance id = $thisdoc->docid , Error: $e->error ");
                                    mtrace($e);
                                }
                            }
                        } 
                    }
                    else{
                        mtrace("No types to update.\n");
                    }
                    //commit changes
                    $index->commit();

                    //update index date
                    set_config($indexdatestring, $startupdatedate);

                    mtrace("Finished $mod->name.\n");
                } 
            } 
        } 
    } 
    
    //commit changes
    $index->commit();

    //update index date
    set_config('search_indexer_update_date', $mainstartupdatedate);

    mtrace("Finished $update_count updates");

?>