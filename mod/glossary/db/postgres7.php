<?php

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

    return true;
}

?>
