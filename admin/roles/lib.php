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
 * Library code used by the roles administration interfaces.
 *
 * Responds to actions:
 *   add       - add a new role
 *   duplicate - like add, only initialise the new role by using an existing one.
 *   edit      - edit the definition of a role
 *   view      - view the definition of a role
 *
 * @package    core
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/selector/lib.php');

// Classes for producing tables with one row per capability ====================

/**
 * This class represents a table with one row for each of a list of capabilities
 * where the first cell in the row contains the capability name, and there is
 * arbitrary stuff in the rest of the row. This class is used by
 * admin/roles/manage.php, override.php and check.php.
 *
 * An ajaxy search UI shown at the top, if JavaScript is on.
 */
abstract class capability_table_base {
    /** The context this table relates to. */
    protected $context;

    /** The capabilities to display. Initialised as fetch_context_capabilities($context). */
    protected $capabilities = array();

    /** Added as an id="" attribute to the table on output. */
    protected $id;

    /** Added to the class="" attribute on output. */
    protected $classes = array('rolecap');

    /** Default number of capabilities in the table for the search UI to be shown. */
    const NUM_CAPS_FOR_SEARCH = 12;

    /**
     * Constructor
     * @param object $context the context this table relates to.
     * @param string $id what to put in the id="" attribute.
     */
    public function __construct($context, $id) {
        $this->context = $context;
        $this->capabilities = fetch_context_capabilities($context);
        $this->id = $id;
    }

    /**
     * Use this to add class="" attributes to the table. You get the rolecap by
     * default.
     * @param array $classnames of class names.
     */
    public function add_classes($classnames) {
        $this->classes = array_unique(array_merge($this->classes, $classnames));
    }

    /**
     * Display the table.
     */
    public function display() {
        if (count($this->capabilities) > capability_table_base::NUM_CAPS_FOR_SEARCH) {
            global $PAGE;
            $PAGE->requires->strings_for_js(array('filter','clear'),'moodle');
            $PAGE->requires->js_init_call('M.core_role.init_cap_table_filter', array($this->id, $this->context->id));
        }
        echo '<table class="' . implode(' ', $this->classes) . '" id="' . $this->id . '">' . "\n<thead>\n";
        echo '<tr><th class="name" align="left" scope="col">' . get_string('capability','role') . '</th>';
        $this->add_header_cells();
        echo "</tr>\n</thead>\n<tbody>\n";

    /// Loop over capabilities.
        $contextlevel = 0;
        $component = '';
        foreach ($this->capabilities as $capability) {
            if ($this->skip_row($capability)) {
                continue;
            }

        /// Prints a breaker if component or name or context level has changed
            if (component_level_changed($capability, $component, $contextlevel)) {
                $this->print_heading_row($capability);
            }
            $contextlevel = $capability->contextlevel;
            $component = $capability->component;

        /// Start the row.
            echo '<tr class="' . implode(' ', array_unique(array_merge(array('rolecap'),
                    $this->get_row_classes($capability)))) . '">';

        /// Table cell for the capability name.
            echo '<th scope="row" class="name"><span class="cap-desc">' . get_capability_docs_link($capability) .
                    '<span class="cap-name">' . $capability->name . '</span></span></th>';

        /// Add the cells specific to this table.
            $this->add_row_cells($capability);

        /// End the row.
            echo "</tr>\n";
        }

    /// End of the table.
        echo "</tbody>\n</table>\n";
    }

    /**
     * Used to output a heading rows when the context level or component changes.
     * @param object $capability gives the new component and contextlevel.
     */
    protected function print_heading_row($capability) {
        echo '<tr class="rolecapheading header"><td colspan="' . (1 + $this->num_extra_columns()) . '" class="header"><strong>' .
                get_component_string($capability->component, $capability->contextlevel) .
                '</strong></td></tr>';

    }

    /** For subclasses to override, output header cells, after the initial capability one. */
    protected abstract function add_header_cells();

    /** For subclasses to override, return the number of cells that add_header_cells/add_row_cells output. */
    protected abstract function num_extra_columns();

    /**
     * For subclasses to override. Allows certain capabilties
     * to be left out of the table.
     *
     * @param object $capability the capability this row relates to.
     * @return boolean. If true, this row is omitted from the table.
     */
    protected function skip_row($capability) {
        return false;
    }

    /**
     * For subclasses to override. A change to reaturn class names that are added
     * to the class="" attribute on the &lt;tr> for this capability.
     *
     * @param object $capability the capability this row relates to.
     * @return array of class name strings.
     */
    protected function get_row_classes($capability) {
        return array();
    }

    /**
     * For subclasses to override. Output the data cells for this capability. The
     * capability name cell will already have been output.
     *
     * You can rely on get_row_classes always being called before add_row_cells.
     *
     * @param object $capability the capability this row relates to.
     */
    protected abstract function add_row_cells($capability);
}

/**
 * Subclass of capability_table_base for use on the Check permissions page.
 *
 * We have one additional column, Allowed, which contains yes/no.
 */
class check_capability_table extends capability_table_base {
    protected $user;
    protected $fullname;
    protected $contextname;
    protected $stryes;
    protected $strno;
    private $hascap;

    /**
     * Constructor
     * @param object $context the context this table relates to.
     * @param object $user the user we are generating the results for.
     * @param string $contextname print_context_name($context) - to save recomputing.
     */
    public function __construct($context, $user, $contextname) {
        global $CFG;
        parent::__construct($context, 'explaincaps');
        $this->user = $user;
        $this->fullname = fullname($user);
        $this->contextname = $contextname;
        $this->stryes = get_string('yes');
        $this->strno = get_string('no');
    }

