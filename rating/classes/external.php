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
 * Completion external API
 *
 * @package    core_rating
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/rating/lib.php");

/**
 * Completion external functions
 *
 * @package    core_rating
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_rating_external extends external_api {

    /**
     * Returns description of get_item_ratings parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_item_ratings_parameters() {
        return new external_function_parameters (
            array(
                'contextlevel' => new external_value(PARAM_ALPHA, 'contextlevel'),
                'instanceid'  => new external_value(PARAM_INT, 'The Instance id of item associated with the context level'),
                'component'   => new external_value(PARAM_COMPONENT, 'component'),
                'ratingarea'  => new external_value(PARAM_AREA, 'Rating area', VALUE_DEFAULT, ''),
                'itemid'      => new external_value(PARAM_INT, 'Associated id'),
                'scaleid'     => new external_value(PARAM_INT, 'Scale id'),
                'sort'        => new external_value(PARAM_TEXT, 'Sort order', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Getting list of ratings for a given item (forum post etc)
     * @param string $contextlevel ('context_course', etc..)
     * @param int $instanceid (eg. the 'id' in the 'book' table)
     * @param string $component the name of the component
     * @param string|null $ratingarea
     * @param int $itemid the item id
     * @param int $scaleid the scale id
     * @param string $sort sql order
     * @return array Result and possible warnings
     * @throws moodle_exception
     * @since Moodle 2.9
     */
    public static function get_item_ratings($contextlevel, $instanceid, $component, $ratingarea, $itemid, $scaleid, $sort) {
        global $USER;

        $arrayparams = array(
                 'contextlevel' => $contextlevel,
                 'instanceid'   => $instanceid,
                 'component'    => $component,
                 'ratingarea'   => $ratingarea,
                 'itemid'       => $itemid,
                 'scaleid'      => $scaleid,
                 'sort'         => $sort);

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::get_item_ratings_parameters(), $arrayparams);

        $context = self::get_context_from_params($params);
        self::validate_context($context);
        list($context, $course, $cm) = get_context_info_array($context->id);

        // Minimal capability required.
        if (!has_capability('moodle/rating:view', $context)) {
            throw new moodle_exception('noviewrate', 'rating');
        }

        // Can we see all ratings?
        $canviewallratings = has_capability('moodle/rating:viewall', $context);

        // Create the Sql sort order string.
        switch ($params['sort']) {
            case 'firstname':
                $sqlsort = "u.firstname ASC";
                break;
            case 'rating':
                $sqlsort = "r.rating ASC";
                break;
            default:
                $sqlsort = "r.timemodified ASC";
        }

        $ratingoptions = new stdClass;
        $ratingoptions->context = $context;
        $ratingoptions->component = $params['component'];
        $ratingoptions->ratingarea = $params['ratingarea'];
        $ratingoptions->itemid = $params['itemid'];
        $ratingoptions->sort = $sqlsort;

        $rm = new rating_manager();
        $ratings = $rm->get_all_ratings_for_item($ratingoptions);
        $scalemenu = make_grades_menu($params['scaleid']);

        // If the scale was changed after ratings were submitted some ratings may have a value above the current maximum.
        // We can't just do count($scalemenu) - 1 as custom scales start at index 1, not 0.
        $maxrating = max(array_keys($scalemenu));

        $results = array();

        foreach ($ratings as $rating) {
            if ($canviewallratings || $USER->id == $rating->userid) {
                $result = array();
                $usercontext = context_user::instance($rating->userid);
                $profileimageurl = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', null, '/', 'f1');
                $result['userid'] = $rating->userid;
                $result['userpictureurl'] = $profileimageurl->out(false);
                $result['fullname'] = fullname($rating);
                $result['rating'] = $scalemenu[$rating->rating];
                $result['timemodified'] = $rating->timemodified;
                $results[] = $result;
            }
        }

        $warnings = array();
        return array('ratings' => $results, 'warning' => $warnings);
    }

    /**
     * Returns description of get_item_ratings result values.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function get_item_ratings_returns() {

        return new external_single_structure(
            array(
                'ratings'    => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid'          => new external_value(PARAM_INT,  'User id'),
                            'userpictureurl'  => new external_value(PARAM_URL,  'URL user picture'),
                            'fullname'        => new external_value(PARAM_TEXT, 'fullname'),
                            'rating'          => new external_value(PARAM_TEXT, 'Rating on scale'),
                            'timemodified'    => new external_value(PARAM_INT,  'Time modified (timestamp)')
                        ), 'Ratings'
                    ), 'List of ratings'
                ),
                'warnings'  => new external_warnings(),
            )
        );
    }

}
