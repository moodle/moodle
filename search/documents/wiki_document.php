<?php

  require_once("$CFG->dirroot/search/documents/document.php");
  
  class WikiSearchDocument extends SearchDocument {  
    public function __construct(&$page, $wiki_id, $cid, $uid, $gid) {
      $this->addField(Zend_Search_Lucene_Field::Text('title', $page->pagename));
      $this->addField(Zend_Search_Lucene_Field::Text('author', $page->author));
      $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $page->content));
      
      $this->addField(Zend_Search_Lucene_Field::Keyword('id', $page->id));
      $this->addField(Zend_Search_Lucene_Field::Keyword('version', $page->version));
      $this->addField(Zend_Search_Lucene_Field::Keyword('wiki', $wiki_id));
      
      parent::__construct(SEARCH_WIKI_TYPE, $cid, $uid, $gid);
    } //constructor    
  } //WikiSearchDocument
  
  function wiki_name_convert($str) {
    return str_replace(' ', '+', $str);
  } //wiki_name_convert
  
  function wiki_make_link(&$doc) {
    global $CFG;    
    return $CFG->wwwroot.'/mod/wiki/view.php?wid='.$doc->wiki.'&page='.wiki_name_convert($doc->title).'&version='.$doc->version;
  } //wiki_make_link
  
?>