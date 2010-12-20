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
 * Lesson page without answers
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**
 * Include formslib if it has not already been included
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * Lesson page without answers
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class lesson_page_without_answers extends moodleform {

    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $title = $this->_customdata['title'];
        $contents = $this->_customdata['contents'];

        if (!empty($title)) {
            $mform->addElement('header', 'pageheader', $title);
        }

        if (!empty($contents)) {
            $mform->addElement('html', $OUTPUT->box($contents, 'contents'));
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        $mform->addElement('hidden', 'newpageid');
        $mform->setType('newpageid', PARAM_INT);

        $this->add_action_buttons(null, get_string("continue", "lesson"));

    }

}
