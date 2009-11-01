<?php
    // This file adds support to rss feeds generation

    // This function is the main entry point to database module
    // rss feeds generation. Foreach database with rss enabled
    // build one XML rss structure.


    function data_rss_feeds() {
        global $CFG, $DB;

        $status = true;

        // Check CFG->enablerssfeeds.
        if (empty($CFG->enablerssfeeds)) {
            debugging("DISABLED (admin variables)");
        }
        // Check CFG->data_enablerssfeeds.
        else if (empty($CFG->data_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
        }
        // It's working so we start...
        else {
            // Iterate over all data.
            if ($datas = $DB->get_records('data')) {
                foreach ($datas as $data) {

                    if ($data->rssarticles > 0) {

                        // Get the first field in the list  (a hack for now until we have a selector)

                        if (!$firstfield = $DB->get_record_sql('SELECT id,name FROM {data_fields} WHERE dataid = ? ORDER by id', array($data->id), true)) {
                            continue;
                        }


                        // Get the data_records out.
                        $approved = ($data->approval) ? ' AND dr.approved = 1 ' : ' ';

                        $sql = "SELECT dr.*, u.firstname, u.lastname
                                  FROM {data_records} dr, {user} u
                                 WHERE dr.dataid = ? $approved
                                       AND dr.userid = u.id
                              ORDER BY dr.timecreated DESC";

                        if (!$records = $DB->get_records_sql($sql, array($data->id), 0, $data->rssarticles)) {
                            continue;
                        }

                        $firstrecord = array_shift($records);  // Get the first and put it back
                        array_unshift($records, $firstrecord);

                        $filename = rss_file_name('data', $data);
                        if (file_exists($filename)) {
                            if (filemtime($filename) >= $firstrecord->timemodified) {
                                continue;
                            }
                        }

                        // Now create all the articles
                        mtrace('Creating feed for '.$data->name);

                        $items = array();
                        foreach ($records as $record) {

                            $recordarray = array();
                            array_push($recordarray, $record);

                            $item = null;

                            // guess title or not
                            if (!empty($data->rsstitletemplate)) {
                                $item->title = data_print_template('rsstitletemplate', $recordarray, $data, '', 0, true);
                            } else { // else we guess
                                $item->title   = strip_tags($DB->get_field('data_content', 'content',
                                                                  array('fieldid'=>$firstfield->id, 'recordid'=>$record->id)));
                            }
                            $item->description = data_print_template('rsstemplate', $recordarray, $data, '', 0, true);
                            $item->pubdate = $record->timecreated;
                            $item->link = $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&rid='.$record->id;

                            array_push($items, $item);
                        }
                        $course = $DB->get_record('course', array('id'=>$data->course));

                        // First all rss feeds common headers.
                        $header = rss_standard_header($course->shortname.': '.format_string($data->name,true),
                                                      $CFG->wwwroot."/mod/data/view.php?d=".$data->id,
                                                      format_string($data->intro,true)); //TODO: fix format

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
                            $status = rss_save_file('data', $data, $rss);
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


