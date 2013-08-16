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
 * Backup and restore actions to help behat feature files writting.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../../lib/behat/behat_field_manager.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Backup-related steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_backup extends behat_base {

    /**
     * Backups the specified course using the provided options. If you are interested in restoring this backup would be useful to provide a 'Filename' option.
     *
     * @Given /^I backup "(?P<course_fullname_string>(?:[^"]|\\")*)" course using this options:$/
     * @param string $backupcourse
     * @param TableNode $options Backup options or false if no options provided
     */
    public function i_backup_course_using_this_options($backupcourse, $options = false) {

        // We can not use other steps here as we don't know where the provided data
        // table elements are used, and we need to catch exceptions contantly.

        // Go to homepage.
        $this->getSession()->visit($this->locate_path('/'));

        // Click the course link.
        $this->find_link($backupcourse)->click();

        // Click the backup link.
        $this->find_link(get_string('backup'))->click();

        // Initial settings.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('backupstage1action', 'backup'))->press();

        // Schema settings.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('backupstage2action', 'backup'))->press();

        // Confirmation and review, backup filename can also be specified.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('backupstage4action', 'backup'))->press();

        // Waiting for it to finish.
        $this->wait(10);

        // Last backup continue button.
        $this->find_button(get_string('backupstage16action', 'backup'))->press();
    }

    /**
     * Imports the specified origin course into the other course using the provided options.
     *
     * Keeping it separatelly from backup & restore, it the number of
     * steps and duplicate code becomes bigger a common method should
     * be generalized.
     *
     * @Given /^I import "(?P<from_course_fullname_string>(?:[^"]|\\")*)" course into "(?P<to_course_fullname_string>(?:[^"]|\\")*)" course using this options:$/
     * @param string $fromcourse
     * @param string $tocourse
     * @param TableNode $options
     */
    public function i_import_course_into_course($fromcourse, $tocourse, $options = false) {

        // We can not use other steps here as we don't know where the provided data
        // table elements are used, and we need to catch exceptions contantly.

        // Go to homepage.
        $this->getSession()->visit($this->locate_path('/'));

        // Click the course link.
        $this->find_link($tocourse)->click();

        // Click the import link.
        $this->find_link(get_string('import'))->click();

        // Select the course.
        $exception = new ExpectationException('"' . $fromcourse . '" course not found in the list of courses to import from', $this->getSession());

        // The argument should be converted to an xpath literal.
        $fromcourse = $this->getSession()->getSelectorsHandler()->xpathLiteral($fromcourse);
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' ics-results ')]" .
            "/descendant::tr[contains(., $fromcourse)]" .
            "/descendant::input[@type='radio']";
        $radionode = $this->find('xpath', $xpath, $exception);
        $radionode->check();
        $radionode->click();

        $this->find_button(get_string('continue'))->press();

        // Initial settings.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('importbackupstage1action', 'backup'))->press();

        // Schema settings.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('importbackupstage2action', 'backup'))->press();

        // Run it.
        $this->find_button(get_string('importbackupstage4action', 'backup'))->press();
        $this->wait();

        // Continue and redirect to 'to' course.
        $this->find_button(get_string('continue'))->press();
    }

    /**
     * Restores the backup into the specified course and the provided options. You should be in the 'Restore' page where the backup is.
     *
     * @Given /^I restore "(?P<backup_filename_string>(?:[^"]|\\")*)" backup into "(?P<existing_course_fullname_string>(?:[^"]|\\")*)" course using this options:$/
     * @param string $backupfilename
     * @param string $existingcourse
     * @param TableNode $options Restore forms options or false if no options provided
     */
    public function i_restore_backup_into_course_using_this_options($backupfilename, $existingcourse, $options = false) {

        // Confirm restore.
        $this->select_backup($backupfilename);

        // The argument should be converted to an xpath literal.
        $existingcourse = $this->getSession()->getSelectorsHandler()->xpathLiteral($existingcourse);

        // Selecting the specified course (we can not call behat_forms::select_radio here as is in another behat subcontext).
        $radionode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), ' bcs-existing-course ')]" .
            "/descendant::div[@class='restore-course-search']" .
            "/descendant::tr[contains(., $existingcourse)]" .
            "/descendant::input[@type='radio']");
        $radionode->check();
        $radionode->click();

        // Pressing the continue button of the restore into an existing course section.
        $continuenode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), ' bcs-existing-course ')]" .
            "/descendant::input[@type='submit'][@value='" . get_string('continue') . "']");
        $continuenode->click();
        $this->wait();

        // Common restore process using provided key/value options.
        $this->process_restore($options);
    }

    /**
     * Restores the specified backup into a new course using the provided options. You should be in the 'Restore' page where the backup is.
     *
     * @Given /^I restore "(?P<backup_filename_string>(?:[^"]|\\")*)" backup into a new course using this options:$/
     * @param string $backupfilename
     * @param TableNode $options Restore forms options or false if no options provided
     */
    public function i_restore_backup_into_a_new_course_using_this_options($backupfilename, $options = false) {

        // Confirm restore.
        $this->select_backup($backupfilename);

        // The first category in the list.
        $radionode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), ' bcs-new-course ')]" .
            "/descendant::div[@class='restore-course-search']" .
            "/descendant::input[@type='radio']");
        $radionode->check();
        $radionode->click();

        // Pressing the continue button of the restore into an existing course section.
        $continuenode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), ' bcs-new-course ')]" .
            "/descendant::input[@type='submit'][@value='" . get_string('continue') . "']");
        $continuenode->click();
        $this->wait();

        // Common restore process using provided key/value options.
        $this->process_restore($options);
    }

    /**
     * Merges the backup into the current course using the provided restore options. You should be in the 'Restore' page where the backup is.
     *
     * @Given /^I merge "(?P<backup_filename_string>(?:[^"]|\\")*)" backup into the current course using this options:$/
     * @param string $backupfilename
     * @param TableNode $options Restore forms options or false if no options provided
     */
    public function i_merge_backup_into_the_current_course($backupfilename, $options = false) {

        // Confirm restore.
        $this->select_backup($backupfilename);

        // Merge without deleting radio option.
        $radionode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), 'bcs-current-course')]" .
            "/descendant::input[@type='radio'][@name='target'][@value='1']");
        $radionode->check();
        $radionode->click();

        // Pressing the continue button of the restore merging section.
        $continuenode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), 'bcs-current-course')]" .
            "/descendant::input[@type='submit'][@value='" . get_string('continue') . "']");
        $continuenode->click();
        $this->wait();

        // Common restore process using provided key/value options.
        $this->process_restore($options);
    }

    /**
     * Merges the backup into the current course after deleting this contents, using the provided restore options. You should be in the 'Restore' page where the backup is.
     *
     * @Given /^I merge "(?P<backup_filename_string>(?:[^"]|\\")*)" backup into the current course after deleting it's contents using this options:$/
     * @param string $backupfilename
     * @param TableNode $options Restore forms options or false if no options provided
     */
    public function i_merge_backup_into_current_course_deleting_its_contents($backupfilename, $options = false) {

        // Confirm restore.
        $this->select_backup($backupfilename);

        // Delete contents radio option.
        $radionode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), 'bcs-current-course')]" .
            "/descendant::input[@type='radio'][@name='target'][@value='0']");
        $radionode->check();
        $radionode->click();

        // Pressing the continue button of the restore merging section.
        $continuenode = $this->find('xpath', "//div[contains(concat(' ', normalize-space(@class), ' '), 'bcs-current-course')]" .
            "/descendant::input[@type='submit'][@value='" . get_string('continue') . "']");
        $continuenode->click();
        $this->wait();

        // Common restore process using provided key/value options.
        $this->process_restore($options);
    }

    /**
     * Selects the backup to restore.
     *
     * @throws ExpectationException
     * @param string $backupfilename
     * @return void
     */
    protected function select_backup($backupfilename) {

        // Using xpath as there are other restore links before this one.
        $exception = new ExpectationException('The "' . $backupfilename . '" backup file can not be found in this page', $this->getSession());

        // The argument should be converted to an xpath literal.
        $backupfilename = $this->getSession()->getSelectorsHandler()->xpathLiteral($backupfilename);

        $xpath = "//tr[contains(., $backupfilename)]/descendant::a[contains(., '" . get_string('restore') . "')]";
        $restorelink = $this->find('xpath', $xpath, $exception);
        $restorelink->click();

        // Confirm the backup contents.
        $restore = $this->find_button(get_string('continue'))->press();
    }

    /**
     * Executes the common steps of all restore processes.
     *
     * @param TableNode $options The backup and restore options or false if no options provided
     * @return void
     */
    protected function process_restore($options) {

        // We can not use other steps here as we don't know where the provided data
        // table elements are used, and we need to catch exceptions contantly.

        // Settings.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('restorestage4action', 'backup'))->press();

        // Schema.
        $this->fill_backup_restore_form($options);
        $this->find_button(get_string('restorestage8action', 'backup'))->press();

        // Review, no options here.
        $this->find_button(get_string('restorestage16action', 'backup'))->press();
        $this->wait(10);

        // Last restore continue button, redirected to restore course after this.
        $this->find_button(get_string('restorestage32action', 'backup'))->press();
    }

    /**
     * Tries to fill the current page form elements with the provided options.
     *
     * This step is slow as it spins over each provided option, we are
     * not expected to have lots of provided options, anyways, is better
     * to be conservative and wait for the elements to appear rather than
     * to have false failures.
     *
     * @param TableNode $options The backup and restore options or false if no options provided
     * @return void
     */
    protected function fill_backup_restore_form($options) {

        // Nothing to fill if no options are provided.
        if (!$options) {
            return;
        }

        // If we find any of the provided options in the current form we should set the value.
        $datahash = $options->getRowsHash();
        foreach ($datahash as $locator => $value) {

            try {
                $fieldnode = $this->find_field($locator);
                $field = behat_field_manager::get_form_field($fieldnode, $this->getSession());
                $field->set_value($value);

            } catch (ElementNotFoundException $e) {
                // Next provided option then, this one should be part of another page's fields.
            }
        }
    }

    /**
     * Waits until the DOM is ready.
     *
     * @param int To override the default timeout
     * @return void
     */
    protected function wait($timeout = false) {

        if (!$this->running_javascript()) {
            return;
        }

        if (!$timeout) {
            $timeout = self::TIMEOUT;
        }
        $this->getSession()->wait($timeout, '(document.readyState === "complete")');
    }

}
