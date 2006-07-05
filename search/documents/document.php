<?php
  /* Base search document from which other module/block types can
   * extend.
   * */
  
  class SearchDocument extends Zend_Search_Lucene_Document {  
    public function __construct($document_type, $cid, $gid) {
      $this->addField(Zend_Search_Lucene_Field::Keyword('type', $document_type));
      $this->addField(Zend_Search_Lucene_Field::Keyword('courseid', $cid));    
      $this->addField(Zend_Search_Lucene_Field::Keyword('groupid', $gid));      
    } //constructor    
  } //SearchDocument
    
?>