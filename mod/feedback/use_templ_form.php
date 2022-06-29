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
 * prints the form to confirm use template
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

/**
 * The mod_feedback_use_templ_form
 *
 * @deprecated since 4.0. New dynamic forms have been created instead.
 */
class mod_feedback_use_templ_form extends moodleform {
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '',
            $attributes = null, $editable = true, $ajaxformdata = null) {
        debugging('Class mod_feedback_use_templ_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    public function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('radio', 'deleteolditems', '', get_string('delete_old_items', 'feedback'), 1);
        $mform->addElement('radio', 'deleteolditems', '', get_string('append_new_items', 'feedback'), 0);
        $mform->setType('deleteolditems', PARAM_INT);
        $mform->setDefault('deleteolditems', 1);

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_INT);
        $mform->setConstant('do_show', 'edit');

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @return array
     */
    public static function get_js_module() {
        debugging('Class mod_feedback_use_templ_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::get_js_module();
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     * @return array
     */
    public static function mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class mod_feedback_use_templ_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $data
     * @return array
     */
    public static function mock_generate_submit_keys($data = []) {
        debugging('Class mod_feedback_use_templ_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_generate_submit_keys($data);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     */
    public static function mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class mod_feedback_use_templ_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }
}

