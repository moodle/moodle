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
 * Advanced role definition form.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * As well as tracking the permissions information about the role we are creating
 * or editing, we also track the other information about the role. (This class is
 * starting to be more and more like a formslib form in some respects.)
 */
class core_role_define_role_table_advanced extends core_role_capability_table_with_risks {
    /** @var stdClass Used to store other information (besides permissions) about the role we are creating/editing. */
    protected $role;
    /** @var array Used to store errors found when validating the data. */
    protected $errors;
    protected $contextlevels;
    protected $allcontextlevels;
    protected $disabled = '';

    protected $allowassign;
    protected $allowoverride;
    protected $allowswitch;

    public function __construct($context, $roleid) {
        $this->roleid = $roleid;
        parent::__construct($context, 'defineroletable', $roleid);
        $this->displaypermissions = $this->allpermissions;
        $this->strperms[$this->allpermissions[CAP_INHERIT]] = get_string('notset', 'core_role');

        $this->allcontextlevels = array();
        $levels = context_helper::get_all_levels();
        foreach ($levels as $level => $classname) {
            $this->allcontextlevels[$level] = context_helper::get_level_name($level);
        }
    }

    protected function load_current_permissions() {
        global $DB;
        if ($this->roleid) {
            if (!$this->role = $DB->get_record('role', array('id' => $this->roleid))) {
                throw new moodle_exception('invalidroleid');
            }
            $contextlevels = get_role_contextlevels($this->roleid);
            // Put the contextlevels in the array keys, as well as the values.
            if (!empty($contextlevels)) {
                $this->contextlevels = array_combine($contextlevels, $contextlevels);
            } else {
                $this->contextlevels = array();
            }
            $this->allowassign = array_keys($this->get_allow_roles_list('assign'));
            $this->allowoverride = array_keys($this->get_allow_roles_list('override'));
            $this->allowswitch = array_keys($this->get_allow_roles_list('switch'));

        } else {
            $this->role = new stdClass;
            $this->role->name = '';
            $this->role->shortname = '';
            $this->role->description = '';
            $this->role->archetype = '';
            $this->contextlevels = array();
            $this->allowassign = array();
            $this->allowoverride = array();
            $this->allowswitch = array();
        }
        parent::load_current_permissions();
    }

    public function read_submitted_permissions() {
        global $DB;
        $this->errors = array();

        // Role short name. We clean this in a special way. We want to end up
        // with only lowercase safe ASCII characters.
        $shortname = optional_param('shortname', null, PARAM_RAW);
        if (!is_null($shortname)) {
            $this->role->shortname = $shortname;
            $this->role->shortname = core_text::specialtoascii($this->role->shortname);
            $this->role->shortname = core_text::strtolower(clean_param($this->role->shortname, PARAM_ALPHANUMEXT));
            if (empty($this->role->shortname)) {
                $this->errors['shortname'] = get_string('errorbadroleshortname', 'core_role');
            }
        }
        if ($DB->record_exists_select('role', 'shortname = ? and id <> ?', array($this->role->shortname, $this->roleid))) {
            $this->errors['shortname'] = get_string('errorexistsroleshortname', 'core_role');
        }

        // Role name.
        $name = optional_param('name', null, PARAM_TEXT);
        if (!is_null($name)) {
            $this->role->name = $name;
            // Hack: short names of standard roles are equal to archetypes, empty name means localised via lang packs.
            $archetypes = get_role_archetypes();
            if (!isset($archetypes[$shortname]) and html_is_blank($this->role->name)) {
                $this->errors['name'] = get_string('errorbadrolename', 'core_role');
            }
        }
        if ($this->role->name !== '' and $DB->record_exists_select('role', 'name = ? and id <> ?', array($this->role->name, $this->roleid))) {
            $this->errors['name'] = get_string('errorexistsrolename', 'core_role');
        }

        // Description.
        $description = optional_param('description', null, PARAM_RAW);
        if (!is_null($description)) {
            $this->role->description = $description;
        }

        // Legacy type.
        $archetype = optional_param('archetype', null, PARAM_RAW);
        if (isset($archetype)) {
            $archetypes = get_role_archetypes();
            if (isset($archetypes[$archetype])) {
                $this->role->archetype = $archetype;
            } else {
                $this->role->archetype = '';
            }
        }

        // Assignable context levels.
        foreach ($this->allcontextlevels as $cl => $notused) {
            $assignable = optional_param('contextlevel' . $cl, null, PARAM_BOOL);
            if (!is_null($assignable)) {
                if ($assignable) {
                    $this->contextlevels[$cl] = $cl;
                } else {
                    unset($this->contextlevels[$cl]);
                }
            }
        }

        // Allowed roles.
        $allow = optional_param_array('allowassign', null, PARAM_INT);
        if (!is_null($allow)) {
            $this->allowassign = $allow;
        }
        $allow = optional_param_array('allowoverride', null, PARAM_INT);
        if (!is_null($allow)) {
            $this->allowoverride = $allow;
        }
        $allow = optional_param_array('allowswitch', null, PARAM_INT);
        if (!is_null($allow)) {
            $this->allowswitch = $allow;
        }

        // Now read the permissions for each capability.
        parent::read_submitted_permissions();
    }