    protected function add_header_cells() {
        echo '<th>' . get_string('allowed', 'role') . '</th>';
    }

    protected function num_extra_columns() {
        return 1;
    }

    protected function get_row_classes($capability) {
        $this->hascap = has_capability($capability->name, $this->context, $this->user->id);
        if ($this->hascap) {
            return array('yes');
        } else {
            return array('no');
        }
    }

    protected function add_row_cells($capability) {
        global $OUTPUT;
        if ($this->hascap) {
            $result = $this->stryes;
        } else {
            $result = $this->strno;
        }
        $a = new stdClass;
        $a->fullname = $this->fullname;
        $a->capability = $capability->name;
        $a->context = $this->contextname;
        echo '<td>' . $result . '</td>';
    }
}


/**
 * Subclass of capability_table_base for use on the Permissions page.
 */
class permissions_table extends capability_table_base {
    protected $contextname;
    protected $allowoverrides;
    protected $allowsafeoverrides;
    protected $overridableroles;
    protected $roles;
    protected $icons = array();

    /**
     * Constructor
     * @param object $context the context this table relates to.
     * @param string $contextname print_context_name($context) - to save recomputing.
     */
    public function __construct($context, $contextname, $allowoverrides, $allowsafeoverrides, $overridableroles) {
        global $DB;

        parent::__construct($context, 'permissions');
        $this->contextname = $contextname;
        $this->allowoverrides = $allowoverrides;
        $this->allowsafeoverrides = $allowsafeoverrides;
        $this->overridableroles = $overridableroles;

        $roles = $DB->get_records('role', null, 'sortorder DESC');
        foreach ($roles as $roleid=>$role) {
            $roles[$roleid] = $role->name;
        }
        $this->roles = role_fix_names($roles, $context);

    }

    protected function add_header_cells() {
        echo '<th>' . get_string('risks', 'role') . '</th>';
        echo '<th>' . get_string('neededroles', 'role') . '</th>';
        echo '<th>' . get_string('prohibitedroles', 'role') . '</th>';
    }

    protected function num_extra_columns() {
        return 3;
    }

    protected function add_row_cells($capability) {
        global $OUTPUT, $PAGE;

        $context = $this->context;
        $contextid = $this->context->id;
        $allowoverrides = $this->allowoverrides;
        $allowsafeoverrides = $this->allowsafeoverrides;
        $overridableroles = $this->overridableroles;
        $roles = $this->roles;


        list($needed, $forbidden) = get_roles_with_cap_in_context($context, $capability->name);
        $neededroles    = array();
        $forbiddenroles = array();
        $allowable      = $overridableroles;
        $forbitable     = $overridableroles;
        foreach ($neededroles as $id=>$unused) {
            unset($allowable[$id]);
        }
        foreach ($forbidden as $id=>$unused) {
            unset($allowable[$id]);
            unset($forbitable[$id]);
        }

        foreach ($roles as $id=>$name) {
            if (isset($needed[$id])) {
                $neededroles[$id] = $roles[$id];
                if (isset($overridableroles[$id]) and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
                    $preventurl = new moodle_url($PAGE->url, array('contextid'=>$contextid, 'roleid'=>$id, 'capability'=>$capability->name, 'prevent'=>1));
                    $neededroles[$id] .= $OUTPUT->action_icon($preventurl, new pix_icon('t/delete', get_string('prevent', 'role')));
                }
            }
        }
        $neededroles = implode(', ', $neededroles);
        foreach ($roles as $id=>$name) {
            if (isset($forbidden[$id])  and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
                $forbiddenroles[$id] = $roles[$id];
                if (isset($overridableroles[$id]) and prohibit_is_removable($id, $context, $capability->name)) {
                    $unprohibiturl = new moodle_url($PAGE->url, array('contextid'=>$contextid, 'roleid'=>$id, 'capability'=>$capability->name, 'unprohibit'=>1));
                    $forbiddenroles[$id] .= $OUTPUT->action_icon($unprohibiturl, new pix_icon('t/delete', get_string('delete')));
                }
            }
        }
        $forbiddenroles = implode(', ', $forbiddenroles);

        if ($allowable and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
            $allowurl = new moodle_url($PAGE->url, array('contextid'=>$contextid, 'capability'=>$capability->name, 'allow'=>1));
            $neededroles .= '<div class="allowmore">'.$OUTPUT->action_icon($allowurl, new pix_icon('t/add', get_string('allow', 'role'))).'</div>';
        }

        if ($forbitable and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
            $prohibiturl = new moodle_url($PAGE->url, array('contextid'=>$contextid, 'capability'=>$capability->name, 'prohibit'=>1));
            $forbiddenroles .= '<div class="prohibitmore">'.$OUTPUT->action_icon($prohibiturl, new pix_icon('t/add', get_string('prohibit', 'role'))).'</div>';
        }

        $risks = $this->get_risks($capability);

        echo '<td>' . $risks . '</td>';
        echo '<td>' . $neededroles . '</td>';
        echo '<td>' . $forbiddenroles . '</td>';
    }

    protected function get_risks($capability) {
        global $OUTPUT;

        $allrisks = get_all_risks();
        $risksurl = new moodle_url(get_docs_url(s(get_string('risks', 'role'))));

        $return = '';

        foreach ($allrisks as $type=>$risk) {
            if ($risk & (int)$capability->riskbitmask) {
                if (!isset($this->icons[$type])) {
                    $pixicon = new pix_icon('/i/' . str_replace('risk', 'risk_', $type), get_string($type . 'short', 'admin'));
                    $this->icons[$type] = $OUTPUT->action_icon($risksurl, $pixicon, new popup_action('click', $risksurl));
                }
                $return .= $this->icons[$type];
            }
        }

        return $return;
    }
}


