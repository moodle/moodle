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
require_once($CFG->libdir . '/formslib.php');

class company_frameworks_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialframeworks = null;
    protected $currentframeworks = null;

    public function __construct($actionurl, $context, $companyid) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;

        $company = new company($this->selectedcompany);
        $syscontext = context_system::instance();

        $options = array('context' => $this->context,
                         'companyid' => $this->selectedcompany,
                         'shared' => false,
                         'partialshared' => true);
        $this->potentialframeworks = new potential_company_frameworks_selector('potentialframeworks',
                                                                         $options);
        $this->currentframeworks = new current_company_frameworks_selector('currentframeworks', $options);

        parent::__construct($actionurl);
    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->selectedcompany);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {
        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the
        // definition function  so that when the currentframeworks or potentialframeworks get changed
        // in the process function, the changes get displayed, rather than the lists as they
        // are before processing.

        $context = context_system::instance();
        $company = new company($this->selectedcompany);
        $mform->addElement('header', 'header', get_string('company_frameworks_for',
                                                          'block_iomad_company_admin',
                                                          $company->get_name() ));

        $mform->addElement('html', '<table summary="" class="companyframeworktable addremovetable'.
                                   ' generaltable generalbox boxaligncenter" cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentframeworks->display(true));

        $mform->addElement('html', '
              </td>
              <td id="buttonscell">
                  <div id="addcontrols">
                      <input name="add" id="add" type="submit" value="&nbsp;'.
                       get_string('add') . '" title="Add" /><br />

                  </div>

                  <div id="removecontrols">
                      <input name="remove" id="remove" type="submit" value="'.
                       get_string('remove') . '&nbsp;" title="Remove" />
                  </div>
              </td>
              <td id="potentialcell">');

        $mform->addElement('html', $this->potentialframeworks->display(true));

        $mform->addElement('html', '
              </td>
            </tr>
          </table>');
    }

    public function process() {
        global $DB;

        $context = context_system::instance();

        // Process incoming assignments.
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $frameworkstoassign = $this->potentialframeworks->get_selected_frameworks();
            if (!empty($frameworkstoassign)) {

                $company = new company($this->selectedcompany);

                foreach ($frameworkstoassign as $addframework) {
                    company::add_competency_framework($this->selectedcompany, $addframework->id);
                }

                $this->potentialframeworks->invalidate_selected_frameworks();
                $this->currentframeworks->invalidate_selected_frameworks();
            }
        }

        // Process incoming unassignments.
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
            $frameworkstounassign = $this->currentframeworks->get_selected_frameworks();
            if (!empty($frameworkstounassign)) {

                $company = new company($this->selectedcompany);

                foreach ($frameworkstounassign as $removeframework) {
                    company::remove_competency_framework($this->selectedcompany,$removeframework->id);
                }

                $this->potentialframeworks->invalidate_selected_frameworks();
                $this->currentframeworks->invalidate_selected_frameworks();
            }
        }
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_framework', $context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assigncompetencyframeworks', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_competency_frameworks_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$mform = new company_frameworks_form($PAGE->url, $context, $companyid);

if ($mform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $mform->process();

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}
