<?php
  /* Index info class
   *
   * Used to retrieve information about an index.
   * Has methods to check for valid database and data directory,
   * and the index itself.
   * */

  require_once("$CFG->dirroot/search/lib.php");
  require_once("$CFG->dirroot/search/Zend/Search/Lucene.php");

  class IndexInfo {
    private $path,        //index data directory
            $size,        //size of directory (i.e. the whole index)
            $filecount,   //number of files
            $indexcount,  //number of docs in index
            $dbcount,     //number of docs in db
            $types,       //array of [document types => count]
            $complete,    //is index completely formed?
            $time;        //date index was generated

    public function __construct($path=SEARCH_INDEX_PATH) {
      global $CFG, $db;

      $this->path = $path;

      //test to see if there is a valid index on disk, at the specified path
      try {
        $test_index = new Zend_Search_Lucene($this->path, false);
        $validindex = true;
      } catch(Exception $e) {
        $validindex = false;
      } //catch

      //retrieve file system info about the index if it is valid
      if ($validindex) {
        $this->size = display_size(get_directory_size($this->path));
        $index_dir  = get_directory_list($this->path, '', false, false);
        $this->filecount = count($index_dir);
        $this->indexcount = $test_index->count();
      } else {
        $this->size = 0;
        $this->filecount = 0;
        $this->indexcount = 0;
      } //else

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
        $types = search_get_document_types();
        sort($types);

        foreach($types as $type) {
          $c = count_records(SEARCH_DATABASE_TABLE, 'doctype', $type);
          $this->types[$type] = (int)$c;
        } //foreach
      } else {
        $this->dbcount = 0;
        $this->types = array();
      } //else

      //check if the busy flag is set
      if ($CFG->search_indexer_busy == '1') {
        $this->complete = false;
      } else {
        $this->complete = true;
      } //if

      //get the last run date for the indexer
      if ($this->valid() && $CFG->search_indexer_run_date) {
        $this->time = $CFG->search_indexer_run_date;
      } else {
        $this->time = 0;
      } //else
    } //__construct

    //returns false on error, and the error message via referenced variable $err
    public function valid(&$err=null) {
      $err = array();
      $ret = true;

      if (!$this->is_valid_dir()) {
        $err['dir'] = 'Index directory either contains an invalid index, or nothing at all.';
        $ret = false;
      } //if

      if (!$this->is_valid_db()) {
        $err['db'] = 'Database table is not present, or contains no index records.';
        $ret = false;
      } //if

      if (!$this->complete) {
        $err['index'] = 'Indexing was not successfully completed, please restart it.';
        $ret = false;
      } //if

      return $ret;
    } //valid

    //is the index dir valid
    public function is_valid_dir() {
      if ($this->filecount > 0) {
        return true;
      } else {
        return false;
      } //else
    } //is_valid_dir

    //is the db table valid
    public function is_valid_db() {
      if ($this->dbcount > 0) {
        return true;
      } else {
        return false;
      } //else
    } //is_valid_db

    //shorthand get method for the class variables
    public function __get($var) {
      if (in_array($var, array_keys(get_class_vars(get_class($this))))) {
        return $this->$var;
      } //if
    } //__get
  } //IndexInfo


  /* DB Index control class
   *
   * Used to control the search index database table
   * */

  class IndexDBControl {
    //does the table exist?
    public function checkTableExists() {
      global $CFG, $db;

      $table = SEARCH_DATABASE_TABLE;
      $tables = $db->MetaTables();

      if (in_array($CFG->prefix.$table, $tables)) {
        return true;
      } else {
        return false;
      } //else
    } //checkTableExists

    //is our database setup valid?
    public function checkDB() {
      global $CFG, $db;

      $sqlfile = "$CFG->dirroot/search/db/$CFG->dbtype.sql";
      $ret = false;

      if ($this->checkTableExists()) {
        execute_sql('drop table '.$CFG->prefix.SEARCH_DATABASE_TABLE, false);
      } //if

      ob_start(); //turn output buffering on - to hide modify_database() output
      $ret = modify_database($sqlfile, '', false);
      ob_end_clean(); //chuck the buffer and resume normal operation

      return $ret;
    } //checkDB

    //add a document record to the table
    public function addDocument($document=null) {
      global $db;

      if ($document == null) {
        return false;
      } //if

      //object to insert into db
      $doc->doctype   = $document->doctype;
      $doc->docid     = $document->docid;
      $doc->title     = search_escape_string($document->title);
      $doc->url       = search_escape_string($document->url);
      $doc->update    = time();
      $doc->docdate   = $document->date;
      $doc->courseid  = $document->course_id;
      $doc->groupid   = $document->group_id;

      //insert summary into db
      $id = insert_record(SEARCH_DATABASE_TABLE, $doc);

      return $id;
    } //addDocument

    //remove a document record from the index
    public function delDocument($document) {
      global $db;

      delete_records(SEARCH_DATABASE_TABLE, 'id', $document->dbid);
    } //delDocument
  } //IndexControl

?>