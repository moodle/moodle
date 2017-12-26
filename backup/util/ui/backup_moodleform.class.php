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
 * This file contains the generic moodleform bridge for the backup user interface
 * as well as the individual forms that relate to the different stages the user
 * interface can exist within.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Backup moodleform bridge
 *
 * Ahhh the mighty moodleform bridge! Strong enough to take the weight of 682 full
 * grown african swallows all of whom have been carring coconuts for several days.
 * EWWWWW!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @package core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_moodleform extends base_moodleform {
    /**
     * Creates the form
     *
     * Overridden for type hinting on the first arg.
     *
     * @param backup_ui_stage $uistage
     * @param moodle_url|string $action
     * @param mixed $customdata
     * @param string $method get|post
     * @param string $target
     * @param array $attributes
     * @param bool $editable
     */
    public function __construct(backup_ui_stage $uistage, $action = null, $customdata = null, $method = 'post',
                                $target = '', $attributes = null, $editable = true) {
        parent::__construct($uistage, $action, $customdata, $method, $target, $attributes, $editable);
    }
}

/**
 * Initial backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_initial_form extends backup_moodleform {}

/**
 * Schema backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_schema_form extends backup_moodleform {}

/**
 * Confirmation backup user interface stage moodleform.
 *
 * Nothing to override we only need it defined so that moodleform doesn't get confused
 * between stages.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_confirmation_form extends backup_moodleform {

    /**
     * Adds the last elements, rules, settings etc to the form after data has been set.
     *
     * We override this to add a rule and type to the filename setting.
     *
     * @throws coding_exception
     */
    public function definition_after_data() {
        parent::definition_after_data();
        $this->_form->addRule('setting_root_filename', get_string('errorfilenamerequired', 'backup'), 'required');
        $this->_form->setType('setting_root_filename', PARAM_FILE);
    }

    /**
     * Validates the form.
     *
     * Relies on the parent::validation for the bulk of the work.
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!array_key_exists('setting_root_filename', $errors)) {
            if (trim($data['setting_root_filename']) == '') {
                $errors['setting_root_filename'] = get_string('errorfilenamerequired', 'backup');
            } else if (strlen(trim($data['setting_root_filename'])) > 255) {
                $errors['setting_root_filename'] = get_string('errorfilenametoolong', 'backup');
            } else if (!preg_match('#\.mbz$#i', $data['setting_root_filename'])) {
                $errors['setting_root_filename'] = get_string('errorfilenamemustbezip', 'backup');
            }
        }

        return $errors;
    }
}
