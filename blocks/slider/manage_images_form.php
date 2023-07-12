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
 * Simple slider block for Moodle
 *
 * If You like my plugin please send a small donation https://paypal.me/limsko Thanks!
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Class add_slider_image
 */
class add_slider_image extends moodleform {

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore!
        $context = context_block::instance($this->_customdata['sliderid']);

        $mform->addElement('hidden', 'view', 'manage');
        $mform->setType('view', PARAM_NOTAGS);

        if (!empty($this->_customdata['id'])) {
            $id = $this->_customdata['id'];
            $mform->addElement('hidden', 'id', $this->_customdata['id']);
            $mform->setType('id', PARAM_INT);
            $mform->addElement('header', 'events', get_string('modify_slide', 'block_slider'));
        } else {
            $mform->addElement('header', 'events', get_string('add_slide', 'block_slider'));
        }

        $mform->addElement('hidden', 'sliderid', $this->_customdata['sliderid']);
        $mform->setType('sliderid', PARAM_INT);

        $mform->addElement('text', 'slide_link', get_string('slide_url', 'block_slider'));
        $mform->setType('slide_link', PARAM_URL);

        $mform->addElement('text', 'slide_title', get_string('slide_title', 'block_slider'));
        $mform->setType('slide_title', PARAM_NOTAGS);

        $mform->addElement('text', 'slide_desc', get_string('slide_desc', 'block_slider'));
        $mform->setType('slide_desc', PARAM_NOTAGS);

        for ($i = -10; $i <= 10; $i++) {
            $orderarray[$i] = $i;
        }
        $mform->addElement('select', 'slide_order', get_string('slide_order', 'block_slider'), $orderarray);
        $mform->setType('slide_order', PARAM_INT);
        $mform->setDefault('slide_order', 0);

        $maxbytes = 2 * 1024 * 1024;

        if (!isset($id) or $id == null) {
            $mform->addElement('filepicker', 'slide_image', get_string('slide_image', 'block_slider'), null,
                    array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg, png, gif, jpeg'));
            $mform->addRule('slide_image', null, 'required');
        } else {
            // Display actual photo.
            $slideimage = $DB->get_field('slider_slides', 'slide_image', array('id' => $id));
            $url = $CFG->wwwroot . '/pluginfile.php/' . $context->id . '/block_slider/slider_slides/' . $id . '/' . $slideimage;
            $img = html_writer::empty_tag('img', array('src' => $url, 'class' => 'img-responsive'));

            $mform->addElement('static', 'aktualne', get_string('slide_image', 'block_slider'), $img);
            $mform->addElement('filepicker', 'slide_image', get_string('new_slide_image', 'block_slider'), null,
                    array('maxbytes' => $maxbytes, 'accepted_types' => 'jpg, png, gif, jpeg'));

        }

        $this->add_action_buttons();
    }

    /**
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        return array();
    }
}


