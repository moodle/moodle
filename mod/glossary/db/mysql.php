<?PHP   // $Id$



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

        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'delete', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'delete entry', 'glossary', 'name') ");
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

        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'add comment', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'update comment', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'delete comment', 'glossary', 'name') ");
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

        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'approve entry', 'glossary', 'name') ");
    }

    if ( $oldversion < 2003102800 ) {
        execute_sql( "ALTER TABLE `{$CFG->prefix}glossary`" .
                     " ADD `globalglossary` TINYINT(2) UNSIGNED NOT NULL default '0' AFTER `defaultapproval`");
    }

    if ( $oldversion < 2003103100 ) {
        print_simple_box("This update might take several seconds.<p>The more glossaries, entries and categories you have created, the more it will take so please be patient.","center", "50%", "$THEME->cellheading", "20", "noticebox");
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
        print_simple_box("This update might take several seconds.<p>The more glossaries, entries and aliases you have created, the more it will take so please be patient.","center", "50%", "$THEME->cellheading", "20", "noticebox");
        if ( $entries = get_records("glossary_entries")) {
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
    
  return true;
}

?>
