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
 * kalpanmaps lib.
 *
 * Class for building scheduled task functions
 * for fixing core and third party issues
 *
 * @package    local_kalpanmaps
 * @copyright  2021 onwards LSUOnline & Continuing Education
 * @copyright  2021 onwards Robert Russo
 */

defined('MOODLE_INTERNAL') or die();

// Building the class for the task to be run during scheduled tasks.
class kalpanmaps {

    public $emaillog;

    /**
     * Master function for moving kaltura video resources to urls.
     *
     * For every kalvidres, the following will be created:
     * A new url in the same course section.
     * A link to the corresponding panopto video.
     *
     * @return boolean
     */
    public function run_convert_kalvidres() {
        global $CFG, $DB;

        // Set up verbose logging preference.
        $verbose = $CFG->local_kalpanmaps_verbose;

        // Let's be sure the table exists before we do anything.
        $tableexists = ($DB->get_manager()->table_exists('local_kalpanmaps'));

        // We need this for controlling item visibility.
        if (!function_exists('set_coursemodule_visible')) {
            require_once($CFG->dirroot . "/course/lib.php");
        }

        // SQL to grab remaining kaltura items to convert.
        $kpsql = 'SELECT kr.id AS krid,
                km.kaltura_id AS "kalturaid",
                km.panopto_id AS "panoptoid",
                kr.course AS "courseid",
                km.id AS kmid,
                kr.name AS "itemname",
                kr.video_title AS "videotitle",
                kr.intro AS "intro",
                kr.height AS "itemheight",
                kr.width AS "itemwidth",
                cm.id AS cmid,
                cm.visible AS "modvis",
                cm.groupmode AS "groupmode",
                cm.groupingid AS "groupingid",
                cs.section AS "coursesection"
            FROM {local_kalpanmaps} km
                INNER JOIN {kalvidres} kr ON km.kaltura_id = kr.entry_id
                INNER JOIN {course_modules} cm ON cm.course = kr.course AND cm.instance = kr.id
                INNER JOIN {modules} m ON m.name = "kalvidres" AND cm.module = m.id
                INNER JOIN {course_sections} cs ON cs.course = kr.course AND cs.id = cm.section
                LEFT JOIN {url} u ON kr.course = u.course
                    AND kr.name = u.name
                    AND u.externalurl LIKE CONCAT("%", km.panopto_id , "%")
            WHERE u.id IS NULL
            GROUP BY kr.id, kr.course';

        // SQL to grab visible previously converted kaltura video resources for future hiding.
        $donesql = 'SELECT cm.id AS cmid
            FROM {local_kalpanmaps} km
                INNER JOIN {kalvidres} kr ON km.kaltura_id = kr.entry_id
                INNER JOIN {course_modules} cm ON cm.course = kr.course AND cm.instance = kr.id
                INNER JOIN {modules} m ON m.name = "kalvidres" AND cm.module = m.id
                INNER JOIN {url} u ON kr.course = u.course
                    AND kr.name = u.name
                    AND u.externalurl LIKE CONCAT("%", km.panopto_id , "%")
            WHERE cm.visible = 1
            GROUP BY cm.id';

        // If the table exists, use a standard moodle function to get records from the above SQL.
        $kpdata = $tableexists ? $DB->get_records_sql($kpsql) : null;

        // Set the start time so we can log how long this takes.
        $starttime = microtime(true);

        // Start feeding data into the logger.
        $this->log("Beginning the process of converting Kaltura Video Resources to Panopto urls.");

        // Set up some counts.
        $converted = 0;
        $hidden = 0;

        // Don't do anything if we don't have any items to work with.
        if ($kpdata) {
            $this->log("    Converting Kaltura Video Resource to Panoptp url.");

            // Loops through and actually does the conversions.
            foreach ($kpdata as $kalturaitem) {
                // Increment the converted count.
                $converted++;

                // Log stuff depending on the verbosity preferences.
                if ($verbose) {
                    $this->log("        Converting Kaltura itemid: " . $kalturaitem->kalturaid . ".");
                    $this->log("            Ceating new url for Kaltura itemid: " . $kalturaitem->kalturaid . ".");
                } else {
                    $eol = ($converted % 50) == 0 ? PHP_EOL : " ";
                    if ($eol == PHP_EOL) {
                        mtrace("Created " . $converted . " entries.", $eol);
                    } else {
                        mtrace(".", $eol);
                    }
                }

                // We have not yet converted all kaltura items in this course, convert the next one.
                self::build_url($kalturaitem);

                // Hide the corresponding kalura item if configured to do so and it's not already hidden.
                if ($kalturaitem->modvis == 1 && $CFG->local_kalpanmaps_kalvidres_conv_hide == 1) {

                    // Actually hide the item.
                    set_coursemodule_visible($kalturaitem->cmid, 0, $visibleoncoursepage = 1);

                    // Increment the hidden count.
                    $hidden++;

                    if ($verbose) {
                        $this->log("                Hiding old kaltura item: " . $kalturaitem->kalturaid .
                                   " with already existing url in courseid: " . $kalturaitem->courseid . ".");
                    }
                }

                if ($verbose) {
                    $this->log("            Finished creating the new url with panopto id: " .
                               $kalturaitem->panoptoid . " and hiding the old kaltura item with id: " .
                               $kalturaitem->krid  . ".");
                    $this->log("        Panopto url itemid: " . $kalturaitem->panoptoid . " has been created.");
                }
            }

            // We're done with conversions.
            $this->log("\n    Completed converting Kaltura Video Resource items to Panopto urls.");
            $this->log("Finished converting outstanding Kaltura Video Resources to panopto urls.");

            // How long in seconds did this conversion job take.
            $elapsedtime = round(microtime(true) - $starttime, 3);
            $this->log("The process to convert Kaltura Video Resources to Panopto urls took " .
                       $elapsedtime . " seconds.");

        } else {
            // We did not have anything to do.
            $this->log("No outstanding Kaltura Video Resources.");
        }

        // Grab an array of objects with previously converted kaltura item's courseids.
        $dones = $DB->get_records_sql($donesql);

        // If we're hiding previously converted kalvidres, let's do it.
        if ($CFG->local_kalpanmaps_kalvidres_postconv_hide == 1 && $dones) {

            // Loop through the converted visible items.
            foreach ($dones as $done) {

                // Hide them.
                set_coursemodule_visible($done->cmid, 0, $visibleoncoursepage = 1);

                // Increment the hidden value for our count later.
                $hidden++;
            }
        }

        // Get some counts in the logs depending if we hide KalVidRes items or not.
        if (($CFG->local_kalpanmaps_kalvidres_conv_hide == 1
               || $CFG->local_kalpanmaps_kalvidres_postconv_hide == 1)
               && ($hidden - $converted > 0)) {
            $this->log("Converted " . $converted . " KalVidRes items and hid " . $hidden . " KalVidRes items.");
        } else if ($CFG->local_kalpanmaps_kalvidres_conv_hide == 1) {
            $this->log("Converted " . $converted . " Kaltura Video Resources and hid them.");
        } else {
            $this->log("Converted " . $converted . " Kaltura Video Resources.");
        }

        // Send an email to administrators regarding this.
        if ($converted + $hidden > 0) {
            $this->email_clog_report_to_admins();
        }
    }

