<?PHP

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function wiki_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004073000) {

       modify_database("", "ALTER TABLE prefix_wiki_pages DROP COLUMN id;"); 
       modify_database("", "ALTER TABLE ONLY prefix_wiki_pages 
                            ADD CONSTRAINT id PRIMARY KEY (pagename, \"version\");"); 
    }    
    if ($oldversion < 2004073001) {

       modify_database("", "ALTER TABLE prefix_wiki_pages DROP CONSTRAINT id;"); 
       modify_database("", "ALTER TABLE ONLY prefix_wiki_pages 
                            ADD CONSTRAINT id PRIMARY KEY (pagename, \"version\", wiki);"); 
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
        execute_sql("DROP INDEX {$CFG->prefix}wiki_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}wiki_entries_wikiid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}wiki_entries_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}wiki_entries_groupid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}wiki_entries_course_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}wiki_entries_pagename_idx;",false);

        modify_database('','CREATE INDEX prefix_wiki_course_idx ON prefix_wiki (course);');
        modify_database('','CREATE INDEX prefix_wiki_entries_wikiid_idx ON prefix_wiki_entries (wikiid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_userid_idx ON prefix_wiki_entries (userid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_groupid_idx ON prefix_wiki_entries (groupid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_course_idx ON prefix_wiki_entries (course);');
        modify_database('','CREATE INDEX prefix_wiki_entries_pagename_idx ON prefix_wiki_entries (pagename);');
    }


    if ($oldversion < 2004112400) {
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP CONSTRAINT id;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP CONSTRAINT {$CFG->prefix}wiki_pages_id;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP CONSTRAINT {$CFG->prefix}wiki_pages_pagename_version_wiki_unique;",false);
        modify_database("", "ALTER TABLE ONLY prefix_wiki_pages 
                            ADD CONSTRAINT prefix_wiki_pages_pagename_version_wiki_unique PRIMARY KEY (pagename, \"version\", wiki);"); 
    }

    if ($oldversion < 2005022000) {
        // recreating the wiki_pages table completelly (missing id, bug 2608)
        if ($rows = count_records("wiki_pages")) {
            // we need to use the temp stuff
            modify_database("","CREATE TABLE prefix_wiki_pages_tmp (
                id SERIAL8 PRIMARY KEY, 
                pagename VARCHAR(160) NOT NULL,
                version INTEGER  NOT NULL DEFAULT 0,
                flags INTEGER  DEFAULT 0,
                content TEXT,
                author VARCHAR(100) DEFAULT 'ewiki',
                userid INTEGER  NOT NULL DEFAULT 0,
                created INTEGER  DEFAULT 0,
                lastmodified INTEGER  DEFAULT 0,
                refs TEXT,
                meta TEXT,
                hits INTEGER  DEFAULT 0,
                wiki INT8  NOT NULL);");
            
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

        modify_database("","CREATE TABLE prefix_wiki_pages (
            id SERIAL8 PRIMARY KEY, 
            pagename VARCHAR(160) NOT NULL,
            version INTEGER  NOT NULL DEFAULT 0,
            flags INTEGER  DEFAULT 0,
            content TEXT,
            author VARCHAR(100) DEFAULT 'ewiki',
            userid INTEGER  NOT NULL DEFAULT 0,
            created INTEGER  DEFAULT 0,
            lastmodified INTEGER  DEFAULT 0,
            refs TEXT,
            meta TEXT,
            hits INTEGER  DEFAULT 0,
            wiki INT8  NOT NULL);");

        modify_database("","CREATE UNIQUE INDEX prefix_wiki_pages_pagename_version_wiki_uk 
                            ON prefix_wiki_pages (pagename, version, wiki);");
        
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
    
    if ($oldversion < 2006032900) {
        global $db;
        table_column("wiki_pages",'','content_base64','text');
        table_column("wiki_pages",'','refs_base64','text');
        $olddebug = $db->debug;
        $db->debug = false;
        $data = $db->GetAll("SELECT id,content,refs FROM {$CFG->prefix}wiki_pages");
        foreach ($data as $d) {
            $db->AutoExecute("{$CFG->prefix}wiki_pages", array('refs_base64' => base64_encode($d['refs']), 'content_base64' => base64_encode($d['content'])), 'UPDATE', 'id = '.$d['id']);
        }
        $db->debug = $olddebug;
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN content");
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN refs");
        table_column("wiki_pages",'','content','bytea');
        table_column("wiki_pages",'','refs','bytea');
        execute_sql("UPDATE {$CFG->prefix}wiki_pages SET refs = decode(refs_base64, 'base64'), content = decode(content_base64, 'base64')");
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN content_base64");
        execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN refs_base64");
    }

    if ($oldversion < 2006042801) {
        modify_database('', 'ALTER TABLE prefix_wiki_pages 
            ALTER COLUMN content SET DEFAULT \'\'');
        modify_database('', 'ALTER TABLE prefix_wiki_pages 
            ALTER COLUMN refs SET DEFAULT \'\'');
        modify_database('', 'ALTER TABLE prefix_wiki_pages 
            ALTER COLUMN content DROP NOT NULL');
        modify_database('', 'ALTER TABLE prefix_wiki_pages 
            ALTER COLUMN refs DROP NOT NULL');
    }
    
    if ($oldversion < 2006092502) {
        modify_database("","
CREATE TABLE prefix_wiki_locks
(
  id SERIAL PRIMARY KEY,
  wikiid INTEGER NOT NULL,
  pagename VARCHAR(160) NOT NULL DEFAULT '',
  lockedby INTEGER NOT NULL DEFAULT 0,
  lockedsince INTEGER NOT NULL DEFAULT 0,
  lockedseen INTEGER NOT NULL DEFAULT 0
);"); 
        modify_database("","CREATE INDEX prefix_wikilock_loc_ix ON prefix_wiki_locks (lockedseen);"); 
        modify_database("","CREATE UNIQUE INDEX prefix_wikilock_wikpag_uix ON prefix_wiki_locks (wikiid, pagename);");  
    }
    
    if($oldversion < 2006092602) {
/*
    // This used to be a BYTEA type for no apparent reason, which caused various queries to fail. The new
    // install.xml uses TEXT so I figure it's safe to change it in upgrade too. This one broke the links page...
    modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN refs DROP DEFAULT;");
modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN refs TYPE TEXT USING ENCODE(refs,'escape');");
modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN refs SET DEFAULT '';");
// ...and this one broke the search page.
modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN content DROP DEFAULT;");
modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN content TYPE TEXT USING ENCODE(content,'escape');");
modify_database('',"ALTER TABLE prefix_wiki_pages ALTER COLUMN content SET DEFAULT '';");
*/
            // following code should be compatible with both pg 8.x and 7.4
            table_column('wiki_pages', '', 'tempcontent', 'TEXT', '', '', '', '');
            execute_sql("UPDATE {$CFG->prefix}wiki_pages SET tempcontent = ENCODE(content,'escape')");
            execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN content");
            execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages RENAME COLUMN tempcontent TO content");
            
            table_column('wiki_pages', '', 'temprefs', 'TEXT', '', '', '', '');
            execute_sql("UPDATE {$CFG->prefix}wiki_pages SET temprefs = ENCODE(refs,'escape')");
            execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages DROP COLUMN refs");
            execute_sql("ALTER TABLE {$CFG->prefix}wiki_pages RENAME COLUMN temprefs TO refs");

    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}

?>
