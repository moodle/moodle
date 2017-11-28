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
 * Contains class core_tag_tag
 *
 * @package   core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents one tag and also contains lots of useful tag-related methods as static functions.
 *
 * Tags can be added to any database records.
 * $itemtype refers to the DB table name
 * $itemid refers to id field in this DB table
 * $component is the component that is responsible for the tag instance
 * $context is the affected context
 *
 * BASIC INSTRUCTIONS :
 *  - to "tag a blog post" (for example):
 *        core_tag_tag::set_item_tags('post', 'core', $blogpost->id, $context, $arrayoftags);
 *
 *  - to "remove all the tags on a blog post":
 *        core_tag_tag::remove_all_item_tags('post', 'core', $blogpost->id);
 *
 * set_item_tags() will create tags that do not exist yet.
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $rawname
 * @property-read int $tagcollid
 * @property-read int $userid
 * @property-read int $isstandard
 * @property-read string $description
 * @property-read int $descriptionformat
 * @property-read int $flag 0 if not flagged or positive integer if flagged
 * @property-read int $timemodified
 *
 * @package   core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_tag {

    /** @var stdClass data about the tag */
    protected $record = null;

    /** @var int indicates that both standard and not standard tags can be used (or should be returned) */
    const BOTH_STANDARD_AND_NOT = 0;

    /** @var int indicates that only standard tags can be used (or must be returned) */
    const STANDARD_ONLY = 1;

    /** @var int indicates that only non-standard tags should be returned - this does not really have use cases, left for BC  */
    const NOT_STANDARD_ONLY = -1;

    /** @var int option to hide standard tags when editing item tags */
    const HIDE_STANDARD = 2;

    /**
     * Constructor. Use functions get(), get_by_name(), etc.
     *
     * @param stdClass $record
     */
    protected function __construct($record) {
        if (empty($record->id)) {
            throw new coding_exeption("Record must contain at least field 'id'");
        }
        $this->record = $record;
    }

    /**
     * Magic getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->record->$name;
    }

    /**
     * Magic isset method
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->record->$name);
    }

    /**
     * Converts to object
     *
     * @return stdClass
     */
    public function to_object() {
        return fullclone($this->record);
    }

    /**
     * Returns tag name ready to be displayed
     *
     * @param bool $ashtml (default true) if true will return htmlspecialchars encoded string
     * @return string
     */
    public function get_display_name($ashtml = true) {
        return static::make_display_name($this->record, $ashtml);
    }

    /**
     * Prepares tag name ready to be displayed
     *
     * @param stdClass|core_tag_tag $tag record from db table tag, must contain properties name and rawname
     * @param bool $ashtml (default true) if true will return htmlspecialchars encoded string
     * @return string
     */
    public static function make_display_name($tag, $ashtml = true) {
        global $CFG;

        if (empty($CFG->keeptagnamecase)) {
            // This is the normalized tag name.
            $tagname = core_text::strtotitle($tag->name);
        } else {
            // Original casing of the tag name.
            $tagname = $tag->rawname;
        }

        // Clean up a bit just in case the rules change again.
        $tagname = clean_param($tagname, PARAM_TAG);

        return $ashtml ? htmlspecialchars($tagname) : $tagname;
    }

    /**
     * Adds one or more tag in the database.  This function should not be called directly : you should
     * use tag_set.
     *
     * @param   int      $tagcollid
     * @param   string|array $tags     one tag, or an array of tags, to be created
     * @param   bool     $isstandard type of tag to be created. A standard tag is kept even if there are no records tagged with it.
     * @return  array    tag objects indexed by their lowercase normalized names. Any boolean false in the array
     *                             indicates an error while adding the tag.
     */
    protected static function add($tagcollid, $tags, $isstandard = false) {
        global $USER, $DB;

        $tagobject = new stdClass();
        $tagobject->isstandard   = $isstandard ? 1 : 0;
        $tagobject->userid       = $USER->id;
        $tagobject->timemodified = time();
        $tagobject->tagcollid    = $tagcollid;

        $rv = array();
        foreach ($tags as $veryrawname) {
            $rawname = clean_param($veryrawname, PARAM_TAG);
            if (!$rawname) {
                $rv[$rawname] = false;
            } else {
                $obj = (object)(array)$tagobject;
                $obj->rawname = $rawname;
                $obj->name    = core_text::strtolower($rawname);
                $obj->id      = $DB->insert_record('tag', $obj);
                $rv[$obj->name] = new static($obj);

                \core\event\tag_created::create_from_tag($rv[$obj->name])->trigger();
            }
        }

        return $rv;
    }

    /**
     * Simple function to just return a single tag object by its id
     *
     * @param    int    $id
     * @param    string $returnfields which fields do we want returned from table {tag}.
     *                        Default value is 'id,name,rawname,tagcollid',
     *                        specify '*' to include all fields.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return   core_tag_tag|false  tag object
     */
    public static function get($id, $returnfields = 'id, name, rawname, tagcollid', $strictness = IGNORE_MISSING) {
        global $DB;
        $record = $DB->get_record('tag', array('id' => $id), $returnfields, $strictness);
        if ($record) {
            return new static($record);
        }
        return false;
    }

    /**
     * Simple function to just return a single tag object by its id
     *
     * @param    int[]  $ids
     * @param    string $returnfields which fields do we want returned from table {tag}.
     *                        Default value is 'id,name,rawname,tagcollid',
     *                        specify '*' to include all fields.
     * @return   core_tag_tag[] array of retrieved tags
     */
    public static function get_bulk($ids, $returnfields = 'id, name, rawname, tagcollid') {
        global $DB;
        $result = array();
        if (empty($ids)) {
            return $result;
        }
        list($sql, $params) = $DB->get_in_or_equal($ids);
        $records = $DB->get_records_select('tag', 'id '.$sql, $params, '', $returnfields);
        foreach ($records as $record) {
            $result[$record->id] = new static($record);
        }
        return $result;
    }

    /**
     * Simple function to just return a single tag object by tagcollid and name
     *
     * @param int $tagcollid tag collection to use,
     *        if 0 is given we will try to guess the tag collection and return the first match
     * @param string $name tag name
     * @param string $returnfields which fields do we want returned. This is a comma separated string
     *         containing any combination of 'id', 'name', 'rawname', 'tagcollid' or '*' to include all fields.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return core_tag_tag|false tag object
     */
    public static function get_by_name($tagcollid, $name, $returnfields='id, name, rawname, tagcollid',
                        $strictness = IGNORE_MISSING) {
        global $DB;
        if ($tagcollid == 0) {
            $tags = static::guess_by_name($name, $returnfields);
            if ($tags) {
                $tag = reset($tags);
                return $tag;
            } else if ($strictness == MUST_EXIST) {
                throw new dml_missing_record_exception('tag', 'name=?', array($name));
            }
            return false;
        }
        $name = core_text::strtolower($name);   // To cope with input that might just be wrong case.
        $params = array('name' => $name, 'tagcollid' => $tagcollid);
        $record = $DB->get_record('tag', $params, $returnfields, $strictness);
        if ($record) {
            return new static($record);
        }
        return false;
    }

    /**
     * Looking in all tag collections for the tag with the given name
     *
     * @param string $name tag name
     * @param string $returnfields
     * @return array array of core_tag_tag instances
     */
    public static function guess_by_name($name, $returnfields='id, name, rawname, tagcollid') {
        global $DB;
        if (empty($name)) {
            return array();
        }
        $tagcolls = core_tag_collection::get_collections();
        list($sql, $params) = $DB->get_in_or_equal(array_keys($tagcolls), SQL_PARAMS_NAMED);
        $params['name'] = core_text::strtolower($name);
        $tags = $DB->get_records_select('tag', 'name = :name AND tagcollid ' . $sql, $params, '', $returnfields);
        if (count($tags) > 1) {
            // Sort in the same order as tag collections.
            $tagcolls = core_tag_collection::get_collections();
            uasort($tags, function($a, $b) use ($tagcolls) {
                return $tagcolls[$a->tagcollid]->sortorder < $tagcolls[$b->tagcollid]->sortorder ? -1 : 1;
            });
        }
        $rv = array();
        foreach ($tags as $id => $tag) {
            $rv[$id] = new static($tag);
        }
        return $rv;
    }

    /**
     * Returns the list of tag objects by tag collection id and the list of tag names
     *
     * @param    int   $tagcollid
     * @param    array $tags array of tags to look for
     * @param    string $returnfields list of DB fields to return, must contain 'id', 'name' and 'rawname'
     * @return   array tag-indexed array of objects. No value for a key means the tag wasn't found.
     */
    public static function get_by_name_bulk($tagcollid, $tags, $returnfields = 'id, name, rawname, tagcollid') {
        global $DB;

        if (empty($tags)) {
            return array();
        }

        $cleantags = self::normalize(self::normalize($tags, false)); // Format: rawname => normalised name.

        list($namesql, $params) = $DB->get_in_or_equal(array_values($cleantags));
        array_unshift($params, $tagcollid);

        $recordset = $DB->get_recordset_sql("SELECT $returnfields FROM {tag} WHERE tagcollid = ? AND name $namesql", $params);

        $result = array_fill_keys($cleantags, null);
        foreach ($recordset as $record) {
            $result[$record->name] = new static($record);
        }
        $recordset->close();
        return $result;
    }


    /**
     * Function that normalizes a list of tag names.
     *
     * @param   array        $rawtags array of tags
     * @param   bool         $tolowercase convert to lower case?
     * @return  array        lowercased normalized tags, indexed by the normalized tag, in the same order as the original array.
     *                       (Eg: 'Banana' => 'banana').
     */
    public static function normalize($rawtags, $tolowercase = true) {
        $result = array();
        foreach ($rawtags as $rawtag) {
            $rawtag = trim($rawtag);
            if (strval($rawtag) !== '') {
                $clean = clean_param($rawtag, PARAM_TAG);
                if ($tolowercase) {
                    $result[$rawtag] = core_text::strtolower($clean);
                } else {
                    $result[$rawtag] = $clean;
                }
            }
        }
        return $result;
    }

    /**
     * Retrieves tags and/or creates them if do not exist yet
     *
     * @param int $tagcollid
     * @param array $tags array of raw tag names, do not have to be normalised
     * @param bool $isstandard create as standard tag (default false)
     * @return core_tag_tag[] array of tag objects indexed with lowercase normalised tag name
     */
    public static function create_if_missing($tagcollid, $tags, $isstandard = false) {
        $cleantags = self::normalize(array_filter(self::normalize($tags, false))); // Array rawname => normalised name .

        $result = static::get_by_name_bulk($tagcollid, $tags, '*');
        $existing = array_filter($result);
        $missing = array_diff_key(array_flip($cleantags), $existing); // Array normalised name => rawname.
        if ($missing) {
            $newtags = static::add($tagcollid, array_values($missing), $isstandard);
            foreach ($newtags as $tag) {
                $result[$tag->name] = $tag;
            }
        }
        return $result;
    }

    /**
     * Creates a URL to view a tag
     *
     * @param int $tagcollid
     * @param string $name
     * @param int $exclusivemode
     * @param int $fromctx context id where this tag cloud is displayed
     * @param int $ctx context id for tag view link
     * @param int $rec recursive argument for tag view link
     * @return \moodle_url
     */
    public static function make_url($tagcollid, $name, $exclusivemode = 0, $fromctx = 0, $ctx = 0, $rec = 1) {
        $coll = core_tag_collection::get_by_id($tagcollid);
        if (!empty($coll->customurl)) {
            $url = '/' . ltrim(trim($coll->customurl), '/');
        } else {
            $url = '/tag/index.php';
        }
        $params = array('tc' => $tagcollid, 'tag' => $name);
        if ($exclusivemode) {
            $params['excl'] = 1;
        }
        if ($fromctx) {
            $params['from'] = $fromctx;
        }
        if ($ctx) {
            $params['ctx'] = $ctx;
        }
        if (!$rec) {
            $params['rec'] = 0;
        }
        return new moodle_url($url, $params);
    }

    /**
     * Returns URL to view the tag
     *
     * @param int $exclusivemode
     * @param int $fromctx context id where this tag cloud is displayed
     * @param int $ctx context id for tag view link
     * @param int $rec recursive argument for tag view link
     * @return \moodle_url
     */
    public function get_view_url($exclusivemode = 0, $fromctx = 0, $ctx = 0, $rec = 1) {
        return static::make_url($this->record->tagcollid, $this->record->rawname,
            $exclusivemode, $fromctx, $ctx, $rec);
    }

    /**
     * Validates that the required fields were retrieved and retrieves them if missing
     *
     * @param array $list array of the fields that need to be validated
     * @param string $caller name of the function that requested it, for the debugging message
     */
    protected function ensure_fields_exist($list, $caller) {
        global $DB;
        $missing = array_diff($list, array_keys((array)$this->record));
        if ($missing) {
            debugging('core_tag_tag::' . $caller . '() must be called on fully retrieved tag object. Missing fields: '.
                    join(', ', $missing), DEBUG_DEVELOPER);
            $this->record = $DB->get_record('tag', array('id' => $this->record->id), '*', MUST_EXIST);
        }
    }

    /**
     * Deletes the tag instance given the record from tag_instance DB table
     *
     * @param stdClass $taginstance
     * @param bool $fullobject whether $taginstance contains all fields from DB table tag_instance
     *          (in this case it is safe to add a record snapshot to the event)
     * @return bool
     */
    protected function delete_instance_as_record($taginstance, $fullobject = false) {
        global $DB;

        $this->ensure_fields_exist(array('name', 'rawname', 'isstandard'), 'delete_instance_as_record');

        $DB->delete_records('tag_instance', array('id' => $taginstance->id));

        // We can not fire an event with 'null' as the contextid.
        if (is_null($taginstance->contextid)) {
            $taginstance->contextid = context_system::instance()->id;
        }

        // Trigger tag removed event.
        $taginstance->tagid = $this->id;
        \core\event\tag_removed::create_from_tag_instance($taginstance, $this->name, $this->rawname, $fullobject)->trigger();

        // If there are no other instances of the tag then consider deleting the tag as well.
        if (!$this->isstandard) {
            if (!$DB->record_exists('tag_instance', array('tagid' => $this->id))) {
                self::delete_tags($this->id);
            }
        }

        return true;
    }

    /**
     * Delete one instance of a tag.  If the last instance was deleted, it will also delete the tag, unless it is standard.
     *
     * @param    string $component component responsible for tagging. For BC it can be empty but in this case the
     *                  query will be slow because DB index will not be used.
     * @param    string $itemtype the type of the record for which to remove the instance
     * @param    int    $itemid   the id of the record for which to remove the instance
     * @param    int    $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     */
    protected function delete_instance($component, $itemtype, $itemid, $tiuserid = 0) {
        global $DB;
        $params = array('tagid' => $this->id,
                'itemtype' => $itemtype, 'itemid' => $itemid);
        if ($tiuserid) {
            $params['tiuserid'] = $tiuserid;
        }
        if ($component) {
            $params['component'] = $component;
        }

        $taginstance = $DB->get_record('tag_instance', $params);
        if (!$taginstance) {
            return;
        }
        $this->delete_instance_as_record($taginstance, true);
    }

    /**
     * Bulk delete all tag instances for a component or tag area
     *
     * @param string $component
     * @param string $itemtype (optional)
     * @param int $contextid (optional)
     */
    public static function delete_instances($component, $itemtype = null, $contextid = null) {
        global $DB;

        $sql = "SELECT ti.*, t.name, t.rawname, t.isstandard
                  FROM {tag_instance} ti
                  JOIN {tag} t
                    ON ti.tagid = t.id
                 WHERE ti.component = :component";
        $params = array('component' => $component);
        if (!is_null($contextid)) {
            $sql .= " AND ti.contextid = :contextid";
            $params['contextid'] = $contextid;
        }
        if (!is_null($itemtype)) {
            $sql .= " AND ti.itemtype = :itemtype";
            $params['itemtype'] = $itemtype;
        }
        if ($taginstances = $DB->get_records_sql($sql, $params)) {
            // Now remove all the tag instances.
            $DB->delete_records('tag_instance', $params);
            // Save the system context in case the 'contextid' column in the 'tag_instance' table is null.
            $syscontextid = context_system::instance()->id;
            // Loop through the tag instances and fire an 'tag_removed' event.
            foreach ($taginstances as $taginstance) {
                // We can not fire an event with 'null' as the contextid.
                if (is_null($taginstance->contextid)) {
                    $taginstance->contextid = $syscontextid;
                }

                // Trigger tag removed event.
                \core\event\tag_removed::create_from_tag_instance($taginstance, $taginstance->name,
                        $taginstance->rawname, true)->trigger();
            }
        }
    }

    /**
     * Adds a tag instance
     *
     * @param string $component
     * @param string $itemtype
     * @param string $itemid
     * @param context $context
     * @param int $ordering
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     * @return int id of tag_instance
     */
    protected function add_instance($component, $itemtype, $itemid, context $context, $ordering, $tiuserid = 0) {
        global $DB;
        $this->ensure_fields_exist(array('name', 'rawname'), 'add_instance');

        $taginstance = new StdClass;
        $taginstance->tagid        = $this->id;
        $taginstance->component    = $component ? $component : '';
        $taginstance->itemid       = $itemid;
        $taginstance->itemtype     = $itemtype;
        $taginstance->contextid    = $context->id;
        $taginstance->ordering     = $ordering;
        $taginstance->timecreated  = time();
        $taginstance->timemodified = $taginstance->timecreated;
        $taginstance->tiuserid     = $tiuserid;

        $taginstance->id = $DB->insert_record('tag_instance', $taginstance);

        // Trigger tag added event.
        \core\event\tag_added::create_from_tag_instance($taginstance, $this->name, $this->rawname, true)->trigger();

        return $taginstance->id;
    }

    /**
     * Updates the ordering on tag instance
     *
     * @param int $instanceid
     * @param int $ordering
     */
    protected function update_instance_ordering($instanceid, $ordering) {
        global $DB;
        $data = new stdClass();
        $data->id = $instanceid;
        $data->ordering = $ordering;
        $data->timemodified = time();

        $DB->update_record('tag_instance', $data);
    }

    /**
     * Get the array of core_tag_tag objects associated with an item (instances).
     *
     * Use {@link core_tag_tag::get_item_tags_array()} if you wish to get the same data as simple array.
     *
     * @param string $component component responsible for tagging. For BC it can be empty but in this case the
     *               query will be slow because DB index will not be used.
     * @param string $itemtype type of the tagged item
     * @param int $itemid
     * @param int $standardonly wether to return only standard tags or any
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging
     * @return core_tag_tag[] each object contains additional fields taginstanceid, taginstancecontextid and ordering
     */
    public static function get_item_tags($component, $itemtype, $itemid, $standardonly = self::BOTH_STANDARD_AND_NOT,
            $tiuserid = 0) {
        global $DB;

        if (static::is_enabled($component, $itemtype) === false) {
            // Tagging area is properly defined but not enabled - return empty array.
            return array();
        }

        $standardonly = (int)$standardonly; // In case somebody passed bool.

        // Note: if the fields in this query are changed, you need to do the same changes in core_tag_tag::get_correlated_tags().
        $sql = "SELECT ti.id AS taginstanceid, tg.id, tg.isstandard, tg.name, tg.rawname, tg.flag,
                    tg.tagcollid, ti.ordering, ti.contextid AS taginstancecontextid
                  FROM {tag_instance} ti
                  JOIN {tag} tg ON tg.id = ti.tagid
                  WHERE ti.itemtype = :itemtype AND ti.itemid = :itemid ".
                ($component ? "AND ti.component = :component " : "").
                ($tiuserid ? "AND ti.tiuserid = :tiuserid " : "").
                (($standardonly == self::STANDARD_ONLY) ? "AND tg.isstandard = 1 " : "").
                (($standardonly == self::NOT_STANDARD_ONLY) ? "AND tg.isstandard = 0 " : "").
               "ORDER BY ti.ordering ASC, ti.id";

        $params = array();
        $params['itemtype'] = $itemtype;
        $params['itemid'] = $itemid;
        $params['component'] = $component;
        $params['tiuserid'] = $tiuserid;

        $records = $DB->get_records_sql($sql, $params);
        $result = array();
        foreach ($records as $id => $record) {
            $result[$id] = new static($record);
        }
        return $result;
    }

    /**
     * Returns the list of display names of the tags that are associated with an item
     *
     * This method is usually used to prefill the form data for the 'tags' form element
     *
     * @param string $component component responsible for tagging. For BC it can be empty but in this case the
     *               query will be slow because DB index will not be used.
     * @param string $itemtype type of the tagged item
     * @param int $itemid
     * @param int $standardonly wether to return only standard tags or any
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging
     * @param bool $ashtml (default true) if true will return htmlspecialchars encoded tag names
     * @return string[] array of tags display names
     */
    public static function get_item_tags_array($component, $itemtype, $itemid, $standardonly = self::BOTH_STANDARD_AND_NOT,
            $tiuserid = 0, $ashtml = true) {
        $tags = array();
        foreach (static::get_item_tags($component, $itemtype, $itemid, $standardonly, $tiuserid) as $tag) {
            $tags[$tag->id] = $tag->get_display_name($ashtml);
        }
        return $tags;
    }

    /**
     * Sets the list of tag instances for one item (table record).
     *
     * Extra exsisting instances are removed, new ones are added. New tags are created if needed.
     *
     * This method can not be used for setting tags relations, please use set_related_tags()
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype type of the tagged item
     * @param int $itemid
     * @param context $context
     * @param array $tagnames
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     */
    public static function set_item_tags($component, $itemtype, $itemid, context $context, $tagnames, $tiuserid = 0) {
        if ($itemtype === 'tag') {
            if ($tiuserid) {
                throw new coding_exeption('Related tags can not have tag instance userid');
            }
            debugging('You can not use set_item_tags() for tagging a tag, please use set_related_tags()', DEBUG_DEVELOPER);
            static::get($itemid, '*', MUST_EXIST)->set_related_tags($tagnames);
            return;
        }

        if ($tagnames !== null && static::is_enabled($component, $itemtype) === false) {
            // Tagging area is properly defined but not enabled - do nothing.
            // Unless we are deleting the item tags ($tagnames === null), in which case proceed with deleting.
            return;
        }

        // Apply clean_param() to all tags.
        if ($tagnames) {
            $tagcollid = core_tag_area::get_collection($component, $itemtype);
            $tagobjects = static::create_if_missing($tagcollid, $tagnames);
        } else {
            $tagobjects = array();
        }

        $currenttags = static::get_item_tags($component, $itemtype, $itemid, self::BOTH_STANDARD_AND_NOT, $tiuserid);

        // For data coherence reasons, it's better to remove deleted tags
        // before adding new data: ordering could be duplicated.
        foreach ($currenttags as $currenttag) {
            if (!array_key_exists($currenttag->name, $tagobjects)) {
                $taginstance = (object)array('id' => $currenttag->taginstanceid,
                    'itemtype' => $itemtype, 'itemid' => $itemid,
                    'contextid' => $currenttag->taginstancecontextid, 'tiuserid' => $tiuserid);
                $currenttag->delete_instance_as_record($taginstance, false);
            }
        }

        $ordering = -1;
        foreach ($tagobjects as $name => $tag) {
            $ordering++;
            foreach ($currenttags as $currenttag) {
                if (strval($currenttag->name) === strval($name)) {
                    if ($currenttag->ordering != $ordering) {
                        $currenttag->update_instance_ordering($currenttag->taginstanceid, $ordering);
                    }
                    continue 2;
                }
            }
            $tag->add_instance($component, $itemtype, $itemid, $context, $ordering, $tiuserid);
        }
    }

    /**
     * Removes all tags from an item.
     *
     * All tags will be removed even if tagging is disabled in this area. This is
     * usually called when the item itself has been deleted.
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype type of the tagged item
     * @param int $itemid
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     */
    public static function remove_all_item_tags($component, $itemtype, $itemid, $tiuserid = 0) {
        $context = context_system::instance(); // Context will not be used.
        static::set_item_tags($component, $itemtype, $itemid, $context, null, $tiuserid);
    }

    /**
     * Adds a tag to an item, without overwriting the current tags.
     *
     * If the tag has already been added to the record, no changes are made.
     *
     * @param string $component the component that was tagged
     * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
     * @param int $itemid the id of the record to tag
     * @param context $context the context of where this tag was assigned
     * @param string $tagname the tag to add
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     * @return int id of tag_instance that was either created or already existed or null if tagging is not enabled
     */
    public static function add_item_tag($component, $itemtype, $itemid, context $context, $tagname, $tiuserid = 0) {
        global $DB;

        if (static::is_enabled($component, $itemtype) === false) {
            // Tagging area is properly defined but not enabled - do nothing.
            return null;
        }

        $rawname = clean_param($tagname, PARAM_TAG);
        $normalisedname = core_text::strtolower($rawname);
        $tagcollid = core_tag_area::get_collection($component, $itemtype);

        $usersql = $tiuserid ? " AND ti.tiuserid = :tiuserid " : "";
        $sql = 'SELECT t.*, ti.id AS taginstanceid
                FROM {tag} t
                LEFT JOIN {tag_instance} ti ON ti.tagid = t.id AND ti.itemtype = :itemtype '.
                $usersql .
                'AND ti.itemid = :itemid AND ti.component = :component
                WHERE t.name = :name AND t.tagcollid = :tagcollid';
        $params = array('name' => $normalisedname, 'tagcollid' => $tagcollid, 'itemtype' => $itemtype,
            'itemid' => $itemid, 'component' => $component, 'tiuserid' => $tiuserid);
        $record = $DB->get_record_sql($sql, $params);
        if ($record) {
            if ($record->taginstanceid) {
                // Tag was already added to the item, nothing to do here.
                return $record->taginstanceid;
            }
            $tag = new static($record);
        } else {
            // The tag does not exist yet, create it.
            $tags = static::add($tagcollid, array($tagname));
            $tag = reset($tags);
        }

        $ordering = $DB->get_field_sql('SELECT MAX(ordering) FROM {tag_instance} ti
                WHERE ti.itemtype = :itemtype AND ti.itemid = itemid AND
                ti.component = :component' . $usersql, $params);

        return $tag->add_instance($component, $itemtype, $itemid, $context, $ordering + 1, $tiuserid);
    }

    /**
     * Removes the tag from an item without changing the other tags
     *
     * @param string $component the component that was tagged
     * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
     * @param int $itemid the id of the record to tag
     * @param string $tagname the tag to remove
     * @param int $tiuserid tag instance user id, only needed for tag areas with user tagging (such as core/course)
     */
    public static function remove_item_tag($component, $itemtype, $itemid, $tagname, $tiuserid = 0) {
        global $DB;

        if (static::is_enabled($component, $itemtype) === false) {
            // Tagging area is properly defined but not enabled - do nothing.
            return array();
        }

        $rawname = clean_param($tagname, PARAM_TAG);
        $normalisedname = core_text::strtolower($rawname);

        $usersql = $tiuserid ? " AND tiuserid = :tiuserid " : "";
        $componentsql = $component ? " AND ti.component = :component " : "";
        $sql = 'SELECT t.*, ti.id AS taginstanceid, ti.contextid AS taginstancecontextid, ti.ordering
                FROM {tag} t JOIN {tag_instance} ti ON ti.tagid = t.id ' . $usersql . '
                WHERE t.name = :name AND ti.itemtype = :itemtype
                AND ti.itemid = :itemid ' . $componentsql;
        $params = array('name' => $normalisedname,
            'itemtype' => $itemtype, 'itemid' => $itemid, 'component' => $component,
            'tiuserid' => $tiuserid);
        if ($record = $DB->get_record_sql($sql, $params)) {
            $taginstance = (object)array('id' => $record->taginstanceid,
                'itemtype' => $itemtype, 'itemid' => $itemid,
                'contextid' => $record->taginstancecontextid, 'tiuserid' => $tiuserid);
            $tag = new static($record);
            $tag->delete_instance_as_record($taginstance, false);
            $componentsql = $component ? " AND component = :component " : "";
            $sql = "UPDATE {tag_instance} SET ordering = ordering - 1
                    WHERE itemtype = :itemtype
                AND itemid = :itemid $componentsql $usersql
                AND ordering > :ordering";
            $params['ordering'] = $record->ordering;
            $DB->execute($sql, $params);
        }
    }

    /**
     * Allows to move all tag instances from one context to another
     *
     * @param string $component the component that was tagged
     * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
     * @param context $oldcontext
     * @param context $newcontext
     */
    public static function move_context($component, $itemtype, $oldcontext, $newcontext) {
        global $DB;
        if ($oldcontext instanceof context) {
            $oldcontext = $oldcontext->id;
        }
        if ($newcontext instanceof context) {
            $newcontext = $newcontext->id;
        }
        $DB->set_field('tag_instance', 'contextid', $newcontext,
                array('component' => $component, 'itemtype' => $itemtype, 'contextid' => $oldcontext));
    }

    /**
     * Moves all tags of the specified items to the new context
     *
     * @param string $component the component that was tagged
     * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
     * @param array $itemids
     * @param context|int $newcontext target context to move tags to
     */
    public static function change_items_context($component, $itemtype, $itemids, $newcontext) {
        global $DB;
        if (empty($itemids)) {
            return;
        }
        if (!is_array($itemids)) {
            $itemids = array($itemids);
        }
        list($sql, $params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        $params['component'] = $component;
        $params['itemtype'] = $itemtype;
        if ($newcontext instanceof context) {
            $newcontext = $newcontext->id;
        }
        $DB->set_field_select('tag_instance', 'contextid', $newcontext,
            'component = :component AND itemtype = :itemtype AND itemid ' . $sql, $params);
    }

    /**
     * Updates the information about the tag
     *
     * @param array|stdClass $data data to update, may contain: isstandard, description, descriptionformat, rawname
     * @return bool whether the tag was updated. False may be returned if: all new values match the existing,
     *         or it was attempted to rename the tag to the name that is already used.
     */
    public function update($data) {
        global $DB, $COURSE;

        $allowedfields = array('isstandard', 'description', 'descriptionformat', 'rawname');

        $data = (array)$data;
        if ($extrafields = array_diff(array_keys($data), $allowedfields)) {
            debugging('The field(s) '.join(', ', $extrafields).' will be ignored when updating the tag',
                    DEBUG_DEVELOPER);
        }
        $data = array_intersect_key($data, array_fill_keys($allowedfields, 1));
        $this->ensure_fields_exist(array_merge(array('tagcollid', 'userid', 'name', 'rawname'), array_keys($data)), 'update');

        // Validate the tag name.
        if (array_key_exists('rawname', $data)) {
            $data['rawname'] = clean_param($data['rawname'], PARAM_TAG);
            $name = core_text::strtolower($data['rawname']);

            if (!$name || $data['rawname'] === $this->rawname) {
                unset($data['rawname']);
            } else if ($existing = static::get_by_name($this->tagcollid, $name, 'id')) {
                // Prevent the rename if a tag with that name already exists.
                if ($existing->id != $this->id) {
                    throw new moodle_exception('namesalreadybeeingused', 'core_tag');
                }
            }
            if (isset($data['rawname'])) {
                $data['name'] = $name;
            }
        }

        // Validate the tag type.
        if (array_key_exists('isstandard', $data)) {
            $data['isstandard'] = $data['isstandard'] ? 1 : 0;
        }

        // Find only the attributes that need to be changed.
        $originalname = $this->name;
        foreach ($data as $key => $value) {
            if ($this->record->$key !== $value) {
                $this->record->$key = $value;
            } else {
                unset($data[$key]);
            }
        }
        if (empty($data)) {
            return false;
        }

        $data['id'] = $this->id;
        $data['timemodified'] = time();
        $DB->update_record('tag', $data);

        $event = \core\event\tag_updated::create(array(
            'objectid' => $this->id,
            'relateduserid' => $this->userid,
            'context' => context_system::instance(),
            'other' => array(
                'name' => $this->name,
                'rawname' => $this->rawname
            )
        ));
        if (isset($data['rawname'])) {
            $event->set_legacy_logdata(array($COURSE->id, 'tag', 'update', 'index.php?id='. $this->id,
                $originalname . '->'. $this->name));
        }
        $event->trigger();
        return true;
    }

    /**
     * Flag a tag as inappropriate
     */
    public function flag() {
        global $DB;

        $this->ensure_fields_exist(array('name', 'userid', 'rawname', 'flag'), 'flag');

        // Update all the tags to flagged.
        $this->timemodified = time();
        $this->flag++;
        $DB->update_record('tag', array('timemodified' => $this->timemodified,
            'flag' => $this->flag, 'id' => $this->id));

        $event = \core\event\tag_flagged::create(array(
            'objectid' => $this->id,
            'relateduserid' => $this->userid,
            'context' => context_system::instance(),
            'other' => array(
                'name' => $this->name,
                'rawname' => $this->rawname
            )

        ));
        $event->trigger();
    }

    /**
     * Remove the inappropriate flag on a tag.
     */
    public function reset_flag() {
        global $DB;

        $this->ensure_fields_exist(array('name', 'userid', 'rawname', 'flag'), 'flag');

        if (!$this->flag) {
            // Nothing to do.
            return false;
        }

        $this->timemodified = time();
        $this->flag = 0;
        $DB->update_record('tag', array('timemodified' => $this->timemodified,
            'flag' => 0, 'id' => $this->id));

        $event = \core\event\tag_unflagged::create(array(
            'objectid' => $this->id,
            'relateduserid' => $this->userid,
            'context' => context_system::instance(),
            'other' => array(
                'name' => $this->name,
                'rawname' => $this->rawname
            )
        ));
        $event->trigger();
    }

    /**
     * Sets the list of tags related to this one.
     *
     * Tag relations are recorded by two instances linking two tags to each other.
     * For tag relations ordering is not used and may be random.
     *
     * @param array $tagnames
     */
    public function set_related_tags($tagnames) {
        $context = context_system::instance();
        $tagobjects = $tagnames ? static::create_if_missing($this->tagcollid, $tagnames) : array();
        unset($tagobjects[$this->name]); // Never link to itself.

        $currenttags = static::get_item_tags('core', 'tag', $this->id);

        // For data coherence reasons, it's better to remove deleted tags
        // before adding new data: ordering could be duplicated.
        foreach ($currenttags as $currenttag) {
            if (!array_key_exists($currenttag->name, $tagobjects)) {
                $taginstance = (object)array('id' => $currenttag->taginstanceid,
                    'itemtype' => 'tag', 'itemid' => $this->id,
                    'contextid' => $context->id);
                $currenttag->delete_instance_as_record($taginstance, false);
                $this->delete_instance('core', 'tag', $currenttag->id);
            }
        }

        foreach ($tagobjects as $name => $tag) {
            foreach ($currenttags as $currenttag) {
                if ($currenttag->name === $name) {
                    continue 2;
                }
            }
            $this->add_instance('core', 'tag', $tag->id, $context, 0);
            $tag->add_instance('core', 'tag', $this->id, $context, 0);
            $currenttags[] = $tag;
        }
    }

    /**
     * Adds to the list of related tags without removing existing
     *
     * Tag relations are recorded by two instances linking two tags to each other.
     * For tag relations ordering is not used and may be random.
     *
     * @param array $tagnames
     */
    public function add_related_tags($tagnames) {
        $context = context_system::instance();
        $tagobjects = static::create_if_missing($this->tagcollid, $tagnames);

        $currenttags = static::get_item_tags('core', 'tag', $this->id);

        foreach ($tagobjects as $name => $tag) {
            foreach ($currenttags as $currenttag) {
                if ($currenttag->name === $name) {
                    continue 2;
                }
            }
            $this->add_instance('core', 'tag', $tag->id, $context, 0);
            $tag->add_instance('core', 'tag', $this->id, $context, 0);
            $currenttags[] = $tag;
        }
    }

    /**
     * Returns the correlated tags of a tag, retrieved from the tag_correlation table.
     *
     * Correlated tags are calculated in cron based on existing tag instances.
     *
     * @param bool $keepduplicates if true, will return one record for each existing
     *      tag instance which may result in duplicates of the actual tags
     * @return core_tag_tag[] an array of tag objects
     */
    public function get_correlated_tags($keepduplicates = false) {
        global $DB;

        $correlated = $DB->get_field('tag_correlation', 'correlatedtags', array('tagid' => $this->id));

        if (!$correlated) {
            return array();
        }
        $correlated = preg_split('/\s*,\s*/', trim($correlated), -1, PREG_SPLIT_NO_EMPTY);
        list($query, $params) = $DB->get_in_or_equal($correlated);

        // This is (and has to) return the same fields as the query in core_tag_tag::get_item_tags().
        $sql = "SELECT ti.id AS taginstanceid, tg.id, tg.isstandard, tg.name, tg.rawname, tg.flag,
                tg.tagcollid, ti.ordering, ti.contextid AS taginstancecontextid
              FROM {tag} tg
        INNER JOIN {tag_instance} ti ON tg.id = ti.tagid
             WHERE tg.id $query AND tg.id <> ? AND tg.tagcollid = ?
          ORDER BY ti.ordering ASC, ti.id";
        $params[] = $this->id;
        $params[] = $this->tagcollid;
        $records = $DB->get_records_sql($sql, $params);
        $seen = array();
        $result = array();
        foreach ($records as $id => $record) {
            if (!$keepduplicates && !empty($seen[$record->id])) {
                continue;
            }
            $result[$id] = new static($record);
            $seen[$record->id] = true;
        }
        return $result;
    }

    /**
     * Returns tags that this tag was manually set as related to
     *
     * @return core_tag_tag[]
     */
    public function get_manual_related_tags() {
        return self::get_item_tags('core', 'tag', $this->id);
    }

    /**
     * Returns tags related to a tag
     *
     * Related tags of a tag come from two sources:
     *   - manually added related tags, which are tag_instance entries for that tag
     *   - correlated tags, which are calculated
     *
     * @return core_tag_tag[] an array of tag objects
     */
    public function get_related_tags() {
        $manual = $this->get_manual_related_tags();
        $automatic = $this->get_correlated_tags();
        $relatedtags = array_merge($manual, $automatic);

        // Remove duplicated tags (multiple instances of the same tag).
        $seen = array();
        foreach ($relatedtags as $instance => $tag) {
            if (isset($seen[$tag->id])) {
                unset($relatedtags[$instance]);
            } else {
                $seen[$tag->id] = 1;
            }
        }

        return $relatedtags;
    }

    /**
     * Find all items tagged with a tag of a given type ('post', 'user', etc.)
     *
     * @param    string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                    query will be slow because DB index will not be used.
     * @param    string   $itemtype  type to restrict search to
     * @param    int      $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
     * @param    int      $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
     * @param    string   $subquery additional query to be appended to WHERE clause, refer to the itemtable as 'it'
     * @param    array    $params additional parameters for the DB query
     * @return   array of matching objects, indexed by record id, from the table containing the type requested
     */
    public function get_tagged_items($component, $itemtype, $limitfrom = '', $limitnum = '', $subquery = '', $params = array()) {
        global $DB;

        if (empty($itemtype) || !$DB->get_manager()->table_exists($itemtype)) {
            return array();
        }
        $params = $params ? $params : array();

        $query = "SELECT it.*
                    FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON it.id = tt.itemid
                   WHERE tt.itemtype = :itemtype AND tt.tagid = :tagid";
        $params['itemtype'] = $itemtype;
        $params['tagid'] = $this->id;
        if ($component) {
            $query .= ' AND tt.component = :component';
            $params['component'] = $component;
        }
        if ($subquery) {
            $query .= ' AND ' . $subquery;
        }
        $query .= ' ORDER BY it.id';

        return $DB->get_records_sql($query, $params, $limitfrom, $limitnum);
    }

    /**
     * Count how many items are tagged with a specific tag.
     *
     * @param    string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                    query will be slow because DB index will not be used.
     * @param    string   $itemtype  type to restrict search to
     * @param    string   $subquery additional query to be appended to WHERE clause, refer to the itemtable as 'it'
     * @param    array    $params additional parameters for the DB query
     * @return   int      number of mathing tags.
     */
    public function count_tagged_items($component, $itemtype, $subquery = '', $params = array()) {
        global $DB;

        if (empty($itemtype) || !$DB->get_manager()->table_exists($itemtype)) {
            return 0;
        }
        $params = $params ? $params : array();

        $query = "SELECT COUNT(it.id)
                    FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON it.id = tt.itemid
                   WHERE tt.itemtype = :itemtype AND tt.tagid = :tagid";
        $params['itemtype'] = $itemtype;
        $params['tagid'] = $this->id;
        if ($component) {
            $query .= ' AND tt.component = :component';
            $params['component'] = $component;
        }
        if ($subquery) {
            $query .= ' AND ' . $subquery;
        }

        return $DB->get_field_sql($query, $params);
    }

    /**
     * Determine if an item is tagged with a specific tag
     *
     * Note that this is a static method and not a method of core_tag object because the tag might not exist yet,
     * for example user searches for "php" and we offer him to add "php" to his interests.
     *
     * @param   string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                   query will be slow because DB index will not be used.
     * @param   string   $itemtype    the record type to look for
     * @param   int      $itemid      the record id to look for
     * @param   string   $tagname     a tag name
     * @return  int                   1 if it is tagged, 0 otherwise
     */
    public static function is_item_tagged_with($component, $itemtype, $itemid, $tagname) {
        global $DB;
        $tagcollid = core_tag_area::get_collection($component, $itemtype);
        $query = 'SELECT 1 FROM {tag} t
                    JOIN {tag_instance} ti ON ti.tagid = t.id
                    WHERE t.name = ? AND t.tagcollid = ? AND ti.itemtype = ? AND ti.itemid = ?';
        $cleanname = core_text::strtolower(clean_param($tagname, PARAM_TAG));
        $params = array($cleanname, $tagcollid, $itemtype, $itemid);
        if ($component) {
            $query .= ' AND ti.component = ?';
            $params[] = $component;
        }
        return $DB->record_exists_sql($query, $params) ? 1 : 0;
    }

    /**
     * Returns whether the tag area is enabled
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype what is being tagged, for example, 'post', 'course', 'user', etc.
     * @return bool|null
     */
    public static function is_enabled($component, $itemtype) {
        return core_tag_area::is_enabled($component, $itemtype);
    }

    /**
     * Retrieves contents of tag area for the tag/index.php page
     *
     * @param stdClass $tagarea
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param int $page 0-based number of page being displayed
     * @return \core_tag\output\tagindex
     */
    public function get_tag_index($tagarea, $exclusivemode, $fromctx, $ctx, $rec, $page = 0) {
        global $CFG;
        if (!empty($tagarea->callback)) {
            if (!empty($tagarea->callbackfile)) {
                require_once($CFG->dirroot . '/' . ltrim($tagarea->callbackfile, '/'));
            }
            $callback = $tagarea->callback;
            return call_user_func_array($callback, [$this, $exclusivemode, $fromctx, $ctx, $rec, $page]);
        }
        return null;
    }

    /**
     * Returns formatted description of the tag
     *
     * @param array $options
     * @return string
     */
    public function get_formatted_description($options = array()) {
        $options = empty($options) ? array() : (array)$options;
        $options += array('para' => false, 'overflowdiv' => true);
        $description = file_rewrite_pluginfile_urls($this->description, 'pluginfile.php',
                context_system::instance()->id, 'tag', 'description', $this->id);
        return format_text($description, $this->descriptionformat, $options);
    }

    /**
     * Returns the list of tag links available for the current user (edit, flag, etc.)
     *
     * @return array
     */
    public function get_links() {
        global $USER;
        $links = array();

        if (!isloggedin() || isguestuser()) {
            return $links;
        }

        $tagname = $this->get_display_name();
        $systemcontext = context_system::instance();

        // Add a link for users to add/remove this from their interests.
        if (static::is_enabled('core', 'user') && core_tag_area::get_collection('core', 'user') == $this->tagcollid) {
            if (static::is_item_tagged_with('core', 'user', $USER->id, $this->name)) {
                $url = new moodle_url('/tag/user.php', array('action' => 'removeinterest',
                    'sesskey' => sesskey(), 'tag' => $this->rawname));
                $links[] = html_writer::link($url, get_string('removetagfrommyinterests', 'tag', $tagname),
                        array('class' => 'removefrommyinterests'));
            } else {
                $url = new moodle_url('/tag/user.php', array('action' => 'addinterest',
                    'sesskey' => sesskey(), 'tag' => $this->rawname));
                $links[] = html_writer::link($url, get_string('addtagtomyinterests', 'tag', $tagname),
                        array('class' => 'addtomyinterests'));
            }
        }

        // Flag as inappropriate link.  Only people with moodle/tag:flag capability.
        if (has_capability('moodle/tag:flag', $systemcontext)) {
            $url = new moodle_url('/tag/user.php', array('action' => 'flaginappropriate',
                'sesskey' => sesskey(), 'id' => $this->id));
            $links[] = html_writer::link($url, get_string('flagasinappropriate', 'tag', $tagname),
                        array('class' => 'flagasinappropriate'));
        }

        // Edit tag: Only people with moodle/tag:edit capability who either have it as an interest or can manage tags.
        if (has_capability('moodle/tag:edit', $systemcontext) ||
                has_capability('moodle/tag:manage', $systemcontext)) {
            $url = new moodle_url('/tag/edit.php', array('id' => $this->id));
            $links[] = html_writer::link($url, get_string('edittag', 'tag'),
                        array('class' => 'edittag'));
        }

        return $links;
    }

    /**
     * Delete one or more tag, and all their instances if there are any left.
     *
     * @param    int|array    $tagids one tagid (int), or one array of tagids to delete
     * @return   bool     true on success, false otherwise
     */
    public static function delete_tags($tagids) {
        global $DB;

        if (!is_array($tagids)) {
            $tagids = array($tagids);
        }
        if (empty($tagids)) {
            return;
        }

        // Use the tagids to create a select statement to be used later.
        list($tagsql, $tagparams) = $DB->get_in_or_equal($tagids);

        // Store the tags and tag instances we are going to delete.
        $tags = $DB->get_records_select('tag', 'id ' . $tagsql, $tagparams);
        $taginstances = $DB->get_records_select('tag_instance', 'tagid ' . $tagsql, $tagparams);

        // Delete all the tag instances.
        $select = 'WHERE tagid ' . $tagsql;
        $sql = "DELETE FROM {tag_instance} $select";
        $DB->execute($sql, $tagparams);

        // Delete all the tag correlations.
        $sql = "DELETE FROM {tag_correlation} $select";
        $DB->execute($sql, $tagparams);

        // Delete all the tags.
        $select = 'WHERE id ' . $tagsql;
        $sql = "DELETE FROM {tag} $select";
        $DB->execute($sql, $tagparams);

        // Fire an event that these items were untagged.
        if ($taginstances) {
            // Save the system context in case the 'contextid' column in the 'tag_instance' table is null.
            $syscontextid = context_system::instance()->id;
            // Loop through the tag instances and fire a 'tag_removed'' event.
            foreach ($taginstances as $taginstance) {
                // We can not fire an event with 'null' as the contextid.
                if (is_null($taginstance->contextid)) {
                    $taginstance->contextid = $syscontextid;
                }

                // Trigger tag removed event.
                \core\event\tag_removed::create_from_tag_instance($taginstance,
                    $tags[$taginstance->tagid]->name, $tags[$taginstance->tagid]->rawname,
                    true)->trigger();
            }
        }

        // Fire an event that these tags were deleted.
        if ($tags) {
            $context = context_system::instance();
            foreach ($tags as $tag) {
                // Delete all files associated with this tag.
                $fs = get_file_storage();
                $files = $fs->get_area_files($context->id, 'tag', 'description', $tag->id);
                foreach ($files as $file) {
                    $file->delete();
                }

                // Trigger an event for deleting this tag.
                $event = \core\event\tag_deleted::create(array(
                    'objectid' => $tag->id,
                    'relateduserid' => $tag->userid,
                    'context' => $context,
                    'other' => array(
                        'name' => $tag->name,
                        'rawname' => $tag->rawname
                    )
                ));
                $event->add_record_snapshot('tag', $tag);
                $event->trigger();
            }
        }

        return true;
    }

    /**
     * Combine together correlated tags of several tags
     *
     * This is a help method for method combine_tags()
     *
     * @param core_tag_tag[] $tags
     */
    protected function combine_correlated_tags($tags) {
        global $DB;
        $ids = array_map(function($t) {
            return $t->id;
        }, $tags);

        // Retrieve the correlated tags of this tag and correlated tags of all tags to be merged in one query
        // but store them separately. Calculate the list of correlated tags that need to be added to the current.
        list($sql, $params) = $DB->get_in_or_equal($ids);
        $params[] = $this->id;
        $records = $DB->get_records_select('tag_correlation', 'tagid '.$sql.' OR tagid = ?',
            $params, '', 'tagid, id, correlatedtags');
        $correlated = array();
        $mycorrelated = array();
        foreach ($records as $record) {
            $taglist = preg_split('/\s*,\s*/', trim($record->correlatedtags), -1, PREG_SPLIT_NO_EMPTY);
            if ($record->tagid == $this->id) {
                $mycorrelated = $taglist;
            } else {
                $correlated = array_merge($correlated, $taglist);
            }
        }
        array_unique($correlated);
        // Strip out from $correlated the ids of the tags that are already in $mycorrelated
        // or are one of the tags that are going to be combined.
        $correlated = array_diff($correlated, [$this->id], $ids, $mycorrelated);

        if (empty($correlated)) {
            // Nothing to do, ignore situation when current tag is correlated to one of the merged tags - they will
            // be deleted later and get_tag_correlation() will not return them. Next cron will clean everything up.
            return;
        }

        // Update correlated tags of this tag.
        $newcorrelatedlist = join(',', array_merge($mycorrelated, $correlated));
        if (isset($records[$this->id])) {
            $DB->update_record('tag_correlation', array('id' => $records[$this->id]->id, 'correlatedtags' => $newcorrelatedlist));
        } else {
            $DB->insert_record('tag_correlation', array('tagid' => $this->id, 'correlatedtags' => $newcorrelatedlist));
        }

        // Add this tag to the list of correlated tags of each tag in $correlated.
        list($sql, $params) = $DB->get_in_or_equal($correlated);
        $records = $DB->get_records_select('tag_correlation', 'tagid '.$sql, $params, '', 'tagid, id, correlatedtags');
        foreach ($correlated as $tagid) {
            if (isset($records[$tagid])) {
                $newcorrelatedlist = $records[$tagid]->correlatedtags . ',' . $this->id;
                $DB->update_record('tag_correlation', array('id' => $records[$tagid]->id, 'correlatedtags' => $newcorrelatedlist));
            } else {
                $DB->insert_record('tag_correlation', array('tagid' => $tagid, 'correlatedtags' => '' . $this->id));
            }
        }
    }

    /**
     * Combines several other tags into this one
     *
     * Combining rules:
     * - current tag becomes the "main" one, all instances
     *   pointing to other tags are changed to point to it.
     * - if any of the tags is standard, the "main" tag becomes standard too
     * - all tags except for the current ("main") are deleted, even when they are standard
     *
     * @param core_tag_tag[] $tags tags to combine into this one
     */
    public function combine_tags($tags) {
        global $DB;

        $this->ensure_fields_exist(array('id', 'tagcollid', 'isstandard', 'name', 'rawname'), 'combine_tags');

        // Retrieve all tag objects, find if there are any standard tags in the set.
        $isstandard = false;
        $tagstocombine = array();
        $ids = array();
        $relatedtags = $this->get_manual_related_tags();
        foreach ($tags as $tag) {
            $tag->ensure_fields_exist(array('id', 'tagcollid', 'isstandard', 'tagcollid', 'name', 'rawname'), 'combine_tags');
            if ($tag && $tag->id != $this->id && $tag->tagcollid == $this->tagcollid) {
                $isstandard = $isstandard || $tag->isstandard;
                $tagstocombine[$tag->name] = $tag;
                $ids[] = $tag->id;
                $relatedtags = array_merge($relatedtags, $tag->get_manual_related_tags());
            }
        }

        if (empty($tagstocombine)) {
            // Nothing to do.
            return;
        }

        // Combine all manually set related tags, exclude itself all the tags it is about to be combined with.
        if ($relatedtags) {
            $relatedtags = array_map(function($t) {
                return $t->name;
            }, $relatedtags);
            array_unique($relatedtags);
            $relatedtags = array_diff($relatedtags, [$this->name], array_keys($tagstocombine));
        }
        $this->set_related_tags($relatedtags);

        // Combine all correlated tags, exclude itself all the tags it is about to be combined with.
        $this->combine_correlated_tags($tagstocombine);

        // If any of the duplicate tags are standard, mark this one as standard too.
        if ($isstandard && !$this->isstandard) {
            $this->update(array('isstandard' => 1));
        }

        // Go through all instances of each tag that needs to be combined and make them point to this tag instead.
        // We go though the list one by one because otherwise looking-for-duplicates logic would be too complicated.
        foreach ($tagstocombine as $tag) {
            $params = array('tagid' => $tag->id, 'mainid' => $this->id);
            $mainsql = 'SELECT ti.*, t.name, t.rawname, tim.id AS alreadyhasmaintag '
                    . 'FROM {tag_instance} ti '
                    . 'LEFT JOIN {tag} t ON t.id = ti.tagid '
                    . 'LEFT JOIN {tag_instance} tim ON ti.component = tim.component AND '
                    . '    ti.itemtype = tim.itemtype AND ti.itemid = tim.itemid AND '
                    . '    ti.tiuserid = tim.tiuserid AND tim.tagid = :mainid '
                    . 'WHERE ti.tagid = :tagid';

            $records = $DB->get_records_sql($mainsql, $params);
            foreach ($records as $record) {
                if ($record->alreadyhasmaintag) {
                    // Item is tagged with both main tag and the duplicate tag.
                    // Remove instance pointing to the duplicate tag.
                    $tag->delete_instance_as_record($record, false);
                    $sql = "UPDATE {tag_instance} SET ordering = ordering - 1
                            WHERE itemtype = :itemtype
                        AND itemid = :itemid AND component = :component AND tiuserid = :tiuserid
                        AND ordering > :ordering";
                    $DB->execute($sql, (array)$record);
                } else {
                    // Item is tagged only with duplicate tag but not the main tag.
                    // Replace tagid in the instance pointing to the duplicate tag with this tag.
                    $DB->update_record('tag_instance', array('id' => $record->id, 'tagid' => $this->id));
                    \core\event\tag_removed::create_from_tag_instance($record, $record->name, $record->rawname)->trigger();
                    $record->tagid = $this->id;
                    \core\event\tag_added::create_from_tag_instance($record, $this->name, $this->rawname)->trigger();
                }
            }
        }

        // Finally delete all tags that we combined into the current one.
        self::delete_tags($ids);
    }
}
