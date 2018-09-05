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
 * Class publication
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\hub;
defined('MOODLE_INTERNAL') || die();

use moodle_exception;
use moodle_url;
use context_user;
use stdClass;
use html_writer;

/**
 * Methods to work with site registration on moodle.net
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class publication {

    /** @var Audience: educators */
    const HUB_AUDIENCE_EDUCATORS = 'educators';

    /** @var Audience: students */
    const HUB_AUDIENCE_STUDENTS = 'students';

    /** @var Audience: admins */
    const HUB_AUDIENCE_ADMINS = 'admins';

    /** @var Educational level: primary */
    const HUB_EDULEVEL_PRIMARY = 'primary';

    /** @var Educational level: secondary */
    const HUB_EDULEVEL_SECONDARY = 'secondary';

    /** @var Educational level: tertiary */
    const HUB_EDULEVEL_TERTIARY = 'tertiary';

    /** @var Educational level: government */
    const HUB_EDULEVEL_GOVERNMENT = 'government';

    /** @var Educational level: association */
    const HUB_EDULEVEL_ASSOCIATION = 'association';

    /** @var Educational level: corporate */
    const HUB_EDULEVEL_CORPORATE = 'corporate';

    /** @var Educational level: other */
    const HUB_EDULEVEL_OTHER = 'other';


    /**
     * Retrieve all the sorted course subjects
     *
     * @return array $subjects
     */
    public static function get_sorted_subjects() {
        $subjects = get_string_manager()->load_component_strings('edufields', current_language());

        // Sort the subjects.
        $return  = [];
        asort($subjects);
        foreach ($subjects as $key => $option) {
            $keylength = strlen($key);
            if ($keylength == 12) {
                $return[$key] = $option; // We want only selectable categories.
            }
        }
        return $return;
    }

    /**
     * Get all publication for a course
     *
     * @param int $courseid local course id
     * @return array of publication
     */
    public static function get_course_publications($courseid) {
        global $DB;
        $sql = 'SELECT cp.id, cp.status, cp.timechecked, cp.timepublished, rh.hubname,
                       rh.huburl, cp.courseid, cp.enrollable, cp.hubcourseid
                FROM {course_published} cp, {registration_hubs} rh
                WHERE cp.huburl = rh.huburl and cp.courseid = :courseid and rh.huburl = :huburl
                ORDER BY cp.enrollable DESC, rh.hubname, cp.timepublished';
        $params = array('courseid' => $courseid, 'huburl' => HUB_MOODLEORGHUBURL);
        $records = $DB->get_records_sql($sql, $params);

        // Add links for publications that are listed.
        foreach ($records as $id => $record) {
            if ($record->status) {
                $records[$id]->link = new moodle_url(HUB_MOODLEORGHUBURL, ['courseid' => $record->hubcourseid]);
            }
        }
        return $records;
    }

    /**
     * Load publication information from local db
     *
     * @param int $id
     * @param int $courseid if specified publication will be checked that it is in the current course
     * @param int $strictness
     * @return stdClass
     */
    public static function get_publication($id, $courseid = 0, $strictness = IGNORE_MISSING) {
        global $DB;
        if (!$id && $strictness != MUST_EXIST) {
            return false;
        }
        $params = ['id' => $id, 'huburl' => HUB_MOODLEORGHUBURL];
        if ($courseid) {
            $params['courseid'] = $courseid;
        }
        return $DB->get_record('course_published', $params, '*', $strictness);
    }

    /**
     * Update a course publication
     * @param stdClass $publication
     */
    protected static function update_publication($publication) {
        global $DB;
        $DB->update_record('course_published', $publication);
    }

    /**
     * Check all courses published from this site if they have been approved
     */
    public static function request_status_update() {
        global $DB;

        list($sitecourses, $coursetotal) = api::get_courses('', 1, 1, ['allsitecourses' => 1]);

        // Update status for all these course.
        foreach ($sitecourses as $sitecourse) {
            // Get the publication from the hub course id.
            $publication = $DB->get_record('course_published', ['hubcourseid' => $sitecourse['id']]);
            if (!empty($publication)) {
                $publication->status = $sitecourse['privacy'];
                $publication->timechecked = time();
                self::update_publication($publication);
            } else {
                $msgparams = new stdClass();
                $msgparams->id = $sitecourse['id'];
                $msgparams->hubname = html_writer::tag('a', 'Moodle.net', array('href' => HUB_MOODLEORGHUBURL));
                \core\notification::add(get_string('detectednotexistingpublication', 'hub', $msgparams)); // TODO action?
            }
        }

    }

    /**
     * Unpublish a course
     *
     * @param stdClass $publication
     */
    public static function unpublish($publication) {
        global $DB;
        // Unpublish the publication by web service.
        api::unregister_courses($publication->hubcourseid);

        // Delete the publication from the database.
        $DB->delete_records('course_published', array('id' => $publication->id));

        // Add confirmation message.
        $course = get_course($publication->courseid);
        $context = \context_course::instance($course->id);
        $publication->courseshortname = format_string($course->shortname, true, ['context' => $context]);
        $publication->hubname = 'Moodle.net';
        \core\notification::add(get_string('courseunpublished', 'hub', $publication), \core\output\notification::NOTIFY_SUCCESS);
    }

    /**
     * Publish a course
     *
     * @param \stdClass $courseinfo
     * @param \stored_file[] $files
     */
    public static function publish_course($courseinfo, $files) {
        global $DB;

        // Register course and get id of the course on moodle.net ($hubcourseid).
        $courseid = $courseinfo->sitecourseid;
        try {
            $hubcourseid = api::register_course($courseinfo);
        } catch (Exception $e) {
            throw new moodle_exception('errorcoursepublish', 'hub',
                new moodle_url('/course/view.php', array('id' => $courseid)), $e->getMessage());
        }

        // Insert/update publication record in the local DB.
        $publication = $DB->get_record('course_published', array('hubcourseid' => $hubcourseid, 'huburl' => HUB_MOODLEORGHUBURL));

        if ($publication) {
            $DB->update_record('course_published', ['id' => $publication->id, 'timepublished' => time()]);
        } else {
            $publication = new stdClass();
            $publication->huburl = HUB_MOODLEORGHUBURL;
            $publication->courseid = $courseid;
            $publication->hubcourseid = $hubcourseid;
            $publication->enrollable = (int)$courseinfo->enrollable;
            $publication->timepublished = time();
            $publication->id = $DB->insert_record('course_published', $publication);
        }

        // Send screenshots.
        if ($files) {
            $screenshotnumber = $courseinfo->screenshots - count($files);
            foreach ($files as $file) {
                $screenshotnumber++;
                api::add_screenshot($hubcourseid, $file, $screenshotnumber);
            }
        }

        return $hubcourseid;
    }

    /**
     * Delete all publications
     *
     * @param int $advertised search for advertised courses
     * @param int $shared search for shared courses
     * @throws moodle_exception
     */
    public static function delete_all_publications($advertised = true, $shared = true) {
        global $DB;

        if (!$advertised && !$shared) {
            // Nothing to do.
            return true;
        }

        $params = ['huburl' => HUB_MOODLEORGHUBURL];
        if (!$advertised || !$shared) {
            // Retrieve ONLY advertised or ONLY shared.
            $params['enrollable'] = $advertised ? 1 : 0;
        }

        if (!$publications = $DB->get_records('course_published', $params)) {
            // Nothing to unpublish.
            return true;
        }

        foreach ($publications as $publication) {
            $hubcourseids[] = $publication->hubcourseid;
        }

        api::unregister_courses($hubcourseids);

        // Delete the published courses from local db.
        $DB->delete_records('course_published', $params);
        return true;
    }

    /**
     * Get an array of all block instances for a given context
     * @param int $contextid a context id
     * @return array of block instances.
     */
    public static function get_block_instances_by_context($contextid) {
        global $DB;
        return $DB->get_records('block_instances', array('parentcontextid' => $contextid), 'blockname');
    }

    /**
     * List of available educational levels
     *
     * @param bool $any add option for "Any" (for search forms)
     * @return array
     */
    public static function educational_level_options($any = false) {
        $options = array();
        if ($any) {
            $options['all'] = get_string('any');
        }
        $options[self::HUB_EDULEVEL_PRIMARY] = get_string('edulevelprimary', 'hub');
        $options[self::HUB_EDULEVEL_SECONDARY] = get_string('edulevelsecondary', 'hub');
        $options[self::HUB_EDULEVEL_TERTIARY] = get_string('eduleveltertiary', 'hub');
        $options[self::HUB_EDULEVEL_GOVERNMENT] = get_string('edulevelgovernment', 'hub');
        $options[self::HUB_EDULEVEL_ASSOCIATION] = get_string('edulevelassociation', 'hub');
        $options[self::HUB_EDULEVEL_CORPORATE] = get_string('edulevelcorporate', 'hub');
        $options[self::HUB_EDULEVEL_OTHER] = get_string('edulevelother', 'hub');
        return $options;
    }

    /**
     * List of available audience options
     *
     * @param bool $any add option for "Any" (for search forms)
     * @return array
     */
    public static function audience_options($any = false) {
        $options = array();
        if ($any) {
            $options['all'] = get_string('any');
        }
        $options[self::HUB_AUDIENCE_EDUCATORS] = get_string('audienceeducators', 'hub');
        $options[self::HUB_AUDIENCE_STUDENTS] = get_string('audiencestudents', 'hub');
        $options[self::HUB_AUDIENCE_ADMINS] = get_string('audienceadmins', 'hub');
        return $options;
    }

    /**
     * Search for courses
     *
     * For the list of fields returned for each course see {@link communication::get_courses}
     *
     * @param string $search search string
     * @param bool $downloadable true - return downloadable courses, false - return enrollable courses
     * @param array|\stdClass $options other options from the list of allowed options:
     *              'ids', 'sitecourseids', 'coverage', 'licenceshortname', 'subject', 'audience',
     *              'educationallevel', 'language', 'orderby', 'givememore', 'allsitecourses'
     * @return array of two elements: [$courses, $coursetotal]
     */
    public static function search($search, $downloadable, $options) {
        try {
            return api::get_courses($search, $downloadable, !$downloadable, $options);
        } catch (moodle_exception $e) {
            \core\notification::add(get_string('errorcourselisting', 'block_community', $e->getMessage()),
                \core\output\notification::NOTIFY_ERROR);
            return [[], 0];
        }
    }

    /**
     * Retrieves information about published course
     *
     * For the list of fields returned for the course see {@link communication::get_courses}
     *
     * @param stdClass $publication
     * @return array|null
     */
    public static function get_published_course($publication) {
        try {
            list($courses, $unused) = api::get_courses('', !$publication->enrollable,
                $publication->enrollable, ['ids' => [$publication->hubcourseid], 'allsitecourses' => 1]);
            return reset($courses);
        } catch (\Exception $e) {
            \core\notification::add(get_string('errorcourseinfo', 'hub', $e->getMessage()),
                \core\output\notification::NOTIFY_ERROR);
        }
        return null;
    }

    /**
     * Downloads course backup and stores it in the user private files
     *
     * @param int $hubcourseid
     * @param string $coursename
     * @return array
     */
    public static function download_course_backup($hubcourseid, $coursename) {
        global $CFG, $USER;
        require_once($CFG->libdir . "/filelib.php");

        $backuptempdir = make_backup_temp_directory('');
        $filename = md5(time() . '-' . $hubcourseid . '-'. $USER->id . '-'. random_string(20));
        $path = $backuptempdir.'/'.$filename.".mbz";

        api::download_course_backup($hubcourseid, $path);

        $fs = get_file_storage();
        $record = new stdClass();
        $record->contextid = context_user::instance($USER->id)->id;
        $record->component = 'user';
        $record->filearea = 'private';
        $record->itemid = 0;
        $record->filename = urlencode($coursename).'_'.time().".mbz";
        $record->filepath = '/downloaded_backup/';
        if (!$fs->file_exists($record->contextid, $record->component,
            $record->filearea, 0, $record->filepath, $record->filename)) {
            $fs->create_file_from_pathname($record, $path);
        }

        return [$record->filepath . $record->filename, $filename];
    }

    /**
     * Uploads a course backup
     *
     * @param int $hubcourseid id of the published course on moodle.net, it must be published from this site
     * @param \stored_file $backupfile
     */
    public static function upload_course_backup($hubcourseid, \stored_file $backupfile) {
        api::upload_course_backup($hubcourseid, $backupfile);
    }
}