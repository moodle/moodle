<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrap
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/enrol/renderer.php");

class theme_bootstrap_core_enrol_renderer extends core_enrol_renderer {

    public function render_course_enrolment_users_table(course_enrolment_users_table $table,
            moodleform $mform) {

        $table->initialise_javascript();

        // Added for the Bootstrap theme. Make this table responsive
        $table->attributes['class'] .= ' table table-responsive';

        $buttons = $table->get_manual_enrol_buttons();
        $buttonhtml = '';
        if (count($buttons) > 0) {
            $buttonhtml .= html_writer::start_tag('div', array('class' => 'enrol_user_buttons'));
            foreach ($buttons as $button) {
                $buttonhtml .= $this->render($button);
            }
            $buttonhtml .= html_writer::end_tag('div');
        }

        $content = '';
        if (!empty($buttonhtml)) {
            $content .= $buttonhtml;
        }
        $content .= $mform->render();

        $content .= $this->output->render($table->get_paging_bar());

        // Check if the table has any bulk operations. If it does we want to wrap the table in a
        // form so that we can capture and perform any required bulk operations.
        if ($table->has_bulk_user_enrolment_operations()) {
            $content .= html_writer::start_tag('form', array('action' => new moodle_url('/enrol/bulkchange.php'), 'method' => 'post'));
            foreach ($table->get_combined_url_params() as $key => $value) {
                if ($key == 'action') {
                    continue;
                }
                $content .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $key, 'value' => $value));
            }
            $content .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'bulkchange'));
            $content .= html_writer::table($table);
            $content .= html_writer::start_tag('div', array('class' => 'singleselect bulkuserop'));
            $content .= html_writer::start_tag('select', array('name' => 'bulkuserop'));
            $content .= html_writer::tag('option', get_string('withselectedusers', 'enrol'), array('value' => ''));
            $options = array('' => get_string('withselectedusers', 'enrol'));
            foreach ($table->get_bulk_user_enrolment_operations() as $operation) {
                $content .= html_writer::tag('option', $operation->get_title(), array('value' => $operation->get_identifier()));
            }
            $content .= html_writer::end_tag('select');
            $content .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go')));
            $content .= html_writer::end_tag('div');

            $content .= html_writer::end_tag('form');
        } else {
            // Added for the Bootstrap theme, a no-overflow wrapper
            $content .= html_writer::start_tag('div', array('class' => 'no-overflow'));
            $content .= html_writer::table($table);
            $content .= html_writer::end_tag('div');
        }
        $content .= $this->output->render($table->get_paging_bar());
        if (!empty($buttonhtml)) {
            $content .= $buttonhtml;
        }
        return $content;
    }

    public function user_roles_and_actions($userid, $roles, $assignableroles, $canassign, $pageurl) {
        $iconenroladd    = $this->output->pix_url('t/enroladd');
        $iconenrolremove = $this->output->pix_url('t/delete');

        // get list of roles
        $rolesoutput = '';
        foreach ($roles as $roleid=>$role) {
            if ($canassign and (is_siteadmin() or isset($assignableroles[$roleid])) and !$role['unchangeable']) {
                $strunassign = get_string('unassignarole', 'role', $role['text']);
                $icon = html_writer::empty_tag('img', array('alt'=>$strunassign, 'src'=>$iconenrolremove));
                $url = new moodle_url($pageurl, array('action'=>'unassign', 'roleid'=>$roleid, 'user'=>$userid));
                $rolesoutput .= html_writer::tag('div', $role['text'] . html_writer::link($url, $icon, array('class'=>'unassignrolelink', 'rel'=>$roleid, 'title'=>$strunassign)), array('class'=>'role role_'.$roleid));
            } else {
                $rolesoutput .= html_writer::tag('div', $role['text'], array('class'=>'role unchangeable', 'rel'=>$roleid));
            }
        }
        $output = '';
        if (!empty($assignableroles) && $canassign) {
            $roleids = array_keys($roles);
            $hasallroles = true;
            foreach (array_keys($assignableroles) as $key) {
                if (!in_array($key, $roleids)) {
                    $hasallroles = false;
                    break;
                }
            }
            if (!$hasallroles) {
                $url = new moodle_url($pageurl, array('action'=>'assign', 'user'=>$userid));
                $icon = html_writer::tag('label', get_string('assignroles', 'role'), array('alt'=>get_string('assignroles', 'role'), 'class' => 'label label-primary'));
                $output = html_writer::tag('div', html_writer::link($url, $icon, array('class'=>'assignrolelink', 'title'=>get_string('assignroles', 'role'))), array('class'=>'addrole'));
            }
        }
        $output .= html_writer::tag('div', $rolesoutput, array('class'=>'roles'));
        return $output;
    }


    public function user_groups_and_actions($userid, $groups, $allgroups, $canmanagegroups, $pageurl) {
        $iconenroladd    = $this->output->pix_url('t/enroladd');
        $iconenrolremove = $this->output->pix_url('t/delete');
        $straddgroup = get_string('addgroup', 'group');

        $groupoutput = '';
        foreach($groups as $groupid=>$name) {
            if ($canmanagegroups and groups_remove_member_allowed($groupid, $userid)) {
                $icon = html_writer::tag('label', get_string('add'), array('alt'=>$straddgroup, 'class' => 'label label-primary'));
                $url = new moodle_url($pageurl, array('action'=>'addmember', 'user'=>$userid));
                $groupoutput .= html_writer::tag('div', html_writer::link($url, $icon), array('class'=>'addgroup'));
                $icon = html_writer::empty_tag('img', array('alt'=>get_string('removefromgroup', 'group', $name), 'src'=>$iconenrolremove));
                $url = new moodle_url($pageurl, array('action'=>'removemember', 'group'=>$groupid, 'user'=>$userid));
                $groupoutput .= html_writer::tag('div', $name . html_writer::link($url, $icon), array('class'=>'group', 'rel'=>$groupid));
            } else {
                $groupoutput .= html_writer::tag('div', $name, array('class'=>'group', 'rel'=>$groupid));
            }
        }
        $groupoutput = html_writer::tag('div', $groupoutput, array('class'=>'groups'));
        if ($canmanagegroups && (count($groups) < count($allgroups))) {
            $icon = html_writer::tag('label', get_string('add'), array('alt'=>$straddgroup, 'class' => 'label label-primary'));
            $url = new moodle_url($pageurl, array('action'=>'addmember', 'user'=>$userid));
            $groupoutput .= html_writer::tag('div', html_writer::link($url, $icon), array('class'=>'addgroup'));
        }
        return $groupoutput;
    }
}