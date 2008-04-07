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
require_once("$CFG->dirroot/search/lib.php");


    require_login();
    
    if (empty($CFG->enableglobalsearch)) {
        error(get_string('globalsearchdisabled', 'search'));
    }
    
    if (!isadmin()) {
        error(get_string('beadmin', 'search'), "$CFG->wwwroot/login/index.php");
    } //if
    
    //check for php5 (lib.php)
    if (!search_check_php5()) {
        $phpversion = phpversion();
        mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version ".phpversion().")");
        exit(0);
    }
    
    require_once("$CFG->dirroot/search/indexlib.php");
    
    $index = new Zend_Search_Lucene(SEARCH_INDEX_PATH);
    $dbcontrol = new IndexDBControl();
    $deletion_count = 0;
    $startcleantime = time();
    
    mtrace('Starting clean-up of removed records...');
    mtrace('Index size before: '.$CFG->search_index_size."\n");
    
    if ($mods = get_records_select('modules')) {
        $mods = array_merge($mods, search_get_additional_modules());
        
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
                           $where = (isset($values[5])) ? 'WHERE '.$values[5] : '';
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
