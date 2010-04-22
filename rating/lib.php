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

define('RATING_UNSET_RATING', -999);

define ('RATING_AGGREGATE_NONE', 0); //no ratings
define ('RATING_AGGREGATE_AVERAGE', 1);
define ('RATING_AGGREGATE_COUNT', 2);
define ('RATING_AGGREGATE_MAXIMUM', 3);
define ('RATING_AGGREGATE_MINIMUM', 4);
define ('RATING_AGGREGATE_SUM', 5);

define ('RATING_DEFAULT_SCALE', 5);

/**
 * The rating class represents a single rating by a single user
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
    * @param object $options {
    *            context => context context to use for the rating [required]
    *            itemid  => int the id of the associated item (forum post, glossary item etc) [required]
    *            scaleid => int The scale in use when the rating was submitted [required]
    *            userid  => int The id of the user who submitted the rating [required]
    * }
    */
    public function __construct($options) {
        $this->context = $options->context;
        $this->itemid = $options->itemid;
        $this->scaleid = $options->scaleid;
        $this->userid = $options->userid;
    }

    /**
    * Update this rating in the database
    * @param int $rating the integer value of this rating
    * @return void
    */
    public function update_rating($rating) {
        global $DB;

        $data = new stdclass();
        $table = 'rating';

        $item = new stdclass();
        $item->id = $this->itemid;
        $items = array($item);

        $ratingoptions = new stdclass();
        $ratingoptions->context = $this->context;
        $ratingoptions->items = $items;
        $ratingoptions->aggregate = RATING_AGGREGATE_AVERAGE;//we dont actually care what aggregation method is applied
        $ratingoptions->scaleid = $this->scaleid;
        $ratingoptions->userid = $this->userid;

        $rm = new rating_manager();
        $items = $rm->get_ratings($ratingoptions);
        if( empty($items) || empty($items[0]->rating) || empty($items[0]->rating->id) ) {
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
            $data->id       = $items[0]->rating->id;
            $data->rating       = $rating;

            $time = time();
            $data->timemodified = $time;

            $DB->update_record($table, $data);
        }
    }

    /**
    * Retreive the integer value of this rating
    * @return int the integer value of this rating object
    */
    public function get_rating() {
        return $this->rating;
    }

    /**
    * Remove this rating from the database
    * @return void
    */
    //public function delete_rating() {
        //todo implement this if its actually needed
    //}
} //end rating class definition

