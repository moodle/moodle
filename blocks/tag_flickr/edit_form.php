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
 * Form for editing tag_flickr block instances.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing tag_flickr block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_tag_flickr_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_tag_flickr'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_numberofphotos', get_string('numberofphotos', 'block_tag_flickr'), array('size' => 5));
        $mform->setType('config_numberofvideos', PARAM_INT);

        $mform->addElement('selectyesno', 'config_includerelatedtags', get_string('includerelatedtags', 'block_tag_flickr'));
        $mform->setDefault('config_includerelatedtags', 0);

        $sortoptions = array(
            'date-posted-asc'  => get_string('date-posted-asc', 'block_tag_flickr'),
            'date-posted-desc' => get_string('date-posted-desc', 'block_tag_flickr'),
            'date-taken-asc' => get_string('date-taken-asc', 'block_tag_flickr'),
            'date-taken-desc' => get_string('date-taken-desc', 'block_tag_flickr'),
            'interestingness-asc' => get_string('interestingness-asc', 'block_tag_flickr'),
            'interestingness-desc' => get_string('interestingness-desc', 'block_tag_flickr'),
            'relevance' => get_string('relevance', 'block_tag_flickr'),
        );
        $mform->addElement('select', 'config_sortby', get_string('sortby', 'block_tag_flickr'), $sortoptions);
        $mform->setDefault('config_sortby', 'relevance');

        $mform->addElement('text', 'config_photoset', get_string('getfromphotoset', 'block_tag_flickr'));
        $mform->setType('config_photoset', PARAM_ALPHANUM);
    }
}
