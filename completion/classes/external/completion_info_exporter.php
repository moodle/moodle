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

declare(strict_types=1);

namespace core_completion\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;

/**
 * Completion info exporter
 *
 * @package    core_completion
 * @copyright  2021 Dongsheng Cai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_info_exporter extends \core\external\exporter {
    /**
     * @var object $course moodle course object
     */
    private $course;
    /**
     * @var object|cm_info $cm course module info
     */
    private $cminfo;
    /**
     * @var int $userid user id
     */
    private $userid;

    /**
     * Constructor for the completion info exporter.
     *
     * @param object $course course object
     * @param object|cm_info $cm course module info
     * @param int $userid user id
     * @param array $related related values
     */
    public function __construct(object $course, object $cm, int $userid, array $related = []) {
        $this->course = $course;
        $this->cminfo = \cm_info::create($cm);
        $this->userid = $userid;
        parent::__construct([], $related);
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output): array {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        $cmcompletion = \core_completion\cm_completion_details::get_instance($this->cminfo, $this->userid);
        $cmcompletiondetails = $cmcompletion->get_details();

        $details = [];
        foreach ($cmcompletiondetails as $rulename => $rulevalue) {
            $details[] = [
                'rulename' => $rulename,
                'rulevalue' => (array)$rulevalue,
            ];
        }
        // Temporary fix for 4.3 only to return via state COMPLETION_COMPLETE depending on the current state and overall status.
        $state = $cmcompletion->get_overall_completion();
        if ($state == COMPLETION_COMPLETE_FAIL && $cmcompletion->is_overall_complete()) {
            $state = COMPLETION_COMPLETE;
        }

        return [
            'state'         => $state,
            'timecompleted' => $cmcompletion->get_timemodified(),
            'overrideby'    => $cmcompletion->overridden_by(),
            'valueused'     => \core_availability\info::completion_value_used($this->course, $this->cminfo->id),
            'hascompletion'    => $cmcompletion->has_completion(),
            'isautomatic'      => $cmcompletion->is_automatic(),
            'istrackeduser'    => $cmcompletion->is_tracked_user(),
            'overallstatus'    => $cmcompletion->get_overall_completion(),
            'uservisible'      => $this->cminfo->uservisible,
            'details'          => $details,
        ];
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array Keys are the property names, and value their definition.
     */
    public static function define_other_properties(): array {
        return [
            'state' => [
                'type' => PARAM_INT,
                'description' => 'overall completion state of this course module.',
            ],
            'timecompleted' => [
                'type' => PARAM_INT,
                'description' => 'course completion timestamp.',
            ],
            'overrideby' => [
                'type' => PARAM_INT,
                'description' => 'user ID that has overridden the completion state of this activity for the user.',
                'null' => NULL_ALLOWED,
            ],
            'valueused' => [
                'type' => PARAM_BOOL,
                'description' => 'True if module is used in a condition, false otherwise.',
            ],
            'hascompletion' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether this activity module has completion enabled.'
            ],
            'isautomatic' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether this activity module instance tracks completion automatically.'
            ],
            'istrackeduser' => [
                'type' => PARAM_BOOL,
                'description' => 'Checks whether completion is being tracked for this user.'
            ],
            'uservisible' => [
                'type' => PARAM_BOOL,
                'description' => 'Whether this activity is visible to user.'
            ],
            'details' => [
                'multiple' => true,
                'description' => 'An array of completion details containing the description and status.',
                'type' => [
                    'rulename' => [
                        'type' => PARAM_TEXT,
                    ],
                    'rulevalue' => [
                        'type' => [
                            'status' => [
                                'type' => PARAM_INT,
                            ],
                            'description' => [
                                'type' => PARAM_TEXT,
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