    /**
     * Function for building the cm for the new url.
     *
     * For every url created, a new course module
     * will be built here.
     *
     * @return $newcm
     */
    public static function build_course_module($kalturaitem) {
        global $DB;

        // Gets the course object from the courseid.
        $course = get_course($kalturaitem->courseid);

        // Get the id for the url module.
        $moduleid = $DB->get_field('modules', 'id', array('name' => 'url'));

        // Build the course module info.
        $newcm = new stdClass;
        $newcm->course = $course->id;
        $newcm->module = $moduleid;
        $newcm->instance = 0;
        $newcm->section = 0;
        $newcm->idnumber = '';
        $newcm->visible = $kalturaitem->modvis;
        $newcm->visibleoncoursepage = $kalturaitem->modvis;
        $newcm->visibleold = $kalturaitem->modvis;
        $newcm->groupmode = $kalturaitem->groupmode;
        $newcm->groupmembersonly = 0;
        $newcm->groupingid = $kalturaitem->groupingid;
        $newcm->completion = 0;
        $newcm->completionview = 0;
        $newcm->completionexpected = 0;
        $newcm->showdescription = 0;
        $newcm->availability = null;

        // Build the course module itself.
        $newcm->id = self::add_cm($newcm);

        return $newcm;
    }

    /**
     * Function for adding the cm to moodle for the new url.
     *
     * For every url created, a new course module
     * will be added here.
     *
     * @return $cmid
     */
    public static function add_cm($newcm) {
        global $DB;

        // Set the time for the new course module.
        $newcm->added = time();

        // Make sure we have no preconceptions about a cmid.
        unset($newcm->id);

        // Add the record and set / store the id.
        $cmid = $DB->insert_record("course_modules", $newcm);

        // Rebuild the course cache.
        rebuild_course_cache($newcm->course, true);
        return $cmid;
    }

    /**
     * Function for building and adding the new url to moodle.
     *
     * @return $module
     */
    public static function build_url($kalturaitem) {
        global $CFG, $DB;

        // Prerequisites.
        if (!function_exists('url_add_instance')) {
            require_once($CFG->dirroot . '/mod/url/lib.php');
        }
        if (!function_exists('set_coursemodule_visible')) {
            require_once($CFG->dirroot . "/course/lib.php");
        }

        // Set some variables up for later.
        $panoptourl = get_config('block_panopto', 'server_name1');
        $config = get_config('url');
        $parms = '" width="' . $kalturaitem->itemwidth . '" height="' . $kalturaitem->itemheight . '"';
        $link = '/Panopto/Pages/Viewer.aspx?id=';

        // Build the course module and set the cmid.
        $cm = self::build_course_module($kalturaitem);

        // Build the module here.
        $module = new stdClass;
        $module->course = $kalturaitem->courseid;
        $module->name = $kalturaitem->itemname;
        $module->intro = '<p>' . $kalturaitem->videotitle . '</p>' . $kalturaitem->intro;
        $module->externalurl = 'https://' . $panoptourl . $link . $kalturaitem->panoptoid;
        $module->introformat = FORMAT_HTML;
        $module->coursemodule = $cm->id;
        $module->section = $kalturaitem->coursesection;
        $module->display = $config->display;
        $module->popupwidth = $config->popupwidth;
        $module->popupheight = $config->popupheight;
        $module->printintro = $config->printintro;

        // Build the url and set the url id.
        $module->id = url_add_instance($module, null);

        // Now that we have the url, we can finish setting up the cm.
        $cm->instance = $module->id;

        // Add the course module to a specific section matching the old kalvidres.
        $cm->section = course_add_cm_to_section($module->course, $module->coursemodule, $module->section, $kalturaitem->cmid);

        // Update the cm.
        $DB->update_record('course_modules', $cm);

        return $module;
    }













    /**
     * Master function for moving kaltura video iframes and links to panotpo.
     *
     * For every kaltura embed in the DB
     * A new panopto iframe or link  will be created
     * To replace the existing kaltura iframe or link
     * In the same resource or activity.
     *
     * @return boolean $success
     */
    public function run_convert_kalembeds() {
        global $CFG, $DB;

        // Set up verbose logging preference.
        $verbose = $CFG->local_kalpanmaps_verbose;

        // Start the log.
        if ($verbose) {
            mtrace("We are in verbose mode and have begun converting kaltura embeds.");
        } else {
            mtrace("Converting kaltura iframe embeds.");
        }

        // Let's be sure the table exists before we do anything.
        $tableexists = ($DB->get_manager()->table_exists('local_kalpanmaps'));

        // If the table exists, convert any outstanding embeds.
        $success = $tableexists ? self::conv_panitems($verbose) : false;

        // Log out what happened.
        if ($success) {
            mtrace("Successfully converted all remaining kaltura embeds.");
        } else {
            mtrace("The process has completed. Any errors would be listed above.");
        }

        return $success;
    }

    /**
     * Function for checking if the tool is supported in for a given course category.
     *
     * @return @bool
     */
    public static function is_supported($cid) {
         global $CFG;

         // Get the course object from the id.
         $course = get_course($cid);

         // Check to see if this course is in a supported category.
	 $enabled = $CFG->local_kalpanmaps_categorylimit;
         $ccats = $CFG->local_kalpanmaps_cats;
         $cats = explode(',', $ccats);
         $is_cat = ($enabled == 1 ? (empty($cats) or in_array($course->category, $cats)) : true);

         // Return true or not based on above.
         return ($is_cat);
     }

