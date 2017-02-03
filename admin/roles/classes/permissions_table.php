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
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Subclass of core_role_capability_table_base for use on the Permissions page.
 */
class core_role_permissions_table extends core_role_capability_table_base {
    protected $contextname;
    protected $allowoverrides;
    protected $allowsafeoverrides;
    protected $overridableroles;
    protected $roles;
    protected $icons = array();

    /**
     * Constructor.
     * @param context $context the context this table relates to.
     * @param string $contextname $context->get_context_name() - to save recomputing.
     * @param array $allowoverrides
     * @param array $allowsafeoverrides
     * @param array $overridableroles
     */
    public function __construct($context, $contextname, $allowoverrides, $allowsafeoverrides, $overridableroles) {
        parent::__construct($context, 'permissions');
        $this->contextname = $contextname;
        $this->allowoverrides = $allowoverrides;
        $this->allowsafeoverrides = $allowsafeoverrides;
        $this->overridableroles = $overridableroles;

        $roles = get_all_roles($context);
        $this->roles = role_fix_names(array_reverse($roles, true), $context, ROLENAME_ALIAS, true);

    }

    protected function add_header_cells() {
        echo '<th>' . get_string('risks', 'core_role') . '</th>';
        echo '<th>' . get_string('neededroles', 'core_role') . '</th>';
        echo '<th>' . get_string('prohibitedroles', 'core_role') . '</th>';
    }

    protected function num_extra_columns() {
        return 3;
    }

    protected function add_row_cells($capability) {
        global $OUTPUT, $PAGE;
        $renderer = $PAGE->get_renderer('core');
        $adminurl = new moodle_url("/admin/");

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
        foreach ($neededroles as $id => $unused) {
            unset($allowable[$id]);
        }
        foreach ($forbidden as $id => $unused) {
            unset($allowable[$id]);
            unset($forbitable[$id]);
        }

        foreach ($roles as $id => $name) {
            if (isset($needed[$id])) {
                $templatecontext = array("rolename" => $name, "roleid" => $id, "action" => "prevent", "spanclass" => "allowed",
                                  "linkclass" => "preventlink", "adminurl" => $adminurl->out(), "imageurl" => "");
                if (isset($overridableroles[$id]) and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
                    $templatecontext['imageurl'] = $renderer->pix_url('t/delete');
                }
                $neededroles[$id] = $renderer->render_from_template('core/permissionmanager_role', $templatecontext);
            }
        }
        $neededroles = implode(' ', $neededroles);
        foreach ($roles as $id => $name) {
            if (isset($forbidden[$id])  and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
                $templatecontext = array("rolename" => $name, "roleid" => $id, "action" => "unprohibit",
                                "spanclass" => "forbidden", "linkclass" => "unprohibitlink", "adminurl" => $adminurl->out(),
                                "imageurl" => "");
                if (isset($overridableroles[$id]) and prohibit_is_removable($id, $context, $capability->name)) {
                    $templatecontext['imageurl'] = $renderer->pix_url('t/delete');
                }
                $forbiddenroles[$id] = $renderer->render_from_template('core/permissionmanager_role', $templatecontext);
            }
        }
        $forbiddenroles = implode(' ', $forbiddenroles);

        if ($allowable and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
            $allowurl = new moodle_url($PAGE->url, array('contextid' => $contextid,
                                       'capability' => $capability->name, 'allow' => 1));
            $allowicon = $OUTPUT->action_icon($allowurl, new pix_icon('t/add', get_string('allow', 'core_role')), null,
                                            array('class' => 'allowlink', 'data-action' => 'allow'));
            $neededroles .= html_writer::div($allowicon, 'allowmore');
        }

        if ($forbitable and ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability)))) {
            $prohibiturl = new moodle_url($PAGE->url, array('contextid' => $contextid,
                                          'capability' => $capability->name, 'prohibit' => 1));
            $prohibiticon = $OUTPUT->action_icon($prohibiturl, new pix_icon('t/add', get_string('prohibit', 'core_role')), null,
                                                array('class' => 'prohibitlink', 'data-action' => 'prohibit'));
            $forbiddenroles .= html_writer::div($prohibiticon, 'prohibitmore');
        }

        $risks = $this->get_risks($capability);

        $contents = html_writer::tag('td', $risks, array('class' => 'risks text-nowrap'));
        $contents .= html_writer::tag('td', $neededroles, array('class' => 'allowedroles'));
        $contents .= html_writer::tag('td', $forbiddenroles, array('class' => 'forbiddenroles'));
        return $contents;
    }

    protected function get_risks($capability) {
        global $OUTPUT;

        $allrisks = get_all_risks();
        $risksurl = new moodle_url(get_docs_url(s(get_string('risks', 'core_role'))));

        $return = '';

        foreach ($allrisks as $type => $risk) {
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

    /**
     * Add additional attributes to row
     *
     * @param stdClass $capability capability that this table row relates to.
     * @return array key value pairs of attribute names and values.
     */
    protected function get_row_attributes($capability) {
        return array(
                'data-id' => $capability->id,
                'data-name' => $capability->name,
                'data-humanname' => get_capability_string($capability->name),
        );
    }
}
