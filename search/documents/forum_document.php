<?php

  /* see wiki_document.php for descriptions */

  require_once("$CFG->dirroot/search/documents/document.php");
  require_once("$CFG->dirroot/mod/forum/lib.php");

  class ForumSearchDocument extends SearchDocument {
    public function __construct(&$post, $forum_id, $course_id, $group_id) {
      // generic information
      $doc->docid     = $post['id'];
      $doc->title     = $post['subject'];
      $doc->author    = $post['firstname']." ".$post['lastname'];
      $doc->contents  = $post['message'];
      $doc->date      = $post['created'];

      $doc->url       = forum_make_link($post['discussion'], $post['id']);

      // module specific information
      $data->forum      = $forum_id;
      $data->discussion = $post['discussion'];

      parent::__construct($doc, $data, SEARCH_TYPE_FORUM, $course_id, $group_id);
    } //constructor
  } //ForumSearchDocument

  function forum_make_link($discussion_id, $post_id) {
    global $CFG;
    return $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion_id.'#'.$post_id;
  } //forum_make_link

  function forum_iterator() {
      //no @ = Undefined index:  82 in moodle/lib/datalib.php on line 2671
      return @get_all_instances_in_courses("forum", get_courses());
  } //forum_iterator

  function forum_get_content_for_index(&$forum) {
      $documents = array();
      if (!$forum) return $documents;

      $posts = forum_get_discussions_fast($forum->id);
      if (!$posts) return $documents;

      while (!$posts->EOF) {
        $post = $posts->fields;

        if (is_array($post)) {
          if (strlen($post['message']) > 0 && ($post['deleted'] != 1)) {
            $documents[] = new ForumSearchDocument($post, $forum->id, $forum->course, $post['groupid']);
          } //if

          if ($children = forum_get_child_posts_fast($post['id'], $forum->id)) {
            while (!$children->EOF) {
              $child = $children->fields;

              if (strlen($child['message']) > 0 && ($child['deleted'] != 1)) {
                $documents[] = new ForumSearchDocument($child, $forum->id, $forum->course, $post['groupid']);
              } //if

              $children->MoveNext();
            } //foreach
          } //if
        } //if

        $posts->MoveNext();
      } //foreach

      return $documents;
  } //forum_get_content_for_index

  //returns a single forum search document based on a forum_entry id
  function forum_single_document($id) {
    $posts = get_recordset('forum_posts', 'id', $id);
    $post = $posts->fields;

    $discussions = get_recordset('forum_discussions', 'id', $post['discussion']);
    $discussion = $discussions->fields;

    $forums = get_recordset('forum', 'id', $discussion['forum']);
    $forum = $forums->fields;

    return new ForumSearchDocument($post, $forum['id'], $forum['course'], $post['groupid']);
  } //forum_single_document

  function forum_delete($info) {
    return $info;
  } //forum_delete

  //returns the var names needed to build a sql query for addition/deletions
  function forum_db_names() {
    //[primary id], [table name], [time created field name], [time modified field name]
    return array('id', 'forum_posts', 'created', 'modified');
  } //forum_db_names

  //reworked faster version from /mod/forum/lib.php
  function forum_get_discussions_fast($forum) {
    global $CFG, $USER;

    $timelimit='';

    if (!empty($CFG->forum_enabletimedposts)) {
      if (!((isadmin() and !empty($CFG->admineditalways)) || isteacher(get_field('forum', 'course', 'id', $forum)))) {
        $now = time();
        $timelimit = " AND ((d.timestart = 0 OR d.timestart <= '$now') AND (d.timeend = 0 OR d.timeend > '$now')";
        if (!empty($USER->id)) {
          $timelimit .= " OR d.userid = '$USER->id'";
        }
        $timelimit .= ')';
      }
    }

    return get_recordset_sql("SELECT p.id, p.subject, p.discussion, p.message,
                                  p.deleted, d.groupid, u.firstname, u.lastname
                              FROM {$CFG->prefix}forum_discussions d
                              JOIN {$CFG->prefix}forum_posts p ON p.discussion = d.id
                              JOIN {$CFG->prefix}user u ON p.userid = u.id
                             WHERE d.forum = '$forum'
                               AND p.parent = 0
                                   $timelimit
                          ORDER BY d.timemodified DESC");
  } //forum_get_discussions_fast

  //reworked faster version from /mod/forum/lib.php
  function forum_get_child_posts_fast($parent, $forumid) {
    global $CFG;

    return get_recordset_sql("SELECT p.id, p.subject, p.discussion, p.message, p.deleted,
                              $forumid AS forum, u.firstname, u.lastname
                              FROM {$CFG->prefix}forum_posts p
                         LEFT JOIN {$CFG->prefix}user u ON p.userid = u.id
                             WHERE p.parent = '$parent'
                          ORDER BY p.created ASC");
  } //forum_get_child_posts_fast

?>