    /**
     * Function for grabbing label data where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_label($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT l.id AS id,
                       l.course AS courseid,
                       l.intro AS itemdata,
                       "label" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_label l
                       INNER JOIN mdl_course c ON c.id = l.course
                   WHERE (l.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
                       OR l.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR l.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing page data where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_page_content($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT p.id AS id,
                       p.course AS courseid,
                       p.content AS itemdata,
                       "page" AS tble,
                       "content" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_page p
                       INNER JOIN mdl_course c ON c.id = p.course
                   WHERE (p.content LIKE "%<iframe id=\"kaltura_player\" src=\"%"
                       OR p.content LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR p.content LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing page intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_page_intro($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT p.id AS id,
                       p.course AS courseid,
                       p.intro AS itemdata,
                       "page" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_page p
                       INNER JOIN mdl_course c ON c.id = p.course
                   WHERE (p.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR p.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR p.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing assignment intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_assign($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT a.id AS id,
                       a.course AS courseid,
                       a.intro AS itemdata,
                       "assign" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_assign a
                       INNER JOIN mdl_course c ON c.id = a.course
                   WHERE (a.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR a.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR a.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing assignment submissions where kaltura filter links are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_assignsubmission_onlinetext($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT aso.id AS id,
                       a.course AS courseid,
                       aso.onlinetext AS itemdata,
                       "assignsubmission_onlinetext" AS tble,
                       "onlinetext" AS dataitem,
                       "student" AS usertype
                   FROM mdl_assignsubmission_onlinetext aso
                       INNER JOIN mdl_assign a ON a.id = aso.assignment
                       INNER JOIN mdl_course c ON c.id = a.course
		   WHERE aso.onlinetext LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing course sections where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_course_sections($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT cs.id AS id,
                       cs.course AS courseid,
                       cs.summary AS itemdata,
                       "course_sections" AS tble,
                       "summary" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_course_sections cs
                       INNER JOIN mdl_course c ON c.id = cs.course
                   WHERE (cs.summary LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR cs.summary LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR cs.summary LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing quiz intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_quiz($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT q.id AS id,
                       q.course AS courseid,
                       q.intro AS itemdata,
                       "quiz" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_quiz q
                       INNER JOIN mdl_course c ON c.id = q.course
                   WHERE (q.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR q.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR q.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing book intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_book($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT b.id AS id,
                       b.course AS courseid,
                       b.intro AS itemdata,
                       "book" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_book b
                       INNER JOIN mdl_course c ON c.id = b.course
                   WHERE (b.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR b.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR b.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing book chapter content where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_book_chapters($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT bc.id AS id,
                       b.course AS courseid,
                       bc.content AS itemdata,
                       "book_chapters" AS tble,
                       "content" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_book_chapters bc
                       INNER JOIN mdl_book b ON b.id = bc.bookid
                       INNER JOIN mdl_course c ON c.id = b.course
                   WHERE (bc.content LIKE "%<iframe id=\"kaltura_player\" src=\"%"
                       OR bc.content LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR bc.content LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing forum intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_forum($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT f.id AS id,
                       f.course AS courseid,
                       f.intro AS itemdata,
                       "forum" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_forum f
                       INNER JOIN mdl_course c ON c.id = f.course
                   WHERE (f.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR f.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR f.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing forum posts where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_forum_posts($students, $limit = 0) {
        global $CFG, $DB;

        // Set these up if we are NOT converting studen submissions.
        $usertype = $students == 0 ? '"faculty" AS usertype' : '"student" AS usertype';

        $joins = $students == 0 ? 'INNER JOIN mdl_context ctx ON f.course = ctx.instanceid
                                   AND ctx.contextlevel = 50
                                   INNER JOIN mdl_role_assignments mra ON mra.contextid = ctx.id
                                   AND fp.userid = mra.userid' : '';

        $wheres = $students == 0 ? 'AND mra.roleid NOT IN (' . $CFG->gradebookroles . ')' : '';

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT fp.id AS id,
                    f.course AS courseid,
                    fp.message AS itemdata,
                    "forum_posts" AS tble,
                    "message" AS dataitem,
                    ' . $usertype . '
                  FROM mdl_forum_posts fp
                    INNER JOIN mdl_forum_discussions fd ON fd.id = fp.discussion
                    INNER JOIN mdl_forum f ON f.id = fd.forum
                    INNER JOIN mdl_course c ON c.id = f.course
                    ' . $joins . '
                  WHERE fp.message LIKE "%browseandembed/index/media/entryid/%"
                    ' . $wheres;

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing lesson intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_lesson($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT l.id AS id,
                       l.course AS courseid,
                       l.intro AS itemdata,
                       "lesson" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_lesson l
                       INNER JOIN mdl_course c ON c.id = l.course
                   WHERE (l.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR l.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR l.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing lesson page contents where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_lesson_pages($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT lp.id AS id,
                       l.course AS courseid,
                       lp.contents AS itemdata,
                       "lesson_pages" AS tble,
                       "contents" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_lesson_pages lp
                       INNER JOIN mdl_lesson l ON l.id = lp.lessonid
                       INNER JOIN mdl_course c ON c.id = l.course
                   WHERE (lp.contents LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR lp.contents LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR lp.contents LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing lesson answers where kaltura links are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_lesson_attempts($limit = 0) {
        global $DB;

        // Set these up if we are NOT converting studen submissions.
        $usertype = '"student" AS usertype';

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT la.id AS id,
                    l.course AS courseid,
                    la.useranswer AS itemdata,
                    "lesson_attempts" AS tble,
                    "useranswer" AS dataitem,
                    ' . $usertype . '
                  FROM mdl_lesson_attempts la
                    INNER JOIN mdl_lesson l ON l.id = la.lessonid
                    INNER JOIN mdl_course c ON c.id = l.course
		  WHERE la.useranswer LIKE "%browseandembed/index/media/entryid/%"';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing lesson answers where kaltura links are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_lesson_answers($limit = 0) {
        global $CFG, $DB;

        // Set these up if we are NOT converting studen submissions.
        $usertype = '"faculty" AS usertype';

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT la.id AS id,
                    l.course AS courseid,
                    la.answer AS itemdata,
                    "lesson_answers" AS tble,
                    "answer" AS dataitem,
                    ' . $usertype . '
                  FROM mdl_lesson_answers la
                    INNER JOIN mdl_lesson l ON l.id = la.lessonid
                    INNER JOIN mdl_course c ON c.id = l.course
		  WHERE la.answer LIKE "%browseandembed/index/media/entryid/%"';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing journal intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_journal($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT j.id AS id,
                       j.course AS courseid,
                       j.intro AS itemdata,
                       "journal" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_journal j
                       INNER JOIN mdl_course c ON c.id = j.course
                   WHERE (j.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR j.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR j.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing journal entries where kaltura filtered links are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_journal_entries($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT je.id AS id,
                       j.course AS courseid,
                       je.text AS itemdata,
                       "journal_entries" AS tble,
                       "text" AS dataitem,
                       "student" AS usertype
                   FROM mdl_journal j
                       INNER JOIN mdl_journal_entries je ON je.journal = j.id
                       INNER JOIN mdl_course c ON c.id = j.course
		   WHERE je.text LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing choice intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_choice($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT c.id AS id,
                       c.course AS courseid,
                       c.intro AS itemdata,
                       "choice" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_choice c
                       INNER JOIN mdl_course cou ON cou.id = c.course
                   WHERE (c.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR c.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR c.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing feedback intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_feedback($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT f.id AS id,
                       f.course AS courseid,
                       f.intro AS itemdata,
                       "feedback" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_feedback f
                       INNER JOIN mdl_course c ON c.id = f.course
                   WHERE (f.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR f.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR f.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing glossary intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_glossary($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT g.id AS id,
                       g.course AS courseid,
                       g.intro AS itemdata,
                       "glossary" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_glossary g
                       INNER JOIN mdl_course c ON c.id = g.course
                   WHERE (g.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR g.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR g.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing glosarry entries where kaltura items are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_glossary_entries($students, $limit = 0) {
        global $CFG, $DB;

        // Set these up if we are NOT converting studen submissions.
        $usertype = $students == 0 ? '"faculty" AS usertype' : '"student" AS usertype';

        $joins = $students == 0 ? 'INNER JOIN mdl_context ctx ON g.course = ctx.instanceid
                                   AND ctx.contextlevel = 50
                                   INNER JOIN mdl_role_assignments mra ON mra.contextid = ctx.id
                                   AND ge.userid = mra.userid' : '';

        $wheres = $students == 0 ? 'AND mra.roleid NOT IN (' . $CFG->gradebookroles . ')' : '';

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT ge.id AS id,
                    g.course AS courseid,
                    ge.definition AS itemdata,
                    "glossary_entries" AS tble,
                    "definition" AS dataitem,
                    ' . $usertype . '
                  FROM mdl_glossary_entries ge
                    INNER JOIN mdl_glossary g ON g.id = ge.glossaryid
                    ' . $joins . '
                       INNER JOIN mdl_course c ON c.id = g.course
                   WHERE (g.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR g.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
                       OR g.intro LIKE "%<iframe src=\"https://www.kaltura.com%")
                    ' . $wheres;

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing group choice intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_choicegroup($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT gc.id AS id,
                       gc.course AS courseid,
                       gc.intro AS itemdata,
                       "choicegroup" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_choicegroup gc
                       INNER JOIN mdl_course c ON c.id = gc.course
                   WHERE (gc.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR gc.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR gc.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing LTI intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_lti($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT l.id AS id,
                       l.course AS courseid,
                       l.intro AS itemdata,
                       "lti" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_lti l
                       INNER JOIN mdl_course c ON c.id = l.course
                   WHERE (l.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR l.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR l.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing questionnaire intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_questionnaire($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT q.id AS id,
                       q.course AS courseid,
                       q.intro AS itemdata,
                       "questionnaire" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_questionnaire q
                       INNER JOIN mdl_course c ON c.id = q.course
                   WHERE (q.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR q.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR q.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing scorm intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_scorm($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT sco.id AS id,
                       sco.course AS courseid,
                       sco.intro AS itemdata,
                       "scorm" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_scorm sco
                       INNER JOIN mdl_course c ON c.id = sco.course
                   WHERE (sco.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR sco.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR sco.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing survey intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_survey($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT s.id AS id,
                       s.course AS courseid,
                       s.intro AS itemdata,
                       "survey" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_survey s
                       INNER JOIN mdl_course c ON c.id = s.course
                   WHERE (s.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR s.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR s.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing turnitin intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_turnitin($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT tii.id AS id,
                       tii.course AS courseid,
                       tii.intro AS itemdata,
                       "turnitintooltwo" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_turnitintooltwo tii
                       INNER JOIN mdl_course c ON c.id = tii.course
                   WHERE (tii.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR tii.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR tii.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing url intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_url($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT u.id AS id,
                       u.course AS courseid,
                       u.intro AS itemdata,
                       "url" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_url u
                       INNER JOIN mdl_course c ON c.id = u.course
                   WHERE (u.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR u.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR u.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing wiki intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_wiki($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT w.id AS id,
                       w.course AS courseid,
                       w.intro AS itemdata,
                       "wiki" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_wiki w
                       INNER JOIN mdl_course c ON c.id = w.course
                   WHERE (w.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR w.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR w.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing workshop intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_workshop($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT w.id AS id,
                       w.course AS courseid,
                       w.intro AS itemdata,
                       "workshop" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_workshop w
                       INNER JOIN mdl_course c ON c.id = w.course
                   WHERE (w.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR w.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR w.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing database intros where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_database($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT db.id AS id,
                       db.course AS courseid,
                       db.intro AS itemdata,
                       "data" AS tble,
                       "intro" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_data db
                       INNER JOIN mdl_course c ON c.id = db.course
                   WHERE (db.intro LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR db.intro LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR db.intro LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing question text where kaltura iframes are present.
     *
     * @return array $kalitems
     */
    public static function get_kal_question($limit = 0) {
        global $DB;

        // Build the SQL to grab items that have kaltura iframes in them.
        $gksql = 'SELECT qq.id AS id,
                       c.id AS courseid,
                       qq.questiontext AS itemdata,
                       "question" AS tble,
                       "questiontext" AS dataitem,
                       "faculty" AS usertype
                   FROM mdl_question qq
                       INNER JOIN mdl_question_categories qc ON qc.id = qq.category
                       INNER JOIN mdl_context ctx ON ctx.id = qc.contextid AND ctx.contextlevel = 50
                       INNER JOIN mdl_course c ON c.id = ctx.instanceid
                   WHERE (qq.questiontext LIKE "%<iframe id=\"kaltura_player\" src=\"%"
		       OR qq.questiontext LIKE "%<a href=\"%/browseandembed/index/media/entryid/%"
		       OR qq.questiontext LIKE "%<iframe src=\"https://www.kaltura.com%")';

        // Build the array of objects.
        $kalitems = array();
        $kalitems = $DB->get_records_sql($gksql, array(null, $limitfrom = 0, $limit));

        // Return the array of objects.
        return $kalitems;
    }

