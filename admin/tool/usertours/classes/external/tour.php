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
 * Web Service functions for steps.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use tool_usertours\tour as tourinstance;
use tool_usertours\step;

/**
 * Web Service functions for steps.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tour extends external_api {
    /**
     * Fetch the tour configuration for the specified tour.
     *
     * @param   int     $tourid     The ID of the tour to fetch.
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $pageurl    The path of the current page.
     * @return  array               As described in fetch_and_start_tour_returns
     */
    public static function fetch_and_start_tour($tourid, $context, $pageurl) {
        global $PAGE;

        $params = self::validate_parameters(self::fetch_and_start_tour_parameters(), [
                'tourid'    => $tourid,
                'context'   => $context,
                'pageurl'   => $pageurl,
            ]);

        $context = \context_helper::instance_by_id($params['context']);
        self::validate_context($context);

        $tour = tourinstance::instance($params['tourid']);
        if (!$tour->should_show_for_user()) {
            return [];
        }

        $touroutput = new \tool_usertours\output\tour($tour);

        \tool_usertours\event\tour_started::create([
            'contextid' => $context->id,
            'objectid'  => $tour->get_id(),
            'other'     => [
                'pageurl' => $params['pageurl'],
            ],
        ])->trigger();

        return [
            'tourconfig' => $touroutput->export_for_template($PAGE->get_renderer('core')),
        ];
    }

    /**
     * The parameters for fetch_and_start_tour.
     *
     * @return external_function_parameters
     */
    public static function fetch_and_start_tour_parameters() {
        return new external_function_parameters([
            'tourid'    => new external_value(PARAM_INT, 'Tour ID'),
            'context'   => new external_value(PARAM_INT, 'Context ID'),
            'pageurl'   => new external_value(PARAM_URL, 'Page URL'),
        ]);
    }

    /**
     * The return configuration for fetch_and_start_tour.
     *
     * @return external_single_structure
     */
    public static function fetch_and_start_tour_returns() {
        return new external_single_structure([
            'tourconfig'    => new external_single_structure([
                'name'      => new external_value(PARAM_RAW, 'Tour Name'),
                'steps'     => new external_multiple_structure(self::step_structure_returns()),
            ], 'Tour config', VALUE_OPTIONAL)
        ]);
    }

    /**
     * Reset the specified tour for the current user.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $pageurl    The path of the current page requesting the reset.
     * @return  array               As described in reset_tour_returns
     */
    public static function reset_tour($tourid, $context, $pageurl) {
        $params = self::validate_parameters(self::reset_tour_parameters(), [
                'tourid'    => $tourid,
                'context'   => $context,
                'pageurl'   => $pageurl,
            ]);

        $context = \context_helper::instance_by_id($params['context']);
        self::validate_context($context);

        $tour = tourinstance::instance($params['tourid']);
        $tour->request_user_reset();

        $result = [];

        $matchingtours = \tool_usertours\manager::get_matching_tours(new \moodle_url($params['pageurl']));
        foreach ($matchingtours as $match) {
            if ($tour->get_id() === $match->get_id()) {
                $result['startTour'] = $tour->get_id();

                \tool_usertours\event\tour_reset::create([
                    'contextid' => $context->id,
                    'objectid'  => $params['tourid'],
                    'other'     => [
                        'pageurl'   => $params['pageurl'],
                    ],
                ])->trigger();
                break;
            }
        }

        return $result;
    }

    /**
     * The parameters for reset_tour.
     *
     * @return external_function_parameters
     */
    public static function reset_tour_parameters() {
        return new external_function_parameters([
            'tourid'    => new external_value(PARAM_INT, 'Tour ID'),
            'context'   => new external_value(PARAM_INT, 'Context ID'),
            'pageurl'   => new external_value(PARAM_URL, 'Current page location'),
        ]);
    }

    /**
     * The return configuration for reset_tour.
     *
     * @return external_single_structure
     */
    public static function reset_tour_returns() {
        return new external_single_structure([
            'startTour'     => new external_value(PARAM_INT, 'Tour ID', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Mark the specified tour as completed for the current user.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $pageurl    The path of the current page.
     * @param   int     $stepid     The step id
     * @param   int     $stepindex  The step index
     * @return  array               As described in complete_tour_returns
     */
    public static function complete_tour($tourid, $context, $pageurl, $stepid, $stepindex) {
        $params = self::validate_parameters(self::complete_tour_parameters(), [
                'tourid'    => $tourid,
                'context'   => $context,
                'pageurl'   => $pageurl,
                'stepid'    => $stepid,
                'stepindex' => $stepindex,
            ]);

        $context = \context_helper::instance_by_id($params['context']);
        self::validate_context($context);

        $tour = tourinstance::instance($params['tourid']);
        $tour->mark_user_completed();

        \tool_usertours\event\tour_ended::create([
            'contextid' => $context->id,
            'objectid'  => $params['tourid'],
            'other'     => [
                'pageurl'   => $params['pageurl'],
                'stepid'    => $params['stepid'],
                'stepindex' => $params['stepindex'],
            ],
        ])->trigger();

        return [];
    }

    /**
     * The parameters for complete_tour.
     *
     * @return external_function_parameters
     */
    public static function complete_tour_parameters() {
        return new external_function_parameters([
            'tourid'    => new external_value(PARAM_INT, 'Tour ID'),
            'context'   => new external_value(PARAM_INT, 'Context ID'),
            'pageurl'   => new external_value(PARAM_LOCALURL, 'Page URL'),
            'stepid'    => new external_value(PARAM_INT, 'Step ID'),
            'stepindex' => new external_value(PARAM_INT, 'Step Number'),
        ]);
    }

    /**
     * The return configuration for complete_tour.
     *
     * @return external_single_structure
     */
    public static function complete_tour_returns() {
        return new external_single_structure([]);
    }

    /**
     * Mark the specified toru step as shown for the current user.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   int     $context    The Context ID of the current page.
     * @param   string  $pageurl    The path of the current page.
     * @param   int     $stepid     The step id
     * @param   int     $stepindex  The step index
     * @return  array               As described in complete_tour_returns
     */
    public static function step_shown($tourid, $context, $pageurl, $stepid, $stepindex) {
        $params = self::validate_parameters(self::step_shown_parameters(), [
                'tourid'    => $tourid,
                'context'   => $context,
                'pageurl'   => $pageurl,
                'stepid'    => $stepid,
                'stepindex' => $stepindex,
            ]);

        $context = \context_helper::instance_by_id($params['context']);
        self::validate_context($context);

        $step = step::instance($params['stepid']);
        if ($step->get_tourid() != $params['tourid']) {
            throw new \moodle_exception('Incorrect tour specified.');
        }

        \tool_usertours\event\step_shown::create([
            'contextid' => $context->id,
            'objectid'  => $params['stepid'],

            'other'     => [
                'pageurl'   => $params['pageurl'],
                'tourid'    => $params['tourid'],
                'stepindex' => $params['stepindex'],
            ],
        ])->trigger();

        return [];
    }

    /**
     * The parameters for step_shown.
     *
     * @return external_function_parameters
     */
    public static function step_shown_parameters() {
        return new external_function_parameters([
            'tourid'    => new external_value(PARAM_INT, 'Tour ID'),
            'context'   => new external_value(PARAM_INT, 'Context ID'),
            'pageurl'   => new external_value(PARAM_URL, 'Page URL'),
            'stepid'    => new external_value(PARAM_INT, 'Step ID'),
            'stepindex' => new external_value(PARAM_INT, 'Step Number'),
        ]);
    }

    /**
     * The return configuration for step_shown.
     *
     * @return external_single_structure
     */
    public static function step_shown_returns() {
        return new external_single_structure([]);
    }

    /**
     * The standard return structure for a step.
     *
     * @return external_multiple_structure
     */
    public static function step_structure_returns() {
        return new external_single_structure([
            'title'             => new external_value(PARAM_RAW,
                    'Step Title'),
            'content'           => new external_value(PARAM_RAW,
                    'Step Content'),
            'element'           => new external_value(PARAM_TEXT,
                    'Step Target'),
            'placement'         => new external_value(PARAM_TEXT,
                    'Step Placement'),
            'delay'             => new external_value(PARAM_INT,
                    'Delay before showing the step (ms)', VALUE_OPTIONAL),
            'backdrop'          => new external_value(PARAM_BOOL,
                    'Whether a backdrop should be used', VALUE_OPTIONAL),
            'reflex'            => new external_value(PARAM_BOOL,
                    'Whether to move to the next step when the target element is clicked', VALUE_OPTIONAL),
            'orphan'            => new external_value(PARAM_BOOL,
                    'Whether to display the step even if it could not be found', VALUE_OPTIONAL),
            'stepid'            => new external_value(PARAM_INT,
                    'The actual ID of the step', VALUE_OPTIONAL),
        ]);
    }
}
