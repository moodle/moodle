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
 * Registration form to be completed by administrator to register plugin with developer.
 *
 * @package format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

namespace format_tiles\form;
defined('MOODLE_INTERNAL') || die();

use moodleform;

global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class registration_form
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_form extends moodleform {

    /**
     * Define the fields and the form.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function definition() {
        global $CFG, $USER;
        $mform = $this->_form;

        $strrequired = get_string('required');
        $stroptional = ' (' . strtolower(get_string('optional', 'form')) . ')';
        $admin = get_admin();
        $site = get_site();

        $mform->addElement('text', 'sitename', get_string('sitename', 'format_tiles'),
            array('class' => 'registration_textfield'));
        $mform->setType('sitename', PARAM_TEXT);
        $mform->setDefault('sitename', $site->fullname);
        $mform->freeze('sitename');

        $mform->addElement('text', 'url', get_string('siteurl', 'core_hub'),
            array('class' => 'registration_textfield'));
        $mform->setType('url', PARAM_TEXT);
        $mform->setDefault('url', $CFG->wwwroot);
        $mform->freeze('url');

        $moodlerelease = $CFG->release;
        if (preg_match('/^(\d+\.\d.*?)[\. ]/', $moodlerelease, $matches)) {
            $moodlerelease = $matches[1];
        }
        $mform->addElement('text', 'moodleversion', get_string('moodleversion'),
            array('class' => 'registration_textfield'));
        $mform->setType('moodleversion', PARAM_TEXT);
        $mform->setDefault('moodleversion', $moodlerelease);
        $mform->freeze('moodleversion');

        $countries = ['' => ''] + get_string_manager()->get_list_of_countries();
        $mform->addElement('select', 'countrycode', get_string('sitecountry', 'hub'), $countries);
        $mform->setType('countrycode', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('countrycode', 'sitecountry', 'hub');
        $mform->setDefault('countrycode', $admin->country ?: $USER->country ?: $CFG->country);

        $languagues = get_string_manager()->get_list_of_languages();
        $mform->addElement('select', 'language', get_string('language', 'hub') . $stroptional, $languagues);
        $mform->setType('language', PARAM_ALPHANUMEXT);
        $mform->setDefault('language', explode('_', current_language())[0]);

        $mform->addElement('text', 'nameuser', get_string('name', 'core_hub') . $stroptional,
            array('class' => 'registration_textfield'));
        $mform->setType('nameuser', PARAM_TEXT);
        $mform->setDefault('nameuser', $USER->firstname . ' ' . $USER->lastname);

        $mform->addElement('text', 'contactemail', get_string('email') . $stroptional,
            array('class' => 'registration_textfield'));
        $mform->setType('contactemail', PARAM_EMAIL);
        $mform->setDefault('contactemail', $USER->email);

        $options = [
            '' => '---' . get_string('registerpickemailpref', 'format_tiles') . '---',
            1 => get_string('registeremailyes', 'format_tiles'),
            0 => get_string('registeremailno', 'format_tiles')
        ];
        $mform->addElement('select', 'emailpref', get_string('registerpickemailpref', 'format_tiles'), $options);
        $mform->addRule('emailpref', $strrequired, 'required', null, 'client');

        $privacypolicylink = \html_writer::link(
            'https://evolutioncode.uk/privacy',
            get_string('registerpolicyagreedlinktext', 'format_tiles')
        );
        $mform->addElement(
            'checkbox',
            'policyagreed',
            get_string('registerpolicyagreedlinktext', 'format_tiles'),
            get_string('registeragreeprivacy', 'format_tiles', array('privacypolicylink' => $privacypolicylink))
        );
        $mform->addRule('policyagreed', $strrequired, 'required', null, 'client');
        $buttonlabel = get_string('register', 'format_tiles');
        $this->add_action_buttons(false, $buttonlabel);
    }

    /**
     * Validate submitted data.
     * @param array $data
     * @param array $files
     * @return array
     * @throws \coding_exception
     */
    public function validation($data, $files) {
        $errors = [];
        if (
            array_key_exists('emailpref', $data) and $data['emailpref'] == "1"
            && array_key_exists('contactemail', $data) and trim($data['contactemail']) == ''
        ) {
            $errors['contactemail'] = get_string('registermissingemail', 'format_tiles');
        }
        return $errors;
    }
}
