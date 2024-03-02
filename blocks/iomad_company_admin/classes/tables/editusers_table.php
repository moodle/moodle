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

namespace block_iomad_company_admin\tables;

use \table_sql;
use \moodle_url;
use \action_menu_link_secondary;
use \action_menu;
use \iomad;
use \html_writer;
use \company;
use \context_system;
use \context_user;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class editusers_table extends table_sql {

    protected $departments;
    protected $assignabledepartments;
    protected $usertypes;

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($row) {
        $name = fullname($row, has_capability('moodle/site:viewfullnames', $this->get_context()));

        // Deal with suspended users.
        if (!empty($row->suspended)) {
            $name = format_string("$name (S)");
        }

        // Can we see a link?
        $usercontext = context_user::instance($row->id);
        if (has_capability('moodle/user:viewdetails', $usercontext) || has_capability('moodle/user:viewalldetails', $usercontext)) {
            $profileurl = new moodle_url('/user/profile.php', ['id' => $row->id]);
            return html_writer::tag('a', $name, ['href' => $profileurl]);
        } else {
            return $name;
        }
    }

    /**
     * Generate the display of the user's departments
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_department($row) {
        global $DB, $USER, $company, $OUTPUT, $companycontext;

        $userdepartments = array_keys($DB->get_records('company_users', ['companyid' => $company->id, 'userid' => $row->id], '', 'departmentid'));
        if (empty($USER->editing) || $row->managertype == 1) {
            $count = count($userdepartments);
            $current = 1;
            $returnstr = "";
            if ($count > 5) {
                $returnstr = "<details><summary>" . get_string('show') . "</summary>";
            }

            $first = true;
            foreach($userdepartments as $department) {
                //$returnstr .= format_string($department->name);
                $returnstr .= format_string($this->departmentsmenu[$department]);

                if ($current < $count) {
                    $returnstr .= ",<br>";
                }
                $current++;
            }

            if ($count > 5) {
                $returnstr .= "</details>";
            }

            return $returnstr;

        } else {
            $editable = new \block_iomad_company_admin\output\user_departments_editable($company,
                                                          $companycontext,
                                                          $row,
                                                          $userdepartments,
                                                          $this->departments,
                                                          $this->assignabledepartments);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
        }
    }

    /**
     * Generate the display of the user's company roles
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_managertype($row) {
        global $CFG, $DB, $USER, $company, $OUTPUT, $companycontext;

        $returnstr = "";

        if (empty($USER->editing)) {
            $returnstr .= $this->usertypes[$row->managertype];
            if (!empty($row->educator) && empty($CFG->iomad_autoenrol_managers)) {
                $returnstr .= ",<br>" . $this->usertypes[3];
            }
    
            return $returnstr;
        } else {
            // Can't be a company manager if you are in more than one department or the department you are in is not the top level department.
            $userdepartments = array_keys($DB->get_records('company_users', ['companyid' => $company->id, 'userid' => $row->id], '', 'departmentid'));
            $usertypeselect = $this->usertypeselect;
            if (count($userdepartments) > 1 ||
                $userdepartments[0] != $this->parentlevel->id) {
                unset($usertypeselect[10]);
                unset($usertypeselect[11]);
            }

            // Set up the current value for the inplace form and display it.
            if (empty($CFG->iomad_autoenrol_managers)) {
                $currentvalue = ($row->managertype * 10) + $row->educator;
            } else {
                $currentvalue = $row->managertype * 10;
            }

            // Added due to value mismatch when editing under certain circumstances.
            if (empty($currentvalue)) {
                $currentvalue = 0;
            }

            $editable = new \block_iomad_company_admin\output\user_roles_editable($company,
                                                          $companycontext,
                                                          $row,
                                                          $currentvalue,
                                                          $usertypeselect);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
        }


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

        if (!empty($row->lastaccess)) {
            return date($CFG->iomad_date_format, $row->lastaccess);
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
        global $USER, $output, $params, $companycontext, $DB, $companyid;

        // User actions
        $actions = array();

        if ($row->username == 'guest') {
            return; // Do not dispaly dummy new user and guest here.
        }

        if (!empty($USER->editing)) {
            if ((iomad::has_capability('block/iomad_company_admin:editusers', $companycontext)
                 or iomad::has_capability('block/iomad_company_admin:editallusers', $companycontext))
                 or $row->id == $USER->id and !is_mnet_remote_user($row)) {
                if ($row->id != $USER->id &&
                    $DB->get_records_select('company_users',
                                            'companyid =:company AND managertype IN (1,2) AND userid = :userid',
                                            array('company' => $row->companyid, 'userid' => $row->id))
                    && !iomad::has_capability('block/iomad_company_admin:editmanagers', $companycontext)) {
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
                    if (iomad::has_capability('block/iomad_company_admin:edituserpassword', $companycontext)) {
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
                if ((iomad::has_capability('block/iomad_company_admin:editusers', $companycontext)
                     or iomad::has_capability('block/iomad_company_admin:editallusers', $companycontext))) {
                    if ($DB->get_records_select('company_users', 'companyid =:company AND managertype != 0 AND userid = :userid', array('company' => $companyid, 'userid' => $row->id))
                    && !iomad::has_capability('block/iomad_company_admin:editmanagers', $companycontext)) {
                        // Do nothing.
                    } else {
                        if (iomad::has_capability('block/iomad_company_admin:deleteuser', $companycontext)) {
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
                        if (iomad::has_capability('block/iomad_company_admin:suspenduser', $companycontext)) {
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
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_course_users', $companycontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $companycontext))
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

        if ((iomad::has_capability('block/iomad_company_admin:company_license_users', $companycontext)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $companycontext))
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

        if (iomad::has_capability('local/report_users:view', $companycontext)) {
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
        $menu->set_menu_left();
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

    /**
     * Constructor
     * @param string $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        global $DB, $companyid, $company, $USER, $CFG, $companycontext;

        $this->uniqueid = $uniqueid;
        $this->request  = array(
            TABLE_VAR_SORT   => 'tsort',
            TABLE_VAR_HIDE   => 'thide',
            TABLE_VAR_SHOW   => 'tshow',
            TABLE_VAR_IFIRST => 'tifirst',
            TABLE_VAR_ILAST  => 'tilast',
            TABLE_VAR_PAGE   => 'page',
            TABLE_VAR_RESET  => 'treset',
            TABLE_VAR_DIR    => 'tdir',
        );

        $this->companyid = $companyid;

        $this->usertypes = ['0' => get_string('user', 'block_iomad_company_admin'),
                            '1' => get_string('companymanager', 'block_iomad_company_admin'),
                            '2' => get_string('departmentmanager', 'block_iomad_company_admin'),
                            '3' => get_string('educator', 'block_iomad_company_admin'),
                            '4' => get_string('companyreporter', 'block_iomad_company_admin')];

        $this->departments = $DB->get_records('department', ['company' => $companyid], 'name', 'id,name');

        $parentlevel = company::get_company_parentnode($companyid);
        $this->parentlevel = $parentlevel;
        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $companycontext)) {
            $userlevels = array($parentlevel->id => $parentlevel->id);
        } else {
            $userlevels = $company->get_userlevel($USER);
        }

        $departmenttree = [];
        foreach ($userlevels as $userlevelid => $userlevel) {
            $departmenttree[] = company::get_all_subdepartments_raw($userlevelid);
        }

        $this->assignabledepartments = company::array_flatten(company::get_department_list($departmenttree[0]));

        $this->departmentsmenu = $DB->get_records_menu('department', ['company' => $companyid], 'name', 'id,name');

        // Deal with role selector.
        $this->usertypeselect = ['0' => get_string('user', 'block_iomad_company_admin')];
        if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $companycontext)) {
            $this->usertypeselect[10] = get_string('companymanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $companycontext)) {
            $this->usertypeselect[20] = get_string('departmentmanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_company_reporter', $companycontext)) {
            $this->usertypeselect[40] = get_string('companyreporter', 'block_iomad_company_admin');
        }
        if (!$CFG->iomad_autoenrol_managers && iomad::has_capability('block/iomad_company_admin:assign_educator', $companycontext)) {
            $this->usertypeselect[1] = get_string('educator', 'block_iomad_company_admin');
            if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $companycontext)) {
                $this->usertypeselect[10] = get_string('companymanager', 'block_iomad_company_admin');
                $this->usertypeselect[11] = get_string('companymanager', 'block_iomad_company_admin') . ' + ' . get_string('educator', 'block_iomad_company_admin');
            }
            if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $companycontext)) {
                $this->usertypeselect[20] = get_string('departmentmanager', 'block_iomad_company_admin');
                $this->usertypeselect[21] = get_string('departmentmanager', 'block_iomad_company_admin') . ' + ' . get_string('educator', 'block_iomad_company_admin'); 
            }
            if (iomad::has_capability('block/iomad_company_admin:assign_company_reporter', $companycontext)) {
                $this->usertypeselect[40] = get_string('companyreporter', 'block_iomad_company_admin');
                $this->usertypeselect[41] = get_string('companyreporter', 'block_iomad_company_admin') . ' + ' . get_string('educator', 'block_iomad_company_admin');
            }
        }
        ksort($this->usertypeselect);
    }
}
