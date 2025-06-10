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
 * Class for preparing data for Forum Posts.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\forums;

/**
 * Class for preparing data for Forum Posts.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forumpost extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'forumposts';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Post ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'forum' => [
                'type' => PARAM_INT,
                'description' => 'Forum ID.',
                'default' => 0,
            ],
            'discussion' => [
                'type' => PARAM_INT,
                'description' => 'Discussion ID.',
                'default' => 0,
            ],
            'parent' => [
                'type' => PARAM_INT,
                'description' => 'Parent ID.',
                'default' => 0,
            ],
            'message' => [
                'type' => PARAM_RAW,
                'description' => 'Forum Message.',
                'default' => '',
            ],
            'deleted' => [
                'type' => PARAM_INT,
                'description' => 'Post deleted.',
                'default' => 0,
            ],
            'created' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when post created.',
                'default' => 0,
            ],
            'modified' => [
                'type' => PARAM_INT,
                'description' => 'Timestamp when post updated.',
                'default' => 0,
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        global $DB;

        if ($discussion = $DB->get_record('forum_discussions', ['id' => $object->discussion])) {
            $object->forum = $discussion->forum;
        }

        return $object;
    }
}
