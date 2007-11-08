<?php
/**
* Global Search Engine for Moodle
* Michael Champanis (mchampan) [cynnical@gmail.com]
* review 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* Asynchronous adder for new indexable contents
*
* Major chages in this review is passing the xxxx_db_names return to
* multiple arity to handle multiple document types modules
*/

require_once('../config.php');
require_once("$CFG->dirroot/search/lib.php");

require_login();

if (empty($CFG->enableglobalsearch)) {
    error(get_string('globalsearchdisabled', 'search'));
}

if (!isadmin()) {
    error(get_string('beadmin', 'search'), "$CFG->wwwroot/login/index.php");
} 

//check for php5 (lib.php)
if (!search_check_php5()) {
    $phpversion = phpversion();
    mtrace("Sorry, global search requires PHP 5.0.0 or later (currently using version $phpversion)");
    exit(0);
} 

require_once("$CFG->dirroot/search/indexlib.php");

$index = new Zend_Search_Lucene(SEARCH_INDEX_PATH);
$dbcontrol = new IndexDBControl();
$addition_count = 0;
$startindextime = time();

$indexdate = $CFG->search_indexer_run_date;

mtrace('<pre>Starting index update (additions)...');
mtrace('Index size before: '.$CFG->search_index_size."\n");

//get all modules
if ($mods = get_records_select('modules')) {

//append virtual modules onto array
$mods = array_merge($mods, search_get_additional_modules());
    foreach ($mods as $mod) {
        //build include file and function names
        $class_file = $CFG->dirroot.'/search/documents/'.$mod->name.'_document.php';
        $db_names_function = $mod->name.'_db_names';
        $get_document_function = $mod->name.'_single_document';
        $get_newrecords_function = $mod->name.'_new_records';
        $additions = array();
        
        if (file_exists($class_file)) {
            require_once($class_file);
            
            //if both required functions exist
            if (function_exists($db_names_function) and function_exists($get_document_function)) {
                mtrace("Checking $mod->name module for additions.");
                $valuesArray = $db_names_function();
                if ($valuesArray){
                    foreach($valuesArray as $values){
                        $where = (isset($values[5])) ? 'AND ('.$values[5].')' : '';
                        $itemtypes = ($values[4] != '*') ? " AND itemtype = '{$values[4]}' " : '' ;
                        
                        //select records in MODULE table, but not in SEARCH_DATABASE_TABLE
                        $table = SEARCH_DATABASE_TABLE;
                        $query = "
                            SELECT 
                                docid,
                                itemtype 
                            FROM 
                                {$CFG->prefix}{$table}
                            WHERE 
                                doctype = '{$mod->name}'
                                $itemtypes
                        ";
                        $docIds = get_records_sql_menu($query);
                        $docIdList = ($docIds) ? implode("','", array_keys($docIds)) : '' ;
                        
                        $query =  "
                            SELECT id, 
                                {$values[0]} as docid 
                            FROM 
                                {$CFG->prefix}{$values[1]} 
                            WHERE 
                                id NOT IN ('{$docIdList}') and 
                                {$values[2]} > {$indexdate}
                                $where
                        ";
                        $records = get_records_sql($query);
                        
                        // foreach record, build a module specific search document using the get_document function
                        if (is_array($records)) {
                            foreach($records as $record) {
                                $add = $get_document_function($record->docid, $values[4]);
                                // some documents may not be indexable
                                if ($add)
                                    $additions[] = $add;
                            } 
                        } 
                    } 
                    
                    // foreach document, add it to the index and database table
                    foreach ($additions as $add) {
                        ++$addition_count;
                        
                        // object to insert into db
                        $dbid = $dbcontrol->addDocument($add);
                        
                        // synchronise db with index
                        $add->addField(Zend_Search_Lucene_Field::Keyword('dbid', $dbid));
                        
                        mtrace("  Add: $add->title (database id = $add->dbid, moodle instance id = $add->docid)");
                        
                        $index->addDocument($add);
                    } 
                }
                else{
                    mtrace("No types to add.\n");
                }
                mtrace("Finished $mod->name.\n");
            } 
        } 
    } 
} 

// commit changes
$index->commit();

// update index date and size
set_config("search_indexer_run_date", $startindextime);
set_config("search_index_size", (int)$CFG->search_index_size + (int)$addition_count);

// print some additional info
mtrace("Added $addition_count documents.");
mtrace('Index size after: '.$index->count().'</pre>');

?>