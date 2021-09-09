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
 * Contains the class for building the user's activity completion details.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_completion;

use cm_info;
use completion_info;

/**
 * Class for building the user's activity completion details.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm_completion_details {
    /** @var completion_info The completion info instance for this cm's course. */
    protected $completioninfo = null;

    /** @var object The completion data. */
    protected $completiondata = null;

    /** @var cm_info The course module information. */
    protected $cminfo = null;

    /** @var int The user ID. */
    protected $userid = 0;

    /** @var bool Whether to return automatic completion details. */
    protected $returndetails = true;

    /** @var activity_custom_completion Activity custom completion object. */
    protected $cmcompletion = null;

    /**
     * Constructor.
     *
     * @param completion_info $completioninfo The completion info instance for this cm's course.
     * @param cm_info $cminfo The course module information.
     * @param int $userid The user ID.
     * @param bool $returndetails Whether to return completion details or not.
     */
    public function __construct(completion_info $completioninfo, cm_info $cminfo, int $userid, bool $returndetails = true) {
        $this->completioninfo = $completioninfo;
        // We need to pass wholecourse = true here for better performance. All the course's completion data for the current
        // logged-in user will get in a single query instead of multiple queries and loaded to cache.
        $this->completiondata = $completioninfo->get_data($cminfo, true, $userid);
        $this->cminfo = $cminfo;
        $this->userid = $userid;
        $this->returndetails = $returndetails;
        $cmcompletionclass = activity_custom_completion::get_cm_completion_class($this->cminfo->modname);
        if ($cmcompletionclass) {
            $this->cmcompletion = new $cmcompletionclass($this->cminfo, $this->userid);
        }
    }

    /**
     * Fetches the completion details for a user.
     *
     * @return array An array of completion details for a user containing the completion requirement's description and status.
     * @throws \coding_exception
     */
    public function get_details(): array {
        if (!$this->is_automatic()) {
            // No details need to be returned for modules that don't have automatic completion tracking enabled.
            return [];
        }

        if (!$this->returndetails) {
            // We don't need to return completion details.
            return [];
        }

        $completiondata = $this->completiondata;
        $hasoverride = !empty($this->overridden_by());

        $details = [];

        // Completion rule: Student must view this activity.
        if (!empty($this->cminfo->completionview)) {
            if (!$hasoverride) {
                $status = COMPLETION_INCOMPLETE;
                if ($completiondata->viewed == COMPLETION_VIEWED) {
                    $status = COMPLETION_COMPLETE;
                }
            } else {
                $status = $completiondata->completionstate;
            }

            $details['completionview'] = (object)[
                'status' => $status,
                'description' => get_string('detail_desc:view', 'completion'),
            ];
        }

        // Completion rule: Student must receive a grade.
        if (!is_null($this->cminfo->completiongradeitemnumber)) {
            if (!$hasoverride) {
                $status = $completiondata->completiongrade ?? COMPLETION_INCOMPLETE;
            } else {
                $status = $completiondata->completionstate;
            }

            $details['completionusegrade'] = (object)[
                'status' => $status,
                'description' => get_string('detail_desc:receivegrade', 'completion'),
            ];
        }

        if ($this->cmcompletion) {
            if (isset($completiondata->customcompletion)) {
                foreach ($completiondata->customcompletion as $rule => $status) {
                    $details[$rule] = (object)[
                        'status' => !$hasoverride ? $status : $completiondata->completionstate,
                        'description' => $this->cmcompletion->get_custom_rule_description($rule),
                    ];
                }

                $details = $this->sort_completion_details($details);
            }
        } else {
            if (function_exists($this->cminfo->modname . '_get_completion_state')) {
                // If the plugin does not have the custom completion implementation but implements the get_completion_state() callback,
                // fallback to displaying the overall completion state of the activity.
                $details = [
                    'plugincompletionstate' => (object)[
                        'status' => $this->get_overall_completion(),
                        'description' => get_string('completeactivity', 'completion')
                    ]
                ];
            }
        }

        return $details;
    }

    /**
     * Sort completion details in the order specified by the activity's custom completion implementation.
     *
     * @param array $details The completion details to be sorted.
     * @return array
     * @throws \coding_exception
     */
    protected function sort_completion_details(array $details): array {
        $sortorder = $this->cmcompletion->get_sort_order();
        $sorteddetails = [];

        foreach ($sortorder as $sortedkey) {
            if (isset($details[$sortedkey])) {
                $sorteddetails[$sortedkey] = $details[$sortedkey];
            }
        }

        // Make sure the sorted list includes all of the conditions that were set.
        if (count($sorteddetails) < count($details)) {
            $exceptiontext = get_class($this->cmcompletion) .'::get_sort_order() is missing one or more completion conditions.' .
                ' All custom and standard conditions that apply to this activity must be listed.';
            throw new \coding_exception($exceptiontext);
        }

        return $sorteddetails;
    }

    /**
     * Fetches the overall completion state of this course module.
     *
     * @return int The overall completion state for this course module.
     */
    public function get_overall_completion(): int {
        return (int)$this->completiondata->completionstate;
    }

    /**
     * Whether this activity module has completion enabled.
     *
     * @return bool
     */
    public function has_completion(): bool {
        return $this->completioninfo->is_enabled($this->cminfo) != COMPLETION_DISABLED;
    }

    /**
     * Whether this activity module instance tracks completion automatically.
     *
     * @return bool
     */
    public function is_automatic(): bool {
        return $this->cminfo->completion == COMPLETION_TRACKING_AUTOMATIC;
    }

    /**
     * Fetches the user ID that has overridden the completion state of this activity for the user.
     *
     * @return int|null
     */
    public function overridden_by(): ?int {
        return isset($this->completiondata->overrideby) ? (int)$this->completiondata->overrideby : null;
    }

    /**
     * Checks whether completion is being tracked for this user.
     *
     * @return bool
     */
    public function is_tracked_user(): bool {
        return $this->completioninfo->is_tracked_user($this->userid);
    }

    /**
     * Determine whether to show the manual completion or not.
     *
     * @return bool
     */
    public function show_manual_completion(): bool {
        global $PAGE;

        if ($PAGE->context->contextlevel == CONTEXT_MODULE) {
            // Manual completion should always be shown on the activity page.
            return true;
        } else {
            $course = $this->cminfo->get_course();
            if ($course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS) {
                return true;
            } else if ($this->cmcompletion) {
                return $this->cmcompletion->manual_completion_always_shown();
            }
        }

        return false;
    }

    /**
     * Completion state timemodified
     *
     * @return int timestamp
     */
    public function get_timemodified(): int {
        return (int)$this->completiondata->timemodified;
    }

    /**
     * Generates an instance of this class.
     *
     * @param cm_info $cminfo The course module info instance.
     * @param int $userid The user ID that we're fetching completion details for.
     * @param bool $returndetails  Whether to return completion details or not.
     * @return cm_completion_details
     */
    public static function get_instance(cm_info $cminfo, int $userid, bool $returndetails = true): cm_completion_details {
        $course = $cminfo->get_course();
        $completioninfo = new \completion_info($course);
        return new self($completioninfo, $cminfo, $userid, $returndetails);
    }
}