    public function is_submission_valid() {
        return empty($this->errors);
    }

    /**
     * Call this after the table has been initialised,
     * this resets everything to that role.
     *
     * @param int $roleid role id or 0 for no role
     * @param array $options array with following keys:
     *      'name', 'shortname', 'description', 'permissions', 'archetype',
     *      'contextlevels', 'allowassign', 'allowoverride', 'allowswitch'
     */
    public function force_duplicate($roleid, array $options) {
        global $DB;

        if ($roleid == 0) {
            // This means reset to nothing == remove everything.

            if ($options['shortname']) {
                $this->role->shortname = '';
            }

            if ($options['name']) {
                $this->role->name = '';
            }

            if ($options['description']) {
                $this->role->description = '';
            }

            if ($options['archetype']) {
                $this->role->archetype = '';
            }

            if ($options['contextlevels']) {
                $this->contextlevels = array();
            }

            if ($options['allowassign']) {
                $this->allowassign = array();
            }
            if ($options['allowoverride']) {
                $this->allowoverride = array();
            }
            if ($options['allowswitch']) {
                $this->allowswitch = array();
            }

            if ($options['permissions']) {
                foreach ($this->capabilities as $capid => $cap) {
                    $this->permissions[$cap->name] = CAP_INHERIT;
                }
            }

            return;
        }

        $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

        if ($options['shortname']) {
            $this->role->shortname = $role->shortname;
        }

        if ($options['name']) {
            $this->role->name = $role->name;
        }

        if ($options['description']) {
            $this->role->description = $role->description;
        }

        if ($options['archetype']) {
            $this->role->archetype = $role->archetype;
        }

        if ($options['contextlevels']) {
            $this->contextlevels = array();
            $levels = get_role_contextlevels($roleid);
            foreach ($levels as $cl) {
                $this->contextlevels[$cl] = $cl;
            }
        }

        if ($options['allowassign']) {
            $this->allowassign = array_keys($this->get_allow_roles_list('assign', $roleid));
        }
        if ($options['allowoverride']) {
            $this->allowoverride = array_keys($this->get_allow_roles_list('override', $roleid));
        }
        if ($options['allowswitch']) {
            $this->allowswitch = array_keys($this->get_allow_roles_list('switch', $roleid));
        }

        if ($options['permissions']) {
            $this->permissions = $DB->get_records_menu('role_capabilities',
                array('roleid' => $roleid, 'contextid' => context_system::instance()->id),
                '', 'capability,permission');

            foreach ($this->capabilities as $capid => $cap) {
                if (!isset($this->permissions[$cap->name])) {
                    $this->permissions[$cap->name] = CAP_INHERIT;
                }
            }
        }
    }

