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
 * @package    core_rating
 * @subpackage rating
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @package   core_rating
 * @category  rating
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class rating implements renderable {

    /**
     * @var stdClass The context in which this rating exists
     */
    public $context;

    /**
     * @var string The component using ratings. For example "mod_forum"
     */
    public $component;

    /**
     * @var string The rating area to associate this rating with
     *             This allows a plugin to rate more than one thing by specifying different rating areas
     */
    public $ratingarea = null;

    /**
     * @var int The id of the item (forum post, glossary item etc) being rated
     */
    public $itemid;

    /**
     * @var int The id scale (1-5, 0-100) that was in use when the rating was submitted
     */
    public $scaleid;

    /**
     * @var int The id of the user who submitted the rating
     */
    public $userid;

    /**
     * @var stdclass settings for this rating. Necessary to render the rating.
     */
    public $settings;

    /**
     * @var int The Id of this rating within the rating table. This is only set if the rating already exists
     */
    public $id = null;

    /**
     * @var int The aggregate of the combined ratings for the associated item. This is only set if the rating already exists
     */
    public $aggregate = null;

    /**
     * @var int The total number of ratings for the associated item. This is only set if the rating already exists
     */
    public $count = 0;

    /**
     * @var int The rating the associated user gave the associated item. This is only set if the rating already exists
     */
    public $rating = null;

    /**
     * @var int The time the associated item was created
     */
    public $itemtimecreated = null;

    /**
     * @var int The id of the user who submitted the rating
     */
    public $itemuserid = null;

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
        $this->context =    $options->context;
        $this->component =  $options->component;
        $this->ratingarea = $options->ratingarea;
        $this->itemid =     $options->itemid;
        $this->scaleid =    $options->scaleid;
        $this->userid =     $options->userid;

        if (isset($options->settings)) {
            $this->settings = $options->settings;
        }
        if (isset($options->id)) {
            $this->id = $options->id;
        }
        if (isset($options->aggregate)) {
            $this->aggregate = $options->aggregate;
        }
        if (isset($options->count)) {
            $this->count = $options->count;
        }
        if (isset($options->rating)) {
            $this->rating = $options->rating;
        }
    }

    /**
     * Update this rating in the database
     *
     * @param int $rating the integer value of this rating
     */
    public function update_rating($rating) {
        global $DB;

        $time = time();

        $data = new stdClass;
        $data->rating       = $rating;
        $data->timemodified = $time;

        $item = new stdclass();
        $item->id = $this->itemid;
        $items = array($item);

        $ratingoptions = new stdClass;
        $ratingoptions->context = $this->context;
        $ratingoptions->component = $this->component;
        $ratingoptions->ratingarea = $this->ratingarea;
        $ratingoptions->items = $items;
        $ratingoptions->aggregate = RATING_AGGREGATE_AVERAGE;//we dont actually care what aggregation method is applied
        $ratingoptions->scaleid = $this->scaleid;
        $ratingoptions->userid = $this->userid;

        $rm = new rating_manager();
        $items = $rm->get_ratings($ratingoptions);
        $firstitem = $items[0]->rating;

        if (empty($firstitem->id)) {
            // Insert a new rating
            $data->contextid    = $this->context->id;
            $data->component    = $this->component;
            $data->ratingarea   = $this->ratingarea;
            $data->rating       = $rating;
            $data->scaleid      = $this->scaleid;
            $data->userid       = $this->userid;
            $data->itemid       = $this->itemid;
            $data->timecreated  = $time;
            $data->timemodified = $time;
            $DB->insert_record('rating', $data);
        } else {
            // Update the rating
            $data->id           = $firstitem->id;
            $DB->update_record('rating', $data);
        }
    }

    /**
     * Retreive the integer value of this rating
     *
     * @return int the integer value of this rating object
     */
    public function get_rating() {
        return $this->rating;
    }

    /**
     * Returns this ratings aggregate value as a string.
     *
     * @return string ratings aggregate value
     */
    public function get_aggregate_string() {

        $aggregate = $this->aggregate;
        $method = $this->settings->aggregationmethod;

        // only display aggregate if aggregation method isn't COUNT
        $aggregatestr = '';
        if ($aggregate && $method != RATING_AGGREGATE_COUNT) {
            if ($method != RATING_AGGREGATE_SUM && !$this->settings->scale->isnumeric) {
                $aggregatestr .= $this->settings->scale->scaleitems[round($aggregate)]; //round aggregate as we're using it as an index
            } else { // aggregation is SUM or the scale is numeric
                $aggregatestr .= round($aggregate, 1);
            }
        }

        return $aggregatestr;
    }

    /**
     * Returns true if the user is able to rate this rating object
     *
     * @param int $userid Current user assumed if left empty
     * @return bool true if the user is able to rate this rating object
     */
    public function user_can_rate($userid = null) {
        if (empty($userid)) {
            global $USER;
            $userid = $USER->id;
        }
        // You can't rate your item
        if ($this->itemuserid == $userid) {
            return false;
        }
        // You can't rate if you don't have the system cap
        if (!$this->settings->permissions->rate) {
            return false;
        }
        // You can't rate if you don't have the plugin cap
        if (!$this->settings->pluginpermissions->rate) {
            return false;
        }

        // You can't rate if the item was outside of the assessment times
        $timestart = $this->settings->assesstimestart;
        $timefinish = $this->settings->assesstimefinish;
        $timecreated = $this->itemtimecreated;
        if (!empty($timestart) && !empty($timefinish) && ($timecreated < $timestart || $timecreated > $timefinish)) {
            return false;
        }
        return true;
    }

    /**
     * Returns true if the user is able to view the aggregate for this rating object.
     *
     * @param int|null $userid If left empty the current user is assumed.
     * @return bool true if the user is able to view the aggregate for this rating object
     */
    public function user_can_view_aggregate($userid = null) {
        if (empty($userid)) {
            global $USER;
            $userid = $USER->id;
        }

        // if the item doesnt belong to anyone or its another user's items and they can see the aggregate on items they don't own
        // Note that viewany doesnt mean you can see the aggregate or ratings of your own items
        if ((empty($this->itemuserid) or $this->itemuserid != $userid) && $this->settings->permissions->viewany && $this->settings->pluginpermissions->viewany ) {
            return true;
        }

        // if its the current user's item and they have permission to view the aggregate on their own items
        if ($this->itemuserid == $userid && $this->settings->permissions->view && $this->settings->pluginpermissions->view) {
            return true;
        }

        return false;
    }

    /**
     * Returns a URL to view all of the ratings for the item this rating is for.
     *
     * If this is a rating of a post then this URL will take the user to a page that shows all of the ratings for the post
     * (this one included).
     *
     * @param bool $popup whether of not the URL should be loaded in a popup
     * @return moodle_url URL to view all of the ratings for the item this rating is for.
     */
    public function get_view_ratings_url($popup = false) {
        $attributes = array(
            'contextid'  => $this->context->id,
            'component'  => $this->component,
            'ratingarea' => $this->ratingarea,
            'itemid'     => $this->itemid,
            'scaleid'    => $this->settings->scale->id
        );
        if ($popup) {
            $attributes['popup'] = 1;
        }
        return new moodle_url('/rating/index.php', $attributes);
    }

    /**
     * Returns a URL that can be used to rate the associated item.
     *
     * @param int|null          $rating    The rating to give the item, if null then no rating param is added.
     * @param moodle_url|string $returnurl The URL to return to.
     * @return moodle_url can be used to rate the associated item.
     */
    public function get_rate_url($rating = null, $returnurl = null) {
        if (empty($returnurl)) {
            if (!empty($this->settings->returnurl)) {
                $returnurl = $this->settings->returnurl;
            } else {
                global $PAGE;
                $returnurl = $PAGE->url;
            }
        }
        $args = array(
            'contextid'   => $this->context->id,
            'component'   => $this->component,
            'ratingarea'  => $this->ratingarea,
            'itemid'      => $this->itemid,
            'scaleid'     => $this->settings->scale->id,
            'returnurl'   => $returnurl,
            'rateduserid' => $this->itemuserid,
            'aggregation' => $this->settings->aggregationmethod,
            'sesskey'     => sesskey()
        );
        if (!empty($rating)) {
            $args['rating'] = $rating;
        }
        $url = new moodle_url('/rating/rate.php', $args);
        return $url;
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
 * @package   core_rating
 * @category  rating
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class rating_manager {

    /**
     * @var array An array of calculated scale options to save us generating them for each request.
     */
    protected $scales = array();

    /**
     * Delete one or more ratings. Specify either a rating id, an item id or just the context id.
     *
     * @global moodle_database $DB
     * @param stdClass $options {
     *            contextid => int the context in which the ratings exist [required]
     *            ratingid => int the id of an individual rating to delete [optional]
     *            userid => int delete the ratings submitted by this user. May be used in conjuction with itemid [optional]
     *            itemid => int delete all ratings attached to this item [optional]
     *            component => string The component to delete ratings from [optional]
     *            ratingarea => string The ratingarea to delete ratings from [optional]
     * }
     */
    public function delete_ratings($options) {
        global $DB;

        if (empty($options->contextid)) {
            throw new coding_exception('The context option is a required option when deleting ratings.');
        }

        $conditions = array('contextid' => $options->contextid);
        $possibleconditions = array(
            'ratingid'   => 'id',
            'userid'     => 'userid',
            'itemid'     => 'itemid',
            'component'  => 'component',
            'ratingarea' => 'ratingarea'
        );
        foreach ($possibleconditions as $option => $field) {
            if (isset($options->{$option})) {
                $conditions[$field] = $options->{$option};
            }
        }
        $DB->delete_records('rating', $conditions);
    }

    /**
     * Returns an array of ratings for a given item (forum post, glossary entry etc). This returns all users ratings for a single item
     *
     * @param stdClass $options {
     *            context => context the context in which the ratings exists [required]
     *            component => component using ratings ie mod_forum [required]
     *            ratingarea => ratingarea to associate this rating with [required]
     *            itemid  =>  int the id of the associated item (forum post, glossary item etc) [required]
     *            sort    => string SQL sort by clause [optional]
     * }
     * @return array an array of ratings
     */
    public function get_all_ratings_for_item($options) {
        global $DB;

        if (!isset($options->context)) {
            throw new coding_exception('The context option is a required option when getting ratings for an item.');
        }
        if (!isset($options->itemid)) {
            throw new coding_exception('The itemid option is a required option when getting ratings for an item.');
        }
        if (!isset($options->component)) {
            throw new coding_exception('The component option is now a required option when getting ratings for an item.');
        }
        if (!isset($options->ratingarea)) {
            throw new coding_exception('The ratingarea option is now a required option when getting ratings for an item.');
        }

        $sortclause = '';
        if( !empty($options->sort) ) {
            $sortclause = "ORDER BY $options->sort";
        }

        $params = array(
            'contextid'  => $options->context->id,
            'itemid'     => $options->itemid,
            'component'  => $options->component,
            'ratingarea' => $options->ratingarea,
        );
        $userfields = user_picture::fields('u', null, 'userid');
        $sql = "SELECT r.id, r.rating, r.itemid, r.userid, r.timemodified, r.component, r.ratingarea, $userfields
                  FROM {rating} r
             LEFT JOIN {user} u ON r.userid = u.id
                 WHERE r.contextid = :contextid AND
                       r.itemid  = :itemid AND
                       r.component = :component AND
                       r.ratingarea = :ratingarea
                       {$sortclause}";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Adds rating objects to an array of items (forum posts, glossary entries etc). Rating objects are available at $item->rating
     *
     * @param stdClass $options {
     *            context          => context the context in which the ratings exists [required]
     *            component        => the component name ie mod_forum [required]
     *            ratingarea       => the ratingarea we are interested in [required]
     *            items            => array an array of items such as forum posts or glossary items. They must have an 'id' member ie $items[0]->id[required]
     *            aggregate        => int what aggregation method should be applied. RATING_AGGREGATE_AVERAGE, RATING_AGGREGATE_MAXIMUM etc [required]
     *            scaleid          => int the scale from which the user can select a rating [required]
     *            userid           => int the id of the current user [optional]
     *            returnurl        => string the url to return the user to after submitting a rating. Can be left null for ajax requests [optional]
     *            assesstimestart  => int only allow rating of items created after this timestamp [optional]
     *            assesstimefinish => int only allow rating of items created before this timestamp [optional]
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

        if (!isset($options->aggregate)) {
            throw new coding_exception('The aggregate option is a required option when getting ratings.');
        } else if ($options->aggregate == RATING_AGGREGATE_NONE) {
            // Ratings arn't enabled.
            return $options->items;
        }
        $aggregatestr = $this->get_aggregation_method($options->aggregate);

        // Default the userid to the current user if it is not set
        if (empty($options->userid)) {
            $userid = $USER->id;
        } else {
            $userid = $options->userid;
        }

        // Get the item table name, the item id field, and the item user field for the given rating item
        // from the related component.
        list($type, $name) = core_component::normalize_component($options->component);
        $default = array(null, 'id', 'userid');
        list($itemtablename, $itemidcol, $itemuseridcol) = plugin_callback($type, $name, 'rating', 'get_item_fields', array($options), $default);

        // Create an array of item ids
        $itemids = array();
        foreach ($options->items as $item) {
            $itemids[] = $item->{$itemidcol};
        }

        // get the items from the database
        list($itemidtest, $params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        $params['contextid'] = $options->context->id;
        $params['userid']    = $userid;
        $params['component']    = $options->component;
        $params['ratingarea'] = $options->ratingarea;

        $sql = "SELECT r.id, r.itemid, r.userid, r.scaleid, r.rating AS usersrating
                  FROM {rating} r
                 WHERE r.userid = :userid AND
                       r.contextid = :contextid AND
                       r.itemid {$itemidtest} AND
                       r.component = :component AND
                       r.ratingarea = :ratingarea
              ORDER BY r.itemid";
        $userratings = $DB->get_records_sql($sql, $params);

        $sql = "SELECT r.itemid, $aggregatestr(r.rating) AS aggrrating, COUNT(r.rating) AS numratings
                  FROM {rating} r
                 WHERE r.contextid = :contextid AND
                       r.itemid {$itemidtest} AND
                       r.component = :component AND
                       r.ratingarea = :ratingarea
              GROUP BY r.itemid, r.component, r.ratingarea, r.contextid
              ORDER BY r.itemid";
        $aggregateratings = $DB->get_records_sql($sql, $params);

        $ratingoptions = new stdClass;
        $ratingoptions->context = $options->context;
        $ratingoptions->component = $options->component;
        $ratingoptions->ratingarea = $options->ratingarea;
        $ratingoptions->settings = $this->generate_rating_settings_object($options);
        foreach ($options->items as $item) {
            $founduserrating = false;
            foreach($userratings as $userrating) {
                //look for an existing rating from this user of this item
                if ($item->{$itemidcol} == $userrating->itemid) {
                    // Note: rec->scaleid = the id of scale at the time the rating was submitted
                    // may be different from the current scale id
                    $ratingoptions->scaleid = $userrating->scaleid;
                    $ratingoptions->userid = $userrating->userid;
                    $ratingoptions->id = $userrating->id;
                    $ratingoptions->rating = min($userrating->usersrating, $ratingoptions->settings->scale->max);

                    $founduserrating = true;
                    break;
                }
            }
            if (!$founduserrating) {
                $ratingoptions->scaleid = null;
                $ratingoptions->userid = null;
                $ratingoptions->id = null;
                $ratingoptions->rating =  null;
            }

            if (array_key_exists($item->{$itemidcol}, $aggregateratings)) {
                $rec = $aggregateratings[$item->{$itemidcol}];
                $ratingoptions->itemid = $item->{$itemidcol};
                $ratingoptions->aggregate = min($rec->aggrrating, $ratingoptions->settings->scale->max);
                $ratingoptions->count = $rec->numratings;
            } else {
                $ratingoptions->itemid = $item->{$itemidcol};
                $ratingoptions->aggregate = null;
                $ratingoptions->count = 0;
            }

            $rating = new rating($ratingoptions);
            $rating->itemtimecreated = $this->get_item_time_created($item);
            if (!empty($item->{$itemuseridcol})) {
                $rating->itemuserid = $item->{$itemuseridcol};
            }
            $item->rating = $rating;
        }

        return $options->items;
    }

    /**
     * Generates a rating settings object based upon the options it is provided.
     *
     * @param stdClass $options {
     *      context           => context the context in which the ratings exists [required]
     *      component         => string The component the items belong to [required]
     *      ratingarea        => string The ratingarea the items belong to [required]
     *      aggregate         => int what aggregation method should be applied. RATING_AGGREGATE_AVERAGE, RATING_AGGREGATE_MAXIMUM etc [required]
     *      scaleid           => int the scale from which the user can select a rating [required]
     *      returnurl         => string the url to return the user to after submitting a rating. Can be left null for ajax requests [optional]
     *      assesstimestart   => int only allow rating of items created after this timestamp [optional]
     *      assesstimefinish  => int only allow rating of items created before this timestamp [optional]
     *      plugintype        => string plugin type ie 'mod' Used to find the permissions callback [optional]
     *      pluginname        => string plugin name ie 'forum' Used to find the permissions callback [optional]
     * }
     * @return stdClass rating settings object
     */
    protected function generate_rating_settings_object($options) {

        if (!isset($options->context)) {
            throw new coding_exception('The context option is a required option when generating a rating settings object.');
        }
        if (!isset($options->component)) {
            throw new coding_exception('The component option is now a required option when generating a rating settings object.');
        }
        if (!isset($options->ratingarea)) {
            throw new coding_exception('The ratingarea option is now a required option when generating a rating settings object.');
        }
        if (!isset($options->aggregate)) {
            throw new coding_exception('The aggregate option is now a required option when generating a rating settings object.');
        }
        if (!isset($options->scaleid)) {
            throw new coding_exception('The scaleid option is now a required option when generating a rating settings object.');
        }

        // settings that are common to all ratings objects in this context
        $settings = new stdClass;
        $settings->scale             = $this->generate_rating_scale_object($options->scaleid); // the scale to use now
        $settings->aggregationmethod = $options->aggregate;
        $settings->assesstimestart   = null;
        $settings->assesstimefinish  = null;

        // Collect options into the settings object
        if (!empty($options->assesstimestart)) {
            $settings->assesstimestart = $options->assesstimestart;
        }
        if (!empty($options->assesstimefinish)) {
            $settings->assesstimefinish = $options->assesstimefinish;
        }
        if (!empty($options->returnurl)) {
            $settings->returnurl = $options->returnurl;
        }

        // check site capabilities
        $settings->permissions = new stdClass;
        $settings->permissions->view    = has_capability('moodle/rating:view', $options->context); // can view the aggregate of ratings of their own items
        $settings->permissions->viewany = has_capability('moodle/rating:viewany', $options->context); // can view the aggregate of ratings of other people's items
        $settings->permissions->viewall = has_capability('moodle/rating:viewall', $options->context); // can view individual ratings
        $settings->permissions->rate    = has_capability('moodle/rating:rate', $options->context); // can submit ratings

        // check module capabilities (mostly for backwards compatability with old modules that previously implemented their own ratings)
        $pluginpermissionsarray = $this->get_plugin_permissions_array($options->context->id, $options->component, $options->ratingarea);
        $settings->pluginpermissions = new stdClass;
        $settings->pluginpermissions->view    = $pluginpermissionsarray['view'];
        $settings->pluginpermissions->viewany = $pluginpermissionsarray['viewany'];
        $settings->pluginpermissions->viewall = $pluginpermissionsarray['viewall'];
        $settings->pluginpermissions->rate    = $pluginpermissionsarray['rate'];

        return $settings;
    }

    /**
     * Generates a scale object that can be returned
     *
     * @global moodle_database $DB moodle database object
     * @param int $scaleid scale-type identifier
     * @return stdClass scale for ratings
     */
    protected function generate_rating_scale_object($scaleid) {
        global $DB;
        if (!array_key_exists('s'.$scaleid, $this->scales)) {
            $scale = new stdClass;
            $scale->id = $scaleid;
            $scale->name = null;
            $scale->courseid = null;
            $scale->scaleitems = array();
            $scale->isnumeric = true;
            $scale->max = $scaleid;

            if ($scaleid < 0) {
                // It is a proper scale (not numeric)
                $scalerecord = $DB->get_record('scale', array('id' => abs($scaleid)));
                if ($scalerecord) {
                    // We need to generate an array with string keys starting at 1
                    $scalearray = explode(',', $scalerecord->scale);
                    $c = count($scalearray);
                    for ($i = 0; $i < $c; $i++) {
                        // treat index as a string to allow sorting without changing the value
                        $scale->scaleitems[(string)($i + 1)] = $scalearray[$i];
                    }
                    krsort($scale->scaleitems); // have the highest grade scale item appear first
                    $scale->isnumeric = false;
                    $scale->name = $scalerecord->name;
                    $scale->courseid = $scalerecord->courseid;
                    $scale->max = count($scale->scaleitems);
                }
            } else {
                //generate an array of values for numeric scales
                for($i = 0; $i <= (int)$scaleid; $i++) {
                    $scale->scaleitems[(string)$i] = $i;
                }
            }
            $this->scales['s'.$scaleid] = $scale;
        }
        return $this->scales['s'.$scaleid];
    }

    /**
     * Gets the time the given item was created
     *
     * TODO: MDL-31511 - Find a better solution for this, its not ideal to test for fields really we should be
     * asking the component the item belongs to what field to look for or even the value we
     * are looking for.
     *
     * @param stdClass $item
     * @return int|null return null if the created time is unavailable, otherwise return a timestamp
     */
    protected function get_item_time_created($item) {
        if( !empty($item->created) ) {
            return $item->created;//the forum_posts table has created instead of timecreated
        }
        else if(!empty($item->timecreated)) {
            return $item->timecreated;
        }
        else {
            return null;
        }
    }

    /**
     * Returns an array of grades calculated by aggregating item ratings.
     *
     * @param stdClass $options {
     *            userid => int the id of the user whose items have been rated. NOT the user who submitted the ratings. 0 to update all. [required]
     *            aggregationmethod => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [required]
     *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
     *            itemtable => int the table containing the items [required]
     *            itemtableusercolum => int the column of the user table containing the item owner's user id [required]
     *            component => The component for the ratings [required]
     *            ratingarea => The ratingarea for the ratings [required]
     *            contextid => int the context in which the rated items exist [optional]
     *            modulename => string the name of the module [optional]
     *            moduleid => int the id of the module instance [optional]
     * }
     * @return array the array of the user's grades
     */
    public function get_user_grades($options) {
        global $DB;

        $contextid = null;

        if (!isset($options->component)) {
            throw new coding_exception('The component option is now a required option when getting user grades from ratings.');
        }
        if (!isset($options->ratingarea)) {
            throw new coding_exception('The ratingarea option is now a required option when getting user grades from ratings.');
        }

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
            $moduleid   = intval($options->moduleid);

            // Going direct to the db for the context id seems wrong.
            $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
            $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = cm.id AND ctx.contextlevel = :contextlevel)";
            $sql = "SELECT cm.* $ctxselect
                      FROM {course_modules} cm
                 LEFT JOIN {modules} mo ON mo.id = cm.module
                 LEFT JOIN {{$modulename}} m ON m.id = cm.instance $ctxjoin
                     WHERE mo.name=:modulename AND
                           m.id=:moduleid";
            $params = array('modulename' => $modulename, 'moduleid' => $moduleid, 'contextlevel' => CONTEXT_MODULE);
            $contextrecord = $DB->get_record_sql($sql, $params, '*', MUST_EXIST);
            $contextid = $contextrecord->ctxid;
        }

        $params = array();
        $params['contextid']  = $contextid;
        $params['component']  = $options->component;
        $params['ratingarea'] = $options->ratingarea;
        $itemtable            = $options->itemtable;
        $itemtableusercolumn  = $options->itemtableusercolumn;
        $scaleid              = $options->scaleid;
        $aggregationstring    = $this->get_aggregation_method($options->aggregationmethod);

        //if userid is not 0 we only want the grade for a single user
        $singleuserwhere = '';
        if ($options->userid != 0) {
            $params['userid1'] = intval($options->userid);
            $singleuserwhere = "AND i.{$itemtableusercolumn} = :userid1";
        }

        //MDL-24648 The where line used to be "WHERE (r.contextid is null or r.contextid=:contextid)"
        //r.contextid will be null for users who haven't been rated yet
        //no longer including users who haven't been rated to reduce memory requirements
        $sql = "SELECT u.id as id, u.id AS userid, $aggregationstring(r.rating) AS rawgrade
                  FROM {user} u
             LEFT JOIN {{$itemtable}} i ON u.id=i.{$itemtableusercolumn}
             LEFT JOIN {rating} r ON r.itemid=i.id
                 WHERE r.contextid = :contextid AND
                       r.component = :component AND
                       r.ratingarea = :ratingarea
                       $singleuserwhere
              GROUP BY u.id";
        $results = $DB->get_records_sql($sql, $params);

        if ($results) {

            $scale = null;
            $max = 0;
            if ($options->scaleid >= 0) {
                //numeric
                $max = $options->scaleid;
            } else {
                //custom scales
                $scale = $DB->get_record('scale', array('id' => -$options->scaleid));
                if ($scale) {
                    $scale = explode(',', $scale->scale);
                    $max = count($scale);
                } else {
                    debugging('rating_manager::get_user_grades() received a scale ID that doesnt exist');
                }
            }

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
                    if (!empty($scale) && $result->rawgrade > $max) {
                        $results[$rid]->rawgrade = $max;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Returns array of aggregate types. Used by ratings.
     *
     * @return array aggregate types
     */
    public function get_aggregate_types() {
        return array (RATING_AGGREGATE_NONE     => get_string('aggregatenone', 'rating'),
                      RATING_AGGREGATE_AVERAGE  => get_string('aggregateavg', 'rating'),
                      RATING_AGGREGATE_COUNT    => get_string('aggregatecount', 'rating'),
                      RATING_AGGREGATE_MAXIMUM  => get_string('aggregatemax', 'rating'),
                      RATING_AGGREGATE_MINIMUM  => get_string('aggregatemin', 'rating'),
                      RATING_AGGREGATE_SUM      => get_string('aggregatesum', 'rating'));
    }

    /**
     * Converts an aggregation method constant into something that can be included in SQL
     *
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
                $aggregatestr = 'COUNT';
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
            default:
                $aggregatestr = 'AVG'; // Default to this to avoid real breakage - MDL-22270
                debugging('Incorrect call to get_aggregation_method(), was called with incorrect aggregate method ' . $aggregate, DEBUG_DEVELOPER);
        }
        return $aggregatestr;
    }

    /**
     * Looks for a callback like forum_rating_permissions() to retrieve permissions from the plugin whose items are being rated
     *
     * @param int $contextid The current context id
     * @param string $component the name of the component that is using ratings ie 'mod_forum'
     * @param string $ratingarea The area the rating is associated with
     * @return array rating related permissions
     */
    public function get_plugin_permissions_array($contextid, $component, $ratingarea) {
        $pluginpermissionsarray = null;
        $defaultpluginpermissions = array('rate'=>false,'view'=>false,'viewany'=>false,'viewall'=>false);//deny by default
        if (!empty($component)) {
            list($type, $name) = core_component::normalize_component($component);
            $pluginpermissionsarray = plugin_callback($type, $name, 'rating', 'permissions', array($contextid, $component, $ratingarea), $defaultpluginpermissions);
        } else {
            $pluginpermissionsarray = $defaultpluginpermissions;
        }
        return $pluginpermissionsarray;
    }

    /**
     * Validates a submitted rating
     *
     * @param array $params submitted data
     *            context => object the context in which the rated items exists [required]
     *            component => The component the rating belongs to [required]
     *            ratingarea => The ratingarea the rating is associated with [required]
     *            itemid => int the ID of the object being rated [required]
     *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
     *            rating => int the submitted rating
     *            rateduserid => int the id of the user whose items have been rated. NOT the user who submitted the ratings. 0 to update all. [required]
     *            aggregation => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [optional]
     * @return boolean true if the rating is valid. False if callback wasnt found and will throw rating_exception if rating is invalid
     */
    public function check_rating_is_valid($params) {

        if (!isset($params['context'])) {
            throw new coding_exception('The context option is a required option when checking rating validity.');
        }
        if (!isset($params['component'])) {
            throw new coding_exception('The component option is now a required option when checking rating validity');
        }
        if (!isset($params['ratingarea'])) {
            throw new coding_exception('The ratingarea option is now a required option when checking rating validity');
        }
        if (!isset($params['itemid'])) {
            throw new coding_exception('The itemid option is now a required option when checking rating validity');
        }
        if (!isset($params['scaleid'])) {
            throw new coding_exception('The scaleid option is now a required option when checking rating validity');
        }
        if (!isset($params['rateduserid'])) {
            throw new coding_exception('The rateduserid option is now a required option when checking rating validity');
        }

        list($plugintype, $pluginname) = core_component::normalize_component($params['component']);

        //this looks for a function like forum_rating_validate() in mod_forum lib.php
        //wrapping the params array in another array as call_user_func_array() expands arrays into multiple arguments
        $isvalid = plugin_callback($plugintype, $pluginname, 'rating', 'validate', array($params), null);

        //if null then the callback doesn't exist
        if ($isvalid === null) {
            $isvalid = false;
            debugging('rating validation callback not found for component '.  clean_param($component, PARAM_ALPHANUMEXT));
        }
        return $isvalid;
    }

    /**
     * Initialises JavaScript to enable AJAX ratings on the provided page
     *
     * @param moodle_page $page
     * @return true always returns true
     */
    public function initialise_rating_javascript(moodle_page $page) {
        global $CFG;

        //only needs to be initialized once
        static $done = false;
        if ($done) {
            return true;
        }

        $page->requires->js_init_call('M.core_rating.init');
        $done = true;

        return true;
    }

    /**
     * Returns a string that describes the aggregation method that was provided.
     *
     * @param string $aggregationmethod
     * @return string describes the aggregation method that was provided
     */
    public function get_aggregate_label($aggregationmethod) {
        $aggregatelabel = '';
        switch ($aggregationmethod) {
            case RATING_AGGREGATE_AVERAGE :
                $aggregatelabel .= get_string("aggregateavg", "rating");
                break;
            case RATING_AGGREGATE_COUNT :
                $aggregatelabel .= get_string("aggregatecount", "rating");
                break;
            case RATING_AGGREGATE_MAXIMUM :
                $aggregatelabel .= get_string("aggregatemax", "rating");
                break;
            case RATING_AGGREGATE_MINIMUM :
                $aggregatelabel .= get_string("aggregatemin", "rating");
                break;
            case RATING_AGGREGATE_SUM :
                $aggregatelabel .= get_string("aggregatesum", "rating");
                break;
        }
        $aggregatelabel .= get_string('labelsep', 'langconfig');
        return $aggregatelabel;
    }

}//end rating_manager class definition

/**
 * The rating_exception class provides the ability to generate exceptions that can be easily identified as coming from the ratings system
 *
 * @package   core_rating
 * @category  rating
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class rating_exception extends moodle_exception {
    /**
     * @var string The message to accompany the thrown exception
     */
    public $message;
    /**
     * Generate exceptions that can be easily identified as coming from the ratings system
     *
     * @param string $errorcode the error code to generate
     */
    function __construct($errorcode) {
        $this->errorcode = $errorcode;
        $this->message = get_string($errorcode, 'error');
    }
}
