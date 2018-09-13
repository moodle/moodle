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
 * Form for adding comments on a gallery
 *
 * @package   mod_lightboxgallery
 * @copyright 2010 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class mod_lightboxgallery_comment_form extends moodleform {

    public function definition() {

        $mform =& $this->_form;
        $gallery = $this->_customdata;

        $straddcomment = get_string('addcomment', 'lightboxgallery');

        $mform->addElement('editor', 'comment', $straddcomment, array('cols' => 85, 'rows' => 18));
        $mform->addRule('comment', get_string('required'), 'required', null, 'client');
        $mform->setType('comment', PARAM_RAW);

        $mform->addElement('hidden', 'id', 0);
        $mform->setDefault('id', $gallery->id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, $straddcomment);

    }
}
