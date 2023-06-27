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
 * Form to upload an image to be shown as tile background.
 *
 * @package format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/


namespace format_tiles\form;
defined('MOODLE_INTERNAL') || die();
use moodleform;
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

/**
 * Class upload_image_form
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_image_form extends moodleform {

    /**
     * Define fields and form.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        // Visible elements.

        $mform->addElement('header', 'guidance', get_string('guidance', 'format_tiles'));
        $mform->addElement('html', \html_writer::div(
            get_string('photoguidance_desc', 'format_tiles'),
            'photoguidance mt-3 mb-3'
            )
        );
        $mform->setExpanded('guidance', true);

        $existingphotourl = isset($instance['existingurl']) ? $instance['existingurl'] : '';
        if ($existingphotourl) {
            $mform->addElement('header', 'headertag', get_string('existingimage', 'format_tiles'));
            $formheading = '';
            $formheading .= \html_writer::div(
                \html_writer::img(
                    $existingphotourl,
                    get_string('existingimage', 'format_tiles'), array('class' => 'existingtilephoto')
                )
            );
            if ($instance['aspectratiomessage']) {
                $formheading .= \html_writer::div($instance['aspectratiomessage']);
            }
            $formheading .= \html_writer::div(
                \html_writer::link(
                    new \moodle_url('/course/format/tiles/editimage.php',
                        array('delete' => 1, 'courseid' => $instance['courseid'], 'sectionid' => $instance['sectionid'])),
                    get_string('deleteimage', 'format_tiles'),
                    array('class' => 'btn btn-secondary')
                ),
                'mt-2 mb-2'
            );
            $mform->addElement('html', $formheading);
            $mform->setExpanded('headertag', false);
        }

        $mform->addElement('header', 'uploadnewphotoheader', get_string('uploadnewphoto', 'format_tiles'));

        $mform->addElement(
            'filepicker',
            'tileimagefile',
            get_string('uploadnewphoto', 'format_tiles'),
            null,
            $instance['options']
        );

        $mform->addHelpButton('tileimagefile', 'uploadnewphoto', 'format_tiles');
        $mform->setExpanded('uploadnewphotoheader', true);

        // Hidden params.
        $mform->addElement('hidden', 'courseid', $instance['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'contextid', $instance['contextid']);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'sectionid', $instance['sectionid']);
        $mform->setType('sectionid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'uploadfile');
        $mform->setType('action', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
}