    /**
     * Change the role definition to match given archetype.
     *
     * @param string $archetype
     * @param array $options array with following keys:
     *      'name', 'shortname', 'description', 'permissions', 'archetype',
     *      'contextlevels', 'allowassign', 'allowoverride', 'allowswitch'
     */
    public function force_archetype($archetype, array $options) {
        $archetypes = get_role_archetypes();
        if (!isset($archetypes[$archetype])) {
            throw new coding_exception('Unknown archetype: '.$archetype);
        }

        if ($options['shortname']) {
            $this->role->shortname = '';
        }

        if ($options['name']) {
            $this->role->name = '';
        }

        if ($options['description']) {
            $this->role->description = '';
        }

        if ($options['archetype']) {
            $this->role->archetype = $archetype;
        }

        if ($options['contextlevels']) {
            $this->contextlevels = array();
            $defaults = get_default_contextlevels($archetype);
            foreach ($defaults as $cl) {
                $this->contextlevels[$cl] = $cl;
            }
        }

        if ($options['allowassign']) {
            $this->allowassign = get_default_role_archetype_allows('assign', $archetype);
        }
        if ($options['allowoverride']) {
            $this->allowoverride = get_default_role_archetype_allows('override', $archetype);
        }
        if ($options['allowswitch']) {
            $this->allowswitch = get_default_role_archetype_allows('switch', $archetype);
        }

        if ($options['permissions']) {
            $defaultpermissions = get_default_capabilities($archetype);
            foreach ($this->permissions as $k => $v) {
                if (isset($defaultpermissions[$k])) {
                    $this->permissions[$k] = $defaultpermissions[$k];
                    continue;
                }
                $this->permissions[$k] = CAP_INHERIT;
            }
        }
    }

    /**
     * Change the role definition to match given preset.
     *
     * @param string $xml
     * @param array $options array with following keys:
     *      'name', 'shortname', 'description', 'permissions', 'archetype',
     *      'contextlevels', 'allowassign', 'allowoverride', 'allowswitch'
     */
    public function force_preset($xml, array $options) {
        if (!$info = core_role_preset::parse_preset($xml)) {
            throw new coding_exception('Invalid role preset');
        }

        if ($options['shortname']) {
            if (isset($info['shortname'])) {
                $this->role->shortname = $info['shortname'];
            }
        }

        if ($options['name']) {
            if (isset($info['name'])) {
                $this->role->name = $info['name'];
            }
        }

        if ($options['description']) {
            if (isset($info['description'])) {
                $this->role->description = $info['description'];
            }
        }

        if ($options['archetype']) {
            if (isset($info['archetype'])) {
                $this->role->archetype = $info['archetype'];
            }
        }

        if ($options['contextlevels']) {
            if (isset($info['contextlevels'])) {
                $this->contextlevels = $info['contextlevels'];
            }
        }

        foreach (array('assign', 'override', 'switch') as $type) {
            if ($options['allow'.$type]) {
                if (isset($info['allow'.$type])) {
                    $this->{'allow'.$type} = $info['allow'.$type];
                }
            }
        }

        if ($options['permissions']) {
            foreach ($this->permissions as $k => $v) {
                // Note: do not set everything else to CAP_INHERIT here
                //       because the xml file might not contain all capabilities.
                if (isset($info['permissions'][$k])) {
                    $this->permissions[$k] = $info['permissions'][$k];
                }
            }
        }
    }

    public function get_role_name() {
        return $this->role->name;
    }

    public function get_role_id() {
        return $this->role->id;
    }

    public function get_archetype() {
        return $this->role->archetype;
    }

    protected function load_parent_permissions() {
        $this->parentpermissions = get_default_capabilities($this->role->archetype);
    }

    public function save_changes() {
        global $DB;

        if (!$this->roleid) {
            // Creating role.
            $this->role->id = create_role($this->role->name, $this->role->shortname, $this->role->description, $this->role->archetype);
            $this->roleid = $this->role->id; // Needed to make the parent::save_changes(); call work.
        } else {
            // Updating role.
            $DB->update_record('role', $this->role);
        }

        // Assignable contexts.
        set_role_contextlevels($this->role->id, $this->contextlevels);

        // Set allowed roles.
        $this->save_allow('assign');
        $this->save_allow('override');
        $this->save_allow('switch');

        // Permissions.
        parent::save_changes();
    }

