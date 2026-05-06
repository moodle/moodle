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
 * LDAP enrolment plugin admin setting classes
 *
 * @package    enrol_ldap
 * @author     Iñaki Arenaza
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A variant on configtext to store trimmed and optionally lowercased values.
 *
 * @package    enrol_ldap
 * @author     Iñaki Arenaza
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_trim_lower extends \core_admin\setting\setting\configtext {
    /** @var bool whether to lowercase the value or not before writing in to the db */
    private $lowercase;

    /** @var bool To store enable/disabled status of the input field. */
    protected $enabled;

    /**
     * Constructor: uses parent::__construct
     *
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for those belonging to a plugin.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting default value for the setting
     * @param bool $lowercase if true, lowercase the value before writing it to the db.
     * @param bool $enabled if true, the input field is enabled, otherwise it's disabled.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $lowercase = false, $enabled = true) {
        $this->lowercase = $lowercase;
        $this->enabled = $enabled;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    #[\Override]
    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT && $data === '') {
            // Do not complain if '' used instead of 0.
            $data = 0;
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        if ($this->lowercase) {
            $data = core_text::strtolower($data);
        }
        if (!$this->enabled) {
            return '';
        }
        return ($this->config_write($this->name, trim($data)) ? '' : get_string('errorsetting', 'admin'));
    }
}

/**
 * Role mapping setting for LDAP enrolment plugin.
 *
 * @package    enrol_ldap
 * @author     Iñaki Arenaza
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_ldap_rolemapping extends \core_admin\setting {
    #[\Override]
    public function get_setting() {
        $roles = role_fix_names(get_all_roles());
        $result = [];
        foreach ($roles as $role) {
            $contexts = $this->config_read('contexts_role' . $role->id);
            $memberattribute = $this->config_read('memberattribute_role' . $role->id);
            $result[] = ['id' => $role->id,
                              'name' => $role->localname,
                              'contexts' => $contexts,
                              'memberattribute' => $memberattribute];
        }
        return $result;
    }

    #[\Override]
    public function write_setting($data) {
        if (!is_array($data)) {
            // Ignore it.
            return '';
        }

        $result = '';
        foreach ($data as $roleid => $data) {
            if (!$this->config_write('contexts_role' . $roleid, trim($data['contexts']))) {
                $return = get_string('errorsetting', 'admin');
            }
            if (!$this->config_write('memberattribute_role' . $roleid, core_text::strtolower(trim($data['memberattribute'])))) {
                $return = get_string('errorsetting', 'admin');
            }
        }
        return $result;
    }

    #[\Override]
    public function output_html($data, $query = '') {
        $return  = html_writer::start_tag('div', ['style' => 'float:left; width:auto; margin-right: 0.5em;']);
        $return .= html_writer::tag('div', get_string('roles', 'role'), ['style' => 'height: 2em;']);
        foreach ($data as $role) {
            $return .= html_writer::tag('div', s($role['name']), ['style' => 'height: 2em;']);
        }
        $return .= html_writer::end_tag('div');

        $return .= html_writer::start_tag('div', ['style' => 'float:left; width:auto; margin-right: 0.5em;']);
        $return .= html_writer::tag('div', get_string('contexts', 'enrol_ldap'), ['style' => 'height: 2em;']);
        foreach ($data as $role) {
            $contextid = $this->get_id() . '[' . $role['id'] . '][contexts]';
            $contextname = $this->get_full_name() . '[' . $role['id'] . '][contexts]';
            $return .= html_writer::start_tag('div', ['style' => 'height: 2em;']);
            $return .= html_writer::label(
                get_string(
                    'role_mapping_context',
                    'enrol_ldap',
                    $role['name']
                ),
                $contextid,
                false,
                ['class' => 'accesshide'],
            );
            $attrs = [
                'type' => 'text',
                'size' => '40',
                'id' => $contextid,
                'name' => $contextname,
                'value' => s($role['contexts']),
                'class' => 'text-ltr',
            ];
            $return .= html_writer::empty_tag('input', $attrs);
            $return .= html_writer::end_tag('div');
        }
        $return .= html_writer::end_tag('div');

        $return .= html_writer::start_tag('div', ['style' => 'float:left; width:auto; margin-right: 0.5em;']);
        $return .= html_writer::tag('div', get_string('memberattribute', 'enrol_ldap'), ['style' => 'height: 2em;']);
        foreach ($data as $role) {
            $memberattrid = $this->get_id() . '[' . $role['id'] . '][memberattribute]';
            $memberattrname = $this->get_full_name() . '[' . $role['id'] . '][memberattribute]';
            $return .= html_writer::start_tag('div', ['style' => 'height: 2em;']);
            $return .= html_writer::label(
                get_string(
                    'role_mapping_attribute',
                    'enrol_ldap',
                    $role['name'],
                ),
                $memberattrid,
                false,
                ['class' => 'accesshide'],
            );
            $attrs = ['type' => 'text', 'size' => '15', 'id' => $memberattrid, 'name' => $memberattrname,
                'value' => s($role['memberattribute']), 'class' => 'text-ltr'];
            $return .= html_writer::empty_tag('input', $attrs);
            $return .= html_writer::end_tag('div');
        }
        $return .= html_writer::end_tag('div');
        $return .= html_writer::tag('div', '', ['style' => 'clear:both;']);

        return format_admin_setting(
            $this,
            $this->visiblename,
            $return,
            $this->description,
            true,
            '',
            '',
            $query
        );
    }
}

/**
 * A specialized setting for course categories that are loaded only when required.
 *
 * @author Darko Miletic
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_ldap_admin_setting_category extends \core_admin\setting\setting\configselect {
    /**
     * Constructor for LDAP Enrolment setting category.
     *
     * @param mixed $name
     * @param mixed $visiblename
     * @param mixed $description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, 1, null);
    }

    #[\Override]
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = core_course_category::make_categories_list('', 0, ' / ');
        return true;
    }
}
