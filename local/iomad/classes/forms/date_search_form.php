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
 * @copyright 2024 Derick Turner
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
 * date search form used on the IOMAD pages.
 *
 */
class date_search_form extends moodleform {
    protected $params = [];

    public function __construct($url, $params) {
        $this->params = $params;
        parent::__construct();
    }

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        foreach ($this->params as $param => $value) {
            if ($param == 'compfrom' || $param == 'compto' || $param == 'yearfrom' || $param == 'yearto') {
                continue;
            }
            $mform->addElement('hidden', $param, $value);
            $mform->setType($param, PARAM_CLEAN);
        }

        $mform->addElement('header', 'datesearchheader', get_string('datesearchfields', 'local_iomad'));
        $mform->setExpanded('datesearchheader', false);
        if (empty($this->params['yearonly'])) {
            $dategroup =[];
            $dategroup[] = $mform->createElement('date_selector', 'compfromraw', get_string('compfromraw', 'block_iomad_company_admin'), ['optional' => 'yes']);
            $dategroup[] = $mform->createElement('html', '&nbsp');
            $dategroup[] = $mform->createElement('date_selector', 'comptoraw', get_string('comptoraw', 'block_iomad_company_admin'), ['optional' => 'yes']);
            $mform->addGroup($dategroup);
        } else {
            // Get the calendar type used - see MDL-18375.
            $calendartype = \core_calendar\type_factory::get_calendar_instance();
            $dateformat = $calendartype->get_date_order();
            $from = array();
            $from[] = $mform->createElement('select', 'yearfrom', get_string('compfromraw', 'block_iomad_company_admin'), $dateformat['year']);
            $from[] = $mform->createElement('checkbox', 'yearfromoptional', '', get_string('optional', 'form'));
            $mform->addGroup($from, 'fromarray', get_string('compfromraw', 'block_iomad_company_admin'));
            $to[] = $mform->createElement('select', 'yearto', get_string('comptoraw', 'block_iomad_company_admin'), $dateformat['year']);
            $to[] = $mform->createElement('checkbox', 'yeartooptional', '', get_string('optional', 'form'));
            $mform->addGroup($to, 'toarray', get_string('comptoraw', 'block_iomad_company_admin'));

            if (!empty($this->params['yearto'])) {
                $mform->setDefault('toarray[yearto]', $this->params['yearto']);
            } else {
                $mform->setDefault('toarray[yearto]', '2018');
            }

            if (!empty($this->params['yearfrom'])) {
                $mform->setDefault('fromarray[yearfrom]', $this->params['yearfrom']);
            } else {
                $mform->setDefault('fromarray[yearfrom]', '2018');
            }

            if (!empty($this->params['yearfromoptional'])) {
                $mform->setDefault('fromarray[yearfromoptional]', 'checked');
            }
            if (!empty($this->params['yeartooptional'])) {
                $mform->setDefault('toarray[yeartooptional]', 'checked');
            }
            $mform->disabledIf('fromarray', 'fromarray[yearfromoptional]');
            $mform->disabledIf('toarray', 'toarray[yeartooptional]');
        }

        // Add the button(s).
        $buttonarray=[];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('datefilter', 'block_iomad_company_admin'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
}
