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

namespace core_course\output;

use cm_info;
use core_availability\info;
use core_completion\cm_completion_details;
use core_user;
use core_user\fields;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * The activity completion renderable class.
 *
 * @package    core_course
 * @copyright  2023 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_completion implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param cm_info $cminfo The course module information.
     * @param cm_completion_details $cmcompletion The course module information.
     */
    public function __construct(
        /** @var cm_info $cminfo the activity cm_info. */
        protected cm_info $cminfo,
        /** @var cm_completion_details $cmcompletion the activity completion details. */
        protected cm_completion_details $cmcompletion,
        /** @var bool $smallbutton if the button is rendered small (like in course page). */
        protected bool $smallbutton = true,
    ) {
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;

        $overallcompletion = $this->cmcompletion->get_overall_completion();
        $isoverallcomplete = $this->cmcompletion->is_overall_complete();
        $overrideby = $this->get_overrideby();
        $course = $this->cminfo->get_course();

        // Whether the completion of this activity controls the availability of other activities/sections in the course.
        // An activity with manual completion tracking which is used to enable access to other activities/sections in
        // the course needs to refresh the page after having its completion state toggled. This withavailability flag will enable
        // this functionality on the course homepage. Otherwise, the completion toggling will just happen normally via ajax.
        if ($this->cmcompletion->has_completion() && $this->cmcompletion->is_manual()) {
            $withavailability = !empty($CFG->enableavailability) && info::completion_value_used($course, $this->cminfo->id);
        }

        return (object) [
            'cmid' => $this->cminfo->id,
            'activityname' => $this->cminfo->get_formatted_name(),
            'uservisible' => $this->cminfo->uservisible,
            'hascompletion' => $this->cmcompletion->has_completion(),
            'isautomatic' => $this->cmcompletion->is_automatic(),
            'ismanual' => $this->cmcompletion->is_manual(),
            'showmanualcompletion' => $this->cmcompletion->show_manual_completion(),
            'istrackeduser' => $this->cmcompletion->is_tracked_user(),
            'overallcomplete' => $isoverallcomplete,
            'overallincomplete' => !$isoverallcomplete,
            'withavailability' => $withavailability ?? false,
            'overrideby' => $overrideby,
            'completiondetails' => $this->get_completion_details($overrideby),
            'accessibledescription' => $this->get_accessible_description($overrideby, $overallcompletion),
            // For backward compatibility, the template uses small button by default when not set normal size.
            'normalbutton' => !$this->smallbutton,
        ];
    }

    /**
     * Returns the name of the user overriding the completion condition, if available.
     *
     * @return string
     */
    private function get_overrideby(): string {
        $overrideby = $this->cmcompletion->overridden_by();
        if (!empty($overrideby)) {
            $userfields = fields::for_name();
            $overridebyrecord = core_user::get_user($overrideby, 'id ' . $userfields->get_sql()->selects, MUST_EXIST);
            return fullname($overridebyrecord);
        }
        return '';
    }

    /**
     * Returns automatic completion details
     *
     * @param string $overrideby The name of the user overriding the completion condition, if available.
     * @return array
     */
    private function get_completion_details($overrideby): array {
        $details = [];

        foreach ($this->cmcompletion->get_details() as $key => $detail) {
            $detail->key = $key;
            $detail->statuscomplete = in_array($detail->status, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS]);
            $detail->statuscompletefail = $detail->status == COMPLETION_COMPLETE_FAIL;
            // This is not used by core themes but may be needed in custom themes.
            $detail->statuscompletepass = $detail->status == COMPLETION_COMPLETE_PASS;
            $detail->statusincomplete = $detail->status == COMPLETION_INCOMPLETE;

            // Add an accessible description to be used for title and aria-label attributes for overridden completion details.
            if ($overrideby) {
                $setbydata = (object)[
                    'condition' => $detail->description,
                    'setby' => $overrideby,
                ];
                $overridestatus = $detail->statuscomplete ? 'done' : 'todo';
                $detail->accessibledescription = get_string('completion_setby:auto:' . $overridestatus, 'course', $setbydata);
            }

            unset($detail->status);
            $details[] = $detail;
        }
        return $details;
    }

    /**
     * Returns the accessible description for manual completions with overridden completion state.
     *
     * @param string $overrideby The name of the user overriding the completion condition, if available.
     * @param int $overallcompletion The overall completion state of the activity.
     * @return string
     */
    private function get_accessible_description($overrideby, $overallcompletion): string {
        if ($this->cmcompletion->is_manual() && $overrideby) {
            $setbydata = (object)[
                'activityname' => $this->cminfo->get_formatted_name(),
                'setby' => $overrideby,
            ];
            $isoverallcompleted = $overallcompletion == COMPLETION_COMPLETE;
            $setbylangkey = $isoverallcompleted ? 'completion_setby:manual:done' : 'completion_setby:manual:markdone';
            return get_string($setbylangkey, 'course', $setbydata);
        }
        return '';
    }
}