/**
 * This subclass is the bases for both the define roles and override roles
 * pages. As well as adding the risks columns, this also provides generic
 * facilities for showing a certain number of permissions columns, and
 * recording the current and submitted permissions for each capability.
 */
abstract class capability_table_with_risks extends capability_table_base {
    protected $allrisks;
    protected $allpermissions; // We don't need perms ourselves, but all our subclasses do.
    protected $strperms; // Language string cache.
    protected $risksurl; // URL in moodledocs about risks.
    protected $riskicons = array(); // Cache to avoid regenerating the HTML for each risk icon.
    /** The capabilities to highlight as default/inherited. */
    protected $parentpermissions;
    protected $displaypermissions;
    protected $permissions;
    protected $changed;
    protected $roleid;

    public function __construct($context, $id, $roleid) {
        parent::__construct($context, $id);

        $this->allrisks = get_all_risks();
        $this->risksurl = get_docs_url(s(get_string('risks', 'role')));

        $this->allpermissions = array(
            CAP_INHERIT => 'inherit',
            CAP_ALLOW => 'allow',
            CAP_PREVENT => 'prevent' ,
            CAP_PROHIBIT => 'prohibit',
        );

        $this->strperms = array();
        foreach ($this->allpermissions as $permname) {
            $this->strperms[$permname] =  get_string($permname, 'role');
        }

        $this->roleid = $roleid;
        $this->load_current_permissions();

    /// Fill in any blank permissions with an explicit CAP_INHERIT, and init a locked field.
        foreach ($this->capabilities as $capid => $cap) {
            if (!isset($this->permissions[$cap->name])) {
                $this->permissions[$cap->name] = CAP_INHERIT;
            }
            $this->capabilities[$capid]->locked = false;
        }
    }

    protected function load_current_permissions() {
        global $DB;

    /// Load the overrides/definition in this context.
        if ($this->roleid) {
            $this->permissions = $DB->get_records_menu('role_capabilities', array('roleid' => $this->roleid,
                    'contextid' => $this->context->id), '', 'capability,permission');
        } else {
            $this->permissions = array();
        }
    }

    protected abstract function load_parent_permissions();

    /**
     * Update $this->permissions based on submitted data, while making a list of
     * changed capabilities in $this->changed.
     */
    public function read_submitted_permissions() {
        $this->changed = array();

        foreach ($this->capabilities as $cap) {
            if ($cap->locked || $this->skip_row($cap)) {
            /// The user is not allowed to change the permission for this capability
                continue;
            }

            $permission = optional_param($cap->name, null, PARAM_PERMISSION);
            if (is_null($permission)) {
            /// A permission was not specified in submitted data.
                continue;
            }

        /// If the permission has changed, update $this->permissions and
        /// Record the fact there is data to save.
            if ($this->permissions[$cap->name] != $permission) {
                $this->permissions[$cap->name] = $permission;
                $this->changed[] = $cap->name;
            }
        }
    }

    /**
     * Save the new values of any permissions that have been changed.
     */
    public function save_changes() {
    /// Set the permissions.
        foreach ($this->changed as $changedcap) {
            assign_capability($changedcap, $this->permissions[$changedcap],
                    $this->roleid, $this->context->id, true);
        }

    /// Force accessinfo refresh for users visiting this context.
        mark_context_dirty($this->context->path);
    }

    public function display() {
        $this->load_parent_permissions();
        foreach ($this->capabilities as $cap) {
            if (!isset($this->parentpermissions[$cap->name])) {
                $this->parentpermissions[$cap->name] = CAP_INHERIT;
            }
        }
        parent::display();
    }

    protected function add_header_cells() {
        global $OUTPUT;
        echo '<th colspan="' . count($this->displaypermissions) . '" scope="col">' .
                get_string('permission', 'role') . ' ' . $OUTPUT->help_icon('permission', 'role') . '</th>';
        echo '<th class="risk" colspan="' . count($this->allrisks) . '" scope="col">' . get_string('risks','role') . '</th>';
    }

    protected function num_extra_columns() {
        return count($this->displaypermissions) + count($this->allrisks);
    }

    protected function get_row_classes($capability) {
        $rowclasses = array();
        foreach ($this->allrisks as $riskname => $risk) {
            if ($risk & (int)$capability->riskbitmask) {
                $rowclasses[] = $riskname;
            }
        }
        return $rowclasses;
    }

    protected abstract function add_permission_cells($capability);

    protected function add_row_cells($capability) {
        $this->add_permission_cells($capability);
    /// One cell for each possible risk.
        foreach ($this->allrisks as $riskname => $risk) {
            echo '<td class="risk ' . str_replace('risk', '', $riskname) . '">';
            if ($risk & (int)$capability->riskbitmask) {
                echo $this->get_risk_icon($riskname);
            }
            echo '</td>';
        }
    }

    /**
     * Print a risk icon, as a link to the Risks page on Moodle Docs.
     *
     * @param string $type the type of risk, will be one of the keys from the
     *      get_all_risks array. Must start with 'risk'.
     */
    function get_risk_icon($type) {
        global $OUTPUT;
        if (!isset($this->riskicons[$type])) {
            $iconurl = $OUTPUT->pix_url('i/' . str_replace('risk', 'risk_', $type));
            $text = '<img src="' . $iconurl . '" alt="' . get_string($type . 'short', 'admin') . '" />';
            $action = new popup_action('click', $this->risksurl, 'docspopup');
            $this->riskicons[$type] = $OUTPUT->action_link($this->risksurl, $text, $action, array('title'=>get_string($type, 'admin')));
        }
        return $this->riskicons[$type];
    }
}

