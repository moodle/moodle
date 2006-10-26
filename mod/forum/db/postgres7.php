<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG;

  if ($oldversion < 2003042402) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
  }

  if ($oldversion < 2003082500) {
      table_column("forum", "", "assesstimestart", "integer", "10", "unsigned", "0", "", "assessed");
      table_column("forum", "", "assesstimefinish", "integer", "10", "unsigned", "0", "", "assesstimestart");
  }

  if ($oldversion < 2003082502) {
      execute_sql("UPDATE {$CFG->prefix}forum SET scale = (- scale)");
  }

  if ($oldversion < 2003100600) {
      table_column("forum", "", "maxbytes", "integer", "10", "unsigned", "0", "", "scale");
  }

  if ($oldversion < 2004010100) {
      table_column("forum", "", "assesspublic", "integer", "4", "unsigned", "0", "", "assessed");
  }

  if ($oldversion < 2004011404) {
      table_column("forum_discussions", "", "userid", "integer", "10", "unsigned", "0", "", "firstpost");

      if ($discussions = get_records_sql("SELECT d.id, p.userid
                                            FROM {$CFG->prefix}forum_discussions as d, 
                                                 {$CFG->prefix}forum_posts as p
                                           WHERE d.firstpost = p.id")) {
          foreach ($discussions as $discussion) {
              update_record("forum_discussions", $discussion);
          }
      }
  }
  if ($oldversion < 2004012200) {
      table_column("forum_discussions", "", "groupid", "integer", "10", "unsigned", "0", "", "userid");
  }

  if ($oldversion < 2004020600) {
      table_column("forum_discussions", "", "usermodified", "integer", "10", "unsigned", "0", "", "timemodified");
  }

  if ($oldversion < 2004050300) {
      table_column("forum","","rsstype","integer","2", "unsigned", "0", "", "forcesubscribe");
      table_column("forum","","rssarticles","integer","2", "unsigned", "0", "", "rsstype");
      set_config("forum_enablerssfeeds",0);
  }

  if ($oldversion < 2004060100) {
      modify_database('', "CREATE TABLE prefix_forum_queue (
                           id SERIAL PRIMARY KEY,
                           userid integer default 0 NOT NULL,
                           discussionid integer default 0 NOT NULL,
                           postid integer default 0 NOT NULL
                           );");
  }

  if ($oldversion < 2004070700) {    // This may be redoing it from STABLE but that's OK
      table_column("forum_discussions", "groupid", "groupid", "integer", "10", "", "0", "");
  }


  if ($oldversion < 2004111700) {
      execute_sql(" DROP INDEX {$CFG->prefix}forum_posts_parent_idx;",false);
      execute_sql(" DROP INDEX {$CFG->prefix}forum_posts_discussion_idx;",false);
      execute_sql(" DROP INDEX {$CFG->prefix}forum_posts_userid_idx;",false);
      execute_sql(" DROP INDEX {$CFG->prefix}forum_discussions_forum_idx;",false);
      execute_sql(" DROP INDEX {$CFG->prefix}forum_discussions_userid_idx;",false);

      execute_sql(" CREATE INDEX {$CFG->prefix}forum_posts_parent_idx ON {$CFG->prefix}forum_posts (parent) ");
      execute_sql(" CREATE INDEX {$CFG->prefix}forum_posts_discussion_idx ON {$CFG->prefix}forum_posts (discussion) ");
      execute_sql(" CREATE INDEX {$CFG->prefix}forum_posts_userid_idx ON {$CFG->prefix}forum_posts (userid) ");
      execute_sql(" CREATE INDEX {$CFG->prefix}forum_discussions_forum_idx ON {$CFG->prefix}forum_discussions (forum) ");
      execute_sql(" CREATE INDEX {$CFG->prefix}forum_discussions_userid_idx ON {$CFG->prefix}forum_discussions (userid) ");
  }

  if ($oldversion < 2004111200) {
      execute_sql("DROP INDEX {$CFG->prefix}forum_course_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}forum_queue_userid_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}forum_queue_discussion_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}forum_queue_postid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}forum_ratings_userid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}forum_ratings_post_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}forum_subscriptions_userid_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}forum_subscriptions_forum_idx;",false);

      modify_database('','CREATE INDEX prefix_forum_course_idx ON prefix_forum (course);');
      modify_database('','CREATE INDEX prefix_forum_queue_userid_idx ON prefix_forum_queue (userid);');
      modify_database('','CREATE INDEX prefix_forum_queue_discussion_idx ON prefix_forum_queue (discussionid);');
      modify_database('','CREATE INDEX prefix_forum_queue_postid_idx ON prefix_forum_queue (postid);');
      modify_database('','CREATE INDEX prefix_forum_ratings_userid_idx ON prefix_forum_ratings (userid);');
      modify_database('','CREATE INDEX prefix_forum_ratings_post_idx ON prefix_forum_ratings (post);');
      modify_database('','CREATE INDEX prefix_forum_subscriptions_userid_idx ON prefix_forum_subscriptions (userid);');
      modify_database('','CREATE INDEX prefix_forum_subscriptions_forum_idx ON prefix_forum_subscriptions (forum);');
  }

  if ($oldversion < 2005011500) {
      modify_database('','CREATE TABLE prefix_forum_read (
                          id SERIAL PRIMARY KEY,
                          userid integer default 0 NOT NULL,
                          forumid integer default 0 NOT NULL,
                          discussionid integer default 0 NOT NULL,
                          postid integer default 0 NOT NULL,
                          firstread integer default 0 NOT NULL,
                          lastread integer default 0 NOT NULL
                        );');

      modify_database('','CREATE INDEX prefix_forum_user_forum_idx ON prefix_forum_read (userid, forumid);');
      modify_database('','CREATE INDEX prefix_forum_user_discussion_idx ON prefix_forum_read (userid, discussionid);');
      modify_database('','CREATE INDEX prefix_forum_user_post_idx ON prefix_forum_read (userid, postid);');

      set_config('upgrade', 'forumread');   // The upgrade of this table will be done later by admin/upgradeforumread.php
  }

  if ($oldversion < 2005032900) {
      modify_database('','CREATE INDEX prefix_forum_posts_created_idx ON prefix_forum_posts (created);');
      modify_database('','CREATE INDEX prefix_forum_posts_mailed_idx ON prefix_forum_posts (mailed);');
  }

  if ($oldversion < 2005041100) { // replace wiki-like with markdown
      include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
      $wtm = new WikiToMarkdown();
      $sql = "select course from {$CFG->prefix}forum_discussions, {$CFG->prefix}forum_posts ";
      $sql .=  "where {$CFG->prefix}forum_posts.discussion = {$CFG->prefix}forum_discussions.id ";
      $sql .=  "and {$CFG->prefix}forum_posts.id = ";
      $wtm->update( 'forum_posts','message','format',$sql );
  }

  if ($oldversion < 2005042300) { // Add tracking prefs table
      modify_database('','CREATE TABLE prefix_forum_track_prefs (
                          id SERIAL PRIMARY KEY, 
                          userid integer default 0 NOT NULL,
                          forumid integer default 0 NOT NULL
                        );');
  }

  if ($oldversion < 2005042600) {
      table_column('forum','','trackingtype','integer','2', 'unsigned', '1', '', 'forcesubscribe');
      modify_database('','CREATE INDEX prefix_forum_track_user_forum_idx ON prefix_forum_track_prefs (userid, forumid);');
  }

  if ($oldversion < 2005042601) { // Mass cleanup of bad postgres upgrade scripts
      modify_database('','ALTER TABLE prefix_forum ALTER trackingtype SET NOT NULL');
  }

  if ($oldversion < 2005111100) {
      table_column('forum_discussions','','timestart','integer');
      table_column('forum_discussions','','timeend','integer');
  }

  if ($oldversion < 2006011600) {
      notify('forum_type does not exists, you can ignore and this will properly removed');
      execute_sql("ALTER TABLE {$CFG->prefix}forum DROP CONSTRAINT {$CFG->prefix}forum_type");
      execute_sql("ALTER TABLE {$CFG->prefix}forum ADD CONSTRAINT {$CFG->prefix}forum_type CHECK (type IN ('single','news','general','social','eachuser','teacher','qanda')) ");
  }

  if ($oldversion < 2006011601) {
      table_column('forum','','warnafter');
      table_column('forum','','blockafter');
      table_column('forum','','blockperiod');
  }

  if ($oldversion < 2006011700) {
      table_column('forum_posts','','mailnow','integer');
  }

  if ($oldversion < 2006011701) {
      execute_sql("ALTER TABLE {$CFG->prefix}forum DROP CONSTRAINT {$CFG->prefix}forum_type_check");
  }

  if ($oldversion < 2006011702) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('forum', 'user report', 'user', 'firstname||\' \'||lastname')");
  }

  if ($oldversion < 2006081800) {
      // Upgrades for new roles and capabilities support.
      require_once($CFG->dirroot.'/mod/forum/lib.php');

      $forummod = get_record('modules', 'name', 'forum');

      if ($forums = get_records('forum')) {

          if (!$teacherroles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
              notify('Default teacher role was not found. Roles and permissions '.
                     'for all your forums will have to be manually set after '.
                     'this upgrade.');
          }
          if (!$studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
              notify('Default student role was not found. Roles and permissions '.
                     'for all your forums will have to be manually set after '.
                     'this upgrade.');
          }
          if (!$guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
              notify('Default guest role was not found. Roles and permissions '.
                     'for teacher forums will have to be manually set after '.
                     'this upgrade.');
          }
          foreach ($forums as $forum) {
              if (!forum_convert_to_roles($forum, $forummod->id, $teacherroles,
                                          $studentroles, $guestroles)) {
                  notify('Forum with id '.$forum->id.' was not upgraded');
              }
          }
          // We need to rebuild all the course caches to refresh the state of
          // the forum modules.
          rebuild_course_cache();
      
      } // End if.
      
      // Drop column forum.open.
      modify_database('', 'ALTER TABLE prefix_forum DROP COLUMN open;');

      // Drop column forum.assesspublic.
      modify_database('', 'ALTER TABLE prefix_forum DROP COLUMN assesspublic;');
  }
    
  if ($oldversion < 2006082700) {
      $sql = "UPDATE {$CFG->prefix}forum_posts SET message = REPLACE(message, '".TRUSTTEXT."', '');";
      $likecond = sql_ilike()." '%".TRUSTTEXT."%'";
      while (true) {
          if (!count_records_select('forum_posts', "message $likecond")) {
              break;
          }
          execute_sql($sql);
      }
  }

  //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

  return true;

}


?>
