<?PHP

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

    if ($oldversion < 2004083124) {
        modify_database('','CREATE INDEX prefix_wiki_course_idx ON prefix_wiki (course);');
        modify_database('','CREATE INDEX prefix_wiki_entries_wikiid_idx ON prefix_wiki_entries (wikiid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_userid_idx ON prefix_wiki_entries (userid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_groupid_idx ON prefix_wiki_entries (groupid);');
        modify_database('','CREATE INDEX prefix_wiki_entries_course_idx ON prefix_wiki_entries (course);');
        modify_database('','CREATE INDEX prefix_wiki_entries_pagename_idx ON prefix_wiki_entries (pagename);');
    }
    
    return true;
}

?>
