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
 * @package dataformfield_ratingmdl
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A class representing a single dataform rating
 * Extends the core rating class
 */

require_once(__DIR__. '/../../../../rating/lib.php');

class ratingmdl_rating extends rating {

    /** @var int The average aggregate of the combined ratings for the associated item. */
    public $ratingavg = null;

    /** @var int The max aggregate of the combined ratings for the associated item. */
    public $ratingmax = null;

    /** @var int The min aggregate of the combined ratings for the associated item. */
    public $ratingmin = null;

    /** @var int The sum aggregate of the combined ratings for the associated item. */
    public $ratingsum = null;

    /**
     * Constructor.
     *
     * @param stdClass $options {
     *            context => context context to use for the rating [required]
     *            component => component using ratings ie mod_forum [required]
     *            ratingarea => ratingarea to associate this rating with [required]
     *            itemid  => int the id of the associated item (forum post, glossary item etc) [required]
     *            scaleid => int The scale in use when the rating was submitted [required]
     *            userid  => int The id of the user who submitted the rating [required]
     *            settings => Settings for the rating object [optional]
     *            id => The id of this rating (if the rating is from the db) [optional]
     *            aggregate => The aggregate for the rating [optional]
     *            count => The number of ratings [optional]
     *            rating => The rating given by the user [optional]
     * }
     */
    public function __construct($options) {
        parent::__construct($options);

        if (isset($options->avgratings)) {
            $this->ratingavg = $options->avgratings;
        }
        if (isset($options->maxratings)) {
            $this->ratingmax = $options->maxratings;
        }
        if (isset($options->avgratings)) {
            $this->ratingmin = $options->minratings;
        }
        if (isset($options->avgratings)) {
            $this->ratingsum = $options->sumratings;
        }
    }

    /**
     * Returns this ratings aggregate value.
     *
     * @return string
     */
    public function get_aggregate_value($aggregation) {

        $aggregate = isset($this->aggregate[$aggregation]) ? $this->aggregate[$aggregation] : '';

        if ($aggregate and $aggregation != RATING_AGGREGATE_COUNT) {
            if ($aggregation != RATING_AGGREGATE_SUM and !$this->settings->scale->isnumeric) {
                // Round aggregate as we're using it as an index.
                $aggregate = $this->settings->scale->scaleitems[round($aggregate)];
            } else {
                // Aggregation is SUM or the scale is numeric.
                $aggregate = round($aggregate, 1);
            }
        }

        return $aggregate;
    }
}

/**
 * The ratingmdl_rating_manager class extends the rating_manager class
 * so as to retrieve sets of ratings from the database for sets of entries.
 */
class ratingmdl_rating_manager extends rating_manager {

    /**
     * Adds rating objects to an array of entries
     * Rating objects are available at $item->rating
     * @param stdClass $options {
     *      context          => context the context in which the ratings exists [required]
     *      component        => the component name ie mod_forum [required]
     *      ratingarea       => the ratingarea we are interested in [required]
     *      items            => array an array of items such as forum posts or glossary items.
     *                          They must have an 'id' member ie $items[0]->id[required]
     *      aggregate        => array an array of aggregation method to be applied.
     *                          RATING_AGGREGATE_AVERAGE, RATING_AGGREGATE_MAXIMUM etc [optional]
     *      scaleid          => int the scale from which the user can select a rating [required]
     *      userid           => int the id of the current user [optional]
     *      returnurl        => string the url to return the user to after submitting a rating.
     *                          Can be left null for ajax requests [optional]
     *      assesstimestart  => int only allow rating of items created after this timestamp [optional]
     *      assesstimefinish => int only allow rating of items created before this timestamp [optional]
     * @return array the array of items with their ratings attached at $items[0]->rating
     */
    public function get_ratings($options) {
        global $DB, $USER;

        if (!isset($options->context)) {
            throw new coding_exception('The context option is a required option when getting ratings.');
        }

        if (!isset($options->component)) {
            throw new coding_exception('The component option is a required option when getting ratings.');
        }

        if (!isset($options->ratingarea)) {
            throw new coding_exception('The ratingarea option is a required option when getting ratings.');
        }

        if (!isset($options->scaleid)) {
            throw new coding_exception('The scaleid option is a required option when getting ratings.');
        }

        if (!isset($options->items)) {
            throw new coding_exception('The items option is a required option when getting ratings.');
        } else if (empty($options->items)) {
            return array();
        }

        list($sql, $params) = $this->get_sql_aggregate($options);
        if (!$ratingrecords = $DB->get_records_sql($sql, $params)) {
            return array();
        }

        foreach ($options->items as $itemid => $item) {
            if (array_key_exists($itemid, $ratingrecords)) {

                $rec = $ratingrecords[$itemid];
                $rec->context = $options->context;
                $rec->component = $options->component;
                $rec->ratingarea = $options->ratingarea;
                $rec->scaleid = $options->scaleid;
                $rec->settings = $this->get_rating_settings_object($options);
                $rec->aggregate = $options->aggregate;

                $options->items[$itemid]->rating = $this->get_rating_object($item, $rec);
            } else {
                unset($options->items[$itemid]);
            }
        }
        return $options->items;
    }