    protected function save_allow($type) {
        global $DB;

        $current = array_keys($this->get_allow_roles_list($type));
        $wanted = $this->{'allow'.$type};

        $addfunction = 'allow_'.$type;
        $deltable = 'role_allow_'.$type;
        $field = 'allow'.$type;

        foreach ($current as $roleid) {
            if (!in_array($roleid, $wanted)) {
                $DB->delete_records($deltable, array('roleid'=>$this->roleid, $field=>$roleid));
                continue;
            }
            $key = array_search($roleid, $wanted);
            unset($wanted[$key]);
        }

        foreach ($wanted as $roleid) {
            if ($roleid == -1) {
                $roleid = $this->roleid;
            }
            $addfunction($this->roleid, $roleid);
        }
    }

    protected function get_name_field($id) {
        return '<input type="text" id="' . $id . '" name="' . $id . '" maxlength="254" value="' . s($this->role->name) . '" />';
    }

    protected function get_shortname_field($id) {
        return '<input type="text" id="' . $id . '" name="' . $id . '" maxlength="254" value="' . s($this->role->shortname) . '" />';
    }

    protected function get_description_field($id) {
        return '<textarea class="form-textarea" id="'. s($id) .'" name="description" rows="10" cols="50">' .
            htmlspecialchars($this->role->description) .
            '</textarea>';
    }

    protected function get_archetype_field($id) {
        $options = array();
        $options[''] = get_string('none');
        foreach (get_role_archetypes() as $type) {
            $options[$type] = get_string('archetype'.$type, 'role');
        }
        return html_writer::select($options, 'archetype', $this->role->archetype, false);
    }

    protected function get_assignable_levels_control() {
        $output = '';
        foreach ($this->allcontextlevels as $cl => $clname) {
            $extraarguments = $this->disabled;
            if (in_array($cl, $this->contextlevels)) {
                $extraarguments .= 'checked="checked" ';
            }
            if (!$this->disabled) {
                $output .= '<input type="hidden" name="contextlevel' . $cl . '" value="0" />';
            }
            $output .= '<input type="checkbox" id="cl' . $cl . '" name="contextlevel' . $cl .
                '" value="1" ' . $extraarguments . '/> ';
            $output .= '<label for="cl' . $cl . '">' . $clname . "</label><br />\n";
        }
        return $output;
    }

    /**
     * Returns an array of roles of the allowed type.
     *
     * @param string $type Must be one of: assign, switch, or override.
     * @param int $roleid (null means current role)
     * @return array
     */
    protected function get_allow_roles_list($type, $roleid = null) {
        global $DB;

        if ($type !== 'assign' and $type !== 'switch' and $type !== 'override') {
            debugging('Invalid role allowed type specified', DEBUG_DEVELOPER);
            return array();
        }

        if ($roleid === null) {
            $roleid = $this->roleid;
        }

        if (empty($roleid)) {
            return array();
        }

        $sql = "SELECT r.*
                  FROM {role} r
                  JOIN {role_allow_{$type}} a ON a.allow{$type} = r.id
                 WHERE a.roleid = :roleid
              ORDER BY r.sortorder ASC";
        return $DB->get_records_sql($sql, array('roleid'=>$roleid));
    }

    /**
     * Returns an array of roles with the allowed type.
     *
     * @param string $type Must be one of: assign, switch, or override.
     * @return array Am array of role names with the allowed type
     */
    protected function get_allow_role_control($type) {
        if ($type !== 'assign' and $type !== 'switch' and $type !== 'override') {
            debugging('Invalid role allowed type specified', DEBUG_DEVELOPER);
            return '';
        }

        $property = 'allow'.$type;
        $selected = $this->$property;

        $options = array();
        foreach (role_get_names(null, ROLENAME_ALIAS) as $role) {
            $options[$role->id] = $role->localname;
        }
        if ($this->roleid == 0) {
            $options[-1] = get_string('thisnewrole', 'core_role');
        }
        return html_writer::select($options, 'allow'.$type.'[]', $selected, false, array('multiple'=>'multiple', 'size'=>10));
    }

