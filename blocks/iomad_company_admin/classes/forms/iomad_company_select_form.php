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

class iomad_company_select_form extends moodleform {
    protected $companies = array();

    public function __construct($actionurl, $companies = array(), $selectedcompany = 0) {
        global $USER, $DB;
        if (empty($selectedcompany) || empty($companies[$selectedcompany])) {
            $this->companies = array(0 => get_string('selectacompany', 'block_iomad_company_selector')) + $companies;
        } else {
            $this->companies = $companies;
        }

        parent::__construct($actionurl);
    }

    public function definition() {
        $mform =& $this->_form;
        $autooptions = array('onchange' => 'this.form.submit()');
        $mform->addElement('autocomplete', 'company', get_string('selectacompany', 'block_iomad_company_selector'), $this->companies, $autooptions);
        $mform->addElement('hidden', 'showsuspendedcompanies');
        $mform->setType('showsuspendedcompanies', PARAM_BOOL);

        // Disable the onchange popup.
        $mform->disable_form_change_checker();

    }
}