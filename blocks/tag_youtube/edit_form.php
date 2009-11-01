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
 * @package   moodlecore
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
        $mform->setType('config_title', PARAM_MULTILANG);

        $mform->addElement('text', 'config_numberofvideos', get_string('numberofvideos', 'block_tag_youtube'), array('size' => 5));
        $mform->setType('config_numberofvideos', PARAM_INTEGER);

        $categorychoices = array(
            0 => get_string('anycategory', 'block_tag_youtube'),
            1 => get_string('filmsanimation', 'block_tag_youtube'),
            2 => get_string('autosvehicles', 'block_tag_youtube'),
            23 => get_string('comedy', 'block_tag_youtube'),
            24 => get_string('entertainment', 'block_tag_youtube'),
            10 => get_string('music', 'block_tag_youtube'),
            25 => get_string('newspolitics', 'block_tag_youtube'),
            22 => get_string('peopleblogs', 'block_tag_youtube'),
            15 => get_string('petsanimals', 'block_tag_youtube'),
            26 => get_string('howtodiy', 'block_tag_youtube'),
            17 => get_string('sports', 'block_tag_youtube'),
            19 => get_string('travel', 'block_tag_youtube'),
            20 => get_string('gadgetsgames', 'block_tag_youtube'),
        );
        $mform->addElement('select', 'config_category', get_string('category', 'block_tag_youtube'), $categorychoices);
        $mform->setDefault('config_category', 0);

        $mform->addElement('text', 'config_playlist', get_string('includeonlyvideosfromplaylist', 'block_tag_youtube'));
        $mform->setType('config_playlist', PARAM_ALPHANUM);
    }
}
