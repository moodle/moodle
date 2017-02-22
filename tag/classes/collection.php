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
 * Class to manage tag collections
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage tag collections
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_collection {

    /** @var string used for function cloud_sort() */
    public static $cloudsortfield = 'name';

    /**
     * Returns the list of tag collections defined in the system.
     *
     * @param bool $onlysearchable only return collections that can be searched.
     * @return array array of objects where each object has properties: id, name, isdefault, itemtypes, sortorder
     */
    public static function get_collections($onlysearchable = false) {
        global $DB;
        $cache = cache::make('core', 'tags');
        if (($tagcolls = $cache->get('tag_coll')) === false) {
            // Retrieve records from DB and create a default one if it is not present.
            $tagcolls = $DB->get_records('tag_coll', null, 'isdefault DESC, sortorder, id');
            if (empty($tagcolls)) {
                // When this method is called for the first time it automatically creates the default tag collection.
                $DB->insert_record('tag_coll', array('isdefault' => 1, 'sortorder' => 0));
                $tagcolls = $DB->get_records('tag_coll');
            } else {
                // Make sure sortorder is correct.
                $idx = 0;
                foreach ($tagcolls as $id => $tagcoll) {
                    if ($tagcoll->sortorder != $idx) {
                        $DB->update_record('tag_coll', array('sortorder' => $idx, 'id' => $id));
                        $tagcolls[$id]->sortorder = $idx;
                    }
                    $idx++;
                }
            }
            $cache->set('tag_coll', $tagcolls);
        }
        if ($onlysearchable) {
            $rv = array();
            foreach ($tagcolls as $id => $tagcoll) {
                if ($tagcoll->searchable) {
                    $rv[$id] = $tagcoll;
                }
            }
            return $rv;
        }
        return $tagcolls;
    }

    /**
     * Returns the tag collection object
     *
     * @param int $tagcollid
     * @return stdClass
     */
    public static function get_by_id($tagcollid) {
        $tagcolls = self::get_collections();
        if (array_key_exists($tagcollid, $tagcolls)) {
            return $tagcolls[$tagcollid];
        }
        return null;
    }

    /**
     * Returns the list of existing tag collections as id=>name
     *
     * @param bool $unlockedonly
     * @param bool $onlysearchable
     * @param string $selectalllabel
     * @return array
     */
    public static function get_collections_menu($unlockedonly = false, $onlysearchable = false,
            $selectalllabel = null) {
        $tagcolls = self::get_collections($onlysearchable);
        $options = array();
        foreach ($tagcolls as $id => $tagcoll) {
            if (!$unlockedonly || empty($tagcoll->component)) {
                $options[$id] = self::display_name($tagcoll);
            }
        }
        if (count($options) > 1 && $selectalllabel) {
            $options = array(0 => $selectalllabel) + $options;
        }
        return $options;
    }

    /**
     * Returns id of the default tag collection
     *
     * @return int
     */
    public static function get_default() {
        $collections = self::get_collections();
        $keys = array_keys($collections);
        return $keys[0];
    }

    /**
     * Returns formatted name of the tag collection
     *
     * @param stdClass $record record from DB table tag_coll
     * @return string
     */
    public static function display_name($record) {
        $syscontext = context_system::instance();
        if (!empty($record->component)) {
            $identifier = 'tagcollection_' .
                    clean_param($record->name, PARAM_STRINGID);
            $component = $record->component;
            if ($component === 'core') {
                $component = 'tag';
            }
            return get_string($identifier, $component);
        }
        if (!empty($record->name)) {
            return format_string($record->name, true, array('context' => $syscontext));
        } else if ($record->isdefault) {
            return get_string('defautltagcoll', 'tag');
        } else {
            return $record->id;
        }
    }

    /**
     * Returns all tag areas in the given tag collection
     *
     * @param int $tagcollid
     * @return array
     */
    public static function get_areas($tagcollid) {
        $allitemtypes = core_tag_area::get_areas($tagcollid, true);
        $itemtypes = array();
        foreach ($allitemtypes as $itemtype => $it) {
            foreach ($it as $component => $v) {
                $itemtypes[$v->id] = $v;
            }
        }
        return $itemtypes;
    }

    /**
     * Returns the list of names of areas (enabled only) that are in this collection.
     *
     * @param int $tagcollid
     * @return array
     */
    public static function get_areas_names($tagcollid, $enabledonly = true) {
        $allitemtypes = core_tag_area::get_areas($tagcollid, $enabledonly);
        $itemtypes = array();
        foreach ($allitemtypes as $itemtype => $it) {
            foreach ($it as $component => $v) {
                $itemtypes[$v->id] = core_tag_area::display_name($component, $itemtype);
            }
        }
        return $itemtypes;
    }

    /**
     * Creates a new tag collection
     *
     * @param stdClass $data data from form core_tag_collection_form
     * @return int|false id of created tag collection or false if failed
     */
    public static function create($data) {
        global $DB;
        $data = (object)$data;
        $tagcolls = self::get_collections();
        $tagcoll = (object)array(
            'name' => $data->name,
            'isdefault' => 0,
            'component' => !empty($data->component) ? $data->component : null,
            'sortorder' => count($tagcolls),
            'searchable' => isset($data->searchable) ? (int)(bool)$data->searchable : 1,
            'customurl' => !empty($data->customurl) ? $data->customurl : null,
        );
        $tagcoll->id = $DB->insert_record('tag_coll', $tagcoll);

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_coll');

        \core\event\tag_collection_created::create_from_record($tagcoll)->trigger();
        return $tagcoll;
    }

    /**
     * Updates the tag collection information
     *
     * @param stdClass $tagcoll existing record in DB table tag_coll
     * @param stdClass $data data to update
     * @return bool wether the record was updated
     */
    public static function update($tagcoll, $data) {
        global $DB;
        $defaulttagcollid = self::get_default();
        $allowedfields = array('name', 'searchable', 'customurl');
        if ($tagcoll->id == $defaulttagcollid) {
            $allowedfields = array('name');
        }

        $updatedata = array();
        $data = (array)$data;
        foreach ($allowedfields as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== $tagcoll->$key) {
                $updatedata[$key] = $data[$key];
            }
        }

        if (!$updatedata) {
            // Nothing to update.
            return false;
        }

        if (isset($updatedata['searchable'])) {
            $updatedata['searchable'] = (int)(bool)$updatedata['searchable'];
        }
        foreach ($updatedata as $key => $value) {
            $tagcoll->$key = $value;
        }
        $updatedata['id'] = $tagcoll->id;
        $DB->update_record('tag_coll', $updatedata);

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_coll');

        \core\event\tag_collection_updated::create_from_record($tagcoll)->trigger();

        return true;
    }

    /**
     * Deletes a custom tag collection
     *
     * @param stdClass $tagcoll existing record in DB table tag_coll
     * @return bool wether the tag collection was deleted
     */
    public static function delete($tagcoll) {
        global $DB, $CFG;

        $defaulttagcollid = self::get_default();
        if ($tagcoll->id == $defaulttagcollid) {
            return false;
        }

        // Move all tags from this tag collection to the default one.
        $allitemtypes = core_tag_area::get_areas($tagcoll->id);
        foreach ($allitemtypes as $it) {
            foreach ($it as $v) {
                core_tag_area::update($v, array('tagcollid' => $defaulttagcollid));
            }
        }

        // Delete tags from this tag_coll.
        core_tag_tag::delete_tags($DB->get_fieldset_select('tag', 'id', 'tagcollid = ?', array($tagcoll->id)));

        // Delete the tag collection.
        $DB->delete_records('tag_coll', array('id' => $tagcoll->id));

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_coll');

        \core\event\tag_collection_deleted::create_from_record($tagcoll)->trigger();

        return true;
    }

    /**
     * Moves the tag collection in the list one position up or down
     *
     * @param stdClass $tagcoll existing record in DB table tag_coll
     * @param int $direction move direction: +1 or -1
     * @return bool
     */
    public static function change_sortorder($tagcoll, $direction) {
        global $DB;
        if ($direction != -1 && $direction != 1) {
            throw coding_exception('Second argument in tag_coll_change_sortorder() can be only 1 or -1');
        }
        $tagcolls = self::get_collections();
        $keys = array_keys($tagcolls);
        $idx = array_search($tagcoll->id, $keys);
        if ($idx === false || $idx == 0 || $idx + $direction < 1 || $idx + $direction >= count($tagcolls)) {
            return false;
        }
        $otherid = $keys[$idx + $direction];
        $DB->update_record('tag_coll', array('id' => $tagcoll->id, 'sortorder' => $idx + $direction));
        $DB->update_record('tag_coll', array('id' => $otherid, 'sortorder' => $idx));
        // Reset cache.
        cache::make('core', 'tags')->delete('tag_coll');
        return true;
    }

    /**
     * Permanently deletes all non-standard tags that no longer have any instances pointing to them
     *
     * @param array $collections optional list of tag collections ids to cleanup
     */
    public static function cleanup_unused_tags($collections = null) {
        global $DB, $CFG;

        $params = array();
        $sql = "SELECT tg.id FROM {tag} tg LEFT OUTER JOIN {tag_instance} ti ON ti.tagid = tg.id
                WHERE ti.id IS NULL AND tg.isstandard = 0";
        if ($collections) {
            list($sqlcoll, $params) = $DB->get_in_or_equal($collections, SQL_PARAMS_NAMED);
            $sql .= " AND tg.tagcollid " . $sqlcoll;
        }
        if ($unusedtags = $DB->get_fieldset_sql($sql, $params)) {
            core_tag_tag::delete_tags($unusedtags);
        }
    }

    /**
     * Returns the list of tags with number of items tagged
     *
     * @param int $tagcollid
     * @param null|bool $isstandard return only standard tags
     * @param int $limit maximum number of tags to retrieve, tags are sorted by the instance count
     *            descending here regardless of $sort parameter
     * @param string $sort sort order for display, default 'name' - tags will be sorted after they are retrieved
     * @param string $search search string
     * @param int $fromctx context id where this tag cloud is displayed
     * @param int $ctx only retrieve tag instances in this context
     * @param int $rec retrieve tag instances in the $ctx context and it's children (default 1)
     * @return \core_tag\output\tagcloud
     */
    public static function get_tag_cloud($tagcollid, $isstandard = false, $limit = 150, $sort = 'name',
            $search = '', $fromctx = 0, $ctx = 0, $rec = 1) {
        global $DB;

        $fromclause = 'FROM {tag_instance} ti JOIN {tag} tg ON tg.id = ti.tagid';
        $whereclause = 'WHERE ti.itemtype <> \'tag\'';
        list($sql, $params) = $DB->get_in_or_equal($tagcollid ? array($tagcollid) :
            array_keys(self::get_collections(true)));
        $whereclause .= ' AND tg.tagcollid ' . $sql;
        if ($isstandard) {
            $whereclause .= ' AND tg.isstandard = 1';
        }
        $context = $ctx ? context::instance_by_id($ctx) : context_system::instance();
        if ($rec && $context->contextlevel != CONTEXT_SYSTEM) {
            $fromclause .= ' JOIN {context} ctx ON ctx.id = ti.contextid ';
            $whereclause .= ' AND ctx.path LIKE ?';
            $params[] = $context->path . '%';
        } else if (!$rec) {
            $whereclause .= ' AND ti.contextid = ?';
            $params[] = $context->id;
        }
        if (strval($search) !== '') {
            $whereclause .= ' AND tg.name LIKE ?';
            $params[] = '%' . core_text::strtolower($search) . '%';
        }
        $tagsincloud = $DB->get_records_sql(
                "SELECT tg.id, tg.rawname, tg.name, tg.isstandard, COUNT(ti.id) AS count, tg.flag, tg.tagcollid
                $fromclause
                $whereclause
                GROUP BY tg.id, tg.rawname, tg.name, tg.flag, tg.isstandard, tg.tagcollid
                ORDER BY count DESC, tg.name ASC",
            $params, 0, $limit);

        $tagscount = count($tagsincloud);
        if ($tagscount == $limit) {
            $tagscount = $DB->get_field_sql("SELECT COUNT(DISTINCT tg.id) $fromclause $whereclause", $params);
        }

        self::$cloudsortfield = $sort;
        usort($tagsincloud, "self::cloud_sort");

        return new core_tag\output\tagcloud($tagsincloud, $tagscount, $fromctx, $ctx, $rec);
    }

    /**
     * This function is used to sort the tags in the cloud.
     *
     * @param   string $a Tag name to compare against $b
     * @param   string $b Tag name to compare against $a
     * @return  int    The result of the comparison/validation 1, 0 or -1
     */
    public static function cloud_sort($a, $b) {
        $tagsort = self::$cloudsortfield ?: 'name';

        if (is_numeric($a->$tagsort)) {
            return ($a->$tagsort == $b->$tagsort) ? 0 : ($a->$tagsort > $b->$tagsort) ? 1 : -1;
        } else if (is_string($a->$tagsort)) {
            return strcmp($a->$tagsort, $b->$tagsort);
        } else {
            return 0;
        }
    }
}