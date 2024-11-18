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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\forms;

use \moodleform;
use \company;
use \potential_company_frameworks_selector;
use \current_company_frameworks_selector;
use \context_system;

class company_frameworks_form extends moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $potentialframeworks = null;
    protected $currentframeworks = null;
    protected $company = null;

    public function __construct($actionurl, $context, $companyid) {
        global $USER;
        $this->selectedcompany = $companyid;
        $this->context = $context;

        $this->company = new company($this->selectedcompany);
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
        global $OUTPUT;

        $mform =& $this->_form;

        // Adding the elements in the definition_after_data function rather than in the
        // definition function  so that when the currentframeworks or potentialframeworks get changed
        // in the process function, the changes get displayed, rather than the lists as they
        // are before processing.

        $context = context_system::instance();

        $mform->addElement('html', '<table summary="" class="companyframeworktable addremovetable'.
                                   ' generaltable generalbox boxaligncenter" cellspacing="0">
            <tr>
              <td id="existingcell">');

        $mform->addElement('html', $this->currentframeworks->display(true));

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

                foreach ($frameworkstounassign as $removeframework) {
                    company::remove_competency_framework($this->selectedcompany,$removeframework->id);
                }

                $this->potentialframeworks->invalidate_selected_frameworks();
                $this->currentframeworks->invalidate_selected_frameworks();
            }
        }
    }
}