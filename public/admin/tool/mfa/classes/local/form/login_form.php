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

namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

/**
 * MFA login form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class login_form extends \moodleform {

    /** @var \tool_mfa\local\form\global_form_manager */
    public $globalmanager;

    /**
     * Create an instance of this class.
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param mixed $customdata if your form defintion method needs access to data such as $course
     *              $cm, etc. to construct the form definition then pass it in this array. You can
     *              use globals for somethings.
     * @param string $method if you set this to anything other than 'post' then _GET and _POST will
     *               be merged and used as incoming data to the form.
     * @param string $target target frame for form submission. You will rarely use this. Don't use
     *               it if you don't need to as the target attribute is deprecated in xhtml strict.
     * @param mixed $attributes you can pass a string of html attributes here or an array.
     *               Special attribute 'data-random-ids' will randomise generated elements ids. This
     *               is necessary when there are several forms on the same page.
     *               Special attribute 'data-double-submit-protection' set to 'off' will turn off
     *               double-submit protection JavaScript - this may be necessary if your form sends
     *               downloadable files in response to a submit button, and can't call
     *               \core_form\util::form_download_complete();
     * @param bool $editable
     * @param array $ajaxformdata Forms submitted via ajax, must pass their data here, instead of relying on _GET and _POST.
     */
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '',
            $attributes = null, $editable = true, $ajaxformdata = null) {
        $this->globalmanager = new \tool_mfa\local\form\global_form_manager();
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition(): void {
        $mform = $this->_form;
        $factor = $this->_customdata['factor'];
        $mform = $factor->login_form_definition($mform);
        // Add a hidden field with the factor name so it is always available.
        $factorname = $mform->addElement('hidden', 'factor', $factor->name);
        $factorname->setType(PARAM_ALPHAEXT);
        $this->globalmanager->definition($mform);
    }

    /**
     * Invokes factor login_form_definition_after_data() method after form data has been set.
     *
     * @return void
     */
    public function definition_after_data(): void {
        $mform = $this->_form;
        $factor = $this->_customdata['factor'];

        $factor->login_form_definition_after_data($mform);
        $this->globalmanager->definition_after_data($mform);

        $buttonarray = [];
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('loginsubmit', 'factor_' . $factor->name));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Validates the login form with given factor validation method.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $factor = $this->_customdata['factor'];
        $errors += $factor->login_form_validation($data);
        $errors += $this->globalmanager->validation($data, $files);

        // Execute sleep time bruteforce mitigation.
        \tool_mfa\manager::sleep_timer();

        return $errors;
    }

    /**
     * Returns error corresponding to validated element.
     *
     * @param string $elementname Name of form element to check.
     * @return string|null Error message corresponding to the validated element.
     */
    public function get_element_error(string $elementname): ?string {
        return $this->_form->getElementError($elementname);
    }

    /**
     * Set an error message for a form element.
     *
     * @param string $elementname Name of form element to set error for.
     * @param string $error Error message, if empty then removes the current error message.
     * @return void
     */
    public function set_element_error(string $elementname, string $error): void {
        $this->_form->setElementError($elementname, $error);
    }

    /**
     * Freeze a form element.
     *
     * @param string $elementname Name of form element to freeze.
     * @return void
     */
    public function freeze(string $elementname): void {
        $this->_form->freeze($elementname);
    }

    /**
     * Returns true if the form element exists.
     *
     * @param string $elementname Name of form element to check.
     * @return bool
     */
    public function element_exists(string $elementname): bool {
        return $this->_form->elementExists($elementname);
    }
}
