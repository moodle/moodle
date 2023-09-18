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
 * File containing the class activity information renderable.
 *
 * @package    core_course
 * @copyright  2021 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\output;

defined('MOODLE_INTERNAL') || die();

use cm_info;
use completion_info;
use context;
use core\activity_dates;
use core_availability\info;
use core_completion\cm_completion_details;
use core_user;
use core_user\fields;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * The activity information renderable class.
 *
 * @deprecated since Moodle 4.3 MDL-78744
 * @todo MDL-78926 This class will be deleted in Moodle 4.7
 *
 * @package    core_course
 * @copyright  2021 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_information implements renderable, templatable {

    /** @var cm_info The course module information. */
    protected $cminfo = null;

    /** @var array The array of relevant dates for this activity. */
    protected $activitydates = [];

    /** @var cm_completion_details The user's completion details for this activity. */
    protected $cmcompletion = null;

    /**
     * Constructor.
     *
     * @deprecated since Moodle 4.3
     *
     * @param cm_info $cminfo The course module information.
     * @param cm_completion_details $cmcompletion The course module information.
     * @param array $activitydates The activity dates.
     */
    public function __construct(cm_info $cminfo, cm_completion_details $cmcompletion, array $activitydates) {
        debugging('activity_information class is deprecated. Use activity_completion and activity_dates instead.', DEBUG_DEVELOPER);
        $this->cminfo = $cminfo;
        $this->cmcompletion = $cmcompletion;
        $this->activitydates = $activitydates;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @deprecated since Moodle 4.3
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        debugging('activity_information class is deprecated. Use activity_completion and activity_dates instead.', DEBUG_DEVELOPER);

        $data = $this->build_completion_data();

        $data->cmid = $this->cminfo->id;
        $data->activityname = $this->cminfo->get_formatted_name();
        $this->build_dates_data($data);
        $data->hasdates = !empty($this->activitydates);

        return $data;
    }

    /**
     * Builds the dates data for export.
     *
     * @param stdClass $data
     */
    protected function build_dates_data(stdClass $data): void {
        foreach ($this->activitydates as $date) {
            if (empty($date['relativeto'])) {
                $date['datestring'] = userdate($date['timestamp'], get_string('strftimedaydatetime', 'core_langconfig'));
            } else {
                $diffstr = get_time_interval_string($date['timestamp'], $date['relativeto']);
                if ($date['timestamp'] >= $date['relativeto']) {
                    $date['datestring'] = get_string('relativedatessubmissionduedateafter', 'core_course',
                        ['datediffstr' => $diffstr]);
                } else {
                    $date['datestring'] = get_string('relativedatessubmissionduedatebefore', 'core_course',
                        ['datediffstr' => $diffstr]);
                }
            }
            $data->activitydates[] = $date;
        }
    }

    /**
     * Builds the completion data for export.
     *
     * @return stdClass
     */
    protected function build_completion_data(): stdClass {
        global $CFG;

        $data = new stdClass();

        $data->hascompletion = $this->cmcompletion->has_completion();
        $data->isautomatic = $this->cmcompletion->is_automatic();
        $data->ismanual = $this->cmcompletion->is_manual();
        $data->showmanualcompletion = $this->cmcompletion->show_manual_completion();

        // Get the name of the user overriding the completion condition, if available.
        $data->overrideby = null;
        $overrideby = $this->cmcompletion->overridden_by();
        $overridebyname = null;
        if (!empty($overrideby)) {
            $userfields = fields::for_name();
            $overridebyrecord = core_user::get_user($overrideby, 'id ' . $userfields->get_sql()->selects, MUST_EXIST);
            $data->overrideby = fullname($overridebyrecord);
        }

        // We'll show only the completion conditions and not the completion status if we're not tracking completion for this user
        // (e.g. a teacher, admin).
        $data->istrackeduser = $this->cmcompletion->is_tracked_user();

        // Overall completion states.
        $overallcompletion = $this->cmcompletion->get_overall_completion();
        $data->overallcomplete = $overallcompletion == COMPLETION_COMPLETE;
        $data->overallincomplete = $overallcompletion == COMPLETION_INCOMPLETE;

        // Set an accessible description for manual completions with overridden completion state.
        if (!$data->isautomatic && $data->overrideby) {
            $setbydata = (object)[
                'activityname' => $this->cminfo->get_formatted_name(),
                'setby' => $data->overrideby,
            ];
            $setbylangkey = $data->overallcomplete ? 'completion_setby:manual:done' : 'completion_setby:manual:markdone';
            $data->accessibledescription = get_string($setbylangkey, 'course', $setbydata);
        }

        // Whether the completion of this activity controls the availability of other activities/sections in the course.
        $data->withavailability = false;
        $course = $this->cminfo->get_course();
        // An activity with manual completion tracking which is used to enable access to other activities/sections in
        // the course needs to refresh the page after having its completion state toggled. This withavailability flag will enable
        // this functionality on the course homepage. Otherwise, the completion toggling will just happen normally via ajax.
        if ($this->cmcompletion->has_completion() && !$this->cmcompletion->is_automatic()) {
            $data->withavailability = !empty($CFG->enableavailability) && info::completion_value_used($course, $this->cminfo->id);
        }

        // Whether this activity is visible to the user. If not, completion information will not be shown.
        $data->uservisible = $this->cminfo->uservisible;

        // Build automatic completion details.
        $details = [];
        foreach ($this->cmcompletion->get_details() as $key => $detail) {
            // Set additional attributes for the template.
            $detail->key = $key;
            $detail->statuscomplete = in_array($detail->status, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS]);
            $detail->statuscompletefail = $detail->status == COMPLETION_COMPLETE_FAIL;
            // This is not used by core themes but may be needed in custom themes.
            $detail->statuscompletepass = $detail->status == COMPLETION_COMPLETE_PASS;
            $detail->statusincomplete = $detail->status == COMPLETION_INCOMPLETE;

            // Add an accessible description to be used for title and aria-label attributes for overridden completion details.
            if ($data->overrideby) {
                $setbydata = (object)[
                    'condition' => $detail->description,
                    'setby' => $data->overrideby,
                ];
                $overridestatus = $detail->statuscomplete ? 'done' : 'todo';
                $detail->accessibledescription = get_string('completion_setby:auto:' . $overridestatus, 'course', $setbydata);
            }

            // We don't need the status in the template.
            unset($detail->status);

            $details[] = $detail;
        }
        $data->completiondetails = $details;

        return $data;
    }
}