/**
 * As well as tracking the permissions information about the role we are creating
 * or editing, we also track the other information about the role. (This class is
 * starting to be more and more like a formslib form in some respects.)
 */
class define_role_table_advanced extends capability_table_with_risks {
    /** Used to store other information (besides permissions) about the role we are creating/editing. */
    protected $role;
    /** Used to store errors found when validating the data. */
    protected $errors;
    protected $contextlevels;
    protected $allcontextlevels;
    protected $disabled = '';

    public function __construct($context, $roleid) {
        $this->roleid = $roleid;
        parent::__construct($context, 'defineroletable', $roleid);
        $this->displaypermissions = $this->allpermissions;
        $this->strperms[$this->allpermissions[CAP_INHERIT]] = get_string('notset', 'role');

        $this->allcontextlevels = array(
            CONTEXT_SYSTEM => get_string('coresystem'),
            CONTEXT_USER => get_string('user'),
            CONTEXT_COURSECAT => get_string('category'),
            CONTEXT_COURSE => get_string('course'),
            CONTEXT_MODULE => get_string('activitymodule'),
            CONTEXT_BLOCK => get_string('block')
        );
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
        } else {
            $this->role = new stdClass;
            $this->role->name = '';
            $this->role->shortname = '';
            $this->role->description = '';
            $this->role->archetype = '';
            $this->contextlevels = array();
        }
        parent::load_current_permissions();
    }

    public function read_submitted_permissions() {
        global $DB;
        $this->errors = array();

        // Role name.
        $name = optional_param('name', null, PARAM_MULTILANG);
        if (!is_null($name)) {
            $this->role->name = $name;
            if (html_is_blank($this->role->name)) {
                $this->errors['name'] = get_string('errorbadrolename', 'role');
            }
        }
        if ($DB->record_exists_select('role', 'name = ? and id <> ?', array($this->role->name, $this->roleid))) {
            $this->errors['name'] = get_string('errorexistsrolename', 'role');
        }

        // Role short name. We clean this in a special way. We want to end up
        // with only lowercase safe ASCII characters.
        $shortname = optional_param('shortname', null, PARAM_RAW);
        if (!is_null($shortname)) {
            $this->role->shortname = $shortname;
            $this->role->shortname = textlib_get_instance()->specialtoascii($this->role->shortname);
            $this->role->shortname = moodle_strtolower(clean_param($this->role->shortname, PARAM_ALPHANUMEXT));
            if (empty($this->role->shortname)) {
                $this->errors['shortname'] = get_string('errorbadroleshortname', 'role');
            }
        }
        if ($DB->record_exists_select('role', 'shortname = ? and id <> ?', array($this->role->shortname, $this->roleid))) {
            $this->errors['shortname'] = get_string('errorexistsroleshortname', 'role');
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
            if (isset($archetypes[$archetype])){
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

        // Now read the permissions for each capability.
        parent::read_submitted_permissions();
    }

    public function is_submission_valid() {
        return empty($this->errors);
    }

    /**
     * Call this after the table has been initialised, so to indicate that
     * when save is called, we want to make a duplicate role.
     */
    public function make_copy() {
        $this->roleid = 0;
        unset($this->role->id);
        $this->role->name .= ' ' . get_string('copyasnoun');
        $this->role->shortname .= 'copy';
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
            // Creating role
            $this->role->id = create_role($this->role->name, $this->role->shortname, $this->role->description, $this->role->archetype);
            $this->roleid = $this->role->id; // Needed to make the parent::save_changes(); call work.
        } else {
            // Updating role
            $DB->update_record('role', $this->role);
        }

        // Assignable contexts.
        set_role_contextlevels($this->role->id, $this->contextlevels);

        // Permissions.
        parent::save_changes();
    }

    protected function get_name_field($id) {
        return '<input type="text" id="' . $id . '" name="' . $id . '" maxlength="254" value="' . s($this->role->name) . '" />';
    }

    protected function get_shortname_field($id) {
        return '<input type="text" id="' . $id . '" name="' . $id . '" maxlength="254" value="' . s($this->role->shortname) . '" />';
    }

    protected function get_description_field($id) {
        return print_textarea(true, 10, 50, 50, 10, 'description', $this->role->description, 0, true);
    }

    protected function get_archetype_field($id) {
        $options = array();
        $options[''] = get_string('none');
        foreach(get_role_archetypes() as $type) {
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
        if (isset($this->errors[$name])) {
            echo $OUTPUT->error_text($this->errors[$name]);
        }
        echo $field;
        echo '</div>';
        echo '</div>';
    }

    protected function print_show_hide_advanced_button() {
        echo '<p class="definenotice">' . get_string('highlightedcellsshowdefault', 'role') . ' </p>';
        echo '<div class="advancedbutton">';
        echo '<input type="submit" name="toggleadvanced" value="' . get_string('hideadvanced', 'form') . '" />';
        echo '</div>';
    }

    public function display() {
        global $OUTPUT;
        // Extra fields at the top of the page.
        echo '<div class="topfields clearfix">';
        $this->print_field('name', get_string('rolefullname', 'role'), $this->get_name_field('name'));
        $this->print_field('shortname', get_string('roleshortname', 'role'), $this->get_shortname_field('shortname'));
        $this->print_field('edit-description', get_string('description'), $this->get_description_field('description'));
        $this->print_field('menuarchetype', get_string('archetype', 'role').'&nbsp;'.$OUTPUT->help_icon('archetype', 'role'), $this->get_archetype_field('archetype'));
        $this->print_field('', get_string('maybeassignedin', 'role'), $this->get_assignable_levels_control());
        echo "</div>";

        $this->print_show_hide_advanced_button();

        // Now the permissions table.
        parent::display();
    }

    protected function add_permission_cells($capability) {
    /// One cell for each possible permission.
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

class define_role_table_basic extends define_role_table_advanced {
    protected $stradvmessage;
    protected $strallow;

    public function __construct($context, $roleid) {
        parent::__construct($context, $roleid);
        $this->displaypermissions = array(CAP_ALLOW => $this->allpermissions[CAP_ALLOW]);
        $this->stradvmessage = get_string('useshowadvancedtochange', 'role');
        $this->strallow = $this->strperms[$this->allpermissions[CAP_ALLOW]];
    }

    protected function print_show_hide_advanced_button() {
        echo '<div class="advancedbutton">';
        echo '<input type="submit" name="toggleadvanced" value="' . get_string('showadvanced', 'form') . '" />';
        echo '</div>';
    }

    protected function add_permission_cells($capability) {
        $perm = $this->permissions[$capability->name];
        $permname = $this->allpermissions[$perm];
        $defaultperm = $this->allpermissions[$this->parentpermissions[$capability->name]];
        echo '<td class="' . $permname . '">';
        if ($perm == CAP_ALLOW || $perm == CAP_INHERIT) {
            $checked = '';
            if ($perm == CAP_ALLOW) {
                $checked = 'checked="checked" ';
            }
            echo '<input type="hidden" name="' . $capability->name . '" value="' . CAP_INHERIT . '" />';
            echo '<label><input type="checkbox" name="' . $capability->name .
                    '" value="' . CAP_ALLOW . '" ' . $checked . '/> ' . $this->strallow . '</label>';
        } else {
            echo '<input type="hidden" name="' . $capability->name . '" value="' . $perm . '" />';
            echo $this->strperms[$permname] . '<span class="note">' . $this->stradvmessage . '</span>';
        }
        echo '</td>';
    }
}
class view_role_definition_table extends define_role_table_advanced {
    public function __construct($context, $roleid) {
        parent::__construct($context, $roleid);
        $this->displaypermissions = array(CAP_ALLOW => $this->allpermissions[CAP_ALLOW]);
        $this->disabled = 'disabled="disabled" ';
    }

    public function save_changes() {
        throw new moodle_exception('invalidaccess');
    }

    protected function get_name_field($id) {
        return strip_tags(format_string($this->role->name));
    }

    protected function get_shortname_field($id) {
        return $this->role->shortname;
    }

    protected function get_description_field($id) {
        return format_text($this->role->description, FORMAT_HTML);
    }

    protected function get_archetype_field($id) {
        if (empty($this->role->archetype)) {
            return get_string('none');
        } else {
            return get_string('archetype'.$this->role->archetype, 'role');
        }
    }

    protected function print_show_hide_advanced_button() {
        // Do nothing.
    }

    protected function add_permission_cells($capability) {
        $perm = $this->permissions[$capability->name];
        $permname = $this->allpermissions[$perm];
        $defaultperm = $this->allpermissions[$this->parentpermissions[$capability->name]];
        if ($permname != $defaultperm) {
            $default = get_string('defaultx', 'role', $this->strperms[$defaultperm]);
        } else {
            $default = "&#xa0;";
        }
        echo '<td class="' . $permname . '">' . $this->strperms[$permname] . '<span class="note">' .
                $default . '</span></td>';

    }
}

class override_permissions_table_advanced extends capability_table_with_risks {
    protected $strnotset;
    protected $haslockedcapabilities = false;

    /**
     * Constructor
     *
     * This method loads loads all the information about the current state of
     * the overrides, then updates that based on any submitted data. It also
     * works out which capabilities should be locked for this user.
     *
     * @param object $context the context this table relates to.
     * @param integer $roleid the role being overridden.
     * @param boolean $safeoverridesonly If true, the user is only allowed to override
     *      capabilities with no risks.
     */
    public function __construct($context, $roleid, $safeoverridesonly) {
        parent::__construct($context, 'overriderolestable', $roleid);
        $this->displaypermissions = $this->allpermissions;
        $this->strnotset = get_string('notset', 'role');

    /// Determine which capabilities should be locked.
        if ($safeoverridesonly) {
            foreach ($this->capabilities as $capid => $cap) {
                if (!is_safe_capability($cap)) {
                    $this->capabilities[$capid]->locked = true;
                    $this->haslockedcapabilities = true;
                }
            }
        }
    }

    protected function load_parent_permissions() {
        global $DB;

    /// Get the capabilities from the parent context, so that can be shown in the interface.
        $parentcontext = get_context_instance_by_id(get_parent_contextid($this->context));
        $this->parentpermissions = role_context_capabilities($this->roleid, $parentcontext);
    }

    public function has_locked_capabilities() {
        return $this->haslockedcapabilities;
    }

    protected function add_permission_cells($capability) {
        $disabled = '';
        if ($capability->locked || $this->parentpermissions[$capability->name] == CAP_PROHIBIT) {
            $disabled = ' disabled="disabled"';
        }

    /// One cell for each possible permission.
        foreach ($this->displaypermissions as $perm => $permname) {
            $strperm = $this->strperms[$permname];
            $extraclass = '';
            if ($perm != CAP_INHERIT && $perm == $this->parentpermissions[$capability->name]) {
                $extraclass = ' capcurrent';
            }
            $checked = '';
            if ($this->permissions[$capability->name] == $perm) {
                $checked = 'checked="checked" ';
            }
            echo '<td class="' . $permname . $extraclass . '">';
            echo '<label><input type="radio" name="' . $capability->name .
                    '" value="' . $perm . '" ' . $checked . $disabled . '/> ';
            if ($perm == CAP_INHERIT) {
                $inherited = $this->parentpermissions[$capability->name];
                if ($inherited == CAP_INHERIT) {
                    $inherited = $this->strnotset;
                } else {
                    $inherited = $this->strperms[$this->allpermissions[$inherited]];
                }
                $strperm .= ' (' . $inherited . ')';
            }
            echo '<span class="note">' . $strperm . '</span>';
            echo '</label></td>';
        }
    }
}

// User selectors for managing role assignments ================================

/**
 * Base class to avoid duplicating code.
 */
abstract class role_assign_user_selector_base extends user_selector_base {
    const MAX_USERS_PER_PAGE = 100;

    protected $roleid;
    protected $context;

    /**
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct($name, $options) {
        global $CFG;
        parent::__construct($name, $options);
        $this->roleid = $options['roleid'];
        if (isset($options['context'])) {
            $this->context = $options['context'];
        } else {
            $this->context = get_context_instance_by_id($options['contextid']);
        }
        require_once($CFG->dirroot . '/group/lib.php');
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        $options['roleid'] = $this->roleid;
        $options['contextid'] = $this->context->id;
        return $options;
    }
}

/**
 * User selector subclass for the list of potential users on the assign roles page,
 * when we are assigning in a context below the course level. (CONTEXT_MODULE and
 * some CONTEXT_BLOCK).
 *
 * This returns only enrolled users in this context.
 */
class potential_assignees_below_course extends role_assign_user_selector_base {
    public function find_users($search) {
        global $DB;

        list($enrolsql, $eparams) = get_enrolled_sql($this->context);

        // Now we have to go to the database.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params = array_merge($params, $eparams);

        if ($wherecondition) {
            $wherecondition = ' AND ' . $wherecondition;
        }

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(u.id)';

        $sql   = " FROM {user} u
                  WHERE u.id IN ($enrolsql) $wherecondition
                        AND u.id NOT IN (
                           SELECT u.id
                             FROM {role_assignments} r, {user} u
                            WHERE r.contextid = :contextid
                                  AND u.id = r.userid
                                  AND r.roleid = :roleid)";
        $order = ' ORDER BY lastname ASC, firstname ASC';

        $params['contextid'] = $this->context->id;
        $params['roleid'] = $this->roleid;

        // Check to see if there are too many to show sensibly.
        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > role_assign_user_selector_base::MAX_USERS_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        // If not, show them.
        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'role', $search);
        } else {
            $groupname = get_string('potusers', 'role');
        }

        return array($groupname => $availableusers);
    }
}

/**
 * User selector subclass for the list of potential users on the assign roles page,
 * when we are assigning in a context at or above the course level. In this case we
 * show all the users in the system who do not already have the role.
 */
class potential_assignees_course_and_above extends role_assign_user_selector_base {
    public function find_users($search) {
        global $DB;

        list($wherecondition, $params) = $this->search_sql($search, '');

        $fields      = 'SELECT ' . $this->required_fields_sql('');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user}
                WHERE $wherecondition
                      AND id NOT IN (
                         SELECT u.id
                           FROM {role_assignments} r, {user} u
                          WHERE r.contextid = :contextid
                                AND u.id = r.userid
                                AND r.roleid = :roleid)";
        $order = ' ORDER BY lastname ASC, firstname ASC';

        $params['contextid'] = $this->context->id;
        $params['roleid'] = $this->roleid;

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > role_assign_user_selector_base::MAX_USERS_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'role', $search);
        } else {
            $groupname = get_string('potusers', 'role');
        }

        return array($groupname => $availableusers);
    }
}

