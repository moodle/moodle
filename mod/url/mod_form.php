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
 * URL configuration form
 *
 * @package    mod_url
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/url/locallib.php');

class mod_url_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $config = get_config('url');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        $mform->addHelpButton('name', 'name', 'url');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addElement('url', 'externalurl', get_string('externalurl', 'url'), array('size'=>'60'), array('usefilepicker'=>true));
        $mform->setType('externalurl', PARAM_RAW_TRIMMED);
        $mform->addRule('externalurl', null, 'required', null, 'client');
        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);
        //-------------------------------------------------------
        $mform->addElement('header', 'optionssection', get_string('appearance'));

        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }
        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'url'), $options);
            $mform->setDefault('display', $config->display);
            $mform->addHelpButton('display', 'displayselect', 'url');
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', get_string('popupwidth', 'url'), array('size'=>3));
            if (count($options) > 1) {
                $mform->hideIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);

            $mform->addElement('text', 'popupheight', get_string('popupheight', 'url'), array('size'=>3));
            if (count($options) > 1) {
                $mform->hideIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_AUTO, $options) or
          array_key_exists(RESOURCELIB_DISPLAY_EMBED, $options) or
          array_key_exists(RESOURCELIB_DISPLAY_FRAME, $options)) {
            $mform->addElement('checkbox', 'printintro', get_string('printintro', 'url'));
            $mform->hideIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_POPUP);
            $mform->hideIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_OPEN);
            $mform->hideIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_NEW);
            $mform->setDefault('printintro', $config->printintro);
        }

        //-------------------------------------------------------
        if ($config->allowvariables) {
            $mform->addElement('header', 'parameterssection', get_string('parametersheader', 'url'));
            $mform->addElement('static', 'parametersinfo', '', get_string('parametersheader_help', 'url'));

            if (empty($this->current->parameters)) {
                $parcount = 5;
            } else {
                $parcount = 5 + count((array)unserialize_array($this->current->parameters));
                $parcount = ($parcount > 100) ? 100 : $parcount;
            }
            $options = url_get_variable_options($config);

            for ($i = 0; $i < $parcount; $i++) {
                $parameter = "parameter_$i";
                $variable = "variable_$i";
                $pargroup = "pargoup_$i";
                $group = [
                    $mform->createElement('text', $parameter, '', ['size' => '12']),
                    $mform->createElement('selectgroups', $variable, '', $options),
                ];
                $mform->addGroup($group, $pargroup, get_string('parameterinfo', 'url'), ' ', false);
                $mform->setType($parameter, PARAM_RAW);
            }
        }

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = (array) unserialize_array($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (!empty($displayoptions['popupwidth'])) {
                $default_values['popupwidth'] = $displayoptions['popupwidth'];
            }
            if (!empty($displayoptions['popupheight'])) {
                $default_values['popupheight'] = $displayoptions['popupheight'];
            }
        }
        if (!empty($default_values['parameters'])) {
            $parameters = (array) unserialize_array($default_values['parameters']);
            $i = 0;
            foreach ($parameters as $parameter=>$variable) {
                $default_values['parameter_'.$i] = $parameter;
                $default_values['variable_'.$i]  = $variable;
                $i++;
            }
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validating Entered url, we are looking for obvious problems only,
        // teachers are responsible for testing if it actually works.

        // This is not a security validation!! Teachers are allowed to enter "javascript:alert(666)" for example.

        // NOTE: do not try to explain the difference between URL and URI, people would be only confused...

        if (!empty($data['externalurl'])) {
            $url = $data['externalurl'];
            if (preg_match('|^/|', $url)) {
                // links relative to server root are ok - no validation necessary

            } else if (preg_match('|^[a-z]+://|i', $url) or preg_match('|^https?:|i', $url) or preg_match('|^ftp:|i', $url)) {
                // normal URL
                if (!url_appears_valid_url($url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'url');
                }

            } else if (preg_match('|^[a-z]+:|i', $url)) {
                // general URI such as teamspeak, mailto, etc. - it may or may not work in all browsers,
                // we do not validate these at all, sorry

            } else {
                // invalid URI, we try to fix it by adding 'http://' prefix,
                // relative links are NOT allowed because we display the link on different pages!
                if (!url_appears_valid_url('http://'.$url)) {
                    $errors['externalurl'] = get_string('invalidurl', 'url');
                }
            }
        }
        return $errors;
    }

}
