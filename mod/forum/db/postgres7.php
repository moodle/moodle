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

  return true;

}


?>

