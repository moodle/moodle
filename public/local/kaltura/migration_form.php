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
 * Migrate data form.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * Download Kaltura logs form class.
 */
class local_kaltura_migration_form extends moodleform {
    /**
     * This function defines the elements on the form.
     */
    public function definition() {
        $mform =& $this->_form;

        $categories = local_kaltura_get_categories();
        $migrationstats = new local_kaltura_migration_progress();

        $mform->addElement('header', 'setup', get_string('options'));

        // If was never started, print a status message, otherwise print the date the migration originally started.
        $notstarted = get_string('migration_not_started', 'local_kaltura');
        $startedtimestamp = local_kaltura_migration_progress::get_migrationstarted();
        $datestarted = userdate($startedtimestamp);
        $message = empty($startedtimestamp) ? $notstarted : $datestarted;

        // Print more stats on the current state of the migration.
        $mform->addElement('static', 'migration_start_time', get_string('migration_start_time', 'local_kaltura'), $message);
        $mform->addElement('static', 'entries_migrated', get_string('entries_migrated', 'local_kaltura'), local_kaltura_migration_progress::get_entriesmigrated());
        $mform->addElement('static', 'categories created', get_string('categories_created', 'local_kaltura'), local_kaltura_migration_progress::get_categoriescreated());

        $buttonarray = array();

        $mform->addElement('select', 'kafcategory', get_string('migration_select_a_category', 'local_kaltura'), $categories);

        $catid = local_kaltura_migration_progress::get_kafcategoryrootid();

        // If the migration was started perviously, then prevent the user from chaning the migration category by disabling the drop down, but setting the default value.
        $migrationstarted = local_kaltura_migration_progress::get_migrationstarted();
        if (!empty($migrationstarted) && !empty($catid) && isset($categories[$catid])) {
            $mform->addElement('hidden', 'disabledropdown', $catid);
            $mform->setType('disabledropdown', PARAM_INT);

            $mform->setDefault('kafcategory', $catid);
            $mform->disabledIf('kafcategory', 'disabledropdown', 'eq', $catid);
        }

        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('migration_start_continue', 'local_kaltura'));
        $buttonarray[] =& $mform->createElement('submit', 'startover', get_string('startover', 'local_kaltura'));
        $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('back'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}
