<?php
  /* Index info class
   *
   * Used to retrieve information about an index.
   * Has methods to check for valid database and data directory,
   * and the index itself.
   * */

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
      
      try {
        $test_index = new Zend_Search_Lucene($this->path, false);
        $validindex = true;        
      } catch(Exception $e) {    
        $validindex = false;
      } //catch
      
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

      $db_exists = false;
            
      $admin_tables = $db->MetaTables();
      
      if (in_array($CFG->prefix.'search_documents', $admin_tables)) {
        $db_exists = true;
        
        //total documents
        $this->dbcount = count_records('search_documents');

        //individual document types
        $types = search_get_document_types();
        sort($types);
  
        foreach($types as $type) {
          $c = count_records('search_documents', 'doctype', $type);
          $this->types[$type] = (int)$c;
        } //foreach
      } else {
        $this->dbcount = 0;
        $this->types = array();
      } //else      
      
      if ($CFG->search_indexer_busy == '1') {
        $this->complete = false;
      } else {
        $this->complete = true;
      } //if
      
      if ($this->valid() && $CFG->search_indexer_run_date) {
        $this->time = $CFG->search_indexer_run_date;
      } else {
        $this->time = 0;
      } //else
    } //__construct
    
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
    
    public function is_valid_dir() {
      if ($this->filecount > 0) {
        return true;
      } else {
        return false;
      } //else
    } //is_valid_dir
    
    public function is_valid_db() {
      if ($this->dbcount > 0) {
        return true;
      } else {
        return false;
      } //else
    } //is_valid_db      
        
    public function __get($var) {      
      if (in_array($var, array_keys(get_class_vars(get_class($this))))) {
        return $this->$var;
      } //if
    } //__get        
  } //IndexInfo
      
?>