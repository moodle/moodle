<?PHP // $Id$

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG;

  if ($oldversion < 2002073008) {
    execute_sql("DELETE FROM modules WHERE name = 'discuss' ");
    execute_sql("ALTER TABLE `discuss` RENAME `forum_discussions` ");
    execute_sql("ALTER TABLE `discuss_posts` RENAME `forum_posts` ");
    execute_sql("ALTER TABLE `discuss_ratings` RENAME `forum_ratings` ");
    execute_sql("ALTER TABLE `forum` CHANGE `intro` `intro` TEXT NOT NULL ");
    execute_sql("ALTER TABLE `forum` ADD `forcesubscribe` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `assessed`");
    execute_sql("ALTER TABLE `forum` CHANGE `type` `type` ENUM( 'single', 'news', 'social', 'general', 
                             'eachuser', 'teacher' ) DEFAULT 'general' NOT NULL ");
    execute_sql("ALTER TABLE `forum_posts` CHANGE `discuss` `discussion` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'add', 'forum', 'name') ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'add discussion', 'forum_discussions', 'name') ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'add post', 'forum_posts', 'subject') ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'update post', 'forum_posts', 'subject') ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'view discussion', 'forum_discussions', 'name') ");
    execute_sql("DELETE FROM log_display WHERE module = 'discuss' ");
    execute_sql("UPDATE log SET action = 'view discussion' WHERE module = 'discuss' AND action = 'view' ");
    execute_sql("UPDATE log SET action = 'add discussion' WHERE module = 'discuss' AND action = 'add' ");
    execute_sql("UPDATE log SET module = 'forum' WHERE module = 'discuss' ");
    notify("Renamed all the old discuss tables (now part of forum) and created new forum_types");
  }

  if ($oldversion < 2002080100) {
    execute_sql("INSERT INTO log_display VALUES ('forum', 'view subscribers', 'forum', 'name') ");
    execute_sql("INSERT INTO log_display VALUES ('forum', 'update', 'forum', 'name') ");
  }

  if ($oldversion < 2002082900) {
    execute_sql(" ALTER TABLE `forum_posts` ADD `attachment` VARCHAR(100) NOT NULL AFTER `message` ");
  }

  if ($oldversion < 2002091000) {
    if (! execute_sql(" ALTER TABLE `forum_posts` ADD `attachment` VARCHAR(100) NOT NULL AFTER `message` ")) {
      echo "<P>Don't worry about this error - your server already had this upgrade applied";
    }
  }

  if ($oldversion < 2002100300) {
      execute_sql(" ALTER TABLE `forum` CHANGE `open` `open` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL ");
      execute_sql(" UPDATE `forum` SET `open` = 2 WHERE `open` = 1 ");
      execute_sql(" UPDATE `forum` SET `open` = 1 WHERE `open` = 0 ");
  }
  if ($oldversion < 2002101001) {
      execute_sql(" ALTER TABLE `forum_posts` ADD `format` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `message` ");
  }

  if ($oldversion < 2002122300) {
      execute_sql("ALTER TABLE `forum_posts` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
      execute_sql("ALTER TABLE `forum_ratings` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
      execute_sql("ALTER TABLE `forum_subscriptions` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
  }

  if ($oldversion < 2003042402) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
  }

  if ($oldversion < 2003081403) {
      table_column("forum", "assessed", "assessed", "integer", "10", "unsigned", "0");
  }

  if ($oldversion < 2003082500) {
      table_column("forum", "", "assesstimestart", "integer", "10", "unsigned", "0", "", "assessed");
      table_column("forum", "", "assesstimefinish", "integer", "10", "unsigned", "0", "", "assesstimestart");
  }

  if ($oldversion < 2003082502) {
      table_column("forum", "scale", "scale", "integer", "10", "", "0");
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

  if ($oldversion < 2004013000) {
      table_column("forum_posts", "mailed", "mailed", "tinyint", "2");
  }

  if ($oldversion < 2004020600) {
      table_column("forum_discussions", "", "usermodified", "integer", "10", "unsigned", "0", "", "timemodified");
  }

  if ($oldversion < 2004050300) {
      table_column("forum","","rsstype","tinyint","2", "unsigned", "0", "", "forcesubscribe");
      table_column("forum","","rssarticles","tinyint","2", "unsigned", "0", "", "rsstype");
      set_config("forum_enablerssfeeds",0);
  }

  if ($oldversion < 2004050301) {  
      table_column("forum_discussions", "groupid", "groupid", "integer", "10", "", "0", "");
  }

  
  return true;

}



?>
