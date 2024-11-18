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
 * @package   local_email
 * @copyright 2023 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create a course for a particular company.
 */

namespace local_email\forms;

use \moodleform;

// Set up the save form.
class template_search_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'manage');
        $mform->setType('manage', PARAM_INT);
        $mform->addElement('html', '<div class="templatesearchfform">');
        $searchgroup = [];
        $searchgroup[] = $mform->createElement('text', 'search');
        $searchgroup[] = $mform->createElement('submit', 'submitbutton', get_string('search'));
        $mform->addgroup($searchgroup, 'searchgroup', '', ' ', false);
        $mform->setType('search', PARAM_CLEAN);
        $mform->addElement('html', '</div>');
    }
}
