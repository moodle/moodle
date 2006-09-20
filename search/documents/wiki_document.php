<?php
  /* Wiki Search Document class and functions
   * This file contains the mapping between a wiki page and it's indexable counterpart,
   * e.g. searchdocument->title = wikipage->pagename
   *
   * Functions for iterating and retrieving the necessary records are now also included
   * in this file, rather than mod/wiki/lib.php
   * */

  require_once("$CFG->dirroot/search/documents/document.php");
  require_once("$CFG->dirroot/mod/wiki/lib.php");

  /* All the $doc->___ fields are required by the base document class!
   * Each and every module that requires search functionality must correctly
   * map their internal fields to the five $doc fields (id, title, author, contents
   * and url). Any module specific data can be added to the $data object, which is
   * serialised into a binary field in the index.
   * */
  class WikiSearchDocument extends SearchDocument {
    public function __construct(&$page, $wiki_id, $course_id, $group_id) {
      // generic information; required
      $doc->docid     = $page->id;
      $doc->title     = $page->pagename;
      $doc->date      = $page->timemodified;

      //remove '(ip.ip.ip.ip)' from wiki author field
      $doc->author    = preg_replace('/\(.*?\)/', '', $page->author);
      $doc->contents  = $page->content;
      $doc->url       = wiki_make_link($wiki_id, $page->pagename, $page->version);

      // module specific information; optional
      $data->version  = $page->version;
      $data->wiki     = $wiki_id;

      // construct the parent class
      parent::__construct($doc, $data, SEARCH_TYPE_WIKI, $course_id, $group_id);
    } //constructor
  } //WikiSearchDocument

  function wiki_name_convert($str) {
    return str_replace(' ', '+', $str);
  } //wiki_name_convert

  function wiki_make_link($wiki_id, $title, $version) {
    global $CFG;
    return $CFG->wwwroot.'/mod/wiki/view.php?wid='.$wiki_id.'&page='.wiki_name_convert($title).'&version='.$version;
  } //wiki_make_link

  //rescued and converted from ewikimoodlelib.php
  //retrieves latest version of a page
  function wiki_get_latest_page(&$entry, $pagename, $version=0) {
    $pagename = "'".addslashes($pagename)."'";

    if ($version > 0 and is_int($version)) {
      $version = "AND (version=$version)";
    } else {
      $version = '';
    } //else

    $select = "(pagename=$pagename) AND wiki=".$entry->id." $version ";
    $sort   = 'version DESC';

    //change this to recordset_select, as per http://docs.moodle.org/en/Datalib_Notes
    if ($result_arr = get_records_select('wiki_pages', $select, $sort, '*', 0, 1)) {
      foreach ($result_arr as $obj) {
        $result_obj = $obj;
      } //foreach
    } //if

    if (isset($result_obj))  {
      $result_obj->meta = @unserialize($result_obj->meta);
      return $result_obj;
    } else {
      return false;
    } //else
  } //wiki_get_latest_page

  //fetches all pages, including old versions
  function wiki_get_pages(&$entry) {
    return get_records('wiki_pages', 'wiki', $entry->id);
  } //wiki_get_pages

  //fetches all the latest versions of all the pages
  function wiki_get_latest_pages(&$entry) {
    //== (My)SQL for this
    /* select * from wiki_pages
       inner join
      (select wiki_pages.pagename, max(wiki_pages.version) as ver
      from wiki_pages group by pagename) as a
      on ((wiki_pages.version = a.ver) and
      (wiki_pages.pagename like a.pagename)) */

    $pages = array();

    //http://moodle.org/bugs/bug.php?op=show&bugid=5877&pos=0
    //if ($ids = get_records('wiki_pages', 'wiki', $entry->id, '', 'distinct pagename')) {
    if ($rs = get_recordset('wiki_pages', 'wiki', $entry->id, '', 'distinct pagename')) {
      $ids = $rs->GetRows();
    //--
      foreach ($ids as $id) {
        $pages[] = wiki_get_latest_page($entry, $id[0]);
      } //foreach
    } else {
      return false;
    } //else

    return $pages;
  } //wiki_get_latest_pages

  function wiki_iterator() {
    return get_all_instances_in_courses("wiki", get_courses());
  } //wiki_iterator

  function wiki_get_content_for_index(&$wiki) {
    $documents = array();

    $entries = wiki_get_entries($wiki);
    foreach($entries as $entry) {
      //all pages
      //$pages = wiki_get_pages($entry);

      //latest pages
      $pages = wiki_get_latest_pages($entry);

      if (is_array($pages)) {
        foreach($pages as $page) {
          if (strlen($page->content) > 0) {
            $documents[] = new WikiSearchDocument($page, $entry->wikiid, $entry->course, $entry->groupid);
          } //if
        } //foreach
      } //if
    } //foreach

    return $documents;
  } //wiki_get_content_for_index

  //returns a single wiki search document based on a wiki_entry id
  function wiki_single_document($id) {
    $pages = get_recordset('wiki_pages', 'id', $id);
    $page = $pages->fields;

    $entries = get_recordset('wiki_entries', 'id', $page['wiki']);
    $entry = $entries->fields;

    return new WikiSearchDocument($page, $entry['wikiid'], $entry['course'], $entry['groupid']);
  } //wiki_single_document

  function wiki_delete($info) {
    return $info;
  } //wiki_delete

  //returns the var names needed to build a sql query for addition/deletions
  function wiki_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array('id', 'wiki_pages', 'created', 'lastmodified');
  } //wiki_db_names

?>