    /**
     * Function for grabbing the panoptoid for a corresponding kalpanmaps kaltura entry_id.
     *
     * @return string $panoptoid
     */
    public static function get_kalpanmaps($entryid, $verbose) {
        global $DB;

        // Build the panoptoid.
        $parms = array('kaltura_id' => $entryid);
        $panoptoid = $DB->get_record('local_kalpanmaps', $parms);

        if ($panoptoid) {
            // Log the entryid and panoptoid accordingly.
            if ($verbose) {
                mtrace("    Retreived $panoptoid->panopto_id from DB with matching entryid $entryid.");
            }
        } else {
            if ($verbose && $entryid) {
                mtrace("    No matching panopto id found for kaltura entryid $entryid. Exiting process.");
            } else if ($verbose) {
                mtrace("  Nothing to do. Exiting process.");
            }
        }

        // Return the panotoid.
        return $panoptoid;
    }

    /**
     * Function for updating the table specified in the kalitem.
     *
     * @return bool $success
     */
    public static function write_panitem($kalitem, $verbose) {
        global $CFG, $DB;

        // Build the SQL as generically as we can for use in any context.
        $item = $kalitem->dataitem;
        $dataitem = new stdClass();

        $dataitem->id = $kalitem->id;
        $dataitem->$item = $kalitem->newitemdata;

        // Run it and store the status.
        $success = false;
        $success = $DB->update_record($kalitem->tble, $dataitem);

        if ($kalitem->tble == 'question') {
            require_once($CFG->dirroot . "/question/engine/bank.php");
            if ($verbose) {
                mtrace("      Purging question cache for question id: $kalitem->id");
            }
            question_finder::get_instance()->uncache_question($kalitem->id);
            if ($verbose) {
                mtrace("      Question cache purged for id: $kalitem->id");
            }
        }

        return $success;
    }

