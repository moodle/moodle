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
 * Badge helper library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_badges;

/**
 * Badge helper library.
 *
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Create a backpack.
     *
     * @param array $params Parameters.
     * @return object
     */
    public static function create_fake_backpack(array $params = []) {
        global $DB;

        $record = (object) array_merge([
            'userid' => null,
            'email' => 'test@example.com',
            'backpackuid' => -1,
            'autosync' => 0,
            'password' => '',
            'externalbackpackid' => 12345,
        ], $params);
        $record->id = $DB->insert_record('badge_backpack', $record);

        return $record;
    }

    /**
     * Create a user backpack collection.
     *
     * @param array $params Parameters.
     * @return object
     */
    public static function create_fake_backpack_collection(array $params = []) {
        global $DB;

        $record = (object) array_merge([
            'backpackid' => 12345,
            'collectionid' => -1,
            'entityid' => random_string(20),
        ], $params);
        $record->id = $DB->insert_record('badge_external', $record);

        return $record;
    }
}
