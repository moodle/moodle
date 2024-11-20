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

namespace core_h5p\form;

use core_h5p\editor;

/**
 * Form to edit an existing H5P content.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editcontent_form extends \moodleform {

    /** @var editor H5P editor object */
    private $editor;

    /**
     * The form definition.
     */
    public function definition() {

        $mform = $this->_form;
        $id = $this->_customdata['id'] ?? null;
        $contenturl = $this->_customdata['contenturl'] ?? null;
        $returnurl = $this->_customdata['returnurl'] ?? null;

        $editor = new editor();

        if ($id) {
            $mform->addElement('hidden', 'id', $id);
            $mform->setType('id', PARAM_INT);

            $editor->set_content($id);
        }

        if ($contenturl) {
            $mform->addElement('hidden', 'url', $contenturl);
            $mform->setType('url', PARAM_LOCALURL);
        }

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        $this->editor = $editor;
        $mformid = 'h5peditor';
        $mform->setAttributes(array('id' => $mformid) + $mform->getAttributes());

        $this->set_display_vertical();

        $this->add_action_buttons();

        $editor->add_editor_to_form($mform);

        $this->add_action_buttons();
    }

    /**
     * Updates an H5P content.
     *
     * @param \stdClass $data Object with all the H5P data.
     *
     * @return void
     */
    public function save_h5p(\stdClass $data): void {
        $this->editor->save_content($data);
    }
}