    /**
     * Function for grabbing the iframe and requisite data for a specific kaltura item.
     *
     * @return object $kalmatches
     */
    public static function get_panmatches($kalitem, $verbose) {
        global $CFG;

        // Instantiate the new object.
        $kalmatches = new stdClass();

        // Use this to differentiate between playlists and standard items.
        $kalmatches->playlist = false;

        // Replace any line breaks so we can ensure regex will work.
        $kalitem->itemdata = preg_replace( "/\r|\n/", " ", $kalitem->itemdata);

        // Grab the original Kaltura iframe in it's entirety and add it to the object.
        preg_match('/(<iframe id=.+?entry_id=.+?<\/iframe>)/', $kalitem->itemdata, $matches);
        $kalmatches->oldiframe = isset($matches[1]) ? $matches[1] : '';
        unset($matches);

        // Grab the original Kaltura playlist iframe in it's entirety and add it to the object.
        if ($kalmatches->oldiframe == '') {
            preg_match('/(\<iframe src=\"https:\/\/www\.kaltura\.com\/.+?\/widget_id\/.+?flashvars\[playlistAPI\.kpl0Id\]=.+?<\/iframe\>)/', $kalitem->itemdata, $matches);
            $kalmatches->oldiframe = isset($matches[1]) ? $matches[1] : '';
            $kalmatches->playlist = $kalmatches->oldiframe == '' ? false : true;
            unset($matches);
        }

        preg_match(
            '/(\<a href="http\S+kaf\S+\.com\/browseandembed\/\S+\/entryid\/.+?\/playerSize\/.+?"\>.+?\<\/a\>)/',
            $kalitem->itemdata, $matches);
        $kalmatches->kalbutton = isset($matches[1]) ? $matches[1] : '';
        unset($matches);

        // Rename "iframe" to a nonsensical "noframe" tag so we don't show up in future searches.
        $kalmatches->noframe = preg_replace('/iframe/', 'noframe', $kalmatches->oldiframe);
        unset($matches);

        // Grab the Kaltura entry_id and add it to the object.
        preg_match('/\<iframe id=.+?entry_id=(\S+?)&.+?\<\/iframe\>/', $kalmatches->oldiframe, $matches);
        $kalmatches->entryid = isset($matches[1]) ? $matches[1] : '';
        unset($matches);

        // Grab the Kaltura playlist entry_id and add it to the object.
        if ($kalmatches->oldiframe <> '' && $kalmatches->entryid == '' && $kalmatches->playlist == true) {
            preg_match('/\<iframe src=\"https:\/\/www\.kaltura\.com\/\S+\/widget_id\/.+?flashvars\[playlistAPI\.kpl0Id\]=(\S+?)&.+?\<\/iframe\>/', $kalmatches->oldiframe, $matches);
            $kalmatches->entryid = isset($matches[1]) ? $matches[1] : '';
            unset($matches);
        }

        // Grab the width and add it to the object.
        preg_match('/\<iframe .+?width="(.+?)".+?\<\/iframe\>/', $kalmatches->oldiframe, $matches);
        preg_match('/(\<a) (href="http\S+kaf\S+\.com\/browseandembed\/\S+\/entryid\/(.+?)\/.+?\/playerSize\/(.+?)x(.+?)\/.+?"\>(.+?))\<\/a\>/',
            $kalmatches->kalbutton, $matches2);
        $kalmatches->width = isset($matches[1]) ? $matches[1] : (isset($matches2[4]) ? $matches2[4] : $CFG->local_kalpanmaps_width);
        unset($matches);

        // Grab the height and add it to the object.
        preg_match('/\<iframe .+?height="(.+?)".+?\<\/iframe\>/', $kalmatches->oldiframe, $matches);
        $kalmatches->height = isset($matches[1]) ? $matches[1] : (isset($matches2[5]) ? $matches2[5] : $CFG->local_kalpanmaps_height);

        // Rename "tinymce-kalturamedia-embed" to "LSU-PanoptoMedia-Embed".
        $kalmatches->kalbutton = preg_replace('/\>.+?\|\|(.+?) \[(.+?)\]\|\|\d.+\|\|\d.+\</', '>$1 - $2<', $kalmatches->kalbutton);
        unset($matches);

        // Grab anything that might be extra and add it to the object.
        preg_match('/\<iframe .+?\>(.*?)\<\/iframe\>/', $kalmatches->oldiframe, $matches);
        $kalmatches->ifxtra = isset($matches[1]) ? $matches[1] : '';
        unset($matches);

        preg_match('/(\<a) (href="http\S+kaf\S+\.com\/browseandembed\/.+?\/entryid\/(.+?)\/.+?\/playerSize\/(.+?)x(.+?)\/.+?"\>(.+?))\<\/a\>/',
            $kalmatches->kalbutton, $matches);
        $kalmatches->noframe = !empty($kalmatches->noframe)
            ? $kalmatches->noframe
            : ($matches ? '<!-- HIDDEN <anchor ' . $matches[2] . '</anchor> HIDDEN -->' : '');

        if ($kalmatches->kalbutton) {
            // Set these for the buttons.
            $kalmatches->entryid = $kalmatches->entryid
                ? $kalmatches->entryid :
                (isset($matches[3]) ? $matches[3] : '');
            $kalmatches->ifxtra = $kalmatches->ifxtra
                ? $kalmatches->ifxtra :
                (isset($matches[6]) ? $matches[6] : '');
        }

        /*
	echo"\n";
	echo'<xmp>';
	echo"\n";
        echo"kalbutton: ";
        print_r($kalmatches->kalbutton);
	echo"\n";
        echo"\nnobutton: ";
        print_r($kalmatches->noframe);
	echo"\n";
        echo"\nentry_id: ";
        print_r($kalmatches->entryid);
	echo"\n";
        echo"\nwidth: ";
        print_r($kalmatches->width);
	echo"\n";
        echo"\nheight: ";
        print_r($kalmatches->height);
	echo"\n";
        echo"\nifextra: ";
        print_r($kalmatches->ifxtra);
	echo"\n";
        echo'</xmp>';
	echo"\n";
        */

        // Log the iframe info in verbose mode.
        if (!empty($kalmatches->entryid) && $verbose) {
            $msg = '  Found ' .
                $kalitem->tble . ' ' .
                $kalitem->dataitem . ' with matching data and entryid: ' .
                $kalmatches->entryid . ', width: ' .
                $kalmatches->width . ', height: ' .
                $kalmatches->height . ' in course: ' .
                $kalitem->courseid;
            mtrace($msg);
        }

        return $kalmatches;
    }

