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
 * @package   local_iomad
 * @copyright 2023 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
use \moodleform;

/**
 * Course search/select form used on the IOMAD pages.
 *
 */
class course_select_form extends moodleform {
    protected $params = array();

    public function __construct($url, $params) {
        $this->params = $params;

        parent::__construct();
    }

    public function definition() {
        global $CFG, $DB, $USER, $SESSION, $company;

        $mform =& $this->_form;
        foreach ($this->params as $param => $value) {
            if ($param == 'courses') {
                continue;
            }
            $mform->addElement('hidden', $param, $value);
            $mform->setType($param, PARAM_CLEAN);
        }

        $courses = $company->get_menu_courses(true, false, false, false, false);
        $autooptions = array('multiple' => true);
        $sarcharray = array();
        $searcharray[] = $mform->createElement('autocomplete', 'courses', get_string('selectlicensecourse', 'block_iomad_company_admin'), $courses, $autooptions);
        $searcharray[] = $mform->createElement('submit', 'searchbutton', get_string('coursenamesearch', 'block_iomad_company_admin'));
        $mform->addGroup($searcharray, 'searcharray', '', ' ', false);
        $mform->setType('coursesearch', PARAM_CLEAN);
    }
}
