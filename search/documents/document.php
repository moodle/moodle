<?php
  
  class SearchDocument extends Zend_Search_Lucene_Document {  
    public function __construct($document_type, $cid, $uid, $gid) {
      $this->addField(Zend_Search_Lucene_Field::Keyword('type', $document_type));
      $this->addField(Zend_Search_Lucene_Field::Keyword('courseid', $cid));
      $this->addField(Zend_Search_Lucene_Field::Keyword('userid', $uid));
      $this->addField(Zend_Search_Lucene_Field::Keyword('groupid', $gid));      
    } //constructor    
  } //SearchDocument
    
?>