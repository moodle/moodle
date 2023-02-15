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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\output;

use context_course;
use core_user;
use core_external;
use coding_exception;
use company;
use iomad;
use cache_helper;

defined('MOODLE_INTERNAL') || die();

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_shared_editable extends \core\output\inplace_editable {

    /** @var $context */
    private $context = null;

    /** @var \stdClass[] $viewableroles */
    private $sharedoptions;

    /**
     * Constructor.
     *
     * @param \stdClass $course The current course
     * @param \context $context The course context
     * @param \stdClass $user The current user
     * @param \stdClass[] $courseroles The list of course roles.
     * @param \stdClass[] $assignableroles The list of assignable roles in this course.
     * @param \stdClass[] $profileroles The list of roles that should be visible in a users profile.
     * @param \stdClass[] $userroles The list of user roles.
     */
    public function __construct($company, $context, $course, $currentvalue) {

        // Check capabilities to get editable value.
        $editable = iomad::has_capability('block/iomad_company_admin:managecourses', $context);

        // Invent an itemid.
        $itemid = $company->id . ':' . $course->courseid;

        $value = $currentvalue;

        // Remember these for the display value.
        $this->sharedoptions = ['0' => get_string('no'),
                                 '1' => get_string('open', 'block_iomad_company_admin'),
                                 '2' => get_string('closed', 'block_iomad_company_admin')];

        $this->context = $context;

        parent::__construct('block_iomad_company_admin', 'courses_shared', $itemid, $editable, $value, $value);

        $this->set_type_select($this->sharedoptions);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $value = json_decode($this->value);

        $this->displayvalue = format_string($this->sharedoptions[$value], true, ['context' => $this->context]);

        return parent::export_for_template($output);
    }

    /**
     * Updates the value in database and returns itself, called from inplace_editable callback
     *
     * @param int $itemid
     * @param mixed $newvalue
     * @return \self
     */
    public static function update($itemid, $newvalue) {
        global $DB, $CFG, $USER;

        require_once($CFG->libdir . '/external/externallib.php');
        // Check caps.
        // Do the thing.
        // Return one of me.
        // Validate the inputs.
        list($companyid, $courseid) = explode(':', $itemid, 2);

        $companyid = clean_param($companyid, PARAM_INT);
        $company = new company($companyid);
        $courseid = clean_param($courseid, PARAM_INT);
        $shared = json_decode($newvalue);
        $shared = clean_param($shared, PARAM_INT);

        // Check user is enrolled in the course.
        $context = \context_system::instance();
        core_external::validate_context($context);

        // Check permissions.
        iomad::require_capability('block/iomad_company_admin:managecourses', $context);

        if (!$courserec = $DB->get_record('iomad_courses', ['courseid' => $courseid])) {
            throw new coding_exception('Course is not under IOMAD control');
        }

        // Store the previous value for this course. 
        $previousshared = $courserec->shared
;
        // Check if we are sharing a course for the first time.
        if ($previousshared == 0 && $shared != 0) { // Turning sharing on.
            $courseinfo = $DB->get_record('course', array('id' => $courseid));

            // Set the shared options on.
            $courseinfo->groupmode = 1;
            $courseinfo->groupmodeforce = 1;
            $DB->update_record('course', $courseinfo);
            $courserec->shared = $shared;
            $DB->update_record('iomad_courses', $courserec);

            // Deal with any current enrolments.
            if ($companycourses = $DB->get_records('company_course', array('courseid' => $courseid))) {
                foreach ($companycourses as $companycourse) {
                    if ($shared == 2) {
                        $sharingrecord = (object) [];
                        $sharingrecord->courseid = $courseid;
                        $sharingrecord->companyid = $companycourse->companyid;
                        $DB->insert_record('company_shared_courses', $sharingrecord);
                    }
                    company::company_users_to_company_course_group($companycourse->companyid, $courseid);
                }
            }
        } else if ($shared == 0 and $previousshared != 0) { // Turning sharing off.
            $courseinfo = $DB->get_record('course', array('id' => $courseid));
            // Set the shared options on.
            $courseinfo->groupmode = 0;
            $courseinfo->groupmodeforce = 0;
            $DB->update_record('course', $courseinfo);

            // Deal with enrolments.
            if ($companygroups = $DB->get_records('company_course_groups', array('courseid' => $courseid))) {
                // Got companies using it.
                $count = 1;

                // Skip the first company, it was the one who had it before anyone else so is
                // assumed to be the owning company.
                foreach ($companygroups as $companygroup) {
                    if ($count == 1) {
                        continue;
                    }
                    $count ++;
                    company::unenrol_company_from_course($companygroup->companyid, $courseid);
                }
            }
        } else {
            // Changing from open sharing to closed sharing.
            if ($companygroups = $DB->get_records('company_course_groups', array('courseid' => $courseid))) {
                // Got companies using it.
                foreach ($companygroups as $companygroup) {
                    $sharingrecord = (object) [];
                    $sharingrecord->courseid = $courseid;
                    $sharingrecord->companyid = $companygroup->companyid;
                    $DB->insert_record('company_shared_courses', $sharingrecord);
                }
            }
        }

        // Process changes.
        $DB->set_field('iomad_courses', 'shared', $shared, ['courseid' => $courseid]);

        // Fire an event for this.
        $eventother = ['iomadcourse' => (array) $courserec];
        $event = \block_iomad_company_admin\event\company_course_updated::create(array('context' => $context,
                                                                                       'objectid' => $courseid,
                                                                                       'userid' => $USER->id,
                                                                                       'other' => $eventother));
        $event->trigger();

        // Clear the caches.
        cache_helper::purge_by_event('changesincompanycourses');

        return new self($company, $context, $courserec, $shared);
    }
}
