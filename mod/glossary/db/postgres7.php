<?php  // $Id$

function glossary_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004022200) {
        if (!empty($CFG->textfilters)) {
            $CFG->textfilters = str_replace("dynalink.php", "filter.php", $CFG->textfilters);
            set_config("textfilters", $CFG->textfilters);
        }
    }

  if ($oldversion < 2004050900) {
      table_column("glossary","","rsstype","integer","2", "unsigned", "0", "", "entbypage");
      table_column("glossary","","rssarticles","integer","2", "unsigned", "0", "", "rsstype");
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
