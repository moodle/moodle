<?PHP

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function wiki_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG, $db;

    if ($oldversion < 2004040200) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` DROP `allowstudentstowiki`');
    }

    if ($oldversion < 2004040700) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` CHANGE `ewikiallowsafehtml` `htmlmode` TINYINT( 4 ) DEFAULT \'0\' NOT NULL');
    }

    if ($oldversion < 2004042100) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` ADD `pagename` VARCHAR( 255 ) AFTER `summary`');
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki_entries` CHANGE `name` `pagename` VARCHAR( 255 ) NOT NULL');
        if ($wikis = get_records('wiki')) {
            foreach ($wikis as $wiki) {
                if (empty($wiki->pagename)) {
                    set_field('wiki', 'pagename', $wiki->name, 'id', $wiki->id);
                }
            }
        }
    }

    if ($oldversion < 2004053100) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` CHANGE `initialcontent` `initialcontent` VARCHAR( 255 ) NOT NULL DEFAULT \'\'');
//      Remove obsolete 'initialcontent' values.
        if ($wikis = get_records('wiki')) {
            foreach ($wikis as $wiki) {
                if (!empty($wiki->initialcontent)) {
                    set_field('wiki', 'initialcontent', null, 'id', $wiki->id);
                }
            }
        }
    }

    if ($oldversion < 2004061300) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki`'
                    .' ADD `setpageflags` TINYINT DEFAULT \'1\' NOT NULL AFTER `ewikiacceptbinary`,'
                    .' ADD `strippages` TINYINT DEFAULT \'1\' NOT NULL AFTER `setpageflags`,'
                    .' ADD `removepages` TINYINT DEFAULT \'1\' NOT NULL AFTER `strippages`,'
                    .' ADD `revertchanges` TINYINT DEFAULT \'1\' NOT NULL AFTER `removepages`');
    }

    if ($oldversion < 2004062400) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki`'
                    .' ADD `disablecamelcase` TINYINT DEFAULT \'0\' NOT NULL AFTER `ewikiacceptbinary`');
    }

    if ($oldversion < 2004082200) {
        table_column('wiki_pages', '', 'userid', "integer", "10", "unsigned", "0", "not null", "author");
    }

    if ($oldversion < 2004082303) {  // Try to update userid for old records
        if ($pages = get_records('wiki_pages', 'userid', 0, 'pagename', 'lastmodified,author,pagename,version')) {
            foreach ($pages as $page) {
                $name = explode('(', $page->author);
                $name = trim($name[0]);
                $name = explode(' ', $name);
                $firstname = $name[0];
                unset($name[0]);
                $lastname = trim(implode(' ', $name));
                if ($user = get_record('user', 'firstname', $firstname, 'lastname', $lastname)) {
                    set_field('wiki_pages', 'userid', $user->id,                                                                                      'pagename', addslashes($page->pagename), 'version', $page->version);
                }
            }
        }
    }

    if ($oldversion < 2004111200) {
        execute_sql("ALTER TABLE {$CFG->prefix}wiki DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_entries DROP INDEX course;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_entries DROP INDEX userid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_entries DROP INDEX groupid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_entries DROP INDEX wikiid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_entries DROP INDEX pagename;",false);

        modify_database('','ALTER TABLE prefix_wiki ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_wiki_entries ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_wiki_entries ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_wiki_entries ADD INDEX groupid (groupid);');
        modify_database('','ALTER TABLE prefix_wiki_entries ADD INDEX wikiid (wikiid);');
        modify_database('','ALTER TABLE prefix_wiki_entries ADD INDEX pagename (pagename);');
    }

    if ($oldversion < 2005022000) {
        // recreating the wiki_pages table completelly (missing id, bug 2608)
        if ($rows = count_records("wiki_pages")) {
            // we need to use the temp stuff
            modify_database("","CREATE TABLE `prefix_wiki_pages_tmp` (
                `pagename` VARCHAR(160) NOT NULL,
                `version` INT(10) UNSIGNED NOT NULL DEFAULT 0,
                `flags` INT(10) UNSIGNED DEFAULT 0,
                `content` MEDIUMTEXT,
                `author` VARCHAR(100) DEFAULT 'ewiki',
                `userid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
                `created` INT(10) UNSIGNED DEFAULT 0,
                `lastmodified` INT(10) UNSIGNED DEFAULT 0,
                `refs` MEDIUMTEXT,
                `meta` MEDIUMTEXT,
                `hits` INT(10) UNSIGNED DEFAULT 0,
                `wiki` INT(10) UNSIGNED NOT NULL);");
            
            execute_sql("INSERT INTO {$CFG->prefix}wiki_pages_tmp (pagename, version, flags, content,
                                                                   author, userid, created, lastmodified,
                                                                   refs, meta, hits, wiki) 
                         SELECT pagename, version, flags, content,
                                author, userid, created, lastmodified,
                                refs, meta, hits, wiki
                         FROM {$CFG->prefix}wiki_pages");

            $insertafter = true;
        }

        execute_sql("DROP TABLE {$CFG->prefix}wiki_pages");

        modify_database("","CREATE TABLE `prefix_wiki_pages` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `pagename` VARCHAR(160) NOT NULL,
            `version` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `flags` INT(10) UNSIGNED DEFAULT 0,
            `content` MEDIUMTEXT,
            `author` VARCHAR(100) DEFAULT 'ewiki',
            `userid` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `created` INT(10) UNSIGNED DEFAULT 0,
            `lastmodified` INT(10) UNSIGNED DEFAULT 0,
            `refs` MEDIUMTEXT,
            `meta` MEDIUMTEXT,
            `hits` INT(10) UNSIGNED DEFAULT 0,
            `wiki` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `wiki_pages_uk` (`pagename`,`version`,`wiki`))
            TYPE=MyISAM COMMENT='Holds the Wiki-Pages';");
        
        if (!empty($insertafter)) {
            execute_sql("INSERT INTO {$CFG->prefix}wiki_pages (pagename, version, flags, content,
                                                               author, userid, created, lastmodified,
                                                               refs, meta, hits, wiki) 
                         SELECT pagename, version, flags, content,
                                author, userid, created, lastmodified,
                                refs, meta, hits, wiki
                         FROM {$CFG->prefix}wiki_pages_tmp");

            execute_sql("DROP TABLE {$CFG->prefix}wiki_pages_tmp");
        }
    }

    if ($oldversion < 2006042800) {

        execute_sql("UPDATE {$CFG->prefix}wiki SET summary='' WHERE summary IS NULL");
        table_column('wiki','summary','summary','text','','','','not null');

        execute_sql("UPDATE {$CFG->prefix}wiki SET pagename='' WHERE pagename IS NULL");
        table_column('wiki','pagename','pagename','varchar','255','','','not null');
        
        execute_sql("UPDATE {$CFG->prefix}wiki SET initialcontent='' WHERE initialcontent IS NULL");
        table_column('wiki','initialcontent','initialcontent','varchar','255','','','not null');
    }
    if ($oldversion < 2006092502) {
        modify_database("","
CREATE TABLE prefix_wiki_locks
(
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  wikiid INT(10) UNSIGNED NOT NULL,
  pagename VARCHAR(160) NOT NULL DEFAULT '',
  lockedby INT(10) NOT NULL DEFAULT 0,
  lockedsince INT(10) NOT NULL DEFAULT 0,
  lockedseen INT(10) NOT NULL DEFAULT 0,
  PRIMARY KEY(id),
  UNIQUE INDEX wiki_locks_uk(wikiid,pagename),
  INDEX wiki_locks_ix(lockedseen)  
);"); 
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
