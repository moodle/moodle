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
    * Asynchronous index cleaner
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

/// makes inclusions of the Zend Engine more reliable
    ini_set('include_path', $CFG->dirroot.DIRECTORY_SEPARATOR.'search'.PATH_SEPARATOR.ini_get('include_path'));

    require_once($CFG->dirroot.'/search/lib.php');
    require_once($CFG->dirroot.'/search/indexlib.php');        
    
/// checks global search activation

    // require_login();

    if (empty($CFG->enableglobalsearch)) {
        error(get_string('globalsearchdisabled', 'search'));
    }
    
    /*
    Obsolete with the MOODLE INTERNAL check
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
        error(get_string('beadmin', 'search'), "$CFG->wwwroot/login/index.php");
    }
    */
    
    try {
        $index = new Zend_Search_Lucene(SEARCH_INDEX_PATH);
    } catch(LuceneException $e) {
        mtrace("Could not construct a valid index. Maybe the first indexation was never made, or files might be corrupted. Run complete indexation again.");
        return;
    }
    $dbcontrol = new IndexDBControl();
    $deletion_count = 0;
    $startcleantime = time();
    
    mtrace('Starting clean-up of removed records...');
    mtrace('Index size before: '.$CFG->search_index_size."\n");
    
/// check all modules
    if ($mods = search_collect_searchables(false, true)){
        
        foreach ($mods as $mod) {
            //build function names
            $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
            $delete_function = $mod->name.'_delete';
            $db_names_function = $mod->name.'_db_names';
            $deletions = array();
            
            if (file_exists($class_file)) {
                require_once($class_file);
                
                //if both required functions exist
                if (function_exists($delete_function) and function_exists($db_names_function)) {
                    mtrace("Checking $mod->name module for deletions.");
                    $valuesArray = $db_names_function();
                    if ($valuesArray){
                        foreach($valuesArray as $values){
                           $where = (!empty($values[5])) ? 'WHERE '.$values[5] : '';
                           $itemtypes = ($values[4] != '*' && $values[4] != 'any') ? " itemtype = '{$values[4]}' AND " : '' ;
                           $query = "
                                SELECT 
                                    id,
                                    {$values[0]}
                                FROM 
                                    {$CFG->prefix}{$values[1]}
                                    $where
                            ";
                            $docIds = get_records_sql($query);
                            $docIdList = ($docIds) ? implode("','", array_keys($docIds)) : '' ;
                            
                            $table = SEARCH_DATABASE_TABLE;
                            $query = "
                                SELECT 
                                    id, 
                                    docid 
                                FROM 
                                    {$CFG->prefix}{$table}
                                WHERE 
                                    doctype = '{$mod->name}' AND 
                                    $itemtypes
                                    docid not in ('{$docIdList}')
                            ";
                            $records = get_records_sql($query);
                            
                            // build an array of all the deleted records
                            if (is_array($records)) {
                                foreach($records as $record) {
                                    $deletions[] = $delete_function($record->docid, $values[4]);
                                }
                            }
                        }
                        
                        foreach ($deletions as $delete) {
                            // find the specific document in the index, using it's docid and doctype as keys
                            $doc = $index->find("+docid:{$delete->id} +doctype:$mod->name +itemtype:{$delete->itemtype}");
                            
                            // get the record, should only be one
                            foreach ($doc as $thisdoc) {
                                ++$deletion_count;
                                mtrace("  Delete: $thisdoc->title (database id = $thisdoc->dbid, index id = $thisdoc->id, moodle instance id = $thisdoc->docid)");
                                
                                //remove it from index and database table
                                $dbcontrol->delDocument($thisdoc);
                                $index->delete($thisdoc->id);
                            }
                        }
                    }
                    else{
                        mtrace("No types to delete.\n");
                    }
                    mtrace("Finished $mod->name.\n");
                }
            }
        }
    }
    
/// commit changes

    $index->commit();
    
/// update index date and index size

    set_config("search_indexer_cleanup_date", $startcleantime);
    set_config("search_index_size", (int)$CFG->search_index_size - (int)$deletion_count);
    
    mtrace("Finished $deletion_count removals.");
    mtrace('Index size after: '.$index->count());

?>