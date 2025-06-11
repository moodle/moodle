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
 * mod_folder data generator.
 *
 * @package    mod_folder
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_folder data generator class.
 *
 * @package    mod_folder
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_folder_generator extends testing_module_generator {

    public function create_instance($record = null, ?array $options = null) {
        // Add default values for folder.
        $record = (array)$record + array('display' => 0);
        if (!isset($record['showexpanded'])) {
            $record['showexpanded'] = get_config('folder', 'showexpanded');
        }
        if (!isset($record['files'])) {
            $record['files'] = file_get_unused_draft_itemid();
        }
        return parent::create_instance($record, (array)$options);
    }
}
