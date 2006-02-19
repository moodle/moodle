<?php
    // This file adds support to rss feeds generation

    // This function is the main entry point to database module
    // rss feeds generation. Foreach database with rss enabled
    // build one XML rss structure.
    function data_rss_feeds() {
        global $CFG;
        $status = true;

        $CFG->debug = true;
        
        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (admin variables)";
            }
        }
        // Check CFG->data_enablerssfeeds.
        else if (empty($CFG->data_enablerssfeeds)) {
            //Some debug...
            if ($CFG->debug > 7) {
                echo "DISABLED (module configuration)";
            }
        }
        // It's working so we start...
        else {
            // Iterate over all data.
            if ($dataActivities = get_records('data')) {
                foreach ($dataActivities as $dataActivity) {
                    
                    if ($dataActivity->rssarticles > 0) {
                        $filename = rss_file_name('data', $dataActivity);  // RSS file
                        
                        // Get the data_records out.
                        $sql = 'SELECT dr.* ' .
                                    'FROM data_records AS dr ' .
                                    "WHERE dr.dataid = {$dataActivity->id} " .
                                    'ORDER BY dr.timecreated DESC ' .
                                    "LIMIT {$dataActivity->rssarticles}";
                        
                        $dataRecords = get_records_sql($sql);
                        
                        // Now all the rss items.
                        $items = array();
                        
                        foreach ($dataRecords as $dataRecord) {
                            $item = null;
                            $temp = array();
                            array_push($temp, $dataRecord);
                            
                            /*$user->firstname = 'test';
                            $user->lastname = 'test';
                            $item->author = fullname($user);*/
                            $item->title = $dataActivity->name;
                            $item->pubdate = $dataRecord->timecreated;
                            $item->link = $CFG->wwwroot.'/mod/data/view.php?d='.$dataActivity->id.'&rid='.$dataRecord->id;
                            $item->description = data_print_template($temp, $dataActivity, '', 'rsstemplate', false, 0, 0, 'timecreated DESC', '', true);
                            
                            array_push($items, $item);
                        }
                        
                        // First all rss feeds common headers.
                        $header = rss_standard_header(format_string($dataActivity->name,true),
                                                      $CFG->wwwroot."/mod/data/view.php?d=".$dataActivity->id,
                                                      format_string($dataActivity->intro,true));
                        
                        if (!empty($header)) {
                            $articles = rss_add_items($items);
                        }
                        
                        // Now all rss feeds common footers.
                        if (!empty($header) && !empty($articles)) {
                            $footer = rss_standard_footer();
                        }
                        // Now, if everything is ok, concatenate it.
                        if (!empty($header) && !empty($articles) && !empty($footer)) {
                            $rss = $header.$articles.$footer;
                            
                            //Save the XML contents to file.
                            $status = rss_save_file("data", $dataActivity, $rss);
                        }
                        else {
                            $status = false;
                        }
                    }
                }
            }
        }
        return $status;
    }

?>
