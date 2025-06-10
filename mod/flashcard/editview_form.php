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
 * A form to edit one card or adding one or three cards
 *
 * @package     mod_flashcard
 * @category    mod
 * @author      Valery Fremaux (valery.fremaux@gmail.com) http://www.mylearningfactory.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class CardEdit_Form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        // Course module id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // MVC Action keyword.
        $mform->addElement('hidden', 'what', $this->_customdata['cmd']);
        $mform->setType('what', PARAM_TEXT);

        $num = 1;
        if ($this->_customdata['cmd'] == 'addthree') {
            $num = 3;
        }

        // Card id.
        if (!empty($this->_customdata['cardid'])) {
            $mform->addElement('hidden', 'cardid');
            $mform->setType('cardid', PARAM_INT);
        }

        for ($i = 0; $i < $num; $i++) {
            $cardnum = $i + 1;
            $mform->addElement('header', 'card'.$i, get_string('card', 'flashcard'). ' '.$cardnum);
            $mform->addElement('html', '<table width=100%">');
            $mform->addElement('html', '<tr><td width="50%">');

            $this->build_card_element('question', $i);

            $mform->addElement('html', '</td><td width="50%">');

            $this->build_card_element('answer', $i);

            $mform->addElement('html', '</td></tr></table>');
        }

        $this->add_action_buttons(true);
    }

    /**
     * Builds an appropriate card content form widget depending on card content media type
     * @param text $side 'front' or 'back'
     * @param $podid id of the card item in the form
     */
    protected function build_card_element($side, $podid) {
        global $COURSE;

        $mform = $this->_form;

        $sideprefix = substr($side, 0, 1);
        $key = $side.'smediatype';
        $mediatype = $this->_customdata['flashcard']->$key;

        if ($mediatype == FLASHCARD_MEDIA_IMAGE) {
            $options = array('maxfiles' => 1, 'maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('.jpg', '.png', '.gif'));
            $mform->addElement('filepicker', $sideprefix.$podid, '', null, $options);
        } else if ($mediatype == FLASHCARD_MEDIA_SOUND) {
            $options = array('maxfiles' => 1, 'maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('.mp3', '.swf'));
            $mform->addElement('filepicker', $sideprefix.$podid, '', null, $options);
        } else if ($mediatype == FLASHCARD_MEDIA_VIDEO) {
            $options = array('maxfiles' => 1, 'maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('.mp4', '.flv'));
            $mform->addElement('filepicker', $sideprefix.$podid, '', null, $options);
        } else if ($mediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
            $options = array('maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('.jpg', '.png', '.gif'));
            $mform->addElement('filepicker', $sideprefix.'i'.$podid, get_string('image', 'flashcard'), null, $options);
            $options = array('maxbytes' => $COURSE->maxbytes, 'accepted_types' => array('.mp3', '.swf'));
            $mform->addElement('filepicker', $sideprefix.'s'.$podid, get_string('sound', 'flashcard'), null, $options);
        } else {
            $mform->addElement('textarea', $sideprefix.$podid, '', array('cols' => 60, 'rows' => 4));
        }
    }

    /**
     * preloads existing images
     *
     */
    public function set_data($data) {
        global $DB;

        if ($cardid = $this->_customdata['cardid']) {
            $card = $DB->get_record('flashcard_deckdata', array('id' => $cardid));

            $flashcard = $this->_customdata['flashcard'];

            if ($flashcard->questionsmediatype != FLASHCARD_MEDIA_TEXT) {
                $this->setup_card_filearea($card, 'question', $flashcard->questionsmediatype, $data);
            } else {
                $data->q0 = $card->questiontext;
            }

            if ($flashcard->answersmediatype != FLASHCARD_MEDIA_TEXT) {
                $this->setup_card_filearea($card, 'answer', $flashcard->answersmediatype, $data);
            } else {
                $data->a0 = $card->answertext;
            }
        }

        parent::set_data($data);
    }

    /**
     * prepares preloaded file area with existing file, or new filearea for the filepicker
     *
     */
    protected function setup_card_filearea(&$card, $side, $mediatype, &$data) {
        global $COURSE;

        $sideprefix = substr($side, 0, 1);
        $cmid = $this->_customdata['cmid'];
        $context = context_module::instance($cmid);

        switch ($mediatype) {
            case FLASHCARD_MEDIA_IMAGE:
                $filearea = $side.'imagefile';
                break;

            case FLASHCARD_MEDIA_SOUND:
                $filearea = $side.'soundfile';
                break;

            case FLASHCARD_MEDIA_VIDEO:
                $filearea = $side.'videofile';
                break;

            case FLASHCARD_MEDIA_IMAGE_AND_SOUND:
                $filearea = $side.'imagefile';
                $filearea2 = $side.'soundfile';
                break;

            default:
                print_error('errorunsupportedformat', 'flashcard');
        }

        $fileoptions = array('subdirs' => 0, 'maxbytes' => $COURSE->maxbytes, 'maxfiles' => 1);

        if ($mediatype != FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
            $elmname = $sideprefix.'0';
            $draftitemid = file_get_submitted_draft_itemid($elmname);
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', $filearea, $card->id, $fileoptions);
            $data->$elmname = $draftitemid;
        } else if ($mediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
            $elmname = $sideprefix.'i0';
            $draftitemid = file_get_submitted_draft_itemid($elmname);
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', $filearea, $card->id, $fileoptions);
            $data->$elmname = $draftitemid;

            $elmname = $sideprefix.'s0';
            $draftitemid = file_get_submitted_draft_itemid($elmname);
            $maxbytes = 100000;
            file_prepare_draft_area($draftitemid, $context->id, 'mod_flashcard', $filearea2, $card->id, $fileoptions);
            $data->$elmname = $draftitemid;
        }
    }
}