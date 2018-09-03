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
 * Form for editing tag_youtube block instances.
 *
 * @package    block_tag_youtube
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing tag_youtube block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_tag_youtube_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_tag_youtube'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_numberofvideos', get_string('numberofvideos', 'block_tag_youtube'), array('size' => 5));
        $mform->setType('config_numberofvideos', PARAM_INT);

        $categorychoices = $this->block->get_categories();
        $mform->addElement('select', 'config_category', get_string('category', 'block_tag_youtube'), $categorychoices);
        $mform->setDefault('config_category', 0);

        $mform->addElement('text', 'config_playlist', get_string('includeonlyvideosfromplaylist', 'block_tag_youtube'));
        $mform->setType('config_playlist', PARAM_ALPHANUM);
    }
}
