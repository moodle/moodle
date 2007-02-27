<?php  // $Id$
    //This file adds support to rss feeds generation

    //This function is the main entry point to glossary
    //rss feeds generation. Foreach site glossary with rss enabled
    //build one XML rss structure.
    function glossary_rss_feeds() {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            debugging("DISABLED (admin variables)");
        //Check CFG->glossary_enablerssfeeds
        } else if (empty($CFG->glossary_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
        //It's working so we start...
        } else {
            //Iterate over all glossaries
            if ($glossaries = get_records("glossary")) {
                foreach ($glossaries as $glossary) {
                    if (!empty($glossary->rsstype) && !empty($glossary->rssarticles) && $status) {

                        $filename = rss_file_name('glossary', $glossary);  // RSS file

                        //First let's make sure there is work to do by checking existing files
                        if (file_exists($filename)) {
                            if ($lastmodified = filemtime($filename)) {
                                if (!glossary_rss_newstuff($glossary, $lastmodified)) {
                                    continue;
                                }
                            }
                        }

                        //Ignore hidden forums
                        if (!instance_is_visible('glossary',$glossary)) {
                            if (file_exists($filename)) {
                                @unlink($filename);
                            }
                            continue;
                        }

                        mtrace("Updating RSS feed for ".format_string($glossary->name,true).", ID: $glossary->id");

                        //Get the XML contents
                        $result = glossary_rss_feed($glossary);
                        //Save the XML contents to file
                        if (!empty($result)) {
                            $status = rss_save_file("glossary",$glossary,$result);
                        }
                        //Some debug...
                        if (debugging()) {
                            if (empty($result)) {
                                echo "ID: $glossary->id-> (empty) ";
                            } else {
                                if (!empty($status)) {
                                    echo "ID: $glossary->id-> OK ";
                                } else {
                                    echo "ID: $glossary->id-> FAIL ";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }

    function glossary_rss_newstuff($glossary, $time) {
    // If there is new stuff in the glossary since $time then this returns
    // true.  Otherwise it returns false.
        if ($glossary->rsstype == 1) {
            $items = glossary_rss_feed_withauthor($glossary, $time);
        } else {
            $items = glossary_rss_feed_withoutauthor($glossary, $time);
        }
        return (!empty($items));
    }

    //This function return the XML rss contents about the glossary record passed as parameter
    //It returns false if something is wrong
    function glossary_rss_feed($glossary) {

        global $CFG;

        $status = true;

        //Check CFG->enablerssfeeds
        if (empty($CFG->enablerssfeeds)) {
            debugging("DISABLED (admin variables)");
        //Check CFG->glossary_enablerssfeeds
        } else if (empty($CFG->glossary_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
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
    function glossary_rss_feed_withauthor($glossary, $newsince=0) {

        global $CFG;

        $items = array();

        if ($newsince) {
            $newsince = " AND e.timecreated > '$newsince'";
        } else {
            $newsince = "";
        }

        if ($recs = get_records_sql ("SELECT e.id AS entryid, 
                                             e.concept AS entryconcept, 
                                             e.definition AS entrydefinition, 
                                             e.format AS entryformat, 
                                             e.timecreated AS entrytimecreated, 
                                             u.id AS userid, 
                                             u.firstname AS userfirstname,
                                             u.lastname AS userlastname
                                      FROM {$CFG->prefix}glossary_entries e,
                                           {$CFG->prefix}user u
                                      WHERE e.glossaryid = '$glossary->id' AND
                                            u.id = e.userid AND
                                            e.approved = 1 $newsince
                                      ORDER BY e.timecreated desc")) {

            //Are we just looking for new ones?  If so, then return now.
            if ($newsince) {
                return true;
            }
            //Iterate over each entry to get glossary->rssarticles records
            $articlesleft = $glossary->rssarticles;
            $item = NULL;
            $user = NULL;

            $formatoptions = new object;
            $formatoptions->trusttext = true;

            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->entryconcept;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                $item->author = fullname($user);
                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;
                $item->description = format_text($rec->entrydefinition,$rec->entryformat,$formatoptions,$glossary->course);
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
    function glossary_rss_feed_withoutauthor($glossary, $newsince=0) {

        global $CFG;

        $items = array();

        if ($newsince) {
            $newsince = " AND e.timecreated > '$newsince'";
        } else {
            $newsince = "";
        }

        if ($recs = get_records_sql ("SELECT e.id AS entryid,
                                             e.concept AS entryconcept,
                                             e.definition AS entrydefinition,
                                             e.format AS entryformat,
                                             e.timecreated AS entrytimecreated,
                                             u.id AS userid,
                                             u.firstname AS userfirstname,
                                             u.lastname AS userlastname
                                      FROM {$CFG->prefix}glossary_entries e,
                                           {$CFG->prefix}user u
                                      WHERE e.glossaryid = '$glossary->id' AND
                                            u.id = e.userid AND
                                            e.approved = 1 $newsince
                                      ORDER BY e.timecreated desc")) {

            //Are we just looking for new ones?  If so, then return now.
            if ($newsince) {
                return true;
            }

            //Iterate over each entry to get glossary->rssarticles records
            $articlesleft = $glossary->rssarticles;
            $item = NULL;
            $user = NULL;

            $formatoptions = new object;
            $formatoptions->trusttext = true;

            foreach ($recs as $rec) {
                unset($item);
                unset($user);
                $item->title = $rec->entryconcept;
                $user->firstname = $rec->userfirstname;
                $user->lastname = $rec->userlastname;
                //$item->author = fullname($user);
                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;
                $item->description = format_text($rec->entrydefinition,$rec->entryformat,$formatoptions,$glossary->course);
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
