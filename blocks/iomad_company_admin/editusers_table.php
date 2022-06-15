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
 * Base class for the table used by iomad_company_admin/editusers.php.
 *
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class block_iomad_company_admin_editusers_table extends table_sql {

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($row) {
        $name = fullname($row, has_capability('moodle/site:viewfullnames', $this->get_context()));
        if (!empty($row->suspended)) {
            $name .= "&nbsp(S)";
        }
        return $name;
    }

    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_department($row) {
        global $DB;

        $departments = $DB->get_records_sql("SELECT d.name FROM {department} d
                                             JOIN {company_users} cu
                                             ON (d.id = cu.departmentid)
                                             WHERE cu.userid = :userid
                                             ORDER BY d.name",
                                             array('userid' => $row->id));
        $returnstr = "";
        $count = count($departments);
        $current = 1;
        if ($count > 5) {
            $returnstr = "<details><summary>" . get_string('show') . "</summary>";
        }

        foreach($departments as $department) {
            $returnstr .= format_string($department->name);
            if ($current < $count) {
                $returnstr .= ",</br>";
            }
            $current++;
        }

        if ($count > 5) {
            $returnstr .= "</details>";
        }

        return $returnstr;

    }

    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_email($row) {

        return $row->email;
    }

    /**
    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_lastaccess($row) {
        global $CFG;

        if (!empty($row->currentlogin)) {
            return date($CFG->iomad_date_format, $row->currentlogin);;
        } else {
            return get_string('never');
        }
    }

    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_company($row) {

        return format_string($row->companyname);
    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $USER, $output, $params, $systemcontext, $DB, $companyid;

        // User actions
        $actions = array();

        if ($row->username == 'guest') {
            return; // Do not dispaly dummy new user and guest here.
        }

        if ((iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             or $row->id == $USER->id and !is_mnet_remote_user($row)) {
            if ($row->id != $USER->id && $DB->get_record_select('company_users', 'companyid =:company AND managertype != 0 AND userid = :userid', array('company' => $row->companyid, 'userid' => $row->id))
                && !iomad::has_capability('block/iomad_company_admin:editmanagers', $systemcontext)) {
               // This manager can't edit manager users.
            } else {
                $url = new moodle_url('/blocks/iomad_company_admin/editadvanced.php', array(
                    'id' => $row->id,
                ));
                $actions['edit'] = new action_menu_link_secondary(
                    $url,
                    null,
                    get_string('edit')
                );
                if (iomad::has_capability('block/iomad_company_admin:edituserpassword', $systemcontext)) {
                    $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                        'password' => $row->id,
                        'sesskey' => sesskey(),
                    ));
                    $actions['password'] = new action_menu_link_secondary(
                        $url,
                        null,
                        get_string('resetpassword', 'block_iomad_company_admin')
                    );
                }
            }
        }

        if ($row->id != $USER->id) {
            if ((iomad::has_capability('block/iomad_company_admin:editusers', $systemcontext)
                 or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))) {
                if ($DB->get_record_select('company_users', 'companyid =:company AND managertype != 0 AND userid = :userid', array('company' => $companyid, 'userid' => $row->id))
                && !iomad::has_capability('block/iomad_company_admin:editmanagers', $systemcontext)) {
                    // Do nothing.
                } else {
                    if (iomad::has_capability('block/iomad_company_admin:deleteuser', $systemcontext)) {
                        $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                            'delete' => $row->id,
                            'sesskey' => sesskey(),
                        ));
                        $actions['delete'] = new action_menu_link_secondary(
                            $url,
                            null,
                            get_string('delete')
                        );
                    }
                    if (iomad::has_capability('block/iomad_company_admin:suspenduser', $systemcontext)) {
                        if (!empty($row->suspended)) {
                            $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                                'unsuspend' => $row->id,
                                'sesskey' => sesskey(),
                            ));
                            $actions['unsuspend'] = new action_menu_link_secondary(
                                $url,
                                null,
                                get_string('unsuspend', 'block_iomad_company_admin')
                            );
                        } else {
                            $url = new moodle_url('/blocks/iomad_company_admin/editusers.php', array(
                                'suspend' => $row->id,
                                'sesskey' => sesskey(),
                            ));
                            $actions['suspend'] = new action_menu_link_secondary(
                                $url,
                                null,
                                get_string('suspend', 'block_iomad_company_admin')
                            );
                        }
                    }
                }
            }
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_course_users', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($row->id == $USER->id or !is_siteadmin($row)
             and !is_mnet_remote_user($row))) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_course_form.php', array(
                'userid' => $row->id,
            ));
            $actions['enrolment'] = new action_menu_link_secondary(
                $url,
                null,
                get_string('userenrolments', 'block_iomad_company_admin')
            );
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_license_users', $systemcontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $systemcontext))
             and ($row->id == $USER->id or !is_siteadmin($row))
             and !is_mnet_remote_user($row)) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php', array(
                'userid' => $row->id,
            ));
            $actions['userlicense'] = new action_menu_link_secondary(
                $url,
                null,
                get_string('userlicenses', 'block_iomad_company_admin')
            );
        }

        if (iomad::has_capability('local/report_users:view', $systemcontext)) {
            $url = new moodle_url('/local/report_users/userdisplay.php', array(
                'userid' => $row->id,
            ));
            $actions['userreport'] = new action_menu_link_secondary(
                $url,
                null,
                get_string('report_users_title', 'local_report_users')
            );
        }


        $menu = new action_menu();
        $menu->set_owner_selector('.iomad_editusers-actionmenu');
        $menu->set_alignment_left();
        $menu->set_menu_trigger(get_string('usercontrols', 'block_iomad_company_admin'));
        foreach ($actions as $action) {
            $menu->add($action);
        }

        return $output->render($menu);

    }

    /**
     * This function is not part of the public api.
     */
    function print_nothing_to_display() {
        global $OUTPUT, $CFG;

        // Render the dynamic table header.
        echo $this->get_dynamic_table_html_start();

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->print_initials_bar();

        echo $OUTPUT->heading(get_string('nothingtodisplay'));

        // Render the dynamic table footer.
        echo $this->get_dynamic_table_html_end();

        // Add the button to add a user.
        echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/company_user_create_form.php'),
                                    get_string('createuser', 'block_iomad_company_admin'));
    }
}
