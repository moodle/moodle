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
 * Capabilities table with risks.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This subclass is the bases for both the define roles and override roles
 * pages. As well as adding the risks columns, this also provides generic
 * facilities for showing a certain number of permissions columns, and
 * recording the current and submitted permissions for each capability.
 */
abstract class core_role_capability_table_with_risks extends core_role_capability_table_base {
    protected $allrisks;
    protected $allpermissions; // We don't need perms ourselves, but all our subclasses do.
    protected $strperms; // Language string cache.
    protected $risksurl; // URL in moodledocs about risks.
    /** @var array The capabilities to highlight as default/inherited. */
    protected $parentpermissions;
    protected $displaypermissions;
    protected $permissions;
    protected $changed;
    protected $roleid;

    public function __construct($context, $id, $roleid) {
        parent::__construct($context, $id);

        $this->allrisks = get_all_risks();
        $this->risksurl = get_docs_url(s(get_string('risks', 'core_role')));

        $this->allpermissions = array(
            CAP_INHERIT => 'inherit',
            CAP_ALLOW => 'allow',
            CAP_PREVENT => 'prevent' ,
            CAP_PROHIBIT => 'prohibit',
        );

        $this->strperms = array();
        foreach ($this->allpermissions as $permname) {
            $this->strperms[$permname] =  get_string($permname, 'core_role');
        }

        $this->roleid = $roleid;
        $this->load_current_permissions();

        // Fill in any blank permissions with an explicit CAP_INHERIT, and init a locked field.
        foreach ($this->capabilities as $capid => $cap) {
            if (!isset($this->permissions[$cap->name])) {
                $this->permissions[$cap->name] = CAP_INHERIT;
            }
            $this->capabilities[$capid]->locked = false;
        }
    }

    protected function load_current_permissions() {
        global $DB;

        // Load the overrides/definition in this context.
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
                // The user is not allowed to change the permission for this capability.
                continue;
            }

            $permission = optional_param($cap->name, null, PARAM_PERMISSION);
            if (is_null($permission)) {
                // A permission was not specified in submitted data.
                continue;
            }

            // If the permission has changed, update $this->permissions and
            // Record the fact there is data to save.
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
        // Set the permissions.
        foreach ($this->changed as $changedcap) {
            assign_capability($changedcap, $this->permissions[$changedcap],
                $this->roleid, $this->context->id, true);
        }

        // Force accessinfo refresh for users visiting this context.
        $this->context->mark_dirty();
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
            get_string('permission', 'core_role') . ' ' . $OUTPUT->help_icon('permission', 'core_role') . '</th>';
        echo '<th class="risk" colspan="' . count($this->allrisks) . '" scope="col">' . get_string('risks', 'core_role') . '</th>';
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
        $cells = $this->add_permission_cells($capability);
        // One cell for each possible risk.
        foreach ($this->allrisks as $riskname => $risk) {
            $cells .= '<td class="risk ' . str_replace('risk', '', $riskname) . '">';
            if ($risk & (int)$capability->riskbitmask) {
                $cells .= $this->get_risk_icon($riskname);
            }
            $cells .= '</td>';
        }
        return $cells;
    }

    /**
     * Print a risk icon, as a link to the Risks page on Moodle Docs.
     *
     * @param string $type the type of risk, will be one of the keys from the
     *      get_all_risks array. Must start with 'risk'.
     */
    public function get_risk_icon($type) {
        global $OUTPUT;

        $alt = get_string("{$type}short", "admin");
        $title = get_string($type, "admin");

        $text = $OUTPUT->pix_icon('i/' . str_replace('risk', 'risk_', $type), $alt, 'moodle', [
                'title' => $title,
            ]);
        $action = new popup_action('click', $this->risksurl, 'docspopup');
        $riskicon = $OUTPUT->action_link($this->risksurl, $text, $action);

        return $riskicon;
    }
}
