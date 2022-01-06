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
 * Rating external functions utility class.
 *
 * @package    core_rating
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_rating\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/rating/lib.php');
require_once($CFG->libdir . '/externallib.php');

use external_multiple_structure;
use external_single_structure;
use external_value;
use rating_manager;
use stdClass;

/**
 * Rating external functions utility class.
 *
 * @package   core_rating
 * @copyright 2017 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 3.4
 */
class util {

    /**
     * Returns the ratings definition for external functions.
     */
    public static function external_ratings_structure() {

        return new external_single_structure (
            [
                'contextid' => new external_value(PARAM_INT, 'Context id.'),
                'component' => new external_value(PARAM_COMPONENT, 'Context name.'),
                'ratingarea' => new external_value(PARAM_AREA, 'Rating area name.'),
                'canviewall' => new external_value(PARAM_BOOL, 'Whether the user can view all the individual ratings.',
                    VALUE_OPTIONAL),
                'canviewany' => new external_value(PARAM_BOOL, 'Whether the user can view aggregate of ratings of others.',
                    VALUE_OPTIONAL),
                'scales' => new external_multiple_structure(
                    new external_single_structure (
                        [
                            'id' => new external_value(PARAM_INT, 'Scale id.'),
                            'courseid' => new external_value(PARAM_INT, 'Course id.', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_TEXT, 'Scale name (when a real scale is used).', VALUE_OPTIONAL),
                            'max' => new external_value(PARAM_INT, 'Max value for the scale.'),
                            'isnumeric' => new external_value(PARAM_BOOL, 'Whether is a numeric scale.'),
                            'items' => new external_multiple_structure(
                                new external_single_structure (
                                    [
                                        'value' => new external_value(PARAM_INT, 'Scale value/option id.'),
                                        'name' => new external_value(PARAM_NOTAGS, 'Scale name.'),
                                    ]
                                ), 'Scale items. Only returned for not numerical scales.', VALUE_OPTIONAL
                            )
                        ], 'Scale information'
                    ), 'Different scales used information', VALUE_OPTIONAL
                ),
                'ratings' => new external_multiple_structure(
                    new external_single_structure (
                        [
                            'itemid' => new external_value(PARAM_INT, 'Item id.'),
                            'scaleid' => new external_value(PARAM_INT, 'Scale id.', VALUE_OPTIONAL),
                            'userid' => new external_value(PARAM_INT, 'User who rated id.', VALUE_OPTIONAL),
                            'aggregate' => new external_value(PARAM_FLOAT, 'Aggregated ratings grade.', VALUE_OPTIONAL),
                            'aggregatestr' => new external_value(PARAM_NOTAGS, 'Aggregated ratings as string.', VALUE_OPTIONAL),
                            'aggregatelabel' => new external_value(PARAM_NOTAGS, 'The aggregation label.', VALUE_OPTIONAL),
                            'count' => new external_value(PARAM_INT, 'Ratings count (used when aggregating).', VALUE_OPTIONAL),
                            'rating' => new external_value(PARAM_INT, 'The rating the user gave.', VALUE_OPTIONAL),
                            'canrate' => new external_value(PARAM_BOOL, 'Whether the user can rate the item.', VALUE_OPTIONAL),
                            'canviewaggregate' => new external_value(PARAM_BOOL, 'Whether the user can view the aggregated grade.',
                                VALUE_OPTIONAL),
                        ]
                    ), 'The ratings', VALUE_OPTIONAL
                ),
            ], 'Rating information', VALUE_OPTIONAL
        );
    }

    /**
     * Returns rating information inside a data structure like the one defined by external_ratings_structure.
     *
     * @param  stdClass $mod        course module object
     * @param  stdClass $context    context object
     * @param  str $component       component name
     * @param  str $ratingarea      rating area
     * @param  array $items         items to add ratings
     * @return array ratings ready to be returned by external functions.
     */
    public static function get_rating_info($mod, $context, $component, $ratingarea, $items) {
        global $USER;

        $ratinginfo = [
            'contextid' => $context->id,
            'component' => $component,
            'ratingarea' => $ratingarea,
            'canviewall' => null,
            'canviewany' => null,
            'scales' => [],
            'ratings' => [],
        ];
        if ($mod->assessed != RATING_AGGREGATE_NONE) {
            $ratingoptions = new stdClass;
            $ratingoptions->context = $context;
            $ratingoptions->component = $component;
            $ratingoptions->ratingarea = $ratingarea;
            $ratingoptions->items = $items;
            $ratingoptions->aggregate = $mod->assessed;
            $ratingoptions->scaleid = $mod->scale;
            $ratingoptions->userid = $USER->id;
            $ratingoptions->assesstimestart = $mod->assesstimestart;
            $ratingoptions->assesstimefinish = $mod->assesstimefinish;

            $rm = new rating_manager();
            $allitems = $rm->get_ratings($ratingoptions);

            foreach ($allitems as $item) {
                if (empty($item->rating)) {
                    continue;
                }
                $rating = [
                    'itemid' => $item->rating->itemid,
                    'scaleid' => $item->rating->scaleid,
                    'userid' => $item->rating->userid,
                    'rating' => $item->rating->rating,
                    'canrate' => $item->rating->user_can_rate(),
                    'canviewaggregate' => $item->rating->user_can_view_aggregate(),
                ];
                // Fill the capabilities fields the first time (the rest are the same values because they are not item dependent).
                if ($ratinginfo['canviewall'] === null) {
                    $ratinginfo['canviewall'] = $item->rating->settings->permissions->viewall &&
                                                    $item->rating->settings->pluginpermissions->viewall;
                    $ratinginfo['canviewany'] = $item->rating->settings->permissions->viewany &&
                                                    $item->rating->settings->pluginpermissions->viewany;
                }

                // Return only the information the user can see.
                if ($rating['canviewaggregate']) {
                    $rating['aggregate'] = $item->rating->aggregate;
                    $rating['aggregatestr'] = $item->rating->get_aggregate_string();
                    $rating['aggregatelabel'] = $rm->get_aggregate_label($item->rating->settings->aggregationmethod);
                    $rating['count'] = $item->rating->count;
                }
                // If the user can rate, return the scale information only one time.
                if ($rating['canrate'] &&
                        !empty($item->rating->settings->scale->id) &&
                        !isset($ratinginfo['scales'][$item->rating->settings->scale->id])) {
                    $scale = $item->rating->settings->scale;
                    // Return only non numeric scales (to avoid return lots of data just including items from 0 to $scale->max).
                    if (!$scale->isnumeric) {
                        $scaleitems = [];
                        foreach ($scale->scaleitems as $value => $name) {
                            $scaleitems[] = [
                                'name' => $name,
                                'value' => $value,
                            ];
                        }
                        $scale->items = $scaleitems;
                    }
                    $ratinginfo['scales'][$item->rating->settings->scale->id] = (array) $scale;
                }
                $ratinginfo['ratings'][] = $rating;
            }
        }
        return $ratinginfo;
    }
}
