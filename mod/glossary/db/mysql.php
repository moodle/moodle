<?php   // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function glossary_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003091000) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary` ".
                    " ADD `allowduplicatedentries` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `studentcanpost` , ".
                    " ADD `displayformat` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `allowduplicatedentries` , ".
                    " ADD `mainglossary` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `displayformat` ");

        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary_entries` ".
                    " ADD timecreated INT(10) UNSIGNED NOT NULL default '0' AFTER `format` , ".
                    " ADD timemodified INT(10) UNSIGNED NOT NULL default '0' AFTER `timecreated` , ".
                    " ADD teacherentry TINYINT(2) UNSIGNED NOT NULL default '0' AFTER `timemodified` ");

        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'delete', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'delete entry', 'glossary', 'name') ");
    }
    
    if ( $oldversion < 2003091500 ) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary_entries` ".
                    " ADD attachment VARCHAR(100) NOT NULL default '' AFTER `format`");
    }

    if ( $oldversion < 2003091600 ) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary` ".
                    " ADD `showspecial` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `mainglossary` , ".
                    " ADD `showalphabet` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `showspecial` , ".
                    " ADD `showall` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `showalphabet` ");
    }
    
    if ( $oldversion < 2003091800 ) {

        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_categories` (
                    `id` INT(10) unsigned NOT NULL auto_increment,
                    `glossaryid` INT(10) UNSIGNED NOT NULL default '0',
                    `name` VARCHAR(255) NOT NULL default '',
                    PRIMARY KEY  (`id`)
                    ) TYPE=MyISAM COMMENT='all categories for glossary entries'");

        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_entries_categories` (
                    `categoryid` INT(10) UNSIGNED NOT NULL default '1',
                    `entryid` INT(10) UNSIGNED NOT NULL default '0',
                    PRIMARY KEY  (`categoryid`, `entryid`)
                    ) TYPE=MyISAM COMMENT='categories of each glossary entry'");
     }
     
     if ( $oldversion < 2003092100 ) {
          execute_sql("ALTER TABLE `{$CFG->prefix}glossary_entries_categories` CHANGE `categoryid` `categoryid` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ");
     }

    if ( $oldversion < 2003092102 ) {
        execute_sql("ALTER TABLE `{$CFG->prefix}glossary_entries_categories` DROP PRIMARY KEY ");
        execute_sql("ALTER TABLE `{$CFG->prefix}glossary_entries_categories` ADD `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
    }
    
    if ( $oldversion < 2003092400 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary_entries` " .
                    "ADD `sourceglossaryid` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `attachment` " );

    }

    if ( $oldversion < 2003101500 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary` " .
                    "ADD `intro`  text NOT NULL DEFAULT '' AFTER `name` " );

    }

    if ( $oldversion < 2003101501 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary` " .
                    "ADD `allowcomments`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `showall` " );

        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_comments` (
                    `id` INT(10) unsigned NOT NULL auto_increment,
                    `entryid` INT(10) UNSIGNED NOT NULL default '0',
                    `userid` INT(10) UNSIGNED NOT NULL default '0',
                    `comment` TEXT NOT NULL default '',
                    `timemodified` INT(10) UNSIGNED NOT NULL default '0',
                    `format` TINYINT(2) UNSIGNED NOT NULL default '0',
                    PRIMARY KEY  (`id`)
                    ) TYPE=MyISAM COMMENT='comments on glossary entries'");

        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'add comment', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'update comment', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'delete comment', 'glossary', 'name') ");
    }

    if ( $oldversion < 2003101600 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary` " .
                    "ADD `usedynalink`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `allowcomments` " );

        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary_entries` " .
                    "ADD `usedynalink`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `sourceglossaryid`, ".
                    "ADD `casesensitive`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `usedynalink` ");
    }

    if ( $oldversion < 2003101601 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary_entries` " .
                    "ADD `fullmatch`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `casesensitive` ");
    }

    if ( $oldversion < 2003101800 ) {
        execute_sql( "UPDATE `{$CFG->prefix}glossary`" .
                    " SET displayformat = 5 WHERE displayformat = 1");
    }
    if ( $oldversion < 2003102000 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary`" .
                     " ADD `defaultapproval` TINYINT(2) UNSIGNED NOT NULL default '1' AFTER `usedynalink`");

        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary_entries`" .
                    " ADD `approved` TINYINT(2) UNSIGNED NOT NULL default '1' AFTER `fullmatch`");

        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('glossary', 'approve entry', 'glossary', 'name') ");
    }

    if ( $oldversion < 2003102800 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary`" .
                     " ADD `globalglossary` TINYINT(2) UNSIGNED NOT NULL default '0' AFTER `defaultapproval`");
    }

    if ( $oldversion < 2003103100 ) {
        print_simple_box('This update might take several seconds.<br />The more glossaries, entries and categories you have created, the more it will take so please be patient.','center', '50%', '', '20', 'noticebox');
        if ( $glossaries = get_records("glossary")) {
            $gids = "";
            foreach ( $glossaries as $glossary ) {
                $gids .= "$glossary->id,";
            }
            $gids = substr($gids,0,-1);  // ID's of VALID glossaries

            if ($categories = get_records_select("glossary_categories","glossaryid NOT IN ($gids)") ) {
                $cids = "";
                foreach ( $categories as $cat ) {
                    $cids .= "$cat->id,";
                }
                $cids = substr($cids,0,-1);   // ID's of INVALID categories
                if ($cids) {
                    delete_records_select("glossary_entries_categories", "categoryid IN ($cids)");
                    delete_records_select("glossary_categories", "id in ($cids)");
                }
            }
            if ( $entries = get_records_select("glossary_entries") ) {
                $eids = "";
                foreach ( $entries as $entry ) {
                    $eids .= "$entry->id,";
                }
                $eids = substr($eids,0,-1);  // ID's of VALID entries
                if ($eids) {
                    delete_records_select("glossary_comments", "entryid NOT IN ($eids)");
                }
            }
        }
    }

    if ( $oldversion < 2003110400 ) {
        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_alias` (
                    `id` INT(10) unsigned NOT NULL auto_increment,
                    `entryid` INT(10) UNSIGNED NOT NULL default '0',
                    `alias` TEXT NOT NULL default '',
                    PRIMARY KEY  (`id`)
                    ) TYPE=MyISAM COMMENT='entries alias'");
    }
    
    if ( $oldversion < 2003111500 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary_categories`
                     ADD `usedynalink`  TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `name`" );
                     
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary`
                     ADD `entbypage`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '10' AFTER `globalglossary`" );
                     
    }

    if ( $oldversion < 2003111800 ) {
        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_displayformats` (
                    `id` INT(10) unsigned NOT NULL auto_increment,
                    `fid` INT(10) UNSIGNED NOT NULL default '0',
                    `visible` TINYINT(2) UNSIGNED NOT NULL default '1',
                    `relatedview` TINYINT(3) NOT NULL default '-1',
                    `showgroup` TINYINT(2) UNSIGNED NOT NULL default '1',
                    `defaultmode` VARCHAR(50) NOT NULL default '',
                    `defaulthook` VARCHAR(50) NOT NULL default '',
                    `sortkey` VARCHAR(50) NOT NULL default '',
                    `sortorder` VARCHAR(50) NOT NULL default '',
                    PRIMARY KEY  (`id`)
                    ) TYPE=MyISAM COMMENT='Setting of the display formats'");

        // Default format
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (0,0,'letter','ALL','CREATION','asc',1,1)");
        // Continuous format
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (1,1,'date','ALL','CREATION','asc',0,1)");
        // Full w/author View
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (2,2,'letter','ALL','CREATION','asc',1,1)");
        // Encyclopedia
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (3,3,'letter','ALL','CREATION','asc',1,1)");
        // FAQ View
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (4,4,'date','ALL','CREATION','asc',0,1)");
        // Full w/o author View
        execute_sql(" INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (5,5,'letter','ALL','CREATION','asc',1,1)");
        // Entry list
        execute_sql("INSERT INTO {$CFG->prefix}glossary_displayformats 
                      (fid, relatedview, defaultmode, defaulthook, sortkey, sortorder, showgroup, visible)
                      VALUES (6,0,'letter','ALL','CREATION','asc',1,1)");

    }

    if ($oldversion < 2003112100) {
        table_column("glossary", "", "assessed", "integer", "10", "unsigned", "0");
        table_column("glossary", "", "assesstimestart", "integer", "10", "unsigned", "0", "", "assessed");
        table_column("glossary", "", "assesstimefinish", "integer", "10", "unsigned", "0", "", "assesstimestart");
        
        execute_sql("CREATE TABLE {$CFG->prefix}glossary_ratings (
                      `id` int(10) unsigned NOT NULL auto_increment,
                      `userid` int(10) unsigned NOT NULL default '0',
                      `entryid` int(10) unsigned NOT NULL default '0',
                      `time` int(10) unsigned NOT NULL default '0',
                      `rating` tinyint(4) NOT NULL default '0',
                      PRIMARY KEY  (`id`)
                    ) COMMENT='Contains user ratings for entries'");
    }

    if ($oldversion < 2003112101) {
        table_column("glossary", "", "scale", "integer", "10", "", "0", "", "assesstimefinish");
    }
    
    if ($oldversion < 2003112701) {
        delete_records("glossary_alias","entryid",0);
    }

    if ($oldversion < 2004022200) {
        if (!empty($CFG->textfilters)) {
            $CFG->textfilters = str_replace("dynalink.php", "filter.php", $CFG->textfilters);
            set_config("textfilters", $CFG->textfilters);
        }
    }

  if ($oldversion < 2004050900) {
      table_column("glossary","","rsstype","tinyint","2", "unsigned", "0", "", "entbypage");
      table_column("glossary","","rssarticles","tinyint","2", "unsigned", "0", "", "rsstype");
      set_config("glossary_enablerssfeeds",0);
  }

  
  if ( $oldversion < 2004051400 ) {
        print_simple_box("This update might take several seconds.<p>The more glossaries, entries and aliases you have created, the more it will take so please be patient.","center", "50%", '', "20", "noticebox");
        if ( $entries = get_records("glossary_entries", '', '', '', 'id,concept')) {
            foreach($entries as $entry) {
                set_field("glossary_entries","concept",addslashes(trim($entry->concept)),"id",$entry->id);
            }
        }
        if ( $aliases = get_records("glossary_alias")) {
            foreach($aliases as $alias) {
                set_field("glossary_alias","alias",addslashes(trim($alias->alias)),"id",$alias->id);
            }
        }
  }

  if ( $oldversion < 2004072300) {
      table_column("glossary_alias", "alias", "alias", "VARCHAR", "255", "", "", "NOT NULL");
  }

  if ( $oldversion < 2004072400) {

      //Create new table glossary_formats to store format info
      execute_sql("CREATE TABLE `{$CFG->prefix}glossary_formats` (
                       `id` INT(10) unsigned NOT NULL auto_increment,
                       `name` VARCHAR(50) NOT NULL,
                       `popupformatname` VARCHAR(50) NOT NULL, 
                       `visible` TINYINT(2) UNSIGNED NOT NULL default '1',
                       `showgroup` TINYINT(2) UNSIGNED NOT NULL default '1',
                       `defaultmode` VARCHAR(50) NOT NULL default '',
                       `defaulthook` VARCHAR(50) NOT NULL default '',
                       `sortkey` VARCHAR(50) NOT NULL default '',
                       `sortorder` VARCHAR(50) NOT NULL default '',
                   PRIMARY KEY  (`id`)                    
                   ) TYPE=MyISAM COMMENT='Setting of the display formats'");

      //Define current 0-6 format names
      $formatnames = array('dictionary','continuous','fullwithauthor','encyclopedia',
                           'faq','fullwithoutauthor','entrylist');

      //Fill the new table from the old one (only 'valid', 0-6, formats)
      if ($formats = get_records('glossary_displayformats')) {
          foreach ($formats as $format) {
              //Format names
              if ($format->fid >= 0 && $format->fid <= 6) {
                  $format->name = $formatnames[$format->fid];
              }

              //Format popupformatname
              $format->popupformatname = 'dictionary';  //Default format
              if ($format->relatedview >= 0 && $format->relatedview <= 6) {
                  $format->popupformatname = $formatnames[$format->relatedview];
              }

              //Insert the new record
              //Only if $format->name is set (ie. formats 0-6)
              if ($format->name) {
                  insert_record('glossary_formats',$format);
              }
              
          }
      }

      //Drop the old formats table
      execute_sql("DROP TABLE `{$CFG->prefix}glossary_displayformats`");

      //Modify the glossary->displayformat field
      table_column('glossary', 'displayformat', 'displayformat', 'VARCHAR', '50', '', 'dictionary', 'NOT NULL');

      //Update glossary->displayformat field
      if ($glossaries = get_records('glossary')) {
          foreach($glossaries as $glossary) {
              $displayformat = 'dictionary';  //Default format
              if ($glossary->displayformat >= 0 && $glossary->displayformat <= 6) {
                  $displayformat = $formatnames[$glossary->displayformat];
              }
              set_field('glossary','displayformat',$displayformat,'id',$glossary->id);
          }
      }
  }

  if ( $oldversion < 2004080800) {
      table_column("glossary","","editalways","tinyint","2", "unsigned", "0", "", "entbypage");
  }

  //Activate editalways in old secondary glossaries (old behaviour)
  if ( $oldversion < 2004080900) {
      set_field('glossary','editalways','1','mainglossary','0');
  }

  if ($oldversion < 2004111200) {
      execute_sql("ALTER TABLE {$CFG->prefix}glossary DROP INDEX course;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_alias DROP INDEX entryid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_categories DROP INDEX glossaryid;",false); 
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_comments DROP INDEX entryid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_comments DROP INDEX userid;",false); 
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_entries DROP INDEX glossaryid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_entries DROP INDEX userid;",false); 
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_entries DROP INDEX concept;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_entries_categories DROP INDEX entryid;",false); 
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_entries_categories DROP INDEX categoryid;",false);
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_ratings DROP INDEX userid;",false); 
      execute_sql("ALTER TABLE {$CFG->prefix}glossary_ratings DROP INDEX entryid;",false);

      modify_database('','ALTER TABLE prefix_glossary ADD INDEX course (course);');
      modify_database('','ALTER TABLE prefix_glossary_alias ADD INDEX entryid (entryid);');
      modify_database('','ALTER TABLE prefix_glossary_categories ADD INDEX glossaryid (glossaryid);');
      modify_database('','ALTER TABLE prefix_glossary_comments ADD INDEX entryid (entryid);');
      modify_database('','ALTER TABLE prefix_glossary_comments ADD INDEX userid (userid);');
      modify_database('','ALTER TABLE prefix_glossary_entries ADD INDEX glossaryid (glossaryid);');
      modify_database('','ALTER TABLE prefix_glossary_entries ADD INDEX userid (userid);');
      modify_database('','ALTER TABLE prefix_glossary_entries ADD INDEX concept (concept);');
      modify_database('','ALTER TABLE prefix_glossary_entries_categories ADD INDEX entryid (entryid);');
      modify_database('','ALTER TABLE prefix_glossary_entries_categories ADD INDEX categoryid (categoryid);');
      modify_database('','ALTER TABLE prefix_glossary_ratings ADD INDEX userid (userid);');
      modify_database('','ALTER TABLE prefix_glossary_ratings ADD INDEX entryid (entryid);');

  }

  //Delete orphaned categories (bug 2140)
  if ($oldversion < 2005011100) {
      $categories = get_records('glossary_categories', '', '', '', 'id, glossaryid');
      if ($categories) {
          foreach ($categories as $category) {
              $glossary = get_record('glossary', 'id', "$category->glossaryid");
              if (!$glossary) {
                  delete_records('glossary_categories', 'id', "$category->id");
              }
          }
      }
  }

  //Allowprintview flag
  if ($oldversion < 2005011200) {
      table_column('glossary','','allowprintview','tinyint','2', 'unsigned', '1', '', 'allowcomments');
      $glossaries = get_records('glossary', '', '', '', 'id, name');
      if ($glossaries) {
          foreach ($glossaries as $glossary) { 
              set_field('glossary', 'allowprintview', '1', 'id', "$glossary->id");
          }
      }
  }

  if ($oldversion < 2005031001) {
      modify_database('',"INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('glossary', 'view entry', 'glossary_entries', 'concept');");
  }
    
    if ($oldversion < 2005041100) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        // update glossary_entries->definition
        $sql = "select course from {$CFG->prefix}glossary,{$CFG->prefix}glossary_entries ";
        $sql .= "where {$CFG->prefix}glossary.id = {$CFG->prefix}glossary_entries.glossaryid ";
        $sql .= "and {$CFG->prefix}glossary_entries.id = ";
        $wtm->update( 'glossary_entries','definition','format' );
        // update glossary_comments->text
        $sql = "select course from {$CFG->prefix}glossary,{$CFG->prefix}glossary_entries,{$CFG->prefix}glossary_comments ";
        $sql .= "where {$CFG->prefix}glossary.id = {$CFG->prefix}glossary_entries.glossaryid ";
        $sql .= "and {$CFG->prefix}glossary_entries.id = {$CFG->prefix}glossary_comments.entryid ";
        $sql .= "and {$CFG->prefix}glossary_comments.id = ";
        $wtm->update( 'glossary_comments','text','format',$sql );
    }

    if ($oldversion < 2006082600) {
        $sql1 = "UPDATE {$CFG->prefix}glossary_entries SET definition = REPLACE(definition, '".TRUSTTEXT."', '');";
        $sql2 = "UPDATE {$CFG->prefix}glossary_comments SET comment = REPLACE(comment, '".TRUSTTEXT."', '');";
        $likecond = sql_ilike()." '%".TRUSTTEXT."%'";
        while (true) {
            if (!count_records_select('glossary_entries', "definition $likecond")) {
                break;
            }
            execute_sql($sql1);
        }
        while (true) {
            if (!count_records_select('glossary_comments', "comment $likecond")) {
                break;
            }
            execute_sql($sql2);
        }
    }

    if ($oldversion < 2006090400) {
         table_column('glossary_comments', 'comment', 'entrycomment', 'text', '', '', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
