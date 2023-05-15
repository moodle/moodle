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
 * Restore structure step for both hvp content and hvp libraries
 *
 * @package     mod_hvp
 * @category    backup
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Structure step to restore one H5P activity
 *
 * @copyright   2018 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_hvp_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines restore element's structure
     *
     * @return array
     * @throws base_step_exception
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        // Restore activities.
        $paths[] = new restore_path_element('hvp', '/activity/hvp');

        if ($userinfo) {
            // Restore content state.
            $paths[] = new restore_path_element('content_user_data', '/activity/hvp/content_user_data/entry');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process H5P, inserting the record into the database.
     *
     * @param $data
     *
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_hvp($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->main_library_id = \restore_hvp_libraries_structure_step::get_library_id($data);

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the hvp record.
        $newitemid = $DB->insert_record('hvp', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process and inserts content user data.
     *
     * @param $data
     *
     * @throws dml_exception
     */
    protected function process_content_user_data($data) {
        global $DB;

        $data = (object) $data;
        $data->user_id = $this->get_mappingid('user', $data->user_id);
        $data->hvp_id = $this->get_new_parentid('hvp');

        $DB->insert_record('hvp_content_user_data', $data);
    }

    /**
     * Additional work that needs to be done after steps executions.
     */
    protected function after_execute() {
        // Add files for intro field.
        $this->add_related_files('mod_hvp', 'intro', null);

        // Add hvp related files.
        $this->add_related_files('mod_hvp', 'content', 'hvp');
    }
}

/**
 * Structure step to restore H5P libraries
 *
 * @copyright   2018 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_hvp_libraries_structure_step extends restore_activity_structure_step {

    /**
     * Determines if library should be restored.
     *
     * @return bool
     * @throws backup_step_exception
     */
    protected function execute_condition() {
        static $librariesrestored;

        // Prevent this step from running more than once
        // since all hvp_libraries.xml files are the same.
        if (!empty($librariesrestored)) {
            return false;
        }
        $librariesrestored = true;

        // Get full path to activity backup location.
        $fullpath = $this->task->get_taskbasepath();
        if (empty($fullpath)) {
            throw new backup_step_exception('backup_structure_step_undefined_fullpath');
        }
        $fullpath = rtrim($fullpath, '/');

        // Check for the activity's local hvp_libraries.xml file.
        if (file_exists("{$fullpath}/{$this->filename}")) {
            // Use that.
            return true;
        }

        // Look for a global hvp_libraries.xml file.
        if (file_exists("{$fullpath}/../{$this->filename}")) {
            // Use it.
            $this->filename = "../{$this->filename}";
            return true;
        }

        // Not able to find a hvp_libraries.xml, skip restore and let the admin
        // be responsible for providing the approperiate libraries.
        // (Could also be using Import which doesn't need libraries).
        return false;
    }

    /**
     * Defines how library should be restored.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = array();

        // Restore libraries first.
        $paths[] = new restore_path_element('hvp_library', '/hvp_libraries/library');

        // Add translations.
        $paths[] = new restore_path_element('hvp_library_translation', '/hvp_libraries/library/translations/translation');

        // ... and dependencies.
        $paths[] = new restore_path_element('hvp_library_dependency', '/hvp_libraries/library/dependencies/dependency');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process and insert library record.
     *
     * @param $data
     *
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_hvp_library($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        unset($data->id);

        $libraryid = self::get_library_id($data);
        if (!$libraryid) {
            // There is no updating of libraries. If an older patch version exists
            // on the site that one will be used instead of the new one in the backup.
            // This is due to the default behavior when files are restored in Moodle.

            // Restore library.
            $libraryid = $DB->insert_record('hvp_libraries', $data);

            // Update libraries cache.
            self::get_library_id($data, $libraryid);
        }

        // Keep track of libraries for translations and dependencies.
        $this->set_mapping('hvp_library', $oldid, $libraryid);

        // Update any dependencies that require this library.
        $this->update_missing_dependencies($oldid, $libraryid);
    }

    /**
     * Process and inserts translations for library.
     *
     * @param $data
     *
     * @throws dml_exception
     */
    protected function process_hvp_library_translation($data) {
        global $DB;

        $data = (object) $data;
        $data->library_id = $this->get_new_parentid('hvp_library');

        // Check that translations doesn't exists.
        $translation = $DB->get_record_sql(
            'SELECT id
               FROM {hvp_libraries_languages}
              WHERE library_id = ?
                AND language_code = ?',
              array($data->library_id,
                    $data->language_code)
        );

        if (empty($translation)) {
            // Only restore translations if library has been restored.
            $DB->insert_record('hvp_libraries_languages', $data);
        }
    }

    /**
     * Process and inserts library dependencies.
     *
     * @param $data
     *
     * @throws dml_exception
     */
    protected function process_hvp_library_dependency($data) {
        global $DB;

        $data             = (object) $data;
        $data->library_id = $this->get_new_parentid('hvp_library');

        $newlibraryid = $this->get_mappingid('hvp_library', $data->required_library_id);
        if ($newlibraryid) {
            $data->required_library_id = $newlibraryid;

            // Check that the dependency doesn't exists.
            $dependency = $DB->get_record_sql(
                'SELECT id
                 FROM {hvp_libraries_libraries}
                WHERE library_id = ?
                  AND required_library_id = ?',
                [
                    $data->library_id,
                    $data->required_library_id,
                ]
            );
            if (empty($dependency)) {
                $DB->insert_record('hvp_libraries_libraries', $data);
            }
        } else {
            // The required dependency hasn't been restored yet. We need to add this dependency later.
            $this->update_missing_dependencies($data->required_library_id, null, $data);
        }
    }

    /**
     * Additional work that is executed after library restoration steps.
     *
     * @throws dml_exception
     */
    protected function after_execute() {
        // Add files for libraries.
        $context = \context_system::instance();
        $this->add_related_files('mod_hvp', 'libraries', null, $context->id);
    }

    /**
     * Cache to reduce queries.
     *
     * @param $library
     * @param null $set
     *
     * @return mixed
     * @throws dml_exception
     */
    public static function get_library_id(&$library, $set = null) {
        static $keytoid;
        global $DB;

        $key = $library->machine_name . ' ' . $library->major_version . '.' . $library->minor_version;
        if (is_null($keytoid)) {
            $keytoid = array();
        }
        if ($set !== null) {
            $keytoid[$key] = $set;
        } else if (!isset($keytoid[$key])) {
            $lib = $DB->get_record_sql(
                'SELECT id
                   FROM {hvp_libraries}
                  WHERE machine_name = ?
                    AND major_version = ?
                    AND minor_version = ?',
                  array($library->machine_name,
                        $library->major_version,
                        $library->minor_version)
            );

            // Non existing = false.
            $keytoid[$key] = (empty($lib) ? false : $lib->id);
        }

        return $keytoid[$key];
    }

    /**
     * Keep track of missing dependencies since libraries aren't inserted
     * in any special order
     *
     * @param $oldid
     * @param $newid
     * @param null $setmissing
     *
     * @throws dml_exception
     */
    private function update_missing_dependencies($oldid, $newid, $setmissing = null) {
        static $missingdeps;
        global $DB;

        if (is_null($missingdeps)) {
            $missingdeps = array();
        }

        if ($setmissing !== null) {
            $missingdeps[$oldid][] = $setmissing;
        } else if (isset($missingdeps[$oldid])) {
            foreach ($missingdeps[$oldid] as $missingdep) {
                $missingdep->required_library_id = $newid;

                // Check that the dependency doesn't exists.
                $dependency = $DB->get_record_sql(
                    'SELECT id
                       FROM {hvp_libraries_libraries}
                      WHERE library_id = ?
                        AND required_library_id = ?',
                      array($missingdep->library_id,
                            $missingdep->required_library_id)
                );
                if (empty($dependency)) {
                    $DB->insert_record('hvp_libraries_libraries', $missingdep);
                }
            }
            unset($missingdeps[$oldid]);
        }
    }
}
