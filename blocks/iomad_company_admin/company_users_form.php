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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');

class company_users_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialusers = null;
    protected $currentusers = null;

    public function __construct($actionurl, $context, $companyid) {
        $this->selectedcompany = $companyid;
        $this->context = $context;

        $options = array('context' => $this->context, 'companyid' => $this->selectedcompany);
        $this->potentialusers = new potential_company_users_user_selector('potentialusers', $options);
        $this->currentusers = new current_company_users_user_selector('currentusers', $options);

        parent::__construct($actionurl);
    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {
        global $USER, $OUTPUT;
        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the definition function
        // so that when the currentusers or potentialusers get changed in the process function, the
        // changes get displayed, rather than the lists as they are before processing.

        $company = new company($this->selectedcompany);
        $mform->addElement('header', 'header', get_string('company_users_for', 'block_iomad_company_admin', $company->get_name()));

        if (count($this->potentialusers->find_users('')) || count($this->currentusers->find_users(''))) {

            $mform->addElement('html', '<table summary=""
                                        class="companyuserstable addremovetable generaltable generalbox boxaligncenter"
                                        cellspacing="0">
                <tr>
                  <td id="existingcell">');

            $mform->addElement('html', $this->currentusers->display(true));

            $mform->addElement('html', '
                  </td>
                  <td id="buttonscell">
                      <p class="arrow_button">
                        <input name="add" id="add" type="submit" value="' . $OUTPUT->larrow().'&nbsp;'.get_string('add') . '"
                               title="' . print_string('add') .'" class="btn btn-secondary"/><br />
                        <input name="remove" id="remove" type="submit" value="'. get_string('remove').'&nbsp;'.$OUTPUT->rarrow(). '"
                               title="'. print_string('remove') .'" class="btn btn-secondary"/><br />
                     </p>
                  </td>
                  <td id="potentialcell">');

            $mform->addElement('html', $this->potentialusers->display(true));

            $mform->addElement('html', '
                  </td>
                </tr>
              </table>');
        } else {
            $mform->addElement('html', get_string('nousers', 'block_iomad_company_admin').
            ' <a href="'.
            new moodle_url('/blocks/iomad_company_admin/company_user_create_form.php?companyid='.
            $this->selectedcompany). '">Create one now</a>');
        }
    }

    public function process() {
        global $DB, $USER, $OUTPUT;

        if ($this->selectedcompany) {
            $company = new company($this->selectedcompany);
            $companyshortname = $company->get_shortname();
            $companydefaultdepartment = company::get_company_parentnode($company->id);

            // Process incoming assignments.
            if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
                $userstoassign = $this->potentialusers->get_selected_users();
                if (!empty($userstoassign)) {

                    // Check if the company has gone over the user quota.
                     $company = new company($this->selectedcompany);
                     if (!$company->check_usercount(count($userstoassign))) {
                        $maxusers = $company->get('maxusers');
                        $returnurl = new moodle_url('/blocks/iomad_company_admin/company_users_form.php');
                        print_error('maxuserswarning', 'block_iomad_company_admin', $returnurl, $maxusers);
                    }

                    // Process them.
                    foreach ($userstoassign as $adduser) {
                        $allow = true;

                        if ($allow) {
                            $user = $DB->get_record('user', array('id' => $adduser->id));
                            // Add user to default company department.
                            $company->assign_user_to_company($adduser->id);

                            \core\event\user_updated::create_from_userid($adduser->id)->trigger();

                            // Fire an event for this.
                            $eventother = array('companyid' => $company->id,
                                                'companyname' => $companyshortname,
                                                'usertype' => 0,
                                                'usertypename' => '',
                                                'oldcompany' => json_encode(array()));

                            $event = \block_iomad_company_admin\event\company_user_assigned::create(array('context' => context_system::instance(),
                                                                                                          'userid' => $USER->id,
                                                                                                          'objectid' => $company->id,
                                                                                                          'relateduserid' => $adduser->id,
                                                                                                           'other' => $eventother));
                            $event->trigger();
                        }
                    }

                    $this->potentialusers->invalidate_selected_users();
                    $this->currentusers->invalidate_selected_users();
                }
            }

            // Process incoming unassignments.
            if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
                $company = new company($this->selectedcompany);
                $userstounassign = $this->currentusers->get_selected_users();
                if (!empty($userstounassign)) {
                    foreach ($userstounassign as $removeuser) {
                        // Remove the user from the company.
                        $company->unassign_user_from_company($removeuser->id);

                        // Fire the user updated event.
                        \core\event\user_updated::create_from_userid($removeuser->id)->trigger();

                        // Fire an event for this.
                        $eventother = array('companyid' => 0,
                                            'companyname' => '',
                                            'usertype' => 0,
                                            'usertypename' => '',
                                            'oldcompany' => json_encode($company));

                        $event = \block_iomad_company_admin\event\company_user_unassigned::create(array('context' => context_system::instance(),
                                                                                                      'userid' => $USER->id,
                                                                                                      'objectid' => 0,
                                                                                                      'relateduserid' => $removeuser->id,
                                                                                                       'other' => $eventother));
                        $event->trigger();
                    }

                    $this->potentialusers->invalidate_selected_users();
                    $this->currentusers->invalidate_selected_users();
                }
            }
        }

    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_user', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assignusers', 'block_iomad_company_admin');
// Set the url..
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_users_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$usersform = new company_users_form($PAGE->url, $context, $companyid);

if ($usersform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $usersform->process();

    echo $OUTPUT->header();

    echo $usersform->display();

    echo $OUTPUT->footer();
}
