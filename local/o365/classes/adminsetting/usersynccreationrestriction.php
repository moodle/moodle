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
 * Admin setting to control field mappings for users.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/adminlib.php');

/**
 * Admin setting to control field mappings for users.
 */
class usersynccreationrestriction extends \admin_setting {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param mixed $defaultsetting string or array depending on implementation
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        global $DB;

        $this->remotefields = [
            'objectId' => get_string('settings_fieldmap_field_objectId', 'auth_oidc'),
            'userPrincipalName' => get_string('settings_fieldmap_field_userPrincipalName', 'auth_oidc'),
            'displayName' => get_string('settings_fieldmap_field_displayName', 'auth_oidc'),
            'givenName' => get_string('settings_fieldmap_field_givenName', 'auth_oidc'),
            'surname' => get_string('settings_fieldmap_field_surname', 'auth_oidc'),
            'mail' => get_string('settings_fieldmap_field_mail', 'auth_oidc'),
            'streetAddress' => get_string('settings_fieldmap_field_streetAddress', 'auth_oidc'),
            'city' => get_string('settings_fieldmap_field_city', 'auth_oidc'),
            'postalCode' => get_string('settings_fieldmap_field_postalCode', 'auth_oidc'),
            'state' => get_string('settings_fieldmap_field_state', 'auth_oidc'),
            'country' => get_string('settings_fieldmap_field_country', 'auth_oidc'),
            'jobTitle' => get_string('settings_fieldmap_field_jobTitle', 'auth_oidc'),
            'department' => get_string('settings_fieldmap_field_department', 'auth_oidc'),
            'companyName' => get_string('settings_fieldmap_field_companyName', 'auth_oidc'),
            'telephoneNumber' => get_string('settings_fieldmap_field_telephoneNumber', 'auth_oidc'),
            'faxNumber' => get_string('settings_fieldmap_field_faxNumber', 'auth_oidc'),
            'mobile' => get_string('settings_fieldmap_field_mobile', 'auth_oidc'),
            'preferredLanguage' => get_string('settings_fieldmap_field_preferredLanguage', 'auth_oidc'),
            'employeeId' => get_string('settings_fieldmap_field_employeeId', 'auth_oidc'),
            'o365group' => get_string('settings_usersynccreationrestriction_o365group', 'local_o365'),
        ];

        return parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return unserialize($this->config_read($this->name));
    }

    /**
     * Write the setting.
     *
     * We do this manually so just pretend here.
     *
     * @param mixed $data Incoming form data.
     * @return string Always empty string representing no issues.
     */
    public function write_setting($data) {
        $newconfig = [];
        if (!isset($data['remotefield']) || !isset($data['value'])) {
            // Broken data, wipe setting.
            $this->config_write($this->name, serialize($newconfig));
            return '';
        }

        $newconfig = [
            'remotefield' => $data['remotefield'],
            'value' => $data['value'],
            'useregex' => (!empty($data['useregex'])) ? true : false,
        ];
        $this->config_write($this->name, serialize($newconfig));
        return '';
    }

    /**
     * Return an XHTML string for the setting.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $DB, $OUTPUT;

        if (empty($data) || !is_array($data)) {
            $data = [];
        }
        $remotefield = (isset($data['remotefield']) && isset($this->remotefields[$data['remotefield']])) ?
            $data['remotefield'] : '';
        $value = (isset($data['value'])) ? $data['value'] : '';
        $useregex = (!empty($data['useregex'])) ? true : false;

        $html = \html_writer::start_tag('div');
        $onchange = 'document.getElementById(\'usercreationrestriction_useregex_wrapper\').style.visibility ' .
            '= (this.value == \'o365group\') ? \'hidden\' : \'visible\'';
        $selectattrs = [
            'style' => 'width: 250px;vertical-align: top;margin-right: 0.25rem;margin-top:0.25rem;',
            'onchange' => $onchange,
        ];
        $html .= \html_writer::select($this->remotefields,
            $this->get_full_name().'[remotefield]', $remotefield, ['' => 'choosedots'], $selectattrs);

        $inputdivattrs = ['style' => 'display:inline-block;margin-top:0.25rem;'];
        $html .= \html_writer::start_tag('div', $inputdivattrs);
        $inputattrs = [
            'type' => 'text',
            'name' => $this->get_full_name().'[value]',
            'placeholder' => get_string('settings_usersynccreationrestriction_fieldval', 'local_o365'),
            'class' => 'form-control',
            'style' => 'width: 250px;display:inline-block;',
            'value' => $value,
        ];
        $html .= \html_writer::empty_tag('input', $inputattrs);
        $html .= \html_writer::empty_tag('br');
        $regexwrapperattrs = [
            'id' => 'usercreationrestriction_useregex_wrapper',
            'style' => ($remotefield == 'o365group') ? 'visibility: hidden' : '',
        ];
        $html .= \html_writer::start_tag('div', $regexwrapperattrs);
        $inputattrs = [
            'type' => 'checkbox',
            'id' => 'usercreationrestriction_useregex',
            'name' => $this->get_full_name().'[useregex]',
            'value' => '1',
        ];
        if ($useregex === true) {
            $inputattrs['checked'] = 'checked';
        }
        $html .= \html_writer::empty_tag('input', $inputattrs);
        $html .= ' ';
        $regexstr = get_string('settings_usersynccreationrestriction_regex', 'local_o365');
        $html .= \html_writer::tag('label', $regexstr, ['for' => 'usercreationrestriction_useregex', 'style' => 'margin:0']);
        $html .= \html_writer::end_tag('div');
        $html .= \html_writer::end_tag('div');

        $html .= \html_writer::end_tag('div');

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }
}
