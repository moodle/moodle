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
    return true;
}

?>
