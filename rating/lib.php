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
 * A class representing a single rating and containing some static methods for manipulating ratings
 *
 * @package   moodlecore
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('RATING_VIEW','moodle/rating:view');
define('RATING_VIEW_ALL','moodle/rating:viewall');
define('RATING_RATE','moodle/rating:rate');

define('RATING_UNSET_RATING', -999);

//define ('RATING_AGGREGATE_NONE', 0); //no ratings
define ('RATING_AGGREGATE_AVERAGE', 1);
define ('RATING_AGGREGATE_COUNT', 2);
define ('RATING_AGGREGATE_MAXIMUM', 3);
define ('RATING_AGGREGATE_MINIMUM', 4);
define ('RATING_AGGREGATE_SUM', 5);

/**
 * The rating class represents a single rating by a single user. It also contains a static method to retrieve sets of ratings.
 *
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class rating implements renderable {

    /**
     * The context in which this rating exists
     * @var context
     */
    public $context;

    /**
     * The id of the item (forum post, glossary item etc) being rated
     * @var int
     */
    public $itemid;

    /**
     * The id scale (1-5, 0-100) that was in use when the rating was submitted
     * @var int
     */
    public $scaleid;

    /**
     * The id of the user who submitted the rating
     * @var int
     */
    public $userid;

    /**
     * settings for this rating. Necessary to render the rating.
     * @var stdclass
     */
    public $settings;

    /**
    * Constructor.
    * @param context $context the current context object
    * @param int $itemid the id of the associated item (forum post, glossary item etc)
    * @param int $scaleid the scale to use
    * @param int $userid the user submitting the rating
    */
    public function __construct($context, $itemid, $scaleid, $userid) {
        $this->context = $context;
        $this->itemid = $itemid;
        $this->scaleid = $scaleid;
        $this->userid = $userid;
    }

    /**
    * Update this rating in the database
    * @param int $rating the integer value of this rating
    */
    public function update_rating($rating) {
        global $DB;

        $data = new stdclass();
        $table = 'rating';

        $item = new stdclass();
        $item->id = $this->itemid;
        $items = array($item);

        $items = rating::load_ratings($this->context, $items, null, $this->scaleid, $this->userid);
        if( !isset($items[0]->rating) || !isset($items[0]->rating->id) ) {
            $data->contextid    = $this->context->id;
            $data->rating       = $rating;
            $data->scaleid      = $this->scaleid;
            $data->userid       = $this->userid;
            $data->itemid       = $this->itemid;

            $time = time();
            $data->timecreated = $time;
            $data->timemodified = $time;

            $DB->insert_record($table, $data);
        }
        else {
            //$data->id       = $this->id;
            $data->id       = $items[0]->rating->id;
            /*$data->contextid    = $this->context->id;
            $data->scaleid      = $this->scaleid;
            $data->userid       = $this->userid;*/
            $data->rating       = $rating;

            $time = time();
            $data->timemodified = $time;

            $DB->update_record($table, $data);
        }
    }

    /**
    * Retreive the integer value of this rating
    * @return the integer value of this rating object
    */
    public function get_rating() {
        return $this->rating;
    }

    /**
    * Remove this rating from the database
    */
    public function delete_rating() {
        //todo implement this if its actually needed
    }

    /**
    * Static method that converts an aggregation method constant into something that can be included in SQL
    * @param $aggregate An aggregation constant. For example, RATING_AGGREGATE_AVERAGE.
    */
    public static function get_aggregation_method($aggregate) {
        $aggregatestr = null;
        switch($aggregate){
            case RATING_AGGREGATE_AVERAGE:
                $aggregatestr = 'AVG';
                break;
            case RATING_AGGREGATE_COUNT:
                $aggregatestr = 'CNT';
                break;
            case RATING_AGGREGATE_MAXIMUM:
                $aggregatestr = 'MAX';
                break;
            case RATING_AGGREGATE_MINIMUM:
                $aggregatestr = 'MIN';
                break;
            case RATING_AGGREGATE_SUM:
                $aggregatestr = 'SUM';
                break;
        }
        return $aggregatestr;
    }

    /**
    * Static method that returns an array of ratings for a given item (forum post, glossary entry etc)
     * This returns all users ratings for a single item
    * @param context $context the context in which the rating exists
    * @param int $itemid The id of the forum posts, glossary items or whatever
    */
    public static function load_ratings_for_item($context, $itemid, $sort) {
        global $DB;

        $userfields = user_picture::fields('u');
        $sql = "SELECT r.id, r.rating, r.itemid, r.userid, r.timemodified,
                    $userfields
                FROM {rating} r
                LEFT JOIN {user} u ON r.userid = u.id
                WHERE r.contextid = :contextid AND
                      r.itemid  = :itemid
                $sort";

        $params['contextid'] = $context->id;
        $params['itemid'] = $itemid;

        return $DB->get_records_sql($sql, $params);
    }

    /**
    * Static method that adds rating objects to an array of items (forum posts, glossary entries etc)
    * Rating objects are available at $item->rating
    * @param context $context the current context object
    * @param array $items an array of items such as forum posts or glossary items. They must have an 'id' member ie $items[0]->id
    * @param $aggregate what aggregation method should be applied. AVG, MAX etc
    * @param int $scaleid the scale from which the user can select a rating
    * @param int $userid the id of the current user
    * @return returns the array of items with their ratings attached at $items[0]->rating
    */
    public static function load_ratings($context, $items, $aggregate=RATING_AGGREGATE_AVERAGE, $scaleid=5, $userid = null, $returnurl = null) {
        global $DB, $USER, $PAGE, $CFG;

        if(empty($items)) {
            return $items;
        }

        if (is_null($userid)) {
            $userid = $USER->id;
        }

        $aggregatestr = rating::get_aggregation_method($aggregate);

        //create an array of item ids
        $itemids = array();
        foreach($items as $item) {
            $itemids[] = $item->id;
        }

        //get the items from the database
        list($itemidtest, $params) = $DB->get_in_or_equal(
                $itemids, SQL_PARAMS_NAMED, 'itemid0000');

        $sql = "SELECT r.itemid, ur.id, ur.userid, ur.scaleid,
        $aggregatestr(r.rating) AS aggrrating,
        COUNT(r.rating) AS numratings,
        ur.rating AS usersrating
    FROM {rating} r
    LEFT JOIN {rating} ur ON ur.contextid = r.contextid AND
            ur.itemid = r.itemid AND
            ur.userid = :userid
    WHERE
        r.contextid = :contextid AND
        r.itemid $itemidtest
    GROUP BY r.itemid, ur.rating
    ORDER BY r.itemid";

        $params['userid'] = $userid;
        $params['contextid'] = $context->id;

        $ratingsrecords = $DB->get_records_sql($sql, $params);

        //now create the rating sub objects
        $scaleobj = new stdClass();
        $scalemax = null;

        //we could look for a scale id on each item to allow each item to use a different scale

        if($scaleid < 0 ) { //if its a scale (not numeric)
            $scalerecord = $DB->get_record('scale', array('id' => -$scaleid));
            if ($scalerecord) {
                $scaleobj->scaleitems = explode(',', $scalerecord->scale);
                $scaleobj->id = $scalerecord->id;
                $scaleobj->name = $scalerecord->name;

                $scalemax = count($scaleobj->scale)-1;
            }
        }
        else { //its numeric
            $scaleobj->scaleitems = $scaleid;
            $scaleobj->id = $scaleid;
            $scaleobj->name = null;

            $scalemax = $scaleid;
        }

        //should $settings and $settings->permissions be declared as proper classes?
        $settings = new stdclass(); //settings that are common to all ratings objects in this context
        $settings->scale = $scaleobj; //the scale to use now
        $settings->aggregationmethod = $aggregate;
        $settings->returnurl = $returnurl;

        $settings->permissions = new stdclass();
        $settings->permissions->canview = has_capability(RATING_VIEW,$context);
        $settings->permissions->canviewall = has_capability(RATING_VIEW_ALL,$context);
        $settings->permissions->canrate = has_capability(RATING_RATE,$context);

        $rating = null;
        foreach($items as $item) {
            $rating = null;
            //match the item with its corresponding rating
            foreach($ratingsrecords as $rec) {
                if( $item->id==$rec->itemid ) {
                    //Note: rec->scaleid = the id of scale at the time the rating was submitted
                    //may be different from the current scale id
                    $rating = new rating($context, $item->id, $rec->scaleid, $rec->userid);
                    $rating->id         = $rec->id;    //unset($rec->id);
                    $rating->aggregate  = $rec->aggrrating; //unset($rec->aggrrating);
                    $rating->count      = $rec->numratings; //unset($rec->numratings);
                    $rating->rating     = $rec->usersrating; //unset($rec->usersrating);
                    break;
                }
            }
            //if there are no ratings for this item
            if( !$rating ) {
                $scaleid = $userid = null;
                $rating = new rating($context, $item->id, $scaleid, $userid);
                $rating->id         = null;
                $rating->aggregate  = null;
                $rating->count      = 0;
                $rating->rating     = null;

                $rating->itemid     = $item->id;
                $rating->userid     = null;
                $rating->scaleid     = null;
            }

            $rating->settings = $settings;
            $item->rating = $rating;

            //Below is a nasty hack presumably here to handle scales being changed (out of 10 to out of 5 for example)
            //
            // it could throw off the grading if count and sum returned a grade higher than scale
            // so to prevent it we review the results and ensure that grade does not exceed the scale, if it does we set grade = scale (i.e. full credit)
            if ($rating->rating > $scalemax) {
                $rating->rating = $scalemax;
            }
        }
        return $items;
    }
} //end rating class definition