<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains all the common stuff to be used for RSS in the Database Module
 *
 * @package    mod_data
 * @category   rss
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_data\manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the Database modules RSS Feed
 *
 * @param strClass $context the context the feed should be created under
 * @param array $args array of arguments to be used in the creation of the RSS Feed
 * @return NULL|string NULL if there is nothing to return, or the file path of the cached file if there is
 */
    function data_rss_get_feed($context, $args) {
        global $CFG, $DB;

        // Check CFG->data_enablerssfeeds.
        if (empty($CFG->data_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
            return null;
        }

        $dataid = clean_param($args[3], PARAM_INT);
        $cm = get_coursemodule_from_instance('data', $dataid, 0, false, MUST_EXIST);
        if ($cm) {
            $modcontext = context_module::instance($cm->id);

            //context id from db should match the submitted one
            if ($context->id != $modcontext->id || !has_capability('mod/data:viewentry', $modcontext)) {
                return null;
            }
        }

        $data = $DB->get_record('data', array('id' => $dataid), '*', MUST_EXIST);
        if (!rss_enabled_for_mod('data', $data, false, true)) {
            return null;
        }

        $manager = manager::create_from_instance($data);

        $sql = data_rss_get_sql($data);

        //get the cache file info
        $filename = rss_get_file_name($data, $sql);
        $cachedfilepath = rss_get_file_full_name('mod_data', $filename);

        //Is the cache out of date?
        $cachedfilelastmodified = 0;
        if (file_exists($cachedfilepath)) {
            $cachedfilelastmodified = filemtime($cachedfilepath);
        }
        //if the cache is more than 60 seconds old and there's new stuff
        $dontrecheckcutoff = time()-60;
        if ( $dontrecheckcutoff > $cachedfilelastmodified && data_rss_newstuff($data, $cachedfilelastmodified)) {
            require_once($CFG->dirroot . '/mod/data/lib.php');

            // Get the first field in the list  (a hack for now until we have a selector)
            if (!$firstfield = $DB->get_record_sql('SELECT id,name FROM {data_fields} WHERE dataid = ? ORDER by id', array($data->id), true)) {
                return null;
            }

            if (!$records = $DB->get_records_sql($sql, array(), 0, $data->rssarticles)) {
                return null;
            }

            $firstrecord = array_shift($records);  // Get the first and put it back
            array_unshift($records, $firstrecord);

            // Now create all the articles
            $items = array();
            foreach ($records as $record) {
                $recordarray = array();
                array_push($recordarray, $record);

                $item = new stdClass();

                // guess title or not
                if (!empty($data->rsstitletemplate)) {
                    $parser = $manager->get_template('rsstitletemplate');
                    $item->title = $parser->parse_entries($recordarray);
                } else { // else we guess
                    $item->title   = strip_tags($DB->get_field('data_content', 'content',
                                                      array('fieldid'=>$firstfield->id, 'recordid'=>$record->id)));
                }
                $parser = $manager->get_template('rsstemplate');
                $item->description = $parser->parse_entries($recordarray);
                $item->pubdate = $record->timecreated;
                $item->link = $CFG->wwwroot.'/mod/data/view.php?d='.$data->id.'&rid='.$record->id;

                array_push($items, $item);
            }
            $course = $DB->get_record('course', array('id'=>$data->course));
            $coursecontext = context_course::instance($course->id);
            $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

            // First all rss feeds common headers.
            $header = rss_standard_header($courseshortname
                . \moodle_page::TITLE_SEPARATOR
                . format_string($data->name, true, ['context' => $context]),
                $CFG->wwwroot . "/mod/data/view.php?d=" . $data->id,
                format_text($data->intro, $data->introformat, ['context' => $context])
            );

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
                $status = rss_save_file('mod_data', $filename, $rss);
            }
        }

        return $cachedfilepath;
    }
/**
 * Creates and returns a SQL query for the items that would be included in the RSS Feed
 *
 * @param stdClass $data database instance object
 * @param int      $time epoch timestamp to compare time-modified to, 0 for all or a proper value
 * @return string SQL query string to get the items for the databse RSS Feed
 */
    function data_rss_get_sql($data, $time=0) {
        //do we only want new posts?
        if ($time) {
            $time = " AND dr.timemodified > '$time'";
        } else {
            $time = '';
        }

        $approved = ($data->approval) ? ' AND dr.approved = 1 ' : ' ';

        $sql = "SELECT dr.*, u.firstname, u.lastname
                  FROM {data_records} dr, {user} u
                 WHERE dr.dataid = {$data->id} $approved
                       AND dr.userid = u.id $time
              ORDER BY dr.timecreated DESC";

        return $sql;
    }

    /**
     * If there is new stuff in since $time this returns true
     * Otherwise it returns false.
     *
     * @param stdClass $data the data activity object
     * @param int      $time timestamp
     * @return bool true if there is new stuff for the RSS Feed
     */
    function data_rss_newstuff($data, $time) {
        global $DB;

        $sql = data_rss_get_sql($data, $time);

        $recs = $DB->get_records_sql($sql, null, 0, 1);//limit of 1. If we get even 1 back we have new stuff
        return ($recs && !empty($recs));
    }

    /**
     * Given a database object, deletes all cached RSS files associated with it.
     *
     * @param stdClass $data
     */
    function data_rss_delete_file($data) {
        global $CFG;
        require_once("$CFG->libdir/rsslib.php");

        rss_delete_file('mod_data', $data);
    }
