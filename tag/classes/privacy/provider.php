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
 * Privacy Subsystem implementation for core_tag.
 *
 * @package    core_tag
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;

/**
 * Privacy Subsystem implementation for core_tag.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // Tags store user data.
        \core_privacy\local\metadata\provider,

        // The tag subsystem provides data to other components.
        \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        // The table 'tag' contains data that a user has entered.
        // It is currently linked with a userid, but this field will hopefulyl go away.
        // Note: The userid is not necessarily 100% accurate. See MDL-61555.
        $collection->add_database_table('tag', [
                'name' => 'privacy:metadata:tag:name',
                'rawname' => 'privacy:metadata:tag:rawname',
                'description' => 'privacy:metadata:tag:description',
                'flag' => 'privacy:metadata:tag:flag',
                'timemodified' => 'privacy:metadata:tag:timemodified',
                'userid' => 'privacy:metadata:tag:userid',
            ], 'privacy:metadata:tag');

        // The table 'tag_instance' contains user data.
        // It links the user of a specific tag, to the item which is tagged.
        // In some cases the userid who 'owns' the tag is also stored.
        $collection->add_database_table('tag_instance', [
                'tagid' => 'privacy:metadata:taginstance:tagid',
                'ordering' => 'privacy:metadata:taginstance:ordering',
                'timecreated' => 'privacy:metadata:taginstance:timecreated',
                'timemodified' => 'privacy:metadata:taginstance:timemodified',
                'tiuserid' => 'privacy:metadata:taginstance:tiuserid',
            ], 'privacy:metadata:taginstance');

        // The table 'tag_area' does not contain any specific user data.
        // It links components and item types to collections and describes how they can be associated.

        // The table 'tag_coll' does not contain any specific user data.
        // It describes a list of tag collections configured by the administrator.

        // The table 'tag_correlation' does not contain any user data.
        // It is a cache for other data already stored.

        return $collection;
    }

    /**
     * Store all tags which match the specified component, itemtype, and itemid.
     *
     * In most situations you will want to specify $onlyuser as false.
     * This will fetch only tags where the user themselves set the tag, or where tags are a shared resource.
     *
     * If you specify $onlyuser as true, only the tags created by that user will be included.
     *
     * @param   int         $userid The user whose information is to be exported
     * @param   \context    $context The context to export for
     * @param   array       $subcontext The subcontext within the context to export this information
     * @param   string      $component The component to fetch data from
     * @param   string      $itemtype The itemtype that the data was exported in within the component
     * @param   int         $itemid The itemid within that tag
     * @param   bool        $onlyuser Whether to only export ratings that the current user has made, or all tags
     */
    public static function export_item_tags(
        int $userid,
        \context $context,
        array $subcontext,
        string $component,
        string $itemtype,
        int $itemid,
        bool $onlyuser = false
    ) {
        global $DB;

        // Do not include the mdl_tag userid data because of bug with re-using existing tags by other users.
        $sql = "SELECT
                    t.id,
                    t.tagcollid,
                    t.name,
                    t.rawname,
                    t.isstandard,
                    t.description,
                    t.descriptionformat,
                    t.flag,
                    t.timemodified
                  FROM {tag} t
            INNER JOIN {tag_instance} ti ON ti.tagid = t.id
                 WHERE ti.component = :component
                   AND ti.itemtype = :itemtype
                   AND ti.itemid = :itemid
                   ";

        if ($onlyuser) {
            $sql .= "AND ti.tiuserid = :userid";
        } else {
            $sql .= "AND (ti.tiuserid = 0 OR ti.tiuserid = :userid)";
        }

        $params = [
            'component' => $component,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'userid' => $userid,
        ];

        if ($tags = $DB->get_records_sql($sql, $params)) {
            $writer = \core_privacy\local\request\writer::with_context($context)
                ->export_related_data($subcontext, 'tags', $tags);
        }
    }
}