/**
 * User selector subclass for the list of users who already have the role in
 * question on the assign roles page.
 */
class existing_role_holders extends role_assign_user_selector_base {

    public function __construct($name, $options) {
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $DB;

        list($wherecondition, $params) = $this->search_sql($search, 'u');
        list($ctxcondition, $ctxparams) = $DB->get_in_or_equal(get_parent_contexts($this->context, true), SQL_PARAMS_NAMED, 'ctx00');
        $params = array_merge($params, $ctxparams);
        $params['roleid'] = $this->roleid;

        $sql = "SELECT ra.id as raid," . $this->required_fields_sql('u') . ",ra.contextid,ra.component
                FROM {role_assignments} ra
                JOIN {user} u ON u.id = ra.userid
                JOIN {context} ctx ON ra.contextid = ctx.id
                WHERE
                    $wherecondition AND
                    ctx.id $ctxcondition AND
                    ra.roleid = :roleid
                ORDER BY ctx.depth DESC, ra.component, u.lastname, u.firstname";
        $contextusers = $DB->get_records_sql($sql, $params);

        // No users at all.
        if (empty($contextusers)) {
            return array();
        }

        // We have users. Out put them in groups by context depth.
        // To help the loop below, tack a dummy user on the end of the results
        // array, to trigger output of the last group.
        $dummyuser = new stdClass;
        $dummyuser->contextid = 0;
        $dummyuser->id = 0;
        $dummyuser->component = '';
        $contextusers[] = $dummyuser;
        $results = array(); // The results array we are building up.
        $doneusers = array(); // Ensures we only list each user at most once.
        $currentcontextid = $this->context->id;
        $currentgroup = array();
        foreach ($contextusers as $user) {
            if (isset($doneusers[$user->id])) {
                continue;
            }
            $doneusers[$user->id] = 1;
            if ($user->contextid != $currentcontextid) {
                // We have got to the end of the previous group. Add it to the results array.
                if ($currentcontextid == $this->context->id) {
                    $groupname = $this->this_con_group_name($search, count($currentgroup));
                } else {
                    $groupname = $this->parent_con_group_name($search, $currentcontextid);
                }
                $results[$groupname] = $currentgroup;
                // Get ready for the next group.
                $currentcontextid = $user->contextid;
                $currentgroup = array();
            }
            // Add this user to the group we are building up.
            unset($user->contextid);
            if ($currentcontextid != $this->context->id) {
                $user->disabled = true;
            }
            if ($user->component !== '') {
                // bad luck, you can tweak only manual role assignments
                $user->disabled = true;
            }
            unset($user->component);
            $currentgroup[$user->id] = $user;
        }

        return $results;
    }

