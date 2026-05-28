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
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_role_view_role_definition_table extends core_role_define_role_table_advanced {
    public function __construct($context, $roleid) {
        parent::__construct($context, $roleid);
        $this->displaypermissions = array(CAP_ALLOW => $this->allpermissions[CAP_ALLOW]);
        $this->disabled = 'disabled="disabled" ';
    }

    public function save_changes() {
        throw new moodle_exception('invalidaccess');
    }

    protected function get_name_field($id) {
        return role_get_name($this->role);
    }

    protected function get_shortname_field($id) {
        return $this->role->shortname;
    }

    protected function get_description_field($id) {
        return role_get_description($this->role);
    }

    protected function get_archetype_field($id) {
        if (empty($this->role->archetype)) {
            return get_string('none');
        } else {
            return get_string('archetype'.$this->role->archetype, 'core_role');
        }
    }

    protected function get_allow_role_control($type) {
        if ($roles = $this->get_allow_roles_list($type)) {
            $roles = role_fix_names($roles, null, ROLENAME_ORIGINAL, true);
            return implode(', ', $roles);
        } else {
            return get_string('none');
        }
    }


    protected function print_show_hide_advanced_button() {
        // Do nothing.
    }

    /**
     * Returns role risks and the number of risky capabilities
     *
     * @return array of risks
     */
    public function get_role_risks() {

        if (empty($this->roleid)) {
            return '';
        }

        $allrisks = get_all_risks();
        $risks = array_fill_keys(array_keys($allrisks), 0);
        foreach ($this->capabilities as $capability) {
            $perm = $this->permissions[$capability->name];
            if ($perm != CAP_ALLOW) {
                continue;
            }
            foreach ($allrisks as $type => $risk) {
                if ($risk & (int)$capability->riskbitmask) {
                    $risks[$type]++;
                }
            }
        }
        return $risks;
    }

    /**
     * Returns HTML risk icons.
     *
     * @return string
     */
    public function get_role_risks_info() {
        global $OUTPUT, $CFG;

        $html = '';
        $filter = optional_param('risk', '', PARAM_TEXT);
        $allrisks = get_all_risks();
        if ($filter && array_key_exists($filter, $allrisks)) {
            $riskname = get_string($filter . 'short', 'admin');
            $html .= $OUTPUT->notification(
                get_string('risksfilter', 'role', [
                    'riskname' => $riskname,
                    'reseturl' => new \moodle_url('/admin/roles/define.php', [
                            'action' => 'view',
                            'roleid' => $this->roleid,
                        ]),
                ]),
                core\output\notification::NOTIFY_INFO
            );
        }

        $riskcount = 0;
        $risks = $this->get_role_risks();
        foreach ($risks as $type => $count) {
            $riskcount += $count;
            if ($count == 0) {
                continue;
            }
            $filterurl = new \moodle_url('/admin/roles/define.php', [
                'action' => 'view',
                'roleid' => $this->roleid,
                'risk' => $type,
            ]);
            $pixicon = new pix_icon('/i/' . str_replace('risk', 'risk_', $type), get_string($type . 'short', 'admin'));
            $icon = $OUTPUT->render($pixicon);
            $text = get_string($type . 'short', 'admin');
            $html .= "<b>$icon $text</b> ";
            $html .= html_writer::tag(
                'small',
                $OUTPUT->action_link($filterurl, get_string('risksfilterwithcount', 'role', $count))
            );
            $html .= html_writer::tag('p', get_string($type, 'admin'), ['class' => 'ml-5']);
        }

        if ($riskcount == 0) {
            return '';
        }

        $html .= $OUTPUT->doc_link(get_docs_url(s(get_string('risks', 'core_role'))), get_string('morehelp'));
        return $html;
    }

    /**
     * Returns true if the row should be skipped.
     *
     * @param string $capability
     * @return bool
     */
    protected function skip_row($capability) {

        // Filter to just capabilities with a certain risk.
        $filter = optional_param('risk', '', PARAM_TEXT);
        $allrisks = get_all_risks();
        if ($filter && array_key_exists($filter, $allrisks)) {
            $bit = $allrisks[$filter];
            if (!($bit & (int)$capability->riskbitmask)) {
                return true;
            }
        }

        $perm = $this->permissions[$capability->name];
        if ($perm == CAP_INHERIT) {
            // Do not print empty rows in role overview, admins need to know quickly what is allowed and prohibited,
            // if they want to see the list of all capabilities they can go to edit role page.
            return true;
        }
        parent::skip_row($capability);
    }

    protected function add_permission_cells($capability) {
        $perm = $this->permissions[$capability->name];
        $permname = $this->allpermissions[$perm];
        $defaultperm = $this->allpermissions[$this->parentpermissions[$capability->name]];
        if ($permname != $defaultperm) {
            $default = get_string('defaultx', 'core_role', $this->strperms[$defaultperm]);
        } else {
            $default = "&#xa0;";
        }
        return '<td class="' . $permname . '">' . $this->strperms[$permname] . '<span class="note">' .
            $default . '</span></td>';

    }
}
