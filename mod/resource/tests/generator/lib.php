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
 * @package mod_resource
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Resource module data generator class.
 *
 * @package mod_resource
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_resource_generator extends testing_module_generator {

    /**
     * Creates new resource module instance. By default it contains a short
     * text file.
     *
     * @param array|stdClass $record Resource module record, as from form
     * @param array $options Standard options about how to create it
     * @return stdClass Activity record, with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/resource/locallib.php');

        // Count generated modules.
        $this->instancecount++;
        $i = $this->instancecount;

        // Ensure the record can be modified without affecting calling code.
        $record = (object)(array)$record;
        $options = (array)$options;

        // Course is required.
        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }

        // Fill in optional values if not specified.
        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'resource') . ' ' . $i;
        }
        if (!isset($record->intro)) {
            $record->intro = 'Test resource ' . $i;
        }
        if (!isset($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }
        if (!isset($record->display)) {
            $record->display = RESOURCELIB_DISPLAY_AUTO;
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }
        if (!isset($record->printheading)) {
            $record->printheading = 1;
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
                    'filename' => 'resource' . $i . '.txt', 'filepath' => '/');
            $fs = get_file_storage();
            $fs->create_file_from_string($filerecord, 'Test resource ' . $i . ' file');
        }

        // Do work to actually add the instance.
        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = resource_add_instance($record, null);
        return $this->post_add_instance($id, $record->coursemodule);
    }
}
