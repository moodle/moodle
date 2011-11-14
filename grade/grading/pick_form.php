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
 * Defines forms used by pick.php
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Allows to search for a specific shared template
 */
class grading_search_template_form extends moodleform {

    /**
     * Pretty simple search box
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'searchheader', get_string('searchtemplate', 'core_grading'));
        $mform->addHelpButton('searchheader', 'searchtemplate', 'core_grading');
        $mform->addGroup(array(
            $mform->createElement('checkbox', 'mode', '', get_string('searchownforms', 'core_grading')),
            $mform->createElement('text', 'needle', '', array('size' => 30)),
            $mform->createElement('submit', 'submitbutton', get_string('search')),
        ), 'buttonar', '', array(' '), false);
        $mform->setType('needle', PARAM_TEXT);
        $mform->setType('buttonar', PARAM_RAW);
    }
}
