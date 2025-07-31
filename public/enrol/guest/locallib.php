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


defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Guest access plugin implementation.
 *
 * @deprecated since Moodle 5.0 - please use {@see enrol_guest\form\enrol_form}
 *
 * @package    enrol_guest
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\deprecated(replacement: enrol_guest\form\enrol_form::class, since: '5.0', reason: 'Now a dynamic form is used')]
class enrol_guest_enrol_form extends moodleform {
    protected $instance;

    /**
     * Constructor
     *
     * @param mixed $action
     * @param mixed $customdata
     * @param string $method
     * @param string $target
     * @param mixed $attributes
     * @param bool $editable
     * @param array $ajaxformdata
     */
    public function __construct($action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true,
                                $ajaxformdata=null) {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('guest');

        $heading = $plugin->get_instance_name($instance);
        $mform->addElement('header', 'guestheader', $heading);

        $mform->addElement('password', 'guestpassword', get_string('password', 'enrol_guest'));

        $this->add_action_buttons(false, get_string('submit'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        $instance = $this->instance;

        if ($instance->password !== '') {
            if ($data['guestpassword'] !== $instance->password) {
                $plugin = enrol_get_plugin('guest');
                if ($plugin->get_config('showhint')) {
                    $hint = core_text::substr($instance->password, 0, 1);
                    $errors['guestpassword'] = get_string('passwordinvalidhint', 'enrol_guest', $hint);
                } else {
                    $errors['guestpassword'] = get_string('passwordinvalid', 'enrol_guest');
                }
            }
        }

        return $errors;
    }
}
