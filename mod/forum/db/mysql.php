<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG, $db;

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
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'add', 'forum', 'name') ");
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'add discussion', 'forum_discussions', 'name') ");
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'add post', 'forum_posts', 'subject') ");
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'update post', 'forum_posts', 'subject') ");
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'view discussion', 'forum_discussions', 'name') ");
    execute_sql("DELETE FROM log_display WHERE module = 'discuss' ");
    execute_sql("UPDATE log SET action = 'view discussion' WHERE module = 'discuss' AND action = 'view' ");
    execute_sql("UPDATE log SET action = 'add discussion' WHERE module = 'discuss' AND action = 'add' ");
    execute_sql("UPDATE log SET module = 'forum' WHERE module = 'discuss' ");
    notify("Renamed all the old discuss tables (now part of forum) and created new forum_types");
  }

  if ($oldversion < 2002080100) {
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'view subscribers', 'forum', 'name') ");
    execute_sql("INSERT INTO log_display (module, action, mtable, field) VALUES ('forum', 'update', 'forum', 'name') ");
  }

  if ($oldversion < 2002082900) {
    execute_sql(" ALTER TABLE `forum_posts` ADD `attachment` VARCHAR(100) NOT NULL AFTER `message` ");
  }

  if ($oldversion < 2002091000) {
    if (! execute_sql(" ALTER TABLE `forum_posts` ADD `attachment` VARCHAR(100) NOT NULL AFTER `message` ")) {
      echo "<p>Don't worry about this error - your server already had this upgrade applied";
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
      execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
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

  if ($oldversion < 2004060100) {
      modify_database('', "CREATE TABLE `prefix_forum_queue` (
                                `id` int(11) unsigned NOT NULL auto_increment,
                                `userid` int(11) unsigned default 0 NOT NULL,
                                `discussionid` int(11) unsigned default 0 NOT NULL,
                                `postid` int(11) unsigned default 0 NOT NULL,
                                PRIMARY KEY  (`id`),
                                KEY `user` (userid),
                                KEY `post` (postid)
                              ) TYPE=MyISAM COMMENT='For keeping track of posts that will be mailed in digest form';");
  }

  if ($oldversion < 2004070700) {    // This may be redoing it from STABLE but that's OK
      table_column("forum_discussions", "groupid", "groupid", "integer", "10", "", "0", "");
  }
  
  if ($oldversion < 2004111700) {
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` DROP INDEX {$CFG->prefix}forum_posts_parent_idx;",false);
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` DROP INDEX {$CFG->prefix}forum_posts_discussion_idx;",false);
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` DROP INDEX {$CFG->prefix}forum_posts_userid_idx;",false);
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_discussions` DROP INDEX {$CFG->prefix}forum_discussions_forum_idx;",false); 
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_discussions` DROP INDEX {$CFG->prefix}forum_discussions_userid_idx;",false);

      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` ADD INDEX {$CFG->prefix}forum_posts_parent_idx (parent) ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` ADD INDEX {$CFG->prefix}forum_posts_discussion_idx (discussion) ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_posts` ADD INDEX {$CFG->prefix}forum_posts_userid_idx (userid) ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_discussions` ADD INDEX {$CFG->prefix}forum_discussions_forum_idx (forum) ");
      execute_sql(" ALTER TABLE `{$CFG->prefix}forum_discussions` ADD INDEX {$CFG->prefix}forum_discussions_userid_idx (userid) ");
  }

  if ($oldversion < 2004111700) {
      execute_sql("ALTER TABLE {$CFG->prefix}forum DROP INDEX course;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}forum_ratings DROP INDEX userid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}forum_ratings DROP INDEX post;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}forum_subscriptions DROP INDEX userid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}forum_subscriptions DROP INDEX forum;",false);

      modify_database('','ALTER TABLE prefix_forum ADD INDEX course (course);');
      modify_database('','ALTER TABLE prefix_forum_ratings ADD INDEX userid (userid);');
      modify_database('','ALTER TABLE prefix_forum_ratings ADD INDEX post (post);');
      modify_database('','ALTER TABLE prefix_forum_subscriptions ADD INDEX userid (userid);');
      modify_database('','ALTER TABLE prefix_forum_subscriptions ADD INDEX forum (forum);');
  }

  if ($oldversion < 2005011500) {
      modify_database('','CREATE TABLE prefix_forum_read (
                  `id` int(10) unsigned NOT NULL auto_increment, 
                  `userid` int(10) NOT NULL default \'0\',
                  `forumid` int(10) NOT NULL default \'0\',
                  `discussionid` int(10) NOT NULL default \'0\',
                  `postid` int(10) NOT NULL default \'0\',
                  `firstread` int(10) NOT NULL default \'0\',
                  `lastread` int(10) NOT NULL default \'0\',
                  PRIMARY KEY  (`id`),
                  KEY `prefix_forum_user_forum_idx` (`userid`,`forumid`),
                  KEY `prefix_forum_user_discussion_idx` (`userid`,`discussionid`),
                  KEY `prefix_forum_user_post_idx` (`userid`,`postid`)
                  ) COMMENT=\'Tracks each users read posts\';');

      set_config('upgrade', 'forumread');   // The upgrade of this table will be done later by admin/upgradeforumread.php
  }

  if ($oldversion < 2005032900) {
      modify_database('','ALTER TABLE prefix_forum_posts ADD INDEX prefix_form_posts_created_idx (created);');
      modify_database('','ALTER TABLE prefix_forum_posts ADD INDEX prefix_form_posts_mailed_idx (mailed);');
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
                  `id` int(10) unsigned NOT NULL auto_increment, 
                  `userid` int(10) NOT NULL default \'0\',
                  `forumid` int(10) NOT NULL default \'0\',
                  PRIMARY KEY  (`id`),
                  KEY `user_forum_idx` (`userid`,`forumid`)
                  ) COMMENT=\'Tracks each users untracked forums.\';');
  }

  if ($oldversion < 2005042500) {
      table_column('forum','','trackingtype','tinyint','2', 'unsigned', '1', '', 'forcesubscribe');
  }

  if ($oldversion < 2005111100) {
      table_column('forum_discussions','','timestart','integer');
      table_column('forum_discussions','','timeend','integer');
  }
  
  if ($oldversion < 2006011600) {
      execute_sql("alter table ".$CFG->prefix."forum change column type type enum('single','news','general','social','eachuser','teacher','qanda') not null default 'general'");
  }

  if ($oldversion < 2006011601) {
      table_column('forum','','warnafter');
      table_column('forum','','blockafter');
      table_column('forum','','blockperiod');
  }

  if ($oldversion < 2006011700) {
      table_column('forum_posts','','mailnow','integer');
  }
  
  if ($oldversion < 2006011702) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('forum', 'user report', 'user', 'CONCAT(firstname,\' \',lastname)')");
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
          include_once( "$CFG->dirroot/course/lib.php" );
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
