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
 * @package    mod_h5pactivity
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * h5pactivity module data generator class.
 *
 * @package    mod_h5pactivity
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_h5pactivity_generator extends testing_module_generator {

    /**
     * Creates new h5pactivity module instance. By default it contains a short
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
        // Ensure the record can be modified without affecting calling code.
        $record = (object)(array)$record;

        // Fill in optional values if not specified.
        if (!isset($record->packagefilepath)) {
            $record->packagefilepath = $CFG->dirroot.'/h5p/tests/fixtures/h5ptest.zip';
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }
        if (!isset($record->displayoptions)) {
            $factory = new \core_h5p\factory();
            $core = $factory->get_core();
            $config = \core_h5p\helper::decode_display_options($core);
            $record->displayoptions = \core_h5p\helper::get_display_options($core, $config);
        }

        // The 'packagefile' value corresponds to the draft file area ID. If not specified, create from packagefilepath.
        if (empty($record->packagefile)) {
            if (!isloggedin() || isguestuser()) {
                throw new coding_exception('Scorm generator requires a current user');
            }
            if (!file_exists($record->packagefilepath)) {
                throw new coding_exception("File {$record->packagefilepath} does not exist");
            }
            $usercontext = context_user::instance($USER->id);

            // Pick a random context id for specified user.
            $record->packagefile = file_get_unused_draft_itemid();

            // Add actual file there.
            $filerecord = ['component' => 'user', 'filearea' => 'draft',
                    'contextid' => $usercontext->id, 'itemid' => $record->packagefile,
                    'filename' => basename($record->packagefilepath), 'filepath' => '/'];
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $record->packagefilepath);
        }

        // Do work to actually add the instance.
        return parent::create_instance($record, (array)$options);
    }
}
