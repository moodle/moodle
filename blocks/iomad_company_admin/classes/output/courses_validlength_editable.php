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

defined('MOODLE_INTERNAL') || die();

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_validlength_editable extends \core\output\inplace_editable {

    /** @var $context */
    private $context = null;

    /** @var \stdClass[] $viewableroles */
    private $roles;

    /** @var \stdClass[] $viewableroles */
    private $userroles;

    /** @var \stdClass[] $viewableroles */
    private $viewableroles;

    /** @var \stdClass[] $assignableroles */
    private $assignableroles;

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

        $value = json_encode($currentvalue);

        // Remember these for the display value.
        $this->context = $context;

        parent::__construct('block_iomad_company_admin', 'courses_validlength', $itemid, $editable, $value, $value);

    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $currentvalue = json_decode($this->value);

        
        $this->value = $currentvalue;
        $this->displayvalue = $currentvalue;

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
        $validlength = json_decode($newvalue);
        $validlength = clean_param($validlength, PARAM_INT);
        if ($validlength < 0) {
            $validlength = 0;
        }

        // Check user is enrolled in the course.
        $context = \context_system::instance();
        core_external::validate_context($context);

        // Check permissions.
        iomad::require_capability('block/iomad_company_admin:managecourses', $context);

        if (!$courserec = $DB->get_record('iomad_courses', ['courseid' => $courseid])) {
            throw new coding_exception('Course is not under IOMAD control');
        }

        // Do some sanity checking.
        if (empty($validlength) || $validlength < 0) {
            $validlength = $courserec->validlength;
        }

        // Process changes.
        $DB->set_field('iomad_courses', 'validlength', $validlength, ['courseid' => $courseid]);

        // Fire an event for this.
        $eventother = ['iomadcourse' => (array) $courserec];
        $event = \block_iomad_company_admin\event\company_course_updated::create(array('context' => $context,
                                                                                       'objectid' => $courseid,
                                                                                       'userid' => $USER->id,
                                                                                       'other' => $eventother));
        $event->trigger();

        return new self($company, $context, $courserec, $validlength);
    }
}
