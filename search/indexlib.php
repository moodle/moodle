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
* Index info class
*
* Used to retrieve information about an index.
* Has methods to check for valid database and data directory,
* and the index itself.
*/

/**
* includes and requires
*/
require_once($CFG->dirroot.'/search/lib.php');
require_once($CFG->dirroot.'/search/Zend/Search/Lucene.php');

/**
* main class for searchable information in the Lucene index 
*/
class IndexInfo {

    private $path,        //index data directory
            $size,        //size of directory (i.e. the whole index)
            $filecount,   //number of files
            $indexcount,  //number of docs in index
            $dbcount,     //number of docs in db
            $types,       //array of [document types => count]
            $complete,    //is index completely formed?
            $time;        //date index was generated
    
    public function __construct($path = SEARCH_INDEX_PATH) {
        global $CFG, $db;
        
        $this->path = $path;
        
        //test to see if there is a valid index on disk, at the specified path
        try {
            $test_index = new Zend_Search_Lucene($this->path, false);
            $validindex = true;
        } catch(Exception $e) {
            $validindex = false;
        } 
        
        //retrieve file system info about the index if it is valid
        if ($validindex) {
            $this->size = display_size(get_directory_size($this->path));
            $index_dir  = get_directory_list($this->path, '', false, false);
            $this->filecount = count($index_dir);
            $this->indexcount = $test_index->count();
        } 
        else {
            $this->size = 0;
            $this->filecount = 0;
            $this->indexcount = 0;
        } 
        
        $db_exists = false; //for now
        
        //get all the current tables in moodle
        $admin_tables = $db->MetaTables();
        
        //TODO: use new IndexDBControl class for database checks?
        
        //check if our search table exists
        if (in_array($CFG->prefix.SEARCH_DATABASE_TABLE, $admin_tables)) {
            //retrieve database information if it does
            $db_exists = true;
            
            //total documents
            $this->dbcount = count_records(SEARCH_DATABASE_TABLE);
            
            //individual document types
            // $types = search_get_document_types();
            $types = search_collect_searchables(true, false);
            sort($types);
            
            foreach($types as $type) {
                $c = count_records(SEARCH_DATABASE_TABLE, 'doctype', $type);
                $this->types[$type] = (int)$c;
            }
        } else {
            $this->dbcount = 0;
            $this->types = array();
        }
        
        //check if the busy flag is set
        if (isset($CFG->search_indexer_busy) && $CFG->search_indexer_busy == '1') {
            $this->complete = false;
        } else {
            $this->complete = true;
        }
        
        //get the last run date for the indexer
        if ($this->valid() && $CFG->search_indexer_run_date) {
            $this->time = $CFG->search_indexer_run_date;
        } else {
          $this->time = 0;
        }
    } 
    
    /**
    * returns false on error, and the error message via referenced variable $err
    * @param array $err array of errors
    */
    public function valid(&$err = null) {
        $err = array();
        $ret = true;
        
        if (!$this->is_valid_dir()) {
            $err['dir'] = get_string('invalidindexerror', 'search');
            $ret = false;
        }
        
        if (!$this->is_valid_db()) {
            $err['db'] = get_string('emptydatabaseerror', 'search');
            $ret = false;
        }
        
        if (!$this->complete) {
            $err['index'] = get_string('uncompleteindexingerror','search');
            $ret = false;
        }
        
        return $ret;
    }
    
    /**
    * is the index dir valid
    *
    */
    public function is_valid_dir() {
        if ($this->filecount > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
    * is the db table valid
    *
    */
    public function is_valid_db() {
        if ($this->dbcount > 0) {
            return true;
        } else {
            return false;
        }
    } 
    
    /**
    * shorthand get method for the class variables
    * @param object $var
    */
    public function __get($var) {
        if (in_array($var, array_keys(get_class_vars(get_class($this))))) {
            return $this->$var;
        }
    } 
} 


/**
* DB Index control class
*
* Used to control the search index database table
*/
class IndexDBControl {

    /**
    * does the table exist?
    * @deprecated
    * @uses CFG, db
    */
    public function checkTableExists() {
        global $CFG, $db;
        
        $table = SEARCH_DATABASE_TABLE;
        $tables = $db->MetaTables();
        if (in_array($CFG->prefix.$table, $tables)) {
            return true;
        } 
        else {
            return false;
        }
    } //checkTableExists

    /**
    * is our database setup valid?
    * @uses db, CFG
    * @deprecated Database is installed at install and should not be dropped out
    */
    public function checkDB() {
        global $CFG, $db;
        
        $sqlfile = "{$CFG->dirroot}/search/db/$CFG->dbtype.sql";
        $ret = false;
        if ($this->checkTableExists()) {
            execute_sql('drop table '.$CFG->prefix.SEARCH_DATABASE_TABLE, false);
        }

        //turn output buffering on - to hide modify_database() output
        ob_start(); 
        $ret = modify_database($sqlfile, '', false);

        //chuck the buffer and resume normal operation
        ob_end_clean(); 
        return $ret;
    } //checkDB

    /**
    * add a document record to the table
    * @param document must be a Lucene SearchDocument instance
    * @uses db, CFG
    */
    public function addDocument($document=null) {
        global $db, $CFG;
        
        if ($document == null) {
             return false;
        }
                
        // object to insert into db
        $doc->doctype   = $document->doctype;
        $doc->docid     = $document->docid;
        $doc->itemtype  = $document->itemtype;
        $doc->title     = search_escape_string($document->title);
        $doc->url       = search_escape_string($document->url);
        $doc->updated   = time();
        $doc->docdate   = $document->date;
        $doc->courseid  = $document->course_id;
        $doc->groupid   = $document->group_id;
        
        if ($doc->groupid < 0) $doc->groupid = 0;
        
        //insert summary into db
        $id = insert_record(SEARCH_DATABASE_TABLE, $doc);
        
        return $id;
    } 

    /**
    * remove a document record from the index
    * @param document must be a Lucene document instance, or at least a dbid enveloppe
    * @uses db
    */
    public function delDocument($document) {
        global $db;
        
        delete_records(SEARCH_DATABASE_TABLE, 'id', $document->dbid);
    }
} 

?>