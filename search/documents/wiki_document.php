<?php
/**
* Global Search Engine for Moodle
* Michael Champanis (mchampan) [cynnical@gmail.com]
* review 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* document handling for wiki activity module
* This file contains the mapping between a wiki page and it's indexable counterpart,
* e.g. searchdocument->title = wikipage->pagename
*
* Functions for iterating and retrieving the necessary records are now also included
* in this file, rather than mod/wiki/lib.php
**/

require_once("$CFG->dirroot/search/documents/document.php");
require_once("$CFG->dirroot/mod/wiki/lib.php");

/* 
* All the $doc->___ fields are required by the base document class!
* Each and every module that requires search functionality must correctly
* map their internal fields to the five $doc fields (id, title, author, contents
* and url). Any module specific data can be added to the $data object, which is
* serialised into a binary field in the index.
**/
class WikiSearchDocument extends SearchDocument {
    public function __construct(&$page, $wiki_id, $course_id, $group_id, $user_id, $context_id) {
        // generic information; required
        $doc->docid         = $page['id'];
        $doc->documenttype  = SEARCH_TYPE_WIKI;
        $doc->itemtype      = 'standard';
        $doc->contextid     = $context_id;

        $doc->title     = $page['pagename'];
        $doc->date      = $page['timemodified'];
        //remove '(ip.ip.ip.ip)' from wiki author field
        $doc->author    = preg_replace('/\(.*?\)/', '', $page['author']);
        $doc->contents  = $page['content'];
        $doc->url       = wiki_make_link($wiki_id, $page['pagename'], $page['version']);
        
        // module specific information; optional
        $data->version  = $page['version'];
        $data->wiki     = $wiki_id;
        
        // construct the parent class
        parent::__construct($doc, $data, $course_id, $group_id, $user_id, PATH_FOR_SEARCH_TYPE_WIKI);
    } //constructor
} //WikiSearchDocument

/**
* converts a page name to cope Wiki constraints. Transforms spaces in plus.
* @param str the name to convert
* @return the converted name
*/
function wiki_name_convert($str) {
    return str_replace(' ', '+', $str);
} //wiki_name_convert

/**
* constructs a valid link to a wiki content
* @param wikiId
* @param title
* @param version
*/
function wiki_make_link($wikiId, $title, $version) {
    global $CFG;

    return $CFG->wwwroot.'/mod/wiki/view.php?wid='.$wikiId.'&amp;page='.wiki_name_convert($title).'&amp;version='.$version;
} //wiki_make_link

/**
* rescued and converted from ewikimoodlelib.php
* retrieves latest version of a page
* @param entry the wiki object as a reference
* @param pagename the name of the page known by the wiki engine
* @param version
*/
function wiki_get_latest_page(&$entry, $pagename, $version = 0) {
    $pagename = "'".addslashes($pagename)."'";
    
    if ($version > 0 and is_int($version)) {
        $version = "AND (version=$version)";
    } else {
        $version = '';
    } 
    
    $select = "(pagename=$pagename) AND wiki=".$entry->id." $version ";
    $sort   = 'version DESC';
    
    //change this to recordset_select, as per http://docs.moodle.org/en/Datalib_Notes
    if ($result_arr = get_records_select('wiki_pages', $select, $sort, '*', 0, 1)) {
        foreach ($result_arr as $obj) {
            $result_obj = $obj;
        } 
    } 
    
    if (isset($result_obj))  {
        $result_obj->meta = @unserialize($result_obj->meta);
        return $result_obj;
    } else {
        return false;
    } 
} //wiki_get_latest_page

/**
* fetches all pages, including old versions
* @param entry the wiki object as a reference
* @return an array of record objects that represents pages of this wiki object
*/
function wiki_get_pages(&$entry) {
    return get_records('wiki_pages', 'wiki', $entry->id);
} //wiki_get_pages

