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
 * Class registration_form
 *
 * @package    tool_brickfield
 * @copyright  2021 Brickfield Education Labs https://www.brickfield.ie
 * @author  2020 JM Tomas <jmtomas@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace tool_brickfield\form;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use dml_exception;
use html_writer;
use moodle_exception;
use moodleform;
use stdClass;
use tool_brickfield\manager;
use tool_brickfield\registration;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class registration_form
 *
 * @package    tool_brickfield
 * @author  2020 JM Tomas <jmtomas@tresipunt.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
class registration_form  extends moodleform {

    /**
     * Form definition.
     * @throws moodle_exception
     */
    public function definition() {
        $mform = & $this->_form;
        $required = get_string('required');
        $info = $this->get_defaultinfo();
        $registration = new registration();
        $key = $registration->get_api_key();
        $hash = $registration->get_secret_key();

        $mform->addElement('header', 'activationheader', get_string('activationheader', manager::PLUGINNAME));
        $mform->addElement('text', 'key', get_string('secretkey', manager::PLUGINNAME));
        $mform->setType('key', PARAM_TEXT);
        $mform->setDefault('key', !empty($key) ? $key : '');
        $mform->addHelpButton('key', 'secretkey', manager::PLUGINNAME);

        $mform->addElement('text', 'hash', get_string('sitehash', manager::PLUGINNAME));
        $mform->setType('hash', PARAM_TEXT);
        $mform->setDefault('hash', !empty($hash) ? $hash : '');
        $mform->addHelpButton('hash', 'sitehash', manager::PLUGINNAME);

        $mform->addElement('header', 'moreinfo', get_string('moreinfo', manager::PLUGINNAME));
        $mform->addElement('static', 'siteinfosummary', '',
            get_string('sendfollowinginfo', manager::PLUGINNAME, $info->moreinfostring));

        $mform->addElement('hidden', 'lang', $info->languagecode);
        $mform->setType('lang', PARAM_TEXT);
        $mform->addElement('hidden', 'countrycode', $info->country);
        $mform->setType('countrycode', PARAM_TEXT);
        $mform->addElement('hidden', 'url', $info->url);
        $mform->setType('url', PARAM_URL);

        $this->add_action_buttons(false, get_string('activate', manager::PLUGINNAME, '#'));
    }

    /**
     * Get default data for registration form
     *
     * @throws moodle_exception
     * @return stdClass
     */
    protected function get_defaultinfo(): stdClass {
        global $CFG;
        $admin = get_admin();
        $site = get_site();
        $data = new stdClass();
        $data->name = $site->fullname;
        $data->url = $CFG->wwwroot;
        $data->language = get_string('thislanguage', 'langconfig');
        $data->languagecode = $admin->lang ?: $CFG->lang;
        $data->country = $admin->country ?: $CFG->country;
        $data->email = $admin->email;
        $data->moreinfo = self::get_moreinfo();
        $data->moreinfostring = self::get_moreinfostring($data->moreinfo);
        return $data;
    }

    /**
     * Get more information.
     * @return array
     * @throws dml_exception
     */
    private static function get_moreinfo(): array {
        global $CFG, $DB;
        $moreinfo = array();
        $moodlerelease = $CFG->release;
        if (preg_match('/^(\d+\.\d.*?)[. ]/', $moodlerelease, $matches)) {
            $moodlerelease = $matches[1];
        }
        $moreinfo['release'] = $moodlerelease;
        $moreinfo['numcourses'] = $DB->count_records('course') - 1;
        $moreinfo['numusers'] = $DB->count_records('user', array('deleted' => 0));
        $moreinfo['numfiles'] = $DB->count_records('files');
        $moreinfo['numfactivities'] = $DB->count_records('course_modules');
        $moreinfo['mobileservice'] = empty($CFG->enablemobilewebservice) ? 0 : $CFG->enablemobilewebservice;
        $moreinfo['usersmobileregistered'] = $DB->count_records('user_devices');
        $moreinfo['contentyperesults'] = '';
        $moreinfo['contenttypeerrors'] = '';
        $moreinfo['percheckerrors'] = '';
        return $moreinfo;
    }

    /**
     * Get HTML list for more information.
     *
     * @param array $moreinfo
     * @return string
     * @throws coding_exception
     */
    private static function get_moreinfostring(array $moreinfo): string {
        $html = html_writer::start_tag('ul');
        foreach ($moreinfo as $key => $value) {
            $html .= html_writer::tag('li', get_string($key, manager::PLUGINNAME, $value));
        }
        $html .= html_writer::end_tag('ul');
        return $html;
    }
}
