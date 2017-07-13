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
 * Data generator.
 *
 * @package    mod_resource
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Resource module data generator class.
 *
 * @package    mod_resource
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_resource_generator extends testing_module_generator {

    /**
     * Creates new resource module instance. By default it contains a short
     * text file.
     *
     * @param array|stdClass $record data for module being generated. Requires 'course' key
     *     (an id or the full object). Also can have any fields from add module form.
     * @param null|array $options general options for course module. Since 2.6 it is
     *     possible to omit this argument by merging options into $record
     * @return stdClass record from module-defined table with additional field
     *     cmid (corresponding id in course_modules table)
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/lib/resourcelib.php');
        // Ensure the record can be modified without affecting calling code.
        $record = (object)(array)$record;

        // Fill in optional values if not specified.
        if (!isset($record->display)) {
            $record->display = RESOURCELIB_DISPLAY_AUTO;
        }
        if (!isset($record->printintro)) {
            $record->printintro = 0;
        }
        if (!isset($record->showsize)) {
            $record->showsize = 0;
        }
        if (!isset($record->showtype)) {
            $record->showtype = 0;
        }

        // The 'files' value corresponds to the draft file area ID. If not
        // specified, create a default file.
        if (!isset($record->files)) {
            if (empty($USER->username) || $USER->username === 'guest') {
                throw new coding_exception('resource generator requires a current user');
            }
            $usercontext = context_user::instance($USER->id);

            // Pick a random context id for specified user.
            $record->files = file_get_unused_draft_itemid();

            // Add actual file there.
            $filerecord = array('component' => 'user', 'filearea' => 'draft',
                    'contextid' => $usercontext->id, 'itemid' => $record->files,
                    'filename' => 'resource' . ($this->instancecount+1) . '.txt', 'filepath' => '/');
            $fs = get_file_storage();
            $fs->create_file_from_string($filerecord, 'Test resource ' . ($this->instancecount+1) . ' file');
        }

        // Do work to actually add the instance.
        return parent::create_instance($record, (array)$options);
    }
}
