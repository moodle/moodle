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

namespace qbank_managecategories\form;

use core_question\category_manager;
use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Form for moving questions between categories.
 *
 * @package    qbank_managecategories
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_move_form extends moodleform {

    /**
     * Build the form definition.
     *
     * This adds all the form fields that the question move feature needs.
     * @throws \coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        $currentcat = $this->_customdata['currentcat'];
        $contexts = $this->_customdata['contexts'];
        $allcount = $this->_customdata['allcount'];
        $inusecount = $this->_customdata['inusecount'];

        // Use an appropriate string depending on whether there are questions in use, or not.
        if ($inusecount > 0) {
            $movestring = get_string(
                'movequestions:inuse',
                'qbank_managecategories',
                $inusecount,
            );
            $moveoption = category_manager::MOVEINUSEQUESTIONS;
        } else {
            $movestring = get_string('none', 'moodle');
            $moveoption = category_manager::MOVENOQUESTIONS;
        }

        $mform->addElement(
            'select',
            'movequestions',
            get_string('movequestions', 'qbank_managecategories'),
            [
                category_manager::MOVEALLQUESTIONS => get_string(
                    'movequestions:all',
                    'qbank_managecategories',
                    $allcount,
                ),
                $moveoption => $movestring,
            ],
        );

        $mform->addElement(
            'questioncategory',
            'category',
            get_string('destinationcategory', 'qbank_managecategories'),
            compact('contexts', 'currentcat')
        );

        // Disable the category dropdown if user is not moving any questions.
        $mform->disabledIf('category', 'movequestions', 'eq', category_manager::MOVENOQUESTIONS);

        $this->add_action_buttons(true, get_string('deletecategory', 'qbank_managecategories'));

        $mform->addElement('hidden', 'delete', $currentcat);
        $mform->setType('delete', PARAM_INT);
    }
}