    /**
     * @return array the array of items with their ratings attached at $items[0]->rating
     */
    public function get_sql_aggregate($options) {
        global $DB, $USER;

        // User id; default to current user.
        if (empty($options->userid)) {
            $userid = $USER->id;
        } else {
            $userid = $options->userid;
        }

        // Params.
        $params = array();
        $params['contextid'] = $options->context->id;
        $params['userid']    = $userid;
        $params['component']    = $options->component;
        $params['ratingarea'] = $options->ratingarea;

        // Aggregation sql.
        if (!empty($options->aggregate)) {
            if (!is_array($options->aggregate)) {
                $option->aggregate = array($option->aggregate);
            }

            $aggregatessql = array();
            foreach ($options->aggregate as $aggregation) {
                // Skip empty or count.
                if (empty($aggregation) or $aggregation == RATING_AGGREGATE_COUNT) {
                    continue;
                }
                $aggrmethod = $this->get_aggregation_method($aggregation);
                $aggrmethodpref = strtolower($aggrmethod);
                $aggregatessql[$aggrmethodpref] = "$aggrmethod(r.rating) AS {$aggrmethodpref}ratings";
            }
        }
        $aggregationsql = !empty($aggregatessql) ? implode(', ', $aggregatessql). ', ' : '';

        // Sql for entry ids.
        $andwhereitems = '';
        if (!empty($options->items)) {
            $itemids = array_keys($options->items);
            list($itemidtest, $paramitems) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
            $andwhereitems = " AND r.itemid $itemidtest ";
            $params = array_merge($params, $paramitems);
        }

        $sql = "SELECT r.itemid, r.component, r.ratingarea, r.contextid,
                       COUNT(r.rating) AS numratings, $aggregationsql
                       ur.id, ur.userid, ur.scaleid, ur.rating AS usersrating
                FROM {rating} r
                        LEFT JOIN {rating} ur ON ur.contextid = r.contextid
                                                AND ur.itemid = r.itemid
                                                AND ur.component = r.component
                                                AND ur.ratingarea = r.ratingarea
                                                AND ur.userid = :userid
                WHERE r.contextid = :contextid
                        AND r.component = :component
                        AND r.ratingarea = :ratingarea
                        $andwhereitems
                GROUP BY r.itemid, r.component, r.ratingarea, r.contextid, ur.id, ur.userid, ur.scaleid
                ORDER BY r.itemid";

        return array($sql, $params);
    }

    /**
     * @return array the array of items with their ratings attached at $items[0]->rating
     */
    public function get_sql_all($options) {
        global $DB, $USER;

        // User id; default to current user.
        if (empty($options->userid)) {
            $userid = $USER->id;
        } else {
            $userid = $options->userid;
        }

        // Params.
        $params = array();
        $params['contextid'] = $options->context->id;
        $params['userid']    = $userid;
        $params['component']    = $options->component;
        $params['ratingarea'] = $options->ratingarea;

        // Sql for entry ids.
        $andwhereitems = '';
        if (!empty($options->items)) {
            $itemids = array_keys($options->items);
            list($itemidtest, $paramitems) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
            $andwhereitems = " AND r.itemid $itemidtest ";
            $params = array_merge($params, $paramitems);
        }

        $sql = "SELECT r.id, r.itemid, r.component, r.ratingarea, r.contextid, r.scaleid,
                       r.rating, r.userid, r.timecreated, r.timemodified, ".
                       user_picture::fields('u', array('idnumber', 'username'), 'uid ').
               " FROM {rating} r
                    JOIN {user} u ON u.id = r.userid

                WHERE r.contextid = :contextid
                        AND r.component = :component
                        AND r.ratingarea = :ratingarea
                        $andwhereitems
                ORDER BY r.itemid";

        return array($sql, $params);
    }

    /**
     * @return array the array of items with their ratings attached at $items[0]->rating
     */
    public function get_rating_settings_object($options) {
        // Ugly hack to work around the exception in generate_settings.
        if (empty($options->aggregate) or is_array($options->aggregate)) {
            $options->aggregate = RATING_AGGREGATE_COUNT;
        }
        return $this->generate_rating_settings_object($options);
    }

    /**
     * @return array the array of items with their ratings attached at $items[0]->rating
     */
    public function get_rating_object($item, $ratingrecord) {

        $rec = $ratingrecord;

        $options = new \stdClass;
        $options->context = $rec->context;
        $options->component = 'mod_dataform';
        $options->ratingarea = $rec->ratingarea;
        $options->itemid = $item->id;
        $options->settings = $rec->settings;
        // Note: rec->scaleid = the id of scale at the time the rating was submitted.
        // May be different from the current scale id.
        $options->scaleid = $rec->scaleid;

        $options->userid = !empty($rec->userid) ? $rec->userid : 0;
        $options->id = !empty($rec->id) ? $rec->id : 0;
        $ratingvalue = (string) $rec->usersrating;
        if ($ratingvalue !== '') {
            $options->rating = min($rec->usersrating, $rec->settings->scale->max);
        } else {
            $options->rating = null;
        }
        $options->count = $rec->numratings;

        // Aggregations.
        foreach (array('avg', 'max', 'min', 'sum') as $aggregation) {
            $aggrmethod = "{$aggregation}ratings";
            if (isset($rec->$aggrmethod)) {
                $options->$aggrmethod = min($rec->$aggrmethod, $rec->settings->scale->max);
            }
        }

        $rating = new ratingmdl_rating($options);
        $rating->itemtimecreated = $this->get_item_time_created($item);
        if (!empty($item->userid)) {
            $rating->itemuserid = $item->userid;
        }

        return $rating;
    }

}
