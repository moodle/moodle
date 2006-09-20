<?php
  /* This document illustrates how easy it is to add a module to
   * the search index - the only modifications made were creating
   * this file, and adding the SEARCH_TYPE_GLOSSARY constant to
   * search/lib.php - everything else is automatically handled
   * by the indexer script.
   * */

  require_once("$CFG->dirroot/search/documents/document.php");

  class GlossarySearchDocument extends SearchDocument {
    public function __construct(&$entry, $glossary_id, $course_id, $group_id) {
      // generic information; required
      $doc->docid     = $entry['id'];
      $doc->title     = $entry['concept'];
      $doc->date      = $entry['timecreated'];

      $user = get_recordset('user', 'id', $entry['userid'])->fields;

      $doc->author    = $user['firstname'].' '.$user['lastname'];
      $doc->contents  = $entry['definition'];
      $doc->url       = glossary_make_link($entry['id']);

      // module specific information; optional
      $data->glossary = $glossary_id;

      // construct the parent class
      parent::__construct($doc, $data, SEARCH_TYPE_GLOSSARY, $course_id, $group_id);
    } //constructor
  } //GlossarySearchDocument

  function glossary_make_link($entry_id) {
    global $CFG;

    //links directly to entry
    //return $CFG->wwwroot.'/mod/glossary/showentry.php?eid='.$entry_id;

    //preserve glossary pop-up, be careful where you place your ' and "s
    //this function is meant to return a url that is placed between href='[url here]'
    return "$CFG->wwwroot/mod/glossary/showentry.php?eid=$entry_id' onclick='return openpopup(\"/mod/glossary/showentry.php?eid=$entry_id\", \"entry\", \"menubar=0,location=0,scrollbars,resizable,width=600,height=450\", 0);";
  } //glossary_make_link

  function glossary_iterator() {
    return get_all_instances_in_courses("glossary", get_courses());
  } //glossary_iterator

  function glossary_get_content_for_index(&$glossary) {
    $documents = array();

    $entries = get_recordset('glossary_entries', 'glossaryid', $glossary->id);

    while (!$entries->EOF) {
      $entry = $entries->fields;

      if ($entry and strlen($entry['definition']) > 0) {
        $documents[] = new GlossarySearchDocument($entry, $glossary->id, $glossary->course, -1);
      } //if

      $entries->MoveNext();
    } //foreach

    return $documents;
  } //glossary_get_content_for_index

  //returns a single glossary search document based on a glossary_entry id
  function glossary_single_document($id) {
    $entries = get_recordset('glossary_entries', 'id', $id);
    $entry = $entries->fields;

    $glossaries = get_recordset('glossary', 'id', $entry['glossaryid']);
    $glossary = $glossaries->fields;

    return new GlossarySearchDocument($entry, $entry['glossaryid'], $glossary['course'], -1);
  } //glossary_single_document

  //dummy delete function that converts docid from the search table to itself..
  //this was here for a reason, but I can't remember it at the moment.
  function glossary_delete($info) {
    return $info;
  } //glossary_delete

  //returns the var names needed to build a sql query for addition/deletions
  function glossary_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array('id', 'glossary_entries', 'timecreated', 'timemodified');
  } //glossary_db_names

?>