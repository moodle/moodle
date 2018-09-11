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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(dirname(__FILE__).'/imageclass.php');

/**
 * Prints a particular instance of lightboxgallery
 *
 * @package   mod_lightboxgallery
 * @author    Adam Olley <adam.olley@netspot.com.au>
 * @copyright 2012 NetSpot Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lightboxgallery_imageadd_form extends moodleform {

    public function definition() {

        global $COURSE, $cm;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('addimage', 'lightboxgallery'));

        $mform->addElement('filemanager', 'image', get_string('file'), '0',
                           array('maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('web_image', 'application/zip')));
        $mform->addRule('image', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('image', 'addimage', 'lightboxgallery');

        if ($this->can_resize()) {
            $resizegroup = array();
            $resizegroup[] = &$mform->createElement('select', 'resize', get_string('edit_resize', 'lightboxgallery'),
                                                    lightboxgallery_resize_options());
            $resizegroup[] = &$mform->createElement('checkbox', 'resizedisabled', null, get_string('disable'));
            $mform->setType('resize', PARAM_INT);
            $mform->addGroup($resizegroup, 'resizegroup', get_string('edit_resize', 'lightboxgallery'), ' ', false);
            $mform->setDefault('resizedisabled', 1);
            $mform->disabledIf('resizegroup', 'resizedisabled', 'checked');
            $mform->setAdvanced('resizegroup');
        }

        $mform->addElement('hidden', 'id', $cm->id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('addimage', 'lightboxgallery'));

    }

    public function validation($data, $files) {
        global $USER;

        if ($errors = parent::validation($data, $files)) {
            return $errors;
        }

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();

        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['image'], 'id', false)) {
            $errors['image'] = get_string('required');
            return $errors;
        } else {
            $file = reset($files);
            if ($file->get_mimetype() != 'application/zip' && !$file->is_valid_image()) {
                $errors['image'] = get_string('invalidfiletype', 'error', $file->get_filename());
                // Better delete current file, it is not usable anyway.
                $fs->delete_area_files($usercontext->id, 'user', 'draft', $data['image']);
            }
        }

        return $errors;
    }


    private function can_resize() {
        $gallery = $this->_customdata['gallery'];
        return !in_array($gallery->autoresize, array(AUTO_RESIZE_UPLOAD, AUTO_RESIZE_BOTH));
    }
}
