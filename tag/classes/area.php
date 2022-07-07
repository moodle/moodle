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
 * Class core_tag_area for managing tag areas
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage tag areas
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_area {

    /**
     * Returns the list of areas indexed by itemtype and component
     *
     * @param int $tagcollid return only areas in this tag collection
     * @param bool $enabledonly return only enabled tag areas
     * @return array itemtype=>component=>tagarea object
     */
    public static function get_areas($tagcollid = null, $enabledonly = false) {
        global $DB;
        $cache = cache::make('core', 'tags');
        if (($itemtypes = $cache->get('tag_area')) === false) {
            $colls = core_tag_collection::get_collections();
            $defaultcoll = reset($colls);
            $itemtypes = array();
            $areas = $DB->get_records('tag_area', array(), 'component,itemtype');
            foreach ($areas as $area) {
                if ($colls[$area->tagcollid]->component) {
                    $area->locked = true;
                }
                $itemtypes[$area->itemtype][$area->component] = $area;
            }
            $cache->set('tag_area', $itemtypes);
        }
        if ($tagcollid || $enabledonly) {
            $rv = array();
            foreach ($itemtypes as $itemtype => $it) {
                foreach ($it as $component => $v) {
                    if (($v->tagcollid == $tagcollid || !$tagcollid) && (!$enabledonly || $v->enabled)) {
                        $rv[$itemtype][$component] = $v;
                    }
                }
            }
            return $rv;
        }
        return $itemtypes;
    }

    /**
     * Retrieves info about one tag area
     *
     * @param int $tagareaid
     * @return stdClass
     */
    public static function get_by_id($tagareaid) {
        $tagareas = self::get_areas();
        foreach ($tagareas as $itemtype => $it) {
            foreach ($it as $component => $v) {
                if ($v->id == $tagareaid) {
                    return $v;
                }
            }
        }
        return null;
    }

    /**
     * Returns the display name for this area
     *
     * @param string $component
     * @param string $itemtype
     * @return lang_string
     */
    public static function display_name($component, $itemtype) {
        $identifier = 'tagarea_' . clean_param($itemtype, PARAM_STRINGID);
        if ($component === 'core') {
            $component = 'tag';
        }
        return new lang_string($identifier, $component);
    }

    /**
     * Returns whether the tag area is enabled
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype what is being tagged, for example, 'post', 'course', 'user', etc.
     * @return bool|null
     */
    public static function is_enabled($component, $itemtype) {
        global $CFG;
        if (empty($CFG->usetags)) {
            return false;
        }
        $itemtypes = self::get_areas();
        if (isset($itemtypes[$itemtype][$component])) {
            return $itemtypes[$itemtype][$component]->enabled ? true : false;
        }
        return null;
    }

    /**
     * Checks if the tag area allows items to be tagged in multiple different contexts.
     *
     * If true then it indicates that not all tag instance contexts must match the
     * context of the item they are tagging. If false then all tag instance should
     * match the context of the item they are tagging.
     *
     * Example use case for multi-context tagging:
     * A question that exists in a course category context may be used by multiple
     * child courses. The question tag area can allow tag instances to be created in
     * multiple contexts which allows the tag API to tag the question at the course
     * category context and then seperately in each of the child course contexts.
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype what is being tagged, for example, 'post', 'course', 'user', etc.
     * @return bool
     */
    public static function allows_tagging_in_multiple_contexts($component, $itemtype) {
        $itemtypes = self::get_areas();
        if (isset($itemtypes[$itemtype][$component])) {
            $config = $itemtypes[$itemtype][$component];
            return isset($config->multiplecontexts) ? $config->multiplecontexts : false;
        }
        return false;
    }

    /**
     * Returns the id of the tag collection that should be used for storing tags of this itemtype
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype what is being tagged, for example, 'post', 'course', 'user', etc.
     * @return int
     */
    public static function get_collection($component, $itemtype) {
        $itemtypes = self::get_areas();
        if (array_key_exists($itemtype, $itemtypes)) {
            if (!array_key_exists($component, $itemtypes[$itemtype])) {
                $component = key($itemtypes[$itemtype]);
            }
            return $itemtypes[$itemtype][$component]->tagcollid;
        }
        return core_tag_collection::get_default();
    }

    /**
     * Returns wether this tag area should display or not standard tags when user edits it.
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype what is being tagged, for example, 'post', 'course', 'user', etc.
     * @return int
     */
    public static function get_showstandard($component, $itemtype) {
        $itemtypes = self::get_areas();
        if (array_key_exists($itemtype, $itemtypes)) {
            if (!array_key_exists($component, $itemtypes[$itemtype])) {
                $component = key($itemtypes[$itemtype]);
            }
            return $itemtypes[$itemtype][$component]->showstandard;
        }
        return core_tag_tag::BOTH_STANDARD_AND_NOT;
    }

    /**
     * Returns all tag areas and collections that are currently cached in DB for this component
     *
     * @param string $componentname
     * @return array first element is the list of areas and the second list of collections
     */
    protected static function get_definitions_for_component($componentname) {
        global $DB;
        list($a, $b) = core_component::normalize_component($componentname);
        $component = $b ? ($a . '_' . $b) : $a;
        $sql = 'component = :component';
        $params = array('component' => $component);
        if ($component === 'core') {
            $sql .= ' OR component LIKE :coreprefix';
            $params['coreprefix'] = 'core_%';
        }
        $fields = $DB->sql_concat_join("':'", array('itemtype', 'component'));
        $existingareas = $DB->get_records_sql(
                "SELECT $fields AS returnkey, a.* FROM {tag_area} a WHERE $sql", $params);
        $fields = $DB->sql_concat_join("':'", array('name', 'component'));
        $existingcolls = $DB->get_records_sql(
                "SELECT $fields AS returnkey, t.* FROM {tag_coll} t WHERE $sql", $params);
        return array($existingareas, $existingcolls);

    }

    /**
     * Completely delete a tag area and all instances inside it
     *
     * @param stdClass $record
     */
    protected static function delete($record) {
        global $DB;

        core_tag_tag::delete_instances($record->component, $record->itemtype);

        $DB->delete_records('tag_area',
                array('itemtype' => $record->itemtype,
                    'component' => $record->component));

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_area');
    }

    /**
     * Create a new tag area
     *
     * @param stdClass $record
     */
    protected static function create($record) {
        global $DB;
        if (empty($record->tagcollid)) {
            $record->tagcollid = core_tag_collection::get_default();
        }
        $DB->insert_record('tag_area', array('component' => $record->component,
            'itemtype' => $record->itemtype,
            'tagcollid' => $record->tagcollid,
            'callback' => $record->callback,
            'callbackfile' => $record->callbackfile,
            'showstandard' => isset($record->showstandard) ? $record->showstandard : core_tag_tag::BOTH_STANDARD_AND_NOT,
            'multiplecontexts' => isset($record->multiplecontexts) ? $record->multiplecontexts : 0));

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_area');
    }

    /**
     * Update the tag area
     *
     * @param stdClass $existing current record from DB table tag_area
     * @param array|stdClass $data fields that need updating
     */
    public static function update($existing, $data) {
        global $DB;
        $data = array_intersect_key((array)$data,
                array('enabled' => 1, 'tagcollid' => 1,
                    'callback' => 1, 'callbackfile' => 1, 'showstandard' => 1,
                    'multiplecontexts' => 1));
        foreach ($data as $key => $value) {
            if ($existing->$key == $value) {
                unset($data[$key]);
            }
        }
        if (!$data) {
            return;
        }

        if (!empty($data['tagcollid'])) {
            self::move_tags($existing->component, $existing->itemtype, $data['tagcollid']);
        }

        $data['id'] = $existing->id;
        $DB->update_record('tag_area', $data);

        // Reset cache.
        cache::make('core', 'tags')->delete('tag_area');
    }

    /**
     * Update the database to contain a list of tagged areas for a component.
     * The list of tagged areas is read from [plugindir]/db/tag.php
     *
     * @param string $componentname - The frankenstyle component name.
     */
    public static function reset_definitions_for_component($componentname) {
        global $DB;
        $dir = core_component::get_component_directory($componentname);
        $file = $dir . '/db/tag.php';
        $tagareas = null;
        if (file_exists($file)) {
            require_once($file);
        }

        list($a, $b) = core_component::normalize_component($componentname);
        $component = $b ? ($a . '_' . $b) : $a;

        list($existingareas, $existingcolls) = self::get_definitions_for_component($componentname);

        $itemtypes = array();
        $collections = array();
        $needcleanup = false;
        if ($tagareas) {
            foreach ($tagareas as $tagarea) {
                $record = (object)$tagarea;
                if ($component !== 'core' || empty($record->component)) {
                    if (isset($record->component) && $record->component !== $component) {
                        debugging("Item type {$record->itemtype} has illegal component {$record->component}", DEBUG_DEVELOPER);
                    }
                    $record->component = $component;
                }
                unset($record->tagcollid);
                if (!empty($record->collection)) {
                    // Create collection if it does not exist, or update 'searchable' and/or 'customurl' if needed.
                    $key = $record->collection . ':' . $record->component;
                    $collectiondata = array_intersect_key((array)$record,
                            array('component' => 1, 'searchable' => 1, 'customurl' => 1));
                    $collectiondata['name'] = $record->collection;
                    if (!array_key_exists($key, $existingcolls)) {
                        $existingcolls[$key] = core_tag_collection::create($collectiondata);
                    } else {
                        core_tag_collection::update($existingcolls[$key], $collectiondata);
                    }
                    $record->tagcollid = $existingcolls[$key]->id;
                    $collections[$key] = $existingcolls[$key];
                    unset($record->collection);
                }
                unset($record->searchable);
                unset($record->customurl);
                if (!isset($record->callback)) {
                    $record->callback = null;
                }
                if (!isset($record->callbackfile)) {
                    $record->callbackfile = null;
                }
                if (!isset($record->multiplecontexts)) {
                    $record->multiplecontexts = false;
                }
                $itemtypes[$record->itemtype . ':' . $record->component] = $record;
            }
        }
        $todeletearea = array_diff_key($existingareas, $itemtypes);
        $todeletecoll = array_diff_key($existingcolls, $collections);

        // Delete tag areas that are no longer needed.
        foreach ($todeletearea as $key => $record) {
            self::delete($record);
        }

        // Update tag areas if changed.
        $toupdatearea = array_intersect_key($existingareas, $itemtypes);
        foreach ($toupdatearea as $key => $tagarea) {
            if (!isset($itemtypes[$key]->tagcollid)) {
                foreach ($todeletecoll as $tagcoll) {
                    if ($tagcoll->id == $tagarea->tagcollid) {
                        $itemtypes[$key]->tagcollid = core_tag_collection::get_default();
                    }
                }
            }
            unset($itemtypes[$key]->showstandard); // Do not override value that was already changed by admin with the default.
            self::update($tagarea, $itemtypes[$key]);
        }

        // Create new tag areas.
        $toaddarea = array_diff_key($itemtypes, $existingareas);
        foreach ($toaddarea as $record) {
            self::create($record);
        }

        // Delete tag collections that are no longer needed.
        foreach ($todeletecoll as $key => $tagcoll) {
            core_tag_collection::delete($tagcoll);
        }
    }

    /**
     * Deletes all tag areas, collections and instances associated with the plugin.
     *
     * @param string $pluginname
     */
    public static function uninstall($pluginname) {
        global $DB;

        list($a, $b) = core_component::normalize_component($pluginname);
        if (empty($b) || $a === 'core') {
            throw new coding_exception('Core component can not be uninstalled');
        }
        $component = $a . '_' . $b;

        core_tag_tag::delete_instances($component);

        $DB->delete_records('tag_area', array('component' => $component));
        $DB->delete_records('tag_coll', array('component' => $component));
        cache::make('core', 'tags')->delete_many(array('tag_area', 'tag_coll'));
    }

    /**
     * Moves existing tags associated with an item type to another tag collection
     *
     * @param string $component
     * @param string $itemtype
     * @param int $tagcollid
     */
    public static function move_tags($component, $itemtype, $tagcollid) {
        global $DB;
        $params = array('itemtype1' => $itemtype, 'component1' => $component,
            'itemtype2' => $itemtype, 'component2' => $component,
            'tagcollid1' => $tagcollid, 'tagcollid2' => $tagcollid);

        // Find all collections that need to be cleaned later.
        $sql = "SELECT DISTINCT t.tagcollid " .
            "FROM {tag_instance} ti " .
            "JOIN {tag} t ON t.id = ti.tagid AND t.tagcollid <> :tagcollid1 " .
            "WHERE ti.itemtype = :itemtype2 AND ti.component = :component2 ";
        $cleanupcollections = $DB->get_fieldset_sql($sql, $params);

        // Find all tags that are related to the tags being moved and make sure they are present in the target tagcoll.
        // This query is a little complicated because Oracle does not allow to run SELECT DISTINCT on CLOB fields.
        $sql = "SELECT name, rawname, description, descriptionformat, userid, isstandard, flag, timemodified ".
                "FROM {tag} WHERE id IN ".
                "(SELECT r.id ".
                "FROM {tag_instance} ti ". // Instances that need moving.
                "JOIN {tag} t ON t.id = ti.tagid AND t.tagcollid <> :tagcollid1 ". // Tags that need moving.
                "JOIN {tag_instance} tr ON tr.itemtype = 'tag' and tr.component = 'core' AND tr.itemid = t.id ".
                "JOIN {tag} r ON r.id = tr.tagid ". // Tags related to the tags that need moving.
                "LEFT JOIN {tag} re ON re.name = r.name AND re.tagcollid = :tagcollid2 ". // Existing tags in the target tagcoll with the same name as related tags.
                "WHERE ti.itemtype = :itemtype2 AND ti.component = :component2 ".
                "    AND re.id IS NULL)"; // We need related tags that ARE NOT present in the target tagcoll.
        $result = $DB->get_records_sql($sql, $params);
        foreach ($result as $tag) {
            $tag->tagcollid = $tagcollid;
            $tag->id = $DB->insert_record('tag', $tag);
            \core\event\tag_created::create_from_tag($tag);
        }

        // Find all tags that need moving and have related tags, remember their related tags.
        $sql = "SELECT t.name AS tagname, r.rawname AS relatedtag ".
                "FROM {tag_instance} ti ". // Instances that need moving.
                "JOIN {tag} t ON t.id = ti.tagid AND t.tagcollid <> :tagcollid1 ". // Tags that need moving.
                "JOIN {tag_instance} tr ON t.id = tr.tagid AND tr.itemtype = 'tag' and tr.component = 'core' ".
                "JOIN {tag} r ON r.id = tr.itemid ". // Tags related to the tags that need moving.
                "WHERE ti.itemtype = :itemtype2 AND ti.component = :component2 ".
                "ORDER BY t.id, tr.ordering ";
        $relatedtags = array();
        $result = $DB->get_recordset_sql($sql, $params);
        foreach ($result as $record) {
            $relatedtags[$record->tagname][] = $record->relatedtag;
        }
        $result->close();

        // Find all tags that are used for this itemtype/component and are not present in the target tag collection.
        // This query is a little complicated because Oracle does not allow to run SELECT DISTINCT on CLOB fields.
        $sql = "SELECT id, name, rawname, description, descriptionformat, userid, isstandard, flag, timemodified
                FROM {tag} WHERE id IN
                (SELECT t.id
                FROM {tag_instance} ti
                JOIN {tag} t ON t.id = ti.tagid AND t.tagcollid <> :tagcollid1
                LEFT JOIN {tag} tt ON tt.name = t.name AND tt.tagcollid = :tagcollid2
                WHERE ti.itemtype = :itemtype2 AND ti.component = :component2
                    AND tt.id IS NULL)";
        $movedtags = array(); // Keep track of moved tags so we don't hit DB index violation.
        $result = $DB->get_records_sql($sql, $params);
        foreach ($result as $tag) {
            $originaltagid = $tag->id;
            if (array_key_exists($tag->name, $movedtags)) {
                // Case of corrupted data when the same tag was in several collections.
                $tag->id = $movedtags[$tag->name];
            } else {
                // Copy the tag into the new collection.
                unset($tag->id);
                $tag->tagcollid = $tagcollid;
                $tag->id = $DB->insert_record('tag', $tag);
                \core\event\tag_created::create_from_tag($tag);
                $movedtags[$tag->name] = $tag->id;
            }
            $DB->execute("UPDATE {tag_instance} SET tagid = ? WHERE tagid = ? AND itemtype = ? AND component = ?",
                    array($tag->id, $originaltagid, $itemtype, $component));
        }

        // Find all tags that are used for this itemtype/component and are already present in the target tag collection.
        $sql = "SELECT DISTINCT t.id, tt.id AS targettagid
                FROM {tag_instance} ti
                JOIN {tag} t ON t.id = ti.tagid AND t.tagcollid <> :tagcollid1
                JOIN {tag} tt ON tt.name = t.name AND tt.tagcollid = :tagcollid2
                WHERE ti.itemtype = :itemtype2 AND ti.component = :component2";
        $result = $DB->get_records_sql($sql, $params);
        foreach ($result as $tag) {
            $DB->execute("UPDATE {tag_instance} SET tagid = ? WHERE tagid = ? AND itemtype = ? AND component = ?",
                    array($tag->targettagid, $tag->id, $itemtype, $component));
        }

        // Add related tags to the moved tags.
        if ($relatedtags) {
            $tags = core_tag_tag::get_by_name_bulk($tagcollid, array_keys($relatedtags));
            foreach ($tags as $tag) {
                $tag->add_related_tags($relatedtags[$tag->name]);
            }
        }

        if ($cleanupcollections) {
            core_tag_collection::cleanup_unused_tags($cleanupcollections);
        }

        // Reset caches.
        cache::make('core', 'tags')->delete('tag_area');
    }
}
