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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

defined('MOODLE_INTERNAL') || die;

use \company_moodleform;
use \company;
use \microlearning;

class microlearning_threads_form extends company_moodleform {
    protected $context = null;
    protected $selectedcompany = 0;
    protected $selectedthread = 0;
    protected $company = null;
    protected $departmentid = 0;
    protected $threads = array();

    public function __construct($actionurl, $context, $companyid, $departmentid, $selectedthread) {
        global $DB, $USER;
        $this->departmentid = $departmentid;
        $this->selectedcompany = $companyid;
        $this->company = new \company($companyid);
        $this->context = $context;
        $this->selectedthread = $selectedthread;
        $this->threads = \microlearning::get_menu_threads($companyid);
        parent::__construct($actionurl);
    }

    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'companyid', $this->selectedcompany);
        $mform->setType('companyid', PARAM_INT);
        $mform->addElement('hidden', 'deptid', $this->departmentid);
        $mform->setType('deptid', PARAM_INT);

        $autooptions = array('setmultiple' => false,
                             'noselectionstring' => get_string('selectthread', 'block_iomad_microlearning'),
                             'onchange' => 'this.form.submit()');

        if ($this->threads) {
            $mform->addElement('autocomplete', 'threadid', get_string('selectthread', 'block_iomad_microlearning'), $this->threads, $autooptions);
        } else {
            $mform->addElement('html', '<div class="alert alert-warning">' . get_string('nothreads', 'block_iomad_microlearning') . '</div>');
        }
        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }
}
