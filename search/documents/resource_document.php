<?php

  require_once("$CFG->dirroot/search/documents/document.php");

  class ResourceSearchDocument extends SearchDocument {
    public function __construct(&$resource) {
      // generic information; required
      $doc->docid     = $resource['id'];
      $doc->title     = strip_tags($resource['name']);
      $doc->date      = $resource['timemodified'];

      $doc->author    = '';
      $doc->contents  = strip_tags($resource['summary']).' '.strip_tags($resource['alltext']);
      $doc->url       = resource_make_link($resource['id']);

      // module specific information; optional
      $data = array();

      // construct the parent class
      parent::__construct($doc, $data, SEARCH_TYPE_RESOURCE, $resource['course'], -1);
    } //constructor
  } //ResourceSearchDocument

  function resource_make_link($resource_id) {
    global $CFG;
    return $CFG->wwwroot.'/mod/resource/view.php?r='.$resource_id;
  } //resource_make_link

  function resource_iterator() {
    //trick to leave search indexer functionality intact, but allow
    //this document to only use the below function to return info
    //to be searched
    return array(true);
  } //resource_iterator

  //this function does not need a content iterator, returns all the info
  //itself; remember to fake the iterator array though
  function resource_get_content_for_index(&$notneeded) {
    $documents = array();

    $resources = get_recordset_sql('SELECT *
                              FROM {$CFG->prefix}resource
                              WHERE alltext NOT LIKE ""
                              AND alltext NOT LIKE " "
                              AND alltext NOT LIKE "&nbsp;"
                              AND TYPE != "file"');

    while (!$resources->EOF) {
      $resource = $resources->fields;

      if ($resource) {
        $documents[] = new ResourceSearchDocument($resource);
      } //if

      $resources->MoveNext();
    } //foreach

    return $documents;
  } //resource_get_content_for_index

  //returns a single resource search document based on a resource_entry id
  function resource_single_document($id) {
    $resources = get_recordset_sql('SELECT *
                              FROM {$CFG->prefix}resource
                              WHERE alltext NOT LIKE ""
                              AND alltext NOT LIKE " "
                              AND alltext NOT LIKE "&nbsp;"
                              AND TYPE != "file",
                              AND id = '.$id);

    $resource = $resources->fields;

    return new ResourceSearchDocument($resource);
  } //resource_single_document

  function resource_delete($info) {
    return $info;
  } //resource_delete

  //returns the var names needed to build a sql query for addition/deletions
  function resource_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name], [additional where conditions for sql]
    return array('id', 'resource', 'timemodified', 'timemodified', "WHERE alltext NOT LIKE '' AND alltext NOT LIKE ' ' AND alltext NOT LIKE '&nbsp;' AND TYPE != 'file'");
  } //resource_db_names

?>