    /**
     * Returns information about the risks associated with a role.
     *
     * @return string
     */
    protected function get_role_risks_info() {
        return '';
    }

    protected function print_field($name, $caption, $field) {
        global $OUTPUT;
        // Attempt to generate HTML like formslib.
        echo '<div class="fitem">';
        echo '<div class="fitemtitle">';
        if ($name) {
            echo '<label for="' . $name . '">';
        }
        echo $caption;
        if ($name) {
            echo "</label>\n";
        }
        echo '</div>';
        if (isset($this->errors[$name])) {
            $extraclass = ' error';
        } else {
            $extraclass = '';
        }
        echo '<div class="felement' . $extraclass . '">';
        echo $field;
        if (isset($this->errors[$name])) {
            echo $OUTPUT->error_text($this->errors[$name]);
        }
        echo '</div>';
        echo '</div>';
    }

    protected function print_show_hide_advanced_button() {
        echo '<p class="definenotice">' . get_string('highlightedcellsshowdefault', 'core_role') . ' </p>';
        echo '<div class="advancedbutton">';
        echo '<input type="submit" name="toggleadvanced" value="' . get_string('hideadvanced', 'form') . '" />';
        echo '</div>';
    }

    public function display() {
        global $OUTPUT;
        // Extra fields at the top of the page.
        echo '<div class="topfields clearfix">';
        $this->print_field('shortname', get_string('roleshortname', 'core_role').'&nbsp;'.$OUTPUT->help_icon('roleshortname', 'core_role'), $this->get_shortname_field('shortname'));
        $this->print_field('name', get_string('customrolename', 'core_role').'&nbsp;'.$OUTPUT->help_icon('customrolename', 'core_role'), $this->get_name_field('name'));
        $this->print_field('edit-description', get_string('customroledescription', 'core_role').'&nbsp;'.$OUTPUT->help_icon('customroledescription', 'core_role'),
            $this->get_description_field('description'));
        $this->print_field('menuarchetype', get_string('archetype', 'core_role').'&nbsp;'.$OUTPUT->help_icon('archetype', 'core_role'), $this->get_archetype_field('archetype'));
        $this->print_field('', get_string('maybeassignedin', 'core_role'), $this->get_assignable_levels_control());
        $this->print_field('menuallowassign', get_string('allowassign', 'core_role'), $this->get_allow_role_control('assign'));
        $this->print_field('menuallowoverride', get_string('allowoverride', 'core_role'), $this->get_allow_role_control('override'));
        $this->print_field('menuallowswitch', get_string('allowswitch', 'core_role'), $this->get_allow_role_control('switch'));
        if ($risks = $this->get_role_risks_info()) {
            $this->print_field('', get_string('rolerisks', 'core_role'), $risks);
        }
        echo "</div>";

        $this->print_show_hide_advanced_button();

        // Now the permissions table.
        parent::display();
    }

    protected function add_permission_cells($capability) {
        // One cell for each possible permission.
        foreach ($this->displaypermissions as $perm => $permname) {
            $strperm = $this->strperms[$permname];
            $extraclass = '';
            if ($perm == $this->parentpermissions[$capability->name]) {
                $extraclass = ' capdefault';
            }
            $checked = '';
            if ($this->permissions[$capability->name] == $perm) {
                $checked = 'checked="checked" ';
            }
            echo '<td class="' . $permname . $extraclass . '">';
            echo '<label><input type="radio" name="' . $capability->name .
                '" value="' . $perm . '" ' . $checked . '/> ';
            echo '<span class="note">' . $strperm . '</span>';
            echo '</label></td>';
        }
    }
}
