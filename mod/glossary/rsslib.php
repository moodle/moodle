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
 * This file adds support to rss feeds generation
 *
 * @package mod_glossary
 * @category rss
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the path to the cached rss feed contents. Creates/updates the cache if necessary.
 *
 * @param stdClass $context the context
 * @param array    $args    the arguments received in the url
 * @return string the full path to the cached RSS feed directory. Null if there is a problem.
 */
    function glossary_rss_get_feed($context, $args) {
        global $CFG, $DB, $COURSE, $USER;

        $status = true;

        if (empty($CFG->glossary_enablerssfeeds)) {
            debugging("DISABLED (module configuration)");
            return null;
        }

        $glossaryid  = clean_param($args[3], PARAM_INT);
        $cm = get_coursemodule_from_instance('glossary', $glossaryid, 0, false, MUST_EXIST);
        $modcontext = context_module::instance($cm->id);

        if ($COURSE->id == $cm->course) {
            $course = $COURSE;
        } else {
            $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
        }
        //context id from db should match the submitted one
        if ($context->id != $modcontext->id || !has_capability('mod/glossary:view', $modcontext)) {
            return null;
        }

        $glossary = $DB->get_record('glossary', array('id' => $glossaryid), '*', MUST_EXIST);
        if (!rss_enabled_for_mod('glossary', $glossary)) {
            return null;
        }

        $sql = glossary_rss_get_sql($glossary);

        //get the cache file info
        $filename = rss_get_file_name($glossary, $sql);
        $cachedfilepath = rss_get_file_full_name('mod_glossary', $filename);

        //Is the cache out of date?
        $cachedfilelastmodified = 0;
        if (file_exists($cachedfilepath)) {
            $cachedfilelastmodified = filemtime($cachedfilepath);
        }
        //if the cache is more than 60 seconds old and there's new stuff
        $dontrecheckcutoff = time()-60;
        if ( $dontrecheckcutoff > $cachedfilelastmodified && glossary_rss_newstuff($glossary, $cachedfilelastmodified)) {
            if (!$recs = $DB->get_records_sql($sql, array(), 0, $glossary->rssarticles)) {
                return null;
            }

            $items = array();

            $formatoptions = new stdClass();
            $formatoptions->trusttext = true;

            foreach ($recs as $rec) {
                $item = new stdClass();
                $item->title = $rec->entryconcept;

                if ($glossary->rsstype == 1) {//With author
                    $item->author = fullname($rec);
                }

                $item->pubdate = $rec->entrytimecreated;
                $item->link = $CFG->wwwroot."/mod/glossary/showentry.php?courseid=".$glossary->course."&eid=".$rec->entryid;

                $definition = file_rewrite_pluginfile_urls($rec->entrydefinition, 'pluginfile.php',
                    $modcontext->id, 'mod_glossary', 'entry', $rec->entryid);
                $item->description = format_text($definition, $rec->entryformat, $formatoptions, $glossary->course);
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
                $status = rss_save_file('mod_glossary', $filename, $rss);
            }
        }

        if (!$status) {
            $cachedfilepath = null;
        }

        return $cachedfilepath;
    }

    /**
     * The appropriate SQL query for the glossary items to go into the RSS feed
     *
     * @param stdClass $glossary the glossary object
     * @param int      $time     check for items since this epoch timestamp
     * @return string the SQL query to be used to get the entried from the glossary table of the database
     */
    function glossary_rss_get_sql($glossary, $time=0) {
        //do we only want new items?
        if ($time) {
            $time = "AND e.timecreated > $time";
        } else {
            $time = "";
        }

        if ($glossary->rsstype == 1) {//With author
            $allnamefields = get_all_user_name_fields(true,'u');
            $sql = "SELECT e.id AS entryid,
                      e.concept AS entryconcept,
                      e.definition AS entrydefinition,
                      e.definitionformat AS entryformat,
                      e.definitiontrust AS entrytrust,
                      e.timecreated AS entrytimecreated,
                      u.id AS userid,
                      $allnamefields
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
     * @param stdClass $glossary the glossary activity object
     * @param int      $time     epoch timestamp to compare new items against, 0 for everyting
     * @return bool true if there are new items
     */
    function glossary_rss_newstuff($glossary, $time) {
        global $DB;

        $sql = glossary_rss_get_sql($glossary, $time);

        $recs = $DB->get_records_sql($sql, null, 0, 1);//limit of 1. If we get even 1 back we have new stuff
        return ($recs && !empty($recs));
    }