    protected function this_con_group_name($search, $numusers) {
        if ($this->context->contextlevel == CONTEXT_SYSTEM) {
            // Special case in the System context.
            if ($search) {
                return get_string('extusersmatching', 'role', $search);
            } else {
                return get_string('extusers', 'role');
            }
        }
        $contexttype = get_contextlevel_name($this->context->contextlevel);
        if ($search) {
            $a = new stdClass;
            $a->search = $search;
            $a->contexttype = $contexttype;
            if ($numusers) {
                return get_string('usersinthisxmatching', 'role', $a);
            } else {
                return get_string('noneinthisxmatching', 'role', $a);
            }
        } else {
            if ($numusers) {
                return get_string('usersinthisx', 'role', $contexttype);
            } else {
                return get_string('noneinthisx', 'role', $contexttype);
            }
        }
    }

    protected function parent_con_group_name($search, $contextid) {
        $context = get_context_instance_by_id($contextid);
        $contextname = print_context_name($context, true, true);
        if ($search) {
            $a = new stdClass;
            $a->contextname = $contextname;
            $a->search = $search;
            return get_string('usersfrommatching', 'role', $a);
        } else {
            return get_string('usersfrom', 'role', $contextname);
        }
    }
}

/**
 * Base class for managing the data in the grid of checkboxes on the role allow
 * allow/overrides/switch editing pages (allow.php).
 */
