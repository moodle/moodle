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
 * Rating external API
 *
 * @package    core_rating
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/rating/lib.php");

/**
 * Rating external functions
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
                'contextlevel'  => new external_value(PARAM_ALPHA, 'context level: course, module, user, etc...'),
                'instanceid'    => new external_value(PARAM_INT, 'the instance id of item associated with the context level'),
                'component'     => new external_value(PARAM_COMPONENT, 'component'),
                'ratingarea'    => new external_value(PARAM_AREA, 'rating area'),
                'itemid'        => new external_value(PARAM_INT, 'associated id'),
                'scaleid'       => new external_value(PARAM_INT, 'scale id'),
                'sort'          => new external_value(PARAM_ALPHA, 'sort order (firstname, rating or timemodified)')
            )
        );
    }

    /**
     * Retrieve a list of ratings for a given item (forum post etc)
     *
     * @param string $contextlevel course, module, user...
     * @param int $instanceid the instance if for the context element
     * @param string $component the name of the component
     * @param string $ratingarea rating area
     * @param int $itemid the item id
     * @param int $scaleid the scale id
     * @param string $sort sql order (firstname, rating or timemodified)
     * @return array Result and possible warnings
     * @throws moodle_exception
     * @since Moodle 2.9
     */
    public static function get_item_ratings($contextlevel, $instanceid, $component, $ratingarea, $itemid, $scaleid, $sort) {
        global $USER, $PAGE;

        $warnings = array();

        $arrayparams = array(
            'contextlevel' => $contextlevel,
            'instanceid'   => $instanceid,
            'component'    => $component,
            'ratingarea'   => $ratingarea,
            'itemid'       => $itemid,
            'scaleid'      => $scaleid,
            'sort'         => $sort
        );

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::get_item_ratings_parameters(), $arrayparams);

        $context = self::get_context_from_params($params);
        self::validate_context($context);

        // Minimal capability required.
        $callbackparams = array('contextid' => $context->id,
                        'component' => $component,
                        'ratingarea' => $ratingarea,
                        'itemid' => $itemid,
                        'scaleid' => $scaleid);
        if (!has_capability('moodle/rating:view', $context) ||
                !component_callback($component, 'rating_can_see_item_ratings', array($callbackparams), true)) {
            throw new moodle_exception('noviewrate', 'rating');
        }

        list($context, $course, $cm) = get_context_info_array($context->id);

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
                if ($rating->rating > $maxrating) {
                    $rating->rating = $maxrating;
                }

                $result = array();
                $result['id'] = $rating->id;
                $result['userid'] = $rating->userid;
                $result['userfullname'] = fullname($rating);
                $result['rating'] = $scalemenu[$rating->rating];
                $result['timemodified'] = $rating->timemodified;

                // The rating object has all the required fields for generating the picture url.
                // Undo the aliasing of the user id column from user_picture::fields().
                $rating->id = $rating->userid;
                $userpicture = new user_picture($rating);
                $userpicture->size = 1; // Size f1.
                $result['userpictureurl'] = $userpicture->get_url($PAGE)->out(false);

                $results[] = $result;
            }
        }

        return array(
            'ratings' => $results,
            'warnings' => $warnings
        );
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
                            'id'              => new external_value(PARAM_INT,  'rating id'),
                            'userid'          => new external_value(PARAM_INT,  'user id'),
                            'userpictureurl'  => new external_value(PARAM_URL,  'URL user picture'),
                            'userfullname'    => new external_value(PARAM_NOTAGS, 'user fullname'),
                            'rating'          => new external_value(PARAM_NOTAGS, 'rating on scale'),
                            'timemodified'    => new external_value(PARAM_INT,  'time modified (timestamp)')
                        ), 'Rating'
                    ), 'list of ratings'
                ),
                'warnings'  => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of add_rating parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function add_rating_parameters() {
        return new external_function_parameters (
            array(
                'contextlevel'  => new external_value(PARAM_ALPHA, 'context level: course, module, user, etc...'),
                'instanceid'    => new external_value(PARAM_INT, 'the instance id of item associated with the context level'),
                'component'     => new external_value(PARAM_COMPONENT, 'component'),
                'ratingarea'    => new external_value(PARAM_AREA, 'rating area'),
                'itemid'        => new external_value(PARAM_INT, 'associated id'),
                'scaleid'       => new external_value(PARAM_INT, 'scale id'),
                'rating'        => new external_value(PARAM_INT, 'user rating'),
                'rateduserid'   => new external_value(PARAM_INT, 'rated user id'),
                'aggregation'   => new external_value(PARAM_INT, 'agreggation method', VALUE_DEFAULT, RATING_AGGREGATE_NONE)
            )
        );
    }

    /**
     * Adds a rating to an item
     *
     * @param string $contextlevel course, module, user...
     * @param int $instanceid the instance if for the context element
     * @param string $component the name of the component
     * @param string $ratingarea rating area
     * @param int $itemid the item id
     * @param int $scaleid the scale id
     * @param int $rating the user rating
     * @param int $rateduserid the rated user id
     * @param int $aggregation the aggregation method
     * @return array result and possible warnings
     * @throws moodle_exception
     * @since Moodle 3.2
     */
    public static function add_rating($contextlevel, $instanceid, $component, $ratingarea, $itemid, $scaleid, $rating, $rateduserid,
                                        $aggregation = RATING_AGGREGATE_NONE) {
        $warnings = array();

        $params = array(
            'contextlevel' => $contextlevel,
            'instanceid'   => $instanceid,
            'component'    => $component,
            'ratingarea'   => $ratingarea,
            'itemid'       => $itemid,
            'scaleid'      => $scaleid,
            'rating'       => $rating,
            'rateduserid'  => $rateduserid,
            'aggregation'  => $aggregation,
        );

        // Validate and normalize parameters.
        $params = self::validate_parameters(self::add_rating_parameters(), $params);

        $context = self::get_context_from_params($params);
        self::validate_context($context);
        $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);

        require_capability('moodle/rating:rate', $context);

        $rm = new rating_manager();
        $result = $rm->add_rating($cm, $context, $params['component'], $params['ratingarea'], $params['itemid'], $params['scaleid'],
                                    $params['rating'], $params['rateduserid'], $params['aggregation']);

        if (!empty($result->error)) {
            throw new moodle_exception($result->error, 'rating');
        }

        $returndata = array(
            'success' => $result->success,
            'warnings' => $warnings
        );

        if (isset($result->aggregate)) {
            $returndata['aggregate'] = $result->aggregate;
            $returndata['count'] = $result->count;
            $returndata['itemid'] = $result->itemid;
        }

        return $returndata;
    }

    /**
     * Returns description of add_rating result values.
     *
     * @return external_single_structure
     * @since Moodle 3.2
     */
    public static function add_rating_returns() {

        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Whether the rate was successfully created'),
                'aggregate' => new external_value(PARAM_TEXT, 'New aggregate', VALUE_OPTIONAL),
                'count' => new external_value(PARAM_INT, 'Ratings count', VALUE_OPTIONAL),
                'itemid' => new external_value(PARAM_INT, 'Rating item id', VALUE_OPTIONAL),
                'warnings'  => new external_warnings(),
            )
        );
    }
}
