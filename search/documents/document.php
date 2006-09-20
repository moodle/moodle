<?php
  /* Base search document from which other module/block types can
   * extend.
   * */

  abstract class SearchDocument extends Zend_Search_Lucene_Document {
    public function __construct(&$doc, &$data, $document_type, $course_id, $group_id) {
      $this->addField(Zend_Search_Lucene_Field::Keyword('docid', $doc->docid));
      $this->addField(Zend_Search_Lucene_Field::Text('title', $doc->title));
      $this->addField(Zend_Search_Lucene_Field::Text('author', $doc->author));
      $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $doc->contents));
      $this->addField(Zend_Search_Lucene_Field::UnIndexed('url', $doc->url));
      $this->addField(Zend_Search_Lucene_Field::UnIndexed('date', $doc->date));

      //additional data added on a per-module basis
      $this->addField(Zend_Search_Lucene_Field::Binary('data', serialize($data)));

      $this->addField(Zend_Search_Lucene_Field::Keyword('doctype', $document_type));
      $this->addField(Zend_Search_Lucene_Field::Keyword('course_id', $course_id));
      $this->addField(Zend_Search_Lucene_Field::Keyword('group_id', $group_id));
    } //constructor
  } //SearchDocument

?>