abstract class role_allow_role_page {
    protected $tablename;
    protected $targetcolname;
    protected $roles;
    protected $allowed = null;

    /**
     * @param string $tablename the table where our data is stored.
     * @param string $targetcolname the name of the target role id column.
     */
    public function __construct($tablename, $targetcolname) {
        $this->tablename = $tablename;
        $this->targetcolname = $targetcolname;
        $this->load_required_roles();
    }

    /**
     * Load information about all the roles we will need information about.
     */
    protected function load_required_roles() {
    /// Get all roles
        $this->roles = get_all_roles();
        role_fix_names($this->roles, get_context_instance(CONTEXT_SYSTEM), ROLENAME_ORIGINAL);
    }

    /**
     * Update the data with the new settings submitted by the user.
     */
    public function process_submission() {
        global $DB;
    /// Delete all records, then add back the ones that should be allowed.
        $DB->delete_records($this->tablename);
        foreach ($this->roles as $fromroleid => $notused) {
            foreach ($this->roles as $targetroleid => $alsonotused) {
                if (optional_param('s_' . $fromroleid . '_' . $targetroleid, false, PARAM_BOOL)) {
                    $this->set_allow($fromroleid, $targetroleid);
                }
            }
        }
    }

    /**
     * Set one allow in the database.
     * @param integer $fromroleid
     * @param integer $targetroleid
     */
    protected abstract function set_allow($fromroleid, $targetroleid);

    /**
     * Load the current allows from the database.
     */
    public function load_current_settings() {
        global $DB;
    /// Load the current settings
        $this->allowed = array();
        foreach ($this->roles as $role) {
            // Make an array $role->id => false. This is probably too clever for its own good.
            $this->allowed[$role->id] = array_combine(array_keys($this->roles), array_fill(0, count($this->roles), false));
        }
        $rs = $DB->get_recordset($this->tablename);
        foreach ($rs as $allow) {
            $this->allowed[$allow->roleid][$allow->{$this->targetcolname}] = true;
        }
    }

    /**
     * @param integer $targetroleid a role id.
     * @return boolean whether the user should be allowed to select this role as a
     * target role.
     */
    protected function is_allowed_target($targetroleid) {
        return true;
    }

    /**
     * @return object a $table structure that can be passed to print_table, containing
     * one cell for each checkbox.
     */
    public function get_table() {
        $table = new html_table();
        $table->tablealign = 'center';
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '90%';
        $table->align = array('left');
        $table->rotateheaders = true;
        $table->head = array('&#xa0;');
        $table->colclasses = array('');

    /// Add role name headers.
        foreach ($this->roles as $targetrole) {
            $table->head[] = $targetrole->localname;
            $table->align[] = 'left';
            if ($this->is_allowed_target($targetrole->id)) {
                $table->colclasses[] = '';
            } else {
                $table->colclasses[] = 'dimmed_text';
            }
        }

    /// Now the rest of the table.
        foreach ($this->roles as $fromrole) {
            $row = array($fromrole->localname);
            foreach ($this->roles as $targetrole) {
                $checked = '';
                $disabled = '';
                if ($this->allowed[$fromrole->id][$targetrole->id]) {
                    $checked = 'checked="checked" ';
                }
                if (!$this->is_allowed_target($targetrole->id)) {
                    $disabled = 'disabled="disabled" ';
                }
                $name = 's_' . $fromrole->id . '_' . $targetrole->id;
                $tooltip = $this->get_cell_tooltip($fromrole, $targetrole);
                $row[] = '<input type="checkbox" name="' . $name . '" id="' . $name .
                        '" title="' . $tooltip . '" value="1" ' . $checked . $disabled . '/>' .
                        '<label for="' . $name . '" class="accesshide">' . $tooltip . '</label>';
            }
            $table->data[] = $row;
        }

        return $table;
    }

