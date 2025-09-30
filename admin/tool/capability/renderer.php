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
 * Capability tool renderer.
 *
 * @package    tool_capability
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The primary renderer for the capability tool.
 *
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_capability_renderer extends plugin_renderer_base {

    /**
     * Returns an array of permission strings.
     *
     * @return lang_string[]
     */
    protected function get_permission_strings() {
        static $strpermissions;
        if (!$strpermissions) {
            $strpermissions = array(
                CAP_INHERIT => new lang_string('inherit', 'role'),
                CAP_ALLOW => new lang_string('allow', 'role'),
                CAP_PREVENT => new lang_string('prevent', 'role'),
                CAP_PROHIBIT => new lang_string('prohibit', 'role')
            );
        }
        return $strpermissions;
    }

    /**
     * Returns an array of permission CSS classes.
     *
     * @return string[]
     */
    protected function get_permission_classes() {
        static $permissionclasses;
        if (!$permissionclasses) {
            $permissionclasses = array(
                CAP_INHERIT => 'inherit',
                CAP_ALLOW => 'allow',
                CAP_PREVENT => 'prevent',
                CAP_PROHIBIT => 'prohibit',
            );
        }
        return $permissionclasses;
    }

    /**
     * Produces a table to visually compare roles and capabilities.
     *
     * @param array $capabilities An array of capabilities to show comparison for.
     * @param int $contextid The context we are displaying for.
     * @param array $roles An array of roles to show comparison for.
     * @param bool $onlydiff show only different permissions
     * @return string
     */
    public function capability_comparison_table(array $capabilities, $contextid, array $roles, $onlydiff=false) {
        static $capabilitycontexts = array();

        $strpermissions = $this->get_permission_strings();
        $permissionclasses = $this->get_permission_classes();

        if ($contextid === context_system::instance()->id) {
            $strpermissions[CAP_INHERIT] = new lang_string('notset', 'role');
        }

        $table = new html_table();
        $table->attributes['class'] = 'comparisontable table-reboot';
        $table->head = array('&nbsp;');
        foreach ($roles as $role) {
            $url = new moodle_url('/admin/roles/define.php', array('action' => 'view', 'roleid' => $role->id));
            $table->head[] = html_writer::div(html_writer::link($url, $role->localname));
        }
        $table->data = array();

        $childcontextsids = [];
        foreach ($capabilities as $capability) {
            if (empty($capabilitycontexts[$capability])) {
                $capabilitycontexts[$capability] = tool_capability_calculate_role_data($capability, $roles);
            }
            $contexts = $capabilitycontexts[$capability];

            $captitle = new html_table_cell(get_capability_string($capability) . html_writer::span($capability));
            $captitle->header = true;

            $row = new html_table_row(array($captitle));

            $permissiontypes = array();
            foreach ($roles as $role) {
                if (isset($contexts[$contextid]->rolecapabilities[$role->id])) {
                    $permission = $contexts[$contextid]->rolecapabilities[$role->id];
                } else {
                    $permission = CAP_INHERIT;
                }
                if (!in_array($permission, $permissiontypes)) {
                    $permissiontypes[] = $permission;
                }
                $cell = new html_table_cell($strpermissions[$permission]);
                $cell->attributes['class'] = $permissionclasses[$permission];
                $row->cells[] = $cell;
            }
            if (!$onlydiff || count($permissiontypes) > 1) {
                $table->data[] = $row;
            }
            if (!empty($contexts[$contextid]->children)) {
                $childcontextsids = array_merge($childcontextsids, $contexts[$contextid]->children);
                $childcontextsids = array_unique($childcontextsids);
            }
        }

        // Start the list item, and print the context name as a link to the place to make changes.
        $context = context::instance_by_id($contextid);

        if ($context instanceof context_system) {
            $url = new moodle_url('/admin/roles/manage.php');
        } else {
            $url = new moodle_url('/admin/roles/permissions.php', ['contextid' => $contextid]);
        }

        $title = get_string('permissionsincontext', 'core_role', $context->get_context_name());

        $html = $this->output->heading(html_writer::link($url, $title), 3);
        if (!empty($table->data)) {
            $html .= html_writer::table($table);
        } else {
            $html .= html_writer::tag('p', get_string('nodifferences', 'tool_capability'));
        }
        // If there are any child contexts, print them recursively.
        if (!empty($childcontextsids)) {
            foreach ($childcontextsids as $childcontextid) {
                $html .= $this->capability_comparison_table($capabilities, $childcontextid, $roles, $onlydiff);
            }
        }
        return $html;
    }

}