/**
* fetches all the latest versions of all the pages
*
*/
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
    if ($ids = get_records('wiki_pages', 'wiki', $entry->id, '', 'distinct pagename')) {
        if ($pagesets = get_records('wiki_pages', 'wiki', $entry->id, '', 'distinct pagename')) {
            foreach ($pagesets as $aPageset) {
                $pages[] = wiki_get_latest_page($entry, $aPageset->id);
            } 
        } else {
            return false;
        } 
    }
    return $pages;
} //wiki_get_latest_pages

/**
* part of search engine API
*
*/
function wiki_iterator() {
    $wikis = get_records('wiki');
    return $wikis;
} //wiki_iterator

/**
* part of search engine API
* @param wiki a wiki instance
* @return an array of searchable deocuments
*/
function wiki_get_content_for_index(&$wiki) {

    $documents = array();
    $entries = wiki_get_entries($wiki);
    foreach($entries as $entry) {
        $coursemodule = get_field('modules', 'id', 'name', 'wiki');
        $cm = get_record('course_modules', 'course', $entry->course, 'module', $coursemodule, 'instance', $entry->wikiid);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        //all pages
        //$pages = wiki_get_pages($entry);
        
        //latest pages
        $pages = wiki_get_latest_pages($entry);
        if (is_array($pages)) {
            foreach($pages as $page) {
                if (strlen($page->content) > 0) {
                    $documents[] = new WikiSearchDocument(get_object_vars($page), $entry->wikiid, $entry->course, $entry->groupid, $page->userid, $context->id);
                } 
            } 
        } 
    } 
    return $documents;
} //wiki_get_content_for_index

/**
* returns a single wiki search document based on a wiki_entry id
* @param id the id of the wiki
* @param itemtype the type of information (standard)
* @retuen a searchable document
*/
function wiki_single_document($id, $itemtype) {
    $page = get_record('wiki_pages', 'id', $id);
    $entry = get_record('wiki_entries', 'id', $page->wiki);
    $coursemodule = get_field('modules', 'id', 'name', 'wiki');
    $cm = get_record('course_modules', 'course', $entry->course, 'module', $coursemodule, 'instance', $entry->wikiid);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    return new WikiSearchDocument(get_object_vars($page), $entry->wikiid, $entry->course, $entry->groupid, $page->userid, $context->id);
} //wiki_single_document

/**
* dummy delete function that packs id with itemtype.
* this was here for a reason, but I can't remember it at the moment.
*
*/
function wiki_delete($info, $itemtype) {
    $object->id = $info;
    $object->itemtype = $itemtype;
    return $object;
} //wiki_delete

//returns the var names needed to build a sql query for addition/deletions
function wiki_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array(array('id', 'wiki_pages', 'created', 'lastmodified', 'standard'));
} //wiki_db_names

/**
* this function handles the access policy to contents indexed as searchable documents. If this 
* function does not exist, the search engine assumes access is allowed.
* When this point is reached, we already know that : 
* - user is legitimate in the surrounding context
* - user may be guest and guest access is allowed to the module
* - the function may perform local checks within the module information logic
* @param path the access path to the module script code
* @param itemtype the information subclassing (usefull for complex modules, defaults to 'standard')
* @param this_id the item id within the information class denoted by itemtype. In wikies, this id 
* points out the indexed wiki page.
* @param user the user record denoting the user who searches
* @param group_id the current group used by the user when searching
* @return true if access is allowed, false elsewhere
*/
function wiki_check_text_access($path, $itemtype, $this_id, $user, $group_id, $context_id){
    global $CFG;
    
    // get the wiki object and all related stuff
    $page = get_record('wiki_pages', 'id', $id);
    $entry = get_record('wiki_entries', 'id', $page->wiki);
    $course = get_record('course', 'id', $entry->course);
    $module_context = get_record('context', 'id', $context_id);
    $cm = get_record('course_modules', 'id', $module_context->instance);
    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $module_context)) return false;
    
    //group consistency check : checks the following situations about groups
    // trap if user is not same group and groups are separated
    $current_group = get_current_group($course->id);
    if ((groupmode($course) == SEPARATEGROUPS) && $group_id != $current_group && !has_capability('moodle/site:accessallgroups', $module_context)) return false;
        
    return true;
} //wiki_check_text_access
?>