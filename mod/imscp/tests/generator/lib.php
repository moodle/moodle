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
 * mod_imscp data generator.
 *
 * @package    mod_imscp
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_imscp data generator class.
 *
 * @package    mod_imscp
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_imscp_generator extends testing_module_generator {

    public function create_instance($record = null, ?array $options = null) {
        global $CFG, $USER;

        // Add default values for imscp.
        $record = (array)$record + array(
            'package' => '',
            'packagepath' => $CFG->dirroot.'/mod/imscp/tests/packages/singlescobasic.zip',
            'keepold' => -1
        );

        // The 'package' value corresponds to the draft file area ID. If not specified, create from packagepath.
        if (empty($record['package'])) {
            if (!isloggedin() || isguestuser()) {
                throw new coding_exception('IMSCP generator requires a current user');
            }
            if (!file_exists($record['packagepath'])) {
                throw new coding_exception("File {$record['packagepath']} does not exist");
            }
            $usercontext = context_user::instance($USER->id);

            // Pick a random context id for specified user.
            $record['package'] = file_get_unused_draft_itemid();

            // Add actual file there.
            $filerecord = array('component' => 'user', 'filearea' => 'draft',
                    'contextid' => $usercontext->id, 'itemid' => $record['package'],
                    'filename' => basename($record['packagepath']), 'filepath' => '/');
            $fs = get_file_storage();
            $fs->create_file_from_pathname($filerecord, $record['packagepath']);
        }

        return parent::create_instance($record, (array)$options);
    }
}
