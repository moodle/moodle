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
 * Component generator base class.
 *
 * @package   core
 * @category  test
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Component generator base class.
 *
 * Extend in path/to/component/tests/generator/lib.php as
 * class type_plugin_generator extends component_generator_base
 * Note that there are more specific classes to extend for mods and blocks.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class component_generator_base {

    /**
     * @var testing_data_generator
     */
    protected $datagenerator;

    /**
     * Constructor.
     * @param testing_data_generator $datagenerator
     */
    public function __construct(testing_data_generator $datagenerator) {
        $this->datagenerator = $datagenerator;
    }

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
    }

    /**
     * Set the current user during data generation.
     *
     * This should be avoided wherever possible, but in some situations underlying code will insert data as the current
     * user.
     *
     * @param stdClass $user
     */
    protected function set_user(?stdClass $user = null): void {
        global $CFG, $DB;

        if ($user === null) {
            $user = (object) [
                'id' => 0,
                'mnethostid' => $CFG->mnet_localhost_id,
            ];
        } else {
            $user = clone($user);
            unset($user->description);
            unset($user->access);
            unset($user->preference);
        }

        // Ensure session is empty, as it may contain caches and user-specific info.
        \core\session\manager::init_empty_session();

        \core\session\manager::set_user($user);
    }

    /**
     * Update the instance record, inserting any files that are referenced.
     *
     * @param stdClass $instance The instance record of the already-created record
     * @param stdClass $record The data passed in to create the instance
     * @param string $table The table that the data exists in
     * @param context $context The context of the instance
     * @param string $component The component of the owning plugin
     * @param string $filearea The name of the file area
     * @param int $targetitemid The itemid to use when saving the files
     * @return stdClass The updated instance record
     */
    protected function insert_files(
        stdClass $instance,
        stdClass $record,
        string $table,
        context $context,
        string $component,
        string $filearea,
        int $targetitemid
    ): stdClass {
        global $CFG, $DB, $USER;

        $fieldname = "[[files::{$filearea}]]";
        if (!isset($record->$fieldname)) {
            return $instance;
        }

        preg_match('/\[\[files::(.*)\]\]/', $fieldname, $matches);
        if (empty($matches[1])) {
            throw new coding_exception('Invalid file field name: ' . $fieldname);
        }

        $referencedfieldname = trim($matches[1]);
        if (!isset($record->$referencedfieldname)) {
            throw new coding_exception("File field '{$fieldname}' references non-existent field '{$referencedfieldname}'");
        }

        $fs = get_file_storage();
        $itemid = file_get_unused_draft_itemid();
        $itemidfieldname = "{$referencedfieldname}[itemid]";
        $record->$itemidfieldname = $itemid;

        $filenames = explode(',', $record->$fieldname);
        foreach ($filenames as $filename) {
            $filename = trim($filename);
            if (!$filename) {
                continue;
            }

            $explodedfilename = explode('::', $filename, 3);
            if (count($explodedfilename) === 2) {
                [$sourcefile, $targetfile] = $explodedfilename;
                $user = $USER;
            } else {
                [$sourcefile, $targetfile, $username] = $explodedfilename;
                $user = \core_user::get_user_by_username($username);
            }
            $filepath = "{$CFG->dirroot}/{$sourcefile}";
            if (!file_exists($filepath)) {
                throw new coding_exception("File '{$filepath}' does not exist");
            }
            $filerecord = [
                'userid' => $user->id,
                'contextid' => context_user::instance($user->id)->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => $itemid,
                'filepath' => '/' . dirname($targetfile),
                'filename' => basename($targetfile),
            ];
            $fs->create_file_from_pathname($filerecord, $filepath);
        }

        $olduser = $USER;
        $this->set_user($user);
        $instance->$referencedfieldname = file_save_draft_area_files(
            $itemid,
            $context->id,
            $component,
            $referencedfieldname,
            $targetitemid,
            null,
            $instance->$referencedfieldname
        );
        $this->set_user($olduser);

        $DB->update_record($table, $instance);
        return $instance;
    }
}
