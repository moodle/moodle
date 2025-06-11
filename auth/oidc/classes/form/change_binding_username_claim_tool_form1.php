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
 * Change binding username claim tool form 1.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\form;

use core_text;
use csv_import_reader;
use html_writer;
use moodle_url;
use moodleform;

/**
 * Class change_binding_username_claim_tool_form1 represents the form on the change binding username claim tool page.
 */
class change_binding_username_claim_tool_form1 extends moodleform {
    /**
     * Form definition.
     *
     * @return void
     */
    protected function definition() {
        $mform =& $this->_form;

        $url = new moodle_url('/auth/oidc/example.csv');
        $link = html_writer::link($url, 'example.csv');
        $mform->addElement('static', 'example.csv', get_string('examplecsv', 'auth_oidc'), $link);

        $mform->addElement('filepicker', 'usernamefile', get_string('usernamefile', 'auth_oidc'));
        $mform->addRule('usernamefile', null, 'required', null, 'client');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'auth_oidc'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'auth_oidc'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = ['10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000];
        $mform->addElement('select', 'previewrowsl', get_string('rowpreviewnum', 'auth_oidc'), $choices);
        $mform->setDefault('previewrowsl', 10);

        $this->add_action_buttons(false, get_string('upload_usernames', 'auth_oidc'));
    }
}
