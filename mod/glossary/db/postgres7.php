<?php  // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

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

  if ($oldversion < 2004111200) {
      execute_sql("DROP INDEX {$CFG->prefix}glossary_course_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_alias_entryid_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_categories_glossaryid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}glossary_comments_entryid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}glossary_comments_userid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}glossary_entries_glossaryid_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_entries_userid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}glossary_entries_concept_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_entries_categories_category_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_entries_categories_entryid_idx;",false);
      execute_sql("DROP INDEX {$CFG->prefix}glossary_ratings_userid_idx;",false); 
      execute_sql("DROP INDEX {$CFG->prefix}glossary_ratings_entryid_idx;",false);

      modify_database('','CREATE INDEX prefix_glossary_course_idx ON prefix_glossary (course);');
      modify_database('','CREATE INDEX prefix_glossary_alias_entryid_idx ON prefix_glossary_alias (entryid);');
      modify_database('','CREATE INDEX prefix_glossary_categories_glossaryid_idx ON prefix_glossary_categories (glossaryid);');
      modify_database('','CREATE INDEX prefix_glossary_comments_entryid_idx ON prefix_glossary_comments (entryid);');
      modify_database('','CREATE INDEX prefix_glossary_comments_userid_idx ON prefix_glossary_comments (userid);');
      modify_database('','CREATE INDEX prefix_glossary_entries_glossaryid_idx ON prefix_glossary_entries (glossaryid);');
      modify_database('','CREATE INDEX prefix_glossary_entries_userid_idx ON prefix_glossary_entries (userid);');
      modify_database('','CREATE INDEX prefix_glossary_entries_concept_idx ON prefix_glossary_entries (concept);');
      modify_database('','CREATE INDEX prefix_glossary_entries_categories_category_idx ON prefix_glossary_entries_categories (categoryid);');
      modify_database('','CREATE INDEX prefix_glossary_entries_categories_entryid_idx ON prefix_glossary_entries_categories (entryid);');
      modify_database('','CREATE INDEX prefix_glossary_ratings_userid_idx ON prefix_glossary_ratings (userid);');
      modify_database('','CREATE INDEX prefix_glossary_ratings_entryid_idx ON prefix_glossary_ratings (entryid);');
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
      table_column('glossary','','allowprintview','integer','2', 'unsigned', '1', '', 'allowcomments');
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

    if ($oldversion < 2005041901) { // Mass cleanup of bad postgres upgrade scripts
        table_column('glossary','allowprintview','allowprintview','smallint','4','unsigned','1');
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