/**
 * The rating_manager class provides the ability to retrieve sets of ratings from the database
 *
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class rating_manager {

    /**
    * Delete one or more ratings. Specify either a rating id, an item id or just the context id.
    * @param object $options {
    *            contextid => int the context in which the ratings exist [required]
    *            ratingid => int the id of an individual rating to delete [optional]
    *            itemid => int delete all ratings attached to this item [optional]
    * }
    * @return void
    */
    public function delete_ratings($options) {
        global $DB;
        
        if( !empty($options->ratingid) ) {
            //delete a single rating
            $DB->delete_records('rating', array('contextid'=>$options->contextid, 'id'=>$options->ratingid) );
        }
        else if( !empty($options->itemid) ) {
            //delete all ratings for an item
            $DB->delete_records('rating', array('contextid'=>$options->contextid, 'itemid'=>$options->itemid) );
        }
        else {
            //delete all ratings for this context
            $DB->delete_records('rating', array('contextid'=>$options->contextid) );
        }
    }

    /**
    * Returns an array of ratings for a given item (forum post, glossary entry etc)
    * This returns all users ratings for a single item
    * @param object $options {
    *            context => context the context in which the ratings exists [required]
    *            itemid  =>  int the id of the associated item (forum post, glossary item etc) [required]
    *            sort    => string SQL sort by clause [optional]
    * }
    * @return array an array of ratings
    */
    public function get_all_ratings_for_item($options) {
        global $DB;

        $sortclause = '';
        if( !empty($options->sort) ) {
            $sortclause = "ORDER BY $options->sort";
        }

        $userfields = user_picture::fields('u','uid');
        $sql = "SELECT r.id, r.rating, r.itemid, r.userid, r.timemodified,
                    $userfields
                FROM {rating} r
                LEFT JOIN {user} u ON r.userid = u.id
                WHERE r.contextid = :contextid AND
                      r.itemid  = :itemid
                {$sortclause}";

        $params['contextid'] = $options->context->id;
        $params['itemid'] = $options->itemid;

        return $DB->get_records_sql($sql, $params);
    }

    /**
    * Adds rating objects to an array of items (forum posts, glossary entries etc)
    * Rating objects are available at $item->rating
    * @param object $options {
    *            context => context the context in which the ratings exists [required]
    *            items  => array an array of items such as forum posts or glossary items. They must have an 'id' member ie $items[0]->id[required]
    *            aggregate    => int what aggregation method should be applied. RATING_AGGREGATE_AVERAGE, RATING_AGGREGATE_MAXIMUM etc [required]
    *            scaleid => int the scale from which the user can select a rating [required]
    *            userid => int the id of the current user [optional]
    *            returnurl => string the url to return the user to after submitting a rating. Can be left null for ajax requests [optional]
    *            assesstimestart => int only allow rating of items created after this timestamp [optional]
    *            assesstimefinish => int only allow rating of items created before this timestamp [optional]
    * @return array the array of items with their ratings attached at $items[0]->rating
    */
    public function get_ratings($options) {
        global $DB, $USER, $PAGE, $CFG;

        if(empty($options->items)) {
            return $options->items;
        }

        if (empty($options->userid)) {
            $userid = $USER->id;
        } else {
            $userid = $options->userid;
        }

        $aggregatestr = $this->get_aggregation_method($options->aggregate);

        //create an array of item ids
        $itemids = array();
        foreach($options->items as $item) {
            $itemids[] = $item->id;
        }

        //get the items from the database
        list($itemidtest, $params) = $DB->get_in_or_equal(
                $itemids, SQL_PARAMS_NAMED, 'itemid0000');

	//note: all the group bys arent really necessary but PostgreSQL complains
	//about selecting a mixture of grouped and non-grouped columns
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
    GROUP BY r.itemid, ur.rating, ur.id, ur.userid, ur.scaleid
    ORDER BY r.itemid";

        $params['userid'] = $userid;
        $params['contextid'] = $options->context->id;

        $ratingsrecords = $DB->get_records_sql($sql, $params);

        //now create the rating sub objects
        $scaleobj = new stdClass();
        $scalemax = null;

        //we could look for a scale id on each item to allow each item to use a different scale
        if($options->scaleid < 0 ) { //if its a scale (not numeric)
            $scalerecord = $DB->get_record('scale', array('id' => -$options->scaleid));
            if ($scalerecord) {
                $scalearray = explode(',', $scalerecord->scale);

                //is there a more efficient way to get the indexes to start at 1 instead of 0?
                //this will go away when scales are refactored
                $c = count($scalearray);
                $n = null;
                for($i=0; $i<$c; $i++) {
                    $n = $i+1;
                    $scaleobj->scaleitems["$n"] = $scalearray[$i];//treat index as a string to allow sorting without changing the value
                }
                krsort($scaleobj->scaleitems);//have the highest grade scale item appear first

                $scaleobj->id = $options->scaleid;//dont use the one from the record or we "forget" that its negative
                $scaleobj->name = $scalerecord->name;
                $scaleobj->courseid = $scalerecord->courseid;

                $scalemax = count($scaleobj->scaleitems);
            }
        }
        else { //its numeric
            $scaleobj->scaleitems = $options->scaleid;
            $scaleobj->id = $options->scaleid;
            $scaleobj->name = null;

            $scalemax = $options->scaleid;
        }

        //should $settings and $settings->permissions be declared as proper classes?
        $settings = new stdclass(); //settings that are common to all ratings objects in this context
        $settings->scale = $scaleobj; //the scale to use now
        $settings->aggregationmethod = $options->aggregate;
        if( !empty($options->returnurl) ) {
            $settings->returnurl = $options->returnurl;
        }

        $settings->assesstimestart = $settings->assesstimefinish = null;
        if( !empty($options->assesstimestart) ) {
            $settings->assesstimestart = $options->assesstimestart;
        }
        if( !empty($options->assesstimefinish) ) {
            $settings->assesstimefinish = $options->assesstimefinish;
        }

        $settings->permissions = new stdclass();
        $settings->permissions->canview = has_capability('moodle/rating:view',$options->context);
        $settings->permissions->canviewall = has_capability('moodle/rating:viewall',$options->context);
        $settings->permissions->canrate = has_capability('moodle/rating:rate',$options->context);

        $rating = null;
        $ratingoptions = new stdclass();
        $ratingoptions->context = $options->context;//context is common to all ratings in the set
        foreach($options->items as $item) {
            $rating = null;
            //match the item with its corresponding rating
            foreach($ratingsrecords as $rec) {
                if( $item->id==$rec->itemid ) {
                    //Note: rec->scaleid = the id of scale at the time the rating was submitted
                    //may be different from the current scale id
                    $ratingoptions->itemid = $item->id;
                    $ratingoptions->scaleid = $rec->scaleid;
                    $ratingoptions->userid = $rec->userid;

                    $rating = new rating($ratingoptions);
                    $rating->id         = $rec->id;    //unset($rec->id);
                    $rating->aggregate  = $rec->aggrrating; //unset($rec->aggrrating);
                    $rating->count      = $rec->numratings; //unset($rec->numratings);
                    $rating->rating     = $rec->usersrating; //unset($rec->usersrating);
                    if( !empty($item->created) ) {
                        $rating->itemtimecreated = $item->created;//the forum_posts table has created instead of timecreated
                    }
                    else if(!empty($item->timecreated)) {
                        $rating->itemtimecreated = $item->timecreated;
                    }
                    else {
                        $rating->itemtimecreated = null;
                    }
                    break;
                }
            }
            //if there are no ratings for this item
            if( !$rating ) {
                $ratingoptions->itemid = $item->id;
                $ratingoptions->scaleid = null;
                $ratingoptions->userid = null;

                $rating = new rating($ratingoptions);
                $rating->id         = null;
                $rating->aggregate  = null;
                $rating->count      = 0;
                $rating->rating     = null;

                $rating->itemid     = $item->id;
                $rating->userid     = null;
                $rating->scaleid     = null;
                $rating->itemtimecreated = null;
            }

            if( !empty($item->userid) ) {
                $rating->itemuserid = $item->userid;
            } else {
                $rating->itemuserid = null;
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
            if ($rating->aggregate > $scalemax) {
                $rating->aggregate = $scalemax;
            }
        }

        return $options->items;
    }

    /**
    * Returns an array of grades calculated by aggregating item ratings.
    * @param object $options {
    *            userid => int the id of the user whose items have been rated. NOT the user who submitted the ratings [required]
    *            aggregationmethod => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [required]
    *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
    *            itemtable => int the table containing the items [required]
    *            itemtableusercolum => int the column of the user table containing the item owner's user id [required]
    *
    *            contextid => int the context in which the rated items exist [optional]
    *
    *            modulename => string the name of the module [optional]
    *            moduleid => int the id of the module instance [optional]
    *
    * @return array the array of the user's grades
    */
    public function get_user_grades($options) {
        global $DB;

        $contextid = null;

        //if the calling code doesn't supply a context id we'll have to figure it out
        if( !empty($options->contextid) ) {
            $contextid = $options->contextid;
        }
        else if( !empty($options->cmid) ) {
            //not implemented as not currently used although cmid is potentially available (the forum supplies it)
            //Is there a convenient way to get a context id from a cm id?
            //$cmidnumber = $options->cmidnumber;
        }
        else if ( !empty($options->modulename) && !empty($options->moduleid) ) {
            $modulename = $options->modulename;
            $moduleid   = $options->moduleid;
            
            //going direct to the db for the context id seems wrong
            list($ctxselect, $ctxjoin) = context_instance_preload_sql('cm.id', CONTEXT_MODULE, 'ctx');
            $sql = "SELECT cm.* $ctxselect
            FROM {course_modules} cm
            LEFT JOIN {modules} mo ON mo.id = cm.module
            LEFT JOIN {{$modulename}} m ON m.id = cm.instance $ctxjoin
            WHERE mo.name=:modulename AND m.id=:moduleid";
            $contextrecord = $DB->get_record_sql($sql, array('modulename'=>$modulename, 'moduleid'=>$moduleid), '*', MUST_EXIST);
            $contextid = $contextrecord->ctxid;
        }

        $params = array();
        $params['contextid']= $contextid;
        $itemtable          = $options->itemtable;
        $itemtableusercolumn= $options->itemtableusercolumn;
        $scaleid            = $options->scaleid;
        $params['userid1'] = $params['userid2'] = $params['userid3']  = $options->userid;

        $aggregationstring = $this->get_aggregation_method($options->aggregationmethod);

        $sql = "SELECT :userid1 as id, :userid2 AS userid, $aggregationstring(r.rating) AS rawgrade
                FROM {rating} r
                WHERE r.contextid=:contextid 
                    AND r.itemid IN (SELECT i.id AS itemid FROM {{$itemtable}} i WHERE i.{$itemtableusercolumn} = :userid3)";

        $results = $DB->get_records_sql($sql, $params);
        if ($results) {
            // it could throw off the grading if count and sum returned a rawgrade higher than scale
            // so to prevent it we review the results and ensure that rawgrade does not exceed the scale, if it does we set rawgrade = scale (i.e. full credit)
            foreach ($results as $rid=>$result) {
                if ($options->scaleid >= 0) {
                    //numeric
                    if ($result->rawgrade > $options->scaleid) {
                        $results[$rid]->rawgrade = $options->scaleid;
                    }
                } else {
                    //scales
                    if ($scale = $DB->get_record('scale', array('id' => -$options->scaleid))) {
                        $scale = explode(',', $scale->scale);
                        $max = count($scale);
                        if ($result->rawgrade > $max) {
                            $results[$rid]->rawgrade = $max;
                        }
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Returns array of aggregate types. Used by ratings.
     *
     * @return array
     */
    public function get_aggregate_types() {
        return array (RATING_AGGREGATE_NONE  => get_string('aggregatenone', 'forum'),
                      RATING_AGGREGATE_AVERAGE   => get_string('aggregateavg', 'forum'),
                      RATING_AGGREGATE_COUNT => get_string('aggregatecount', 'forum'),
                      RATING_AGGREGATE_MAXIMUM   => get_string('aggregatemax', 'forum'),
                      RATING_AGGREGATE_MINIMUM   => get_string('aggregatemin', 'forum'),
                      RATING_AGGREGATE_SUM   => get_string('aggregatesum', 'forum'));
    }

    /**
    * Converts an aggregation method constant into something that can be included in SQL
    * @param int $aggregate An aggregation constant. For example, RATING_AGGREGATE_AVERAGE.
    * @return string an SQL aggregation method
    */
    public function get_aggregation_method($aggregate) {
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
}//end rating_manager class definition