    /**
     * Snippet of text displayed above the table, telling the admin what to do.
     * @return unknown_type
     */
    public abstract function get_intro_text();
}

/**
 * Subclass of role_allow_role_page for the Allow assigns tab.
 */
class role_allow_assign_page extends role_allow_role_page {
    public function __construct() {
        parent::__construct('role_allow_assign', 'allowassign');
    }

    protected function set_allow($fromroleid, $targetroleid) {
        allow_assign($fromroleid, $targetroleid);
    }

    protected function get_cell_tooltip($fromrole, $targetrole) {
        $a = new stdClass;
        $a->fromrole = $fromrole->localname;
        $a->targetrole = $targetrole->localname;
        return get_string('allowroletoassign', 'role', $a);
    }

    public function get_intro_text() {
        return get_string('configallowassign', 'admin');
    }
}

/**
 * Subclass of role_allow_role_page for the Allow overrides tab.
 */
class role_allow_override_page extends role_allow_role_page {
    public function __construct() {
        parent::__construct('role_allow_override', 'allowoverride');
    }

    protected function set_allow($fromroleid, $targetroleid) {
        allow_override($fromroleid, $targetroleid);
    }

    protected function get_cell_tooltip($fromrole, $targetrole) {
        $a = new stdClass;
        $a->fromrole = $fromrole->localname;
        $a->targetrole = $targetrole->localname;
        return get_string('allowroletooverride', 'role', $a);
    }

    public function get_intro_text() {
        return get_string('configallowoverride2', 'admin');
    }
}

/**
 * Subclass of role_allow_role_page for the Allow switches tab.
 */
class role_allow_switch_page extends role_allow_role_page {
    protected $allowedtargetroles;

    public function __construct() {
        parent::__construct('role_allow_switch', 'allowswitch');
    }

    protected function load_required_roles() {
        global $DB;
        parent::load_required_roles();
        $this->allowedtargetroles = $DB->get_records_menu('role', NULL, 'id');
    }

    protected function set_allow($fromroleid, $targetroleid) {
        allow_switch($fromroleid, $targetroleid);
    }

    protected function is_allowed_target($targetroleid) {
        return isset($this->allowedtargetroles[$targetroleid]);
    }

    protected function get_cell_tooltip($fromrole, $targetrole) {
        $a = new stdClass;
        $a->fromrole = $fromrole->localname;
        $a->targetrole = $targetrole->localname;
        return get_string('allowroletoswitch', 'role', $a);
    }

    public function get_intro_text() {
        return get_string('configallowswitch', 'admin');
    }
}

/**
 * Get the potential assignees selector for a given context.
 *
 * If this context is a course context, or inside a course context (module or
 * some blocks) then return a potential_assignees_below_course object. Otherwise
 * return a potential_assignees_course_and_above.
 *
 * @param stdClass $context a context.
 * @param string $name passed to user selector constructor.
 * @param array $options to user selector constructor.
 * @return user_selector_base an appropriate user selector.
 */
function roles_get_potential_user_selector($context, $name, $options) {
        $blockinsidecourse = false;
        if ($context->contextlevel == CONTEXT_BLOCK) {
            $parentcontext = get_context_instance_by_id(get_parent_contextid($context));
            $blockinsidecourse = in_array($parentcontext->contextlevel, array(CONTEXT_MODULE, CONTEXT_COURSE));
        }

        if (($context->contextlevel == CONTEXT_MODULE || $blockinsidecourse) &&
                !is_inside_frontpage($context)) {
            $potentialuserselector = new potential_assignees_below_course('addselect', $options);
        } else {
            $potentialuserselector = new potential_assignees_course_and_above('addselect', $options);
        }
    return $potentialuserselector;
}

class admins_potential_selector extends user_selector_base {
    /**
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct() {
        global $CFG, $USER;
        $admins = explode(',', $CFG->siteadmins);
        parent::__construct('addselect', array('multiselect'=>false, 'exclude'=>$admins));
    }

    public function find_users($search) {
        global $CFG, $DB;
        list($wherecondition, $params) = $this->search_sql($search, '');

        $fields      = 'SELECT ' . $this->required_fields_sql('');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user}
                WHERE $wherecondition AND mnethostid = :localmnet";
        $order = ' ORDER BY lastname ASC, firstname ASC';
        $params['localmnet'] = $CFG->mnet_localhost_id; // it could be dangerous to make remote users admins and also this could lead to other problems

        // Check to see if there are too many to show sensibly.
        if (!$this->is_validating()) {
            $potentialcount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialcount > 100) {
                return $this->too_many_results($search, $potentialcount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'role', $search);
        } else {
            $groupname = get_string('potusers', 'role');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}

class admins_existing_selector extends user_selector_base {
    /**
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct() {
        global $CFG, $USER;
        parent::__construct('removeselect', array('multiselect'=>false));
    }

    public function find_users($search) {
        global $DB, $CFG;
        list($wherecondition, $params) = $this->search_sql($search, '');

        $fields      = 'SELECT ' . $this->required_fields_sql('');
        $countfields = 'SELECT COUNT(1)';

        if ($wherecondition) {
            $wherecondition = "$wherecondition AND id IN ($CFG->siteadmins)";
        } else {
            $wherecondition = "id IN ($CFG->siteadmins)";
        }
        $sql = " FROM {user}
                WHERE $wherecondition";
        $order = ' ORDER BY lastname ASC, firstname ASC';

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('extusersmatching', 'role', $search);
        } else {
            $groupname = get_string('extusers', 'role');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}
