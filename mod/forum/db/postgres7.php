<?php // $Id$

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG;

  if ($oldversion < 2003042402) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
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

  return true;

}


?>

