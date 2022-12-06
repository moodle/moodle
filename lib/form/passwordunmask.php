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
 * Password type form element with unmask option
 *
 * Contains HTML class for a password type element with unmask option
 *
 * @package   core_form
 * @copyright 2009 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir.'/form/password.php');

/**
 * Password type form element with unmask option
 *
 * HTML class for a password type element with unmask option
 *
 * @package   core_form
 * @category  form
 * @copyright 2009 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_passwordunmask extends MoodleQuickForm_password {
    /**
     * constructor
     *
     * @param string $elementName (optional) name of the password element
     * @param string $elementLabel (optional) label for password element
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        $this->_persistantFreeze = true;

        parent::__construct($elementName, $elementLabel, $attributes);
        $this->setType('passwordunmask');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_passwordunmask($elementName=null, $elementLabel=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Function to export the renderer data in a format that is suitable for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        $context = parent::export_for_template($output);
        $context['valuechars'] = array_fill(0, strlen($context['value']), 'x');

        return $context;
    }

    /**
     * Check that there is no whitespace at the beginning and end of the password.
     *
     * It turned out that wrapping whitespace can easily be pasted by accident when copying the text from elsewhere.
     * Such a mistake is very hard to debug as the whitespace is not displayed.
     *
     * @param array $value Submitted value.
     * @return string|null Validation error message or null.
     */
    public function validateSubmitValue($value) {
        if ($value !== null && $value !== trim($value)) {
            return get_string('err_wrappingwhitespace', 'core_form');
        }

        return;
    }
}
