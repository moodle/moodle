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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/cps/classes/lib.php');

abstract class blocks_cps_profile_field_helper {

    public static function process($user, $shortname) {
        $category = self::get_category();

        $params = array(
            'categoryid' => $category->id,
            'shortname' => $shortname
        );

        $field = self::default_profile_field($params);

        $params = array(
            'userid' => $user->id,
            'fieldid' => $field->id
        );

        return self::set_profile_data($user->$shortname, $params);
    }

    public static function get_category() {
        global $DB;

        $catid = get_config('block_cps', 'user_field_catid');

        // Backup.
        $sql = 'SELECT id FROM {user_info_category} LIMIT 1';

        $catid = empty($catid) ? $DB->get_field_sql($sql) : $catid;

        $params = array('id' => $catid);

        if (!$category = $DB->get_record('user_info_category', $params)) {
            $category = new stdClass;
            $category->name = get_string('user_info_category', 'block_cps');
            $category->sortorder = 1;

            $category->id = $DB->insert_record('user_info_category', $category);
        }

        return $category;
    }

    public static function default_profile_field($params) {
        global $DB;

        if (!$field = $DB->get_record('user_info_field', $params)) {
            $field = new stdClass;
            $field->shortname = $params['shortname'];
            $field->name = get_string($field->shortname, 'block_cps');
            $field->description = get_string('auto_field_desc', 'block_cps');
            $field->descriptionformat = 1;
            $field->datatype = 'text';
            $field->categoryid = $params['categoryid'];
            $field->locked = 1;
            $field->visible = 1;
            $field->param1 = 30;
            $field->param2 = 2048;

            $field->id = $DB->insert_record('user_info_field', $field);
        }

        return $field;
    }

    public static function set_profile_data($info, $params) {
        global $DB;

        if (!$data = $DB->get_record('user_info_data', $params)) {
            $data = new stdClass;
            $data->userid = $params['userid'];
            $data->fieldid = $params['fieldid'];

            $data->data = '';

            $data->id = $DB->insert_record('user_info_data', $data);
        }

        $data->data = $info;

        return $DB->update_record('user_info_data', $data);
    }

    public static function clear_field_data($user, $shortname) {
        global $DB;

        $category = self::get_category();

        $params = array(
            'categoryid' => $category->id,
            'shortname' => $shortname
        );

        $field = self::default_profile_field($params);

        $params = array(
            'fieldid' => $field->id,
            'userid' => $user->id
        );

        return $DB->delete_records('user_info_data', $params);
    }
}