    /**
     * Function where the work gets done.
     *
     * @return bool
     */
    public static function conv_panitems($verbose) {
        global $CFG;

        $fails = 0;
        $successes = 0;
        $students = $CFG->local_kalpanmaps_kalprocessstudents;

        // Populate the kalitems array.

        // Labels.
        $kalitems = self::get_kal_label($limit = 0);

        // Assignments.
        $kalitems = array_merge($kalitems, self::get_kal_assign($limit = 0));
        if ($students) {
            // Assignment submissions.
            $kalitems = array_merge($kalitems, self::get_kal_assignsubmission_onlinetext($limit = 0));
        }

        // Book.
        $kalitems = array_merge($kalitems, self::get_kal_book($limit = 0));
        $kalitems = array_merge($kalitems, self::get_kal_book_chapters($limit = 0));

        // Choice.
        $kalitems = array_merge($kalitems, self::get_kal_choice($limit = 0));

        // Group choice.
        $kalitems = array_merge($kalitems, self::get_kal_choicegroup($limit = 0));

        // Course sections.
        $kalitems = array_merge($kalitems, self::get_kal_course_sections($limit = 0));

        // URLs.
        $kalitems = array_merge($kalitems, self::get_kal_url($limit = 0));

        // Feedback.
        $kalitems = array_merge($kalitems, self::get_kal_feedback($limit = 0));

        // Forum.
        $kalitems = array_merge($kalitems, self::get_kal_forum($limit = 0));
        if ($students) {
            $kalitems = array_merge($kalitems, self::get_kal_forum_posts($limit = 0, $students = 1));
        } else {
            $kalitems = array_merge($kalitems, self::get_kal_forum_posts($limit = 0, $students = 0));
        }

        // Glossary.
        $kalitems = array_merge($kalitems, self::get_kal_glossary($limit = 0));
        if ($students) {
            $kalitems = array_merge($kalitems, self::get_kal_glossary_entries($limit = 0, $students = 1));
        } else {
            $kalitems = array_merge($kalitems, self::get_kal_glossary_entries($limit = 0, $students = 0));
        }

        // Journal.
        $kalitems = array_merge($kalitems, self::get_kal_journal($limit = 0));
        if ($students) {
            $kalitems = array_merge($kalitems, self::get_kal_journal_entries($limit = 0));
        }

        // Lesson.
        $kalitems = array_merge($kalitems, self::get_kal_lesson($limit = 0));
        $kalitems = array_merge($kalitems, self::get_kal_lesson_pages($limit = 0));
        $kalitems = array_merge($kalitems, self::get_kal_lesson_answers($limit = 0));
        if ($students) {
            // TODO: WE Currently do not have any lesson attempts containing kaltura videos.
        }

        // LTI.
        $kalitems = array_merge($kalitems, self::get_kal_lti($limit = 0));

        // Page.
        $kalitems = array_merge($kalitems, self::get_kal_page_intro($limit = 0));
        $kalitems = array_merge($kalitems, self::get_kal_page_content($limit = 0));

        // Questionnaire.
        $kalitems = array_merge($kalitems, self::get_kal_questionnaire($limit = 0));

        // Quiz.
        $kalitems = array_merge($kalitems, self::get_kal_quiz($limit = 0));

        // SCORM.
        $kalitems = array_merge($kalitems, self::get_kal_scorm($limit = 0));

        // Survey.
        $kalitems = array_merge($kalitems, self::get_kal_survey($limit = 0));

        // Turnitin.
        $kalitems = array_merge($kalitems, self::get_kal_turnitin($limit = 0));

        // Wiki.
        $kalitems = array_merge($kalitems, self::get_kal_wiki($limit = 0));

        // Workshop.
        $kalitems = array_merge($kalitems, self::get_kal_workshop($limit = 0));

        // Database.
        $kalitems = array_merge($kalitems, self::get_kal_database($limit = 0));

        // Quiz questions.
        // TODO: Deal with this BS.
        // $kalitems = array_merge($kalitems, self::get_kal_question($limit = 0));

        // Grab the panopto server.
        $panoptourl = get_config('block_panopto', 'server_name1');

        // The link we're using for faculty items.
        $flink = '/Panopto/Pages/Embed.aspx?id=';

        // The link we're using for faculty playlists.
        $plink = '/Panopto/Pages/Embed.aspx?pid=';

        // The link for student data.
        $slink = '/Panopto/Pages/Viewer.aspx?id=';

        /*
        echo'<xmp>';
        print_r($kalitems);
        echo'</xmp>';
        */

        // Get the values for the urlparms from settings.
        $kpvalues = array();
        $kpvalues['showtitle'] = $CFG->local_kalpanmaps_showtitle == 1 ? "true" : "false";
        $kpvalues['captions'] = $CFG->local_kalpanmaps_captions == 1 ? "true" : "false";
        $kpvalues['autoplay'] = $CFG->local_kalpanmaps_autoplay == 1 ? "true" : "false";
        $kpvalues['offerviewer'] = $CFG->local_kalpanmaps_offerviewer == 1 ? "true" : "false";
        $kpvalues['showbrand'] = $CFG->local_kalpanmaps_showbrand == 1 ? "true" : "false";
        $kpvalues['interactivity'] = $CFG->local_kalpanmaps_interactivity == 1 ? "all" : "none";

        // Build the urlparms for use in building the Panopto iframes.
        $kalframeparms = http_build_query($kpvalues, '', '&');

        /*
        echo'<xmp>';
        echo"\nKaltura Frame Params: ";
        print_r($kalframeparms);
        echo'</xmp>';
        */

        // Setting up these for later.
        $forcelinked = array();
        $forcelinked[] = "forum_posts";
        $forcelinked[] = "lesson_answers";

        // Loop through the kaltura items and do stuff.
        foreach ($kalitems as $kalitem) {

        if (self::is_supported($kalitem->courseid)) {
        $cc = get_course($kalitem->courseid); 
        mtrace('Course ID: ' . $cc->id . ' is in Category ID: ' . $cc->category . ' and is supported. Processing.');

            // We have to foce links where Moodle prohibits embed code.
            $forcelinks = false;
            if (in_array($kalitem->tble, $forcelinked)) {
                $forcelinks = true;

                /*
                echo'<xmp>';
                echo"\nTables: ";
                print_r(implode($forcelinked, ","));
                echo"\ntble: ";
                print_r($kalitem->tble);
                echo'</xmp>';
                */

            }

            // Get outta here if we don't have an item to work with.
            if (!isset($kalitem->itemdata)) {
                return;
            }

            // Get an object for future use in building the new data entry.
            $panmatches = self::get_panmatches($kalitem, $verbose);

            /*
            echo'<xmp>';
            print_r($panmatches);
            echo'</xmp>';
            */

            // Get the corresponding panopto_id.
            if ($panmatches->entryid) {
                $panoptoid = self::get_kalpanmaps($panmatches->entryid, $verbose);
            } else {
                continue;
            }

            if (!$panoptoid) {
                continue;
            }

            // Build the URL for the new data item depending on who generated the content.
            if ($kalitem->usertype == "student") {
                $kalframe = 'https://' . $panoptourl . $slink . $panoptoid->panopto_id . '&' . $kalframeparms;
            } else if ($panmatches->playlist == true) {
                $kalframe = 'https://' . $panoptourl . $plink . $panoptoid->panopto_id . '&' . $kalframeparms;
            } else {
                $kalframe = 'https://' . $panoptourl . $flink . $panoptoid->panopto_id . '&' . $kalframeparms;
            }

            /*
            echo'<xmp>';
            echo"\nKaltura Frame Params: ";
            print_r($kalframe);
            echo'</xmp>';
            */

            if ($verbose) {
                mtrace("  Found data item with kaltura entryid: $panmatches->entryid.");
            }

            /*
            echo'<xmp>';
            print_r($kalitem->itemdata);
            print_r($panmatches->oldiframe);
            echo'</xmp>';
            */

            // Replace the old iframe with the new one and a hidden version of itself.
            if ($panmatches->oldiframe <> '') {
                if ($kalitem->tble == "course_sections" && $panmatches->playlist == false) {
                    $kalitem->newitemdata = preg_replace('/\<iframe id=.+?entry_id=' .
                                          $panmatches->entryid .
                                          '.+?\<\/iframe\>/',
                                          '<iframe width="'.
                                          $panmatches->width .
                                          'px" height="'.
                                          $panmatches->height .
                                          'px" src="' .
                                          $kalframe .
                                          '"> Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          '<!--HIDDEN ' .
                                          $panmatches->noframe .
                                          ' HIDDEN-->',
                                          $kalitem->itemdata, 1);
                } else if ($kalitem->tble == "course_sections" && $panmatches->playlist == true) {
                    mtrace('We have encountered a Kaltura playlist ' . $panmatches->entryid . 'and will be replacing it with PanoptoID ' . $panoptoid->panopto_id . '.');
                    $kalitem->newitemdata = preg_replace('/\<iframe src=\"https:\/\/www\.kaltura\.com\/\S+\/widget_id\/.+?flashvars\[playlistAPI\.kpl0Id\]=' .
                                          $panmatches->entryid .
                                          '&.+?\<\/iframe\>/',
                                          '<iframe width="'.
                                          $panmatches->width .
                                          'px" height="'.
                                          $panmatches->height .
                                          'px" src="' .
                                          $kalframe .
                                          '"> Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          '<!--HIDDEN ' .
                                          $panmatches->noframe .
                                          ' HIDDEN-->',
                                          $kalitem->itemdata, 1);
                } else if ($panmatches->playlist == true) {
                    mtrace('We have encountered a Kaltura playlist ' . $panmatches->entryid . 'and will be replacing it with PanoptoID ' . $panoptoid->panopto_id . '.');
                    $kalitem->newitemdata = preg_replace('/\<iframe src=\"https:\/\/www\.kaltura\.com\/\S+\/widget_id\/.+?flashvars\[playlistAPI\.kpl0Id\]=' .
                                          $panmatches->entryid .
                                          '&.+?\<\/iframe\>/',
                                          '<div style="max-width: ' .
                                          $panmatches->width . 'px;">' .
                                          '<div class="pandiv" style="padding-top: '
                                          . (($panmatches->height / $panmatches->width) * 100) .
                                          '%;">' .
                                          '<iframe class="paniframe" src="' .
                                          $kalframe .
                                          '">Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          '</div>' .
                                          '</div>' .
                                          '<!--HIDDEN ' .
                                          $panmatches->noframe .
                                          ' HIDDEN-->',
                                          $kalitem->itemdata, 1);
                } else {
                    $kalitem->newitemdata = preg_replace('/\<iframe id=.+?entry_id=' .
                                          $panmatches->entryid .
                                          '.+?\<\/iframe\>/',
                                          '<div style="max-width: ' .
                                          $panmatches->width . 'px;">' .
                                          '<div class="pandiv" style="padding-top: '
                                          . (($panmatches->height / $panmatches->width) * 100) .
                                          '%;">' .
                                          '<iframe class="paniframe" src="' .
                                          $kalframe .
                                          '">Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          '</div>' .
                                          '</div>' .
                                          '<!--HIDDEN ' .
                                          $panmatches->noframe .
                                          ' HIDDEN-->',
                                          $kalitem->itemdata, 1);
                }
            } else {
                if (($students && $kalitem->usertype == "student") || $forcelinks) {
                     $kalitem->newitemdata = preg_replace('/\<a href="http\S+kaf\S+\.com\/browseandembed\/\S+\/entryid\/' .
                                          $panmatches->entryid .
                                          '\/.+?\>.+?\<\/a\>/',
                                          '<a class="panlink" href="' .
                                          $kalframe .
                                          '" target="_blank">' .
                                          $panmatches->ifxtra .
                                          '</a>' .
                                          $panmatches->noframe,
                                          $kalitem->itemdata, 1);
                } else {
                        if ($kalitem->tble == "course_sections") {
                            $kalitem->newitemdata =
                                          preg_replace('/\<a href="http\S+kaf\S+\.com\/browseandembed\/\S+\/entryid\/' .
                                          $panmatches->entryid .
                                          '\/.+?\>.+?\<\/a\>/',
                                          '<iframe width="'.
                                          $panmatches->width .
                                          'px" height="'.
                                          $panmatches->height .
                                          'px" src="' .
                                          $kalframe .
                                          '"> Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          $panmatches->noframe,
                                          $kalitem->itemdata, 1);
                        } else {
                            $kalitem->newitemdata =
                                          preg_replace('/\<a href="http\S+kaf\S+\.com\/browseandembed\/\S+\/entryid\/' .
                                          $panmatches->entryid .
                                          '\/.+?\>.+?\<\/a\>/',
                                          '<div style="max-width: ' .
                                          $panmatches->width . 'px;">' .
                                          '<div class="pandiv" style="padding-top: '
                                          . (($panmatches->height / $panmatches->width) * 100) .
                                          '%;"><iframe class="paniframe" src="' .
                                          $kalframe .
                                          '"> Panopto Video - ' .
                                          $panmatches->ifxtra .
                                          '</iframe>' .
                                          '</div>' .
                                          '</div>' .
                                          $panmatches->noframe,
                                          $kalitem->itemdata, 1);
                    }
                }
            }

            /*
            echo'<xmp>';
            print_r($panmatches->noframe);
            echo"\nNewItemData: ";
            print_r($kalitem->newitemdata);
            echo'</xmp>';
            */

            // Update the record with the new iframe and hidden noframe.
            if (self::write_panitem($kalitem, $verbose)) {
                // Increment our successes.
                $successes++;

                // Log that we've done it in verbose mode or just update the page with a period.
                if ($verbose) {
                    $msg = '  Replaced ' .
                        $kalitem->tble .
                        $kalitem->dataitem . ' kaltura entry_id: ' .
                        $panmatches->entryid . ' item data with Panopto id: ' .
                        $panoptoid->panopto_id . ' in course ' .
                        $kalitem->courseid;

                    mtrace($msg);
                } else {
                    mtrace(".");
                }
            } else {
                // Increment our failures.
                $fails++;

                // We have a failure, log it regardless of status.
                $msg = '  Conversion of ' .
                    $kalitem->tble . '.' .
                    $kalitem->dataitem . ' failed for kaltura entryid: ' .
                    $panmatches->entryid . ' and panopto id: ' .
                    $panoptoid . ' in courseid ' .
                    $kalitem->courseid;

                    mtrace($msg);
            }

            // Rebuild the course cache.
            rebuild_course_cache($kalitem->courseid, true);
        } else {
            mtrace('Course ID: ' . $kalitem->courseid . ' is not supported. Skipping.');
        }
        }

        // Log what we did.
        if ($successes > 0) {
            mtrace("  Success: $successes");
        }
        if ($fails > 0) {
            mtrace("  Failures: $fails");
        }
        mtrace("  Kaltura embed conversion is complete for now.");
        mtrace("Turn off verbose mode if you're running against a large DB.");
    }




















    /**
     * Emails a kalvidres conversion log to admin users
     *
     * @return void
     */
    private function email_clog_report_to_admins() {
        global $CFG;

        // Get email content from email log.
        $emailcontent = implode("\n", $this->emaillog);

        // Send to each admin.
        $users = get_admins();
        foreach ($users as $user) {
            $replyto = '';
            email_to_user($user,
                "Kaltura Video Resource conversion",
                sprintf('Converting KalVidRes for [%s]',
                $CFG->wwwroot),
                $emailcontent);
        }
    }

    /**
     * print during cron run and prep log data for emailling
     *
     * @param $what: data being sent to $this->log
     */
    private function log($what) {
        mtrace($what);

        $this->emaillog[] = $what;
    }
}
