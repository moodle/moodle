<?PHP  // $Id$
    //This file adds support to rss feeds generation

    //This function is the main entry point to glossary
    //rss feeds generation. Foreach site glossary with rss enabled
    //build one XML rss structure.
    function glossary_rss_feeds() {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        //Check CFG->glossary_enablerssfeeds
        } else if (empty($CFG->glossary_enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (module configuration)";
            }
        //It's working so we start...
        } else {
            //Iterate over all glossaries
            if ($glossaries = get_records("glossary")) {
                foreach ($glossaries as $glossary) {
                    if (!empty($glossary->rsstype) && !empty($glossary->rssarticles) && $status) {
                        //Some debug...
                        if ($CFG->debug > 7) {
                            echo "ID: $glossary->id->";
                        }
                        //Get the XML contents
                        $result = glossary_rss_feed($glossary);
                        //Save the XML contents to file
                        if (!empty($result)) {
                            $status = rss_save_file("glossary",$glossary,$result);
                        }
                        //Some debug...
                        if ($CFG->debug > 7) {
                            if (empty($result)) {
                                echo "(empty) ";
                            } else {
                                if (!empty($status)) {
                                    echo "OK ";
                                } else {
                                    echo "FAIL ";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }

    //This function return the XML rss contents about the glossary record passed as parameter
    //It returns false if something is wrong
    function glossary_rss_feed($glossary) {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)"; 
            }           
        //Check CFG->glossary_enablerssfeeds
        } else if (empty($CFG->glossary_enablerssfeeds)) {
            //Some debug... 
            if ($CFG->debug > 7) {
                echo "DISABLED (module configuration)";
            }           
        //It's working so we start...
        } else {
            //Check the glossary has rss activated
            if (!empty($glossary->rsstype) && !empty($glossary->rssarticles)) {
                //Depending of the glossary->rsstype, we are going to execute, different sqls
                if ($glossary->rsstype == 1) {    //With author RSS
                    $items = glossary_rss_feed_withauthor($glossary);
                } else {                //Without author RSS
                    $items = glossary_rss_feed_withoutauthor($glossary);
     
                }
                //Now, if items, we begin building the structure
                if (!empty($items)) {
                    //First all rss feeds common headers
                    $header = rss_standard_header($glossary->name,
                                                  $CFG->wwwroot."/mod/glossary/view.php?f=".$glossary->id,
                                                  $glossary->intro);
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
                        $status = $header.$articles.$footer;
                    } else {
                        $status = false;
                    } 
                } else {
                    $status = false;
                }
            }
        }
        return $status;
    }

    //This function returns "items" record array to be used to build the rss feed
    //for a Type=with author glossary
    function glossary_rss_feed_withauthor($glossary) {

        global $CFG;

        $items = array();

        if ($recs = get_records_sql ("SELECT e.id entryid, 
                                             e.concept entryconcept, 
                                             e.definition entrydefinition, 
                                             e.format entryformat, 
                                             e.timecreated entrytimecreated, 
                                             u.id userid, 
                                             u.firstname userfirstname,
                                             u.lastname userlastname
                                      FROM {$CFG->prefix}glossary_entries e,
                                           {$CFG->prefix}user u
                                      WHERE e.glossaryid = '$glossary->id' AND
                                            u.id = e.userid AND
                                            e.approved = 1
                                      ORDER BY e.timecreated desc")) {
            //Iterate over each entry to get glossary->rssarticles records
            $articlesleft = $glossary->rssarticles;
            $item = NULL;
            $user = NULL;
            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->entryconcept;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;
                $item->description = format_text($rec->entrydefinition,$rec->entryformat,NULL,$glossary->course);
                $items[] = $item;
                $articlesleft--;
                if ($articlesleft < 1) {
                    break;
                }
            }
        }
        return $items;
    }

    //This function returns "items" record array to be used to build the rss feed
    //for a Type=without author glossary
    function glossary_rss_feed_withoutauthor($glossary) {

        global $CFG;

        $items = array();

        if ($recs = get_records_sql ("SELECT e.id entryid,
                                             e.concept entryconcept,
                                             e.definition entrydefinition,
                                             e.format entryformat,
                                             e.timecreated entrytimecreated,
                                             u.id userid,
                                             u.firstname userfirstname,
                                             u.lastname userlastname
                                      FROM {$CFG->prefix}glossary_entries e,
                                           {$CFG->prefix}user u
                                      WHERE e.glossaryid = '$glossary->id' AND
                                            u.id = e.userid AND
                                            e.approved = 1
                                      ORDER BY e.timecreated desc")) {
            //Iterate over each entry to get glossary->rssarticles records
            $articlesleft = $glossary->rssarticles;
            $item = NULL;
            $user = NULL;
            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->entryconcept;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                //$item->author = fullname($user);
                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;
                $item->description = format_text($rec->entrydefinition,$rec->entryformat,NULL,$glossary->course);
                $items[] = $item;
                $articlesleft--;
                if ($articlesleft < 1) {
                    break;
                }
            }
        }
        return $items;
    }
    
?>
