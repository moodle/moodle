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
      execute_sql("CREATE TABLE {$CFG->prefix}glossary_formats (
                       id SERIAL8 PRIMARY KEY,
                       name VARCHAR(50) NOT NULL,
                       popupformatname VARCHAR(50) NOT NULL, 
                       visible int2  NOT NULL default '1',
                       showgroup int2  NOT NULL default '1',
                       defaultmode VARCHAR(50) NOT NULL default '',
                       defaulthook VARCHAR(50) NOT NULL default '',
                       sortkey VARCHAR(50) NOT NULL default '',
                       sortorder VARCHAR(50) NOT NULL default ''
                   ) ");

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
      execute_sql("DROP TABLE {$CFG->prefix}glossary_displayformats");

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
      table_column("glossary","","editalways","integer","2", "unsigned", "0", "", "entbypage");
  }

  //Activate editalways in old secondary glossaries (old behaviour)
  if ( $oldversion < 2004080900) {
      set_field('glossary','editalways','1','mainglossary','0');
  }

  return true;
}

?>
