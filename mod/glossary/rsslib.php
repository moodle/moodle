<?php
    //This file adds support to rss feeds generation

    //This function is the main entry point to glossary
    //rss feeds generation.
    function glossary_rss_get_feed($context, $cm, $instance, $args) {
        global $CFG, $DB;

        if (empty($CFG->glossary_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
            return null;
        }

        $status = true;

        //check capabilities
        //glossary module doesn't require any capabilities to view glossary entries (aside from being logged in)

        $glossary = $DB->get_record('glossary', array('id' => $instance), '*', MUST_EXIST);

        if (!rss_enabled('glossary', $glossary)) {
            return null;
        }

        $sql = glossary_rss_get_sql($glossary, $cm);

        //get the cache file info
        $filename = rss_get_file_name($glossary, $sql);
        $cachedfilepath = rss_get_file_full_name('glossary', $filename);

        //Is the cache out of date?
        $cachedfilelastmodified = 0;
        if (file_exists($cachedfilepath)) {
            $cachedfilelastmodified = filemtime($cachedfilepath);
        }

        if (glossary_rss_newstuff($glossary, $cm, $cachedfilelastmodified)) {
            if (!$recs = $DB->get_records_sql($sql, array(), 0, $glossary->rssarticles)) {
                return null;
            }

            $items = array();
            
            $formatoptions = new object;
            $formatoptions->trusttext = true;

            foreach ($recs as $rec) {
                $item = new object();
                $user = new object();
                $item->title = $rec->entryconcept;

                if ($glossary->rsstype == 1) {//With author
                    $user->firstname = $rec->userfirstname;
                    $user->lastname = $rec->userlastname;
                    
                    $item->author = fullname($user);
                }

                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;
                $item->description = format_text($rec->entrydefinition,$rec->entryformat,$formatoptions,$glossary->course);
                $items[] = $item;
            }

            //First all rss feeds common headers
            $header = rss_standard_header(format_string($glossary->name,true),
                                          $CFG->wwwroot."/mod/glossary/view.php?g=".$glossary->id,
                                          format_string($glossary->intro,true));
            //Now all the rss items
            if (!empty($header)) {
                $articles = rss_add_items($items);
            }
            //Now all rss feeds common footers
            if (!empty($header) && !empty($articles)) {
                $footer = rss_standard_footer();
            }
            //Now, if everything is ok, concatenate it
            if (!empty($header) && !empty($articles) && !empty($footer)) {
                $rss = $header.$articles.$footer;

                //Save the XML contents to file.
                $status = rss_save_file('glossary', $filename, $rss);
            }
        }

        if (!$status) {
            $cachedfilepath = null;
        }

        return $cachedfilepath;
    }

    function glossary_rss_get_sql($glossary, $cm, $time=0) {
        //do we only want new items?
        if ($time) {
            $time = "AND e.timecreated > $time";
        } else {
            $time = "";
        }

        if ($glossary->rsstype == 1) {//With author
            $sql = "SELECT e.id AS entryid,
                      e.concept AS entryconcept,
                      e.definition AS entrydefinition,
                      e.definitionformat AS entryformat,
                      e.definitiontrust AS entrytrust,
                      e.timecreated AS entrytimecreated,
                      u.id AS userid,
                      u.firstname AS userfirstname,
                      u.lastname AS userlastname
                 FROM {glossary_entries} e,
                      {user} u
                WHERE e.glossaryid = {$glossary->id} AND
                      u.id = e.userid AND
                      e.approved = 1 $time
             ORDER BY e.timecreated desc";
        } else {//Without author
            $sql = "SELECT e.id AS entryid,
                      e.concept AS entryconcept,
                      e.definition AS entrydefinition,
                      e.definitionformat AS entryformat,
                      e.definitiontrust AS entrytrust,
                      e.timecreated AS entrytimecreated,
                      u.id AS userid
                 FROM {glossary_entries} e,
                      {user} u
                WHERE e.glossaryid = {$glossary->id} AND
                      u.id = e.userid AND
                      e.approved = 1 $time
             ORDER BY e.timecreated desc";
        }

        return $sql;
    }

    /**
     * If there is new stuff in since $time this returns true
     * Otherwise it returns false.
     *
     * @param object $glossary the glossary activity object
     * @param object $cm
     * @param int $time timestamp
     * @return bool
     */
    function glossary_rss_newstuff($glossary, $cm, $time) {
        global $DB;

        $sql = glossary_rss_get_sql($glossary, $cm, $time);

        $recs = $DB->get_records_sql($sql, null, 0, 1);//limit of 1. If we get even 1 back we have new stuff
        return ($recs && !empty($recs));
    }


