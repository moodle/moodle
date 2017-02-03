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
 * File manager form element.
 *
 * @package    core_form
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_field.php');

/**
 * File manager form field.
 *
 * Simple filemanager field manager to allow
 * forms to be filled using TableNodes. It only
 * adds files and checks the field contents in the
 * root directory. If you want to run complex actions
 * that involves subdirectories or other repositories
 * than 'Upload a file' you should use steps related with
 * behat_filepicker::i_add_file_from_repository_to_filemanager
 * this is intended to be used with multi-field
 *
 * This field manager allows you to:
 * - Get: A comma-separated list of the root directory
 *   file names, including folders.
 * - Set: Add a file, in case you want to add more than
 *     one file you can always set two table rows using
 *     the same locator.
 * - Match: A comma-separated list of file names.
 *
 * @package    core_form
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_filemanager extends behat_form_field {

    /**
     * Gets the value.
     *
     * @return string A comma-separated list of the root directory file names.
     */
    public function get_value() {

        // Wait until DOM and JS is ready.
        $this->session->wait(behat_base::TIMEOUT, behat_base::PAGE_READY_JS);

        // Get the label to restrict the files to this single form field.
        $fieldlabel = $this->get_field_locator();

        // Get the name of the current directory elements.
        $xpath = "//label[contains(., '" . $fieldlabel . "')]" .
            "/ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' fitem ')]" .
            "/descendant::div[@data-fieldtype = 'filemanager']" .
            "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename ')]";

        // We don't need to wait here, also we don't have access to protected
        // contexts find* methods.
        $files = $this->session->getPage()->findAll('xpath', $xpath);

        if (!$files) {
            return '';
        }

        $filenames = array();
        foreach ($files as $filenode) {
            $filenames[] = $filenode->getText();
        }

        return implode(',', $filenames);
    }

    /**
     * Sets the field value.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {

        // Getting the filemanager label from the DOM.
        $fieldlabel = $this->get_field_locator();

        // Getting the filepicker context and using the step definition
        // to upload the requested file.
        $uploadcontext = behat_context_helper::get('behat_repository_upload');
        $uploadcontext->i_upload_file_to_filemanager($value, $fieldlabel);
    }

    /**
     * Matches the provided filename/s against the current field value.
     *
     * If the filemanager contains more than one file the $expectedvalue
     * value should include all the file names separating them by comma.
     *
     * @param string $expectedvalue
     * @return bool The provided value matches the field value?
     */
    public function matches($expectedvalue) {
        return $this->text_matches($expectedvalue);
    }

}
