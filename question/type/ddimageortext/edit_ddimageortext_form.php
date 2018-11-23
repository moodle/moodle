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
 * Defines the editing form for the drag-and-drop images onto images question type.
 *
 * @package   qtype_ddimageortext
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/ddimageortext/edit_ddtoimage_form_base.php');


/**
 * Drag-and-drop images onto images  editing form definition.
 *
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_edit_form extends qtype_ddtoimage_edit_form_base {
    public function qtype() {
        return 'ddimageortext';
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_combined_feedback($question, true);
        $question = $this->data_preprocessing_hints($question, true, true);

        $dragids = array(); // Drag no -> dragid.
        if (!empty($question->options)) {
            $question->shuffleanswers = $question->options->shuffleanswers;
            $question->drags = array();
            foreach ($question->options->drags as $drag) {
                $dragindex = $drag->no - 1;
                $question->drags[$dragindex] = array();
                $question->draglabel[$dragindex] = $drag->label;
                $question->drags[$dragindex]['infinite'] = $drag->infinite;
                $question->drags[$dragindex]['draggroup'] = $drag->draggroup;
                $dragids[$dragindex] = $drag->id;
            }
            $question->drops = array();
            foreach ($question->options->drops as $drop) {
                $question->drops[$drop->no - 1] = array();
                $question->drops[$drop->no - 1]['choice'] = $drop->choice;
                $question->drops[$drop->no - 1]['droplabel'] = $drop->label;
                $question->drops[$drop->no - 1]['xleft'] = $drop->xleft;
                $question->drops[$drop->no - 1]['ytop'] = $drop->ytop;
            }
        }
        // Initialise file picker for bgimage.
        $draftitemid = file_get_submitted_draft_itemid('bgimage');

        file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_ddimageortext',
                                'bgimage', !empty($question->id) ? (int) $question->id : null,
                                self::file_picker_options());
        $question->bgimage = $draftitemid;

        // Initialise file picker for dragimages.
        list(, $imagerepeats) = $this->get_drag_item_repeats();
        $draftitemids = optional_param_array('dragitem', array(), PARAM_INT);
        for ($imageindex = 0; $imageindex < $imagerepeats; $imageindex++) {
            $draftitemid = isset($draftitemids[$imageindex]) ? $draftitemids[$imageindex] : 0;
            // Numbers not allowed in filearea name.
            $itemid = isset($dragids[$imageindex]) ? $dragids[$imageindex] : null;
            file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_ddimageortext',
                                'dragimage', $itemid, self::file_picker_options());
            $question->dragitem[$imageindex] = $draftitemid;
        }
        if (!empty($question->options)) {
            foreach ($question->options->drags as $drag) {
                $dragindex = $drag->no - 1;
                if (!isset($question->dragitem[$dragindex])) {
                    $fileexists = false;
                } else {
                    $fileexists = self::file_uploaded($question->dragitem[$dragindex]);
                }
                $labelexists = (trim($question->draglabel[$dragindex]) != '');
                if ($labelexists && !$fileexists) {
                    $question->drags[$dragindex]['dragitemtype'] = 'word';
                } else {
                    $question->drags[$dragindex]['dragitemtype'] = 'image';
                }
            }
        }
        $this->js_call();

        return $question;
    }


    public function js_call() {
        global $PAGE;

        $maxbgimagesize = [
                'width' => QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXWIDTH,
                'height' => QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXHEIGHT
        ];
        $maxdragimagesize = [
                'width' => QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXWIDTH,
                'height' => QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXHEIGHT
        ];
        $PAGE->requires->js_call_amd('qtype_ddimageortext/form', 'init',
                [$maxbgimagesize, $maxdragimagesize]);
    }

    // Drag items.

    protected function definition_draggable_items($mform, $itemrepeatsatstart) {
        $mform->addElement('header', 'draggableitemheader',
                                get_string('draggableitems', 'qtype_ddimageortext'));
        $mform->addElement('advcheckbox', 'shuffleanswers', ' ',
                                get_string('shuffleimages', 'qtype_'.$this->qtype()));
        $mform->setDefault('shuffleanswers', 0);
        $this->repeat_elements($this->draggable_item($mform), $itemrepeatsatstart,
                $this->draggable_items_repeated_options(),
                'noitems', 'additems', self::ADD_NUM_ITEMS,
                get_string('addmoreimages', 'qtype_ddimageortext'), true);
    }

    protected function draggable_item($mform) {
        $draggableimageitem = array();

        $grouparray = array();
        $dragitemtypes = array('image' => get_string('draggableimage', 'qtype_ddimageortext'),
                                'word' => get_string('draggableword', 'qtype_ddimageortext'));
        $grouparray[] = $mform->createElement('select', 'dragitemtype',
                                            get_string('draggableitemtype', 'qtype_ddimageortext'),
                                            $dragitemtypes,
                                            array('class' => 'dragitemtype'));
        $options = array();
        for ($i = 1; $i <= self::MAX_GROUPS; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('select', 'draggroup',
                                                get_string('group', 'qtype_gapselect'),
                                                $options,
                                                array('class' => 'draggroup'));
        $grouparray[] = $mform->createElement('advcheckbox', 'infinite', ' ',
                get_string('infinite', 'qtype_ddimageortext'));
        $draggableimageitem[] = $mform->createElement('group', 'drags',
                get_string('draggableitemheader', 'qtype_ddimageortext', '{no}'), $grouparray);

        $draggableimageitem[] = $mform->createElement('filepicker', 'dragitem', '', null,
                                    self::file_picker_options());

        $draggableimageitem[] = $mform->createElement('text', 'draglabel',
                                                get_string('label', 'qtype_ddimageortext'),
                                                array('size' => 30, 'class' => 'tweakcss draglabel'));
        $mform->setType('draglabel', PARAM_RAW); // These are validated manually.
        return $draggableimageitem;
    }

    protected function draggable_items_repeated_options() {
        $repeatedoptions = array();
        $repeatedoptions['draggroup']['default'] = '1';
        return $repeatedoptions;
    }

    // Drop zones.

    protected function drop_zone($mform, $imagerepeats) {
        $dropzoneitem = array();

        $grouparray = array();
        $grouparray[] = $mform->createElement('text', 'xleft',
                                                get_string('xleft', 'qtype_ddimageortext'),
                                                array('size' => 5, 'class' => 'tweakcss'));
        $grouparray[] = $mform->createElement('text', 'ytop',
                                                get_string('ytop', 'qtype_ddimageortext'),
                                                array('size' => 5, 'class' => 'tweakcss'));
        $options = array();

        $options[0] = '';
        for ($i = 1; $i <= $imagerepeats; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('select', 'choice',
                                    get_string('draggableitem', 'qtype_ddimageortext'), $options);
        $grouparray[] = $mform->createElement('text', 'droplabel',
                                                get_string('label', 'qtype_ddimageortext'),
                                                array('size' => 10, 'class' => 'tweakcss'));
        $mform->setType('droplabel', PARAM_NOTAGS);
        $dropzone = $mform->createElement('group', 'drops',
                get_string('dropzone', 'qtype_ddimageortext', '{no}'), $grouparray);
        return array($dropzone);
    }

    protected function drop_zones_repeated_options() {
        $repeatedoptions = array();
        // The next two are PARAM_RAW becuase we need to distinguish 0 and ''.
        // We do the necessary validation in the validation method.
        $repeatedoptions['drops[xleft]']['type']     = PARAM_RAW;
        $repeatedoptions['drops[ytop]']['type']      = PARAM_RAW;
        $repeatedoptions['drops[droplabel]']['type'] = PARAM_RAW;
        $repeatedoptions['choice']['default'] = '0';
        return $repeatedoptions;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!self::file_uploaded($data['bgimage'])) {
            $errors["bgimage"] = get_string('formerror_nobgimage', 'qtype_'.$this->qtype());
        }

        $allchoices = array();
        for ($i = 0; $i < $data['nodropzone']; $i++) {
            $ytoppresent = (trim($data['drops'][$i]['ytop']) !== '');
            $xleftpresent = (trim($data['drops'][$i]['xleft']) !== '');
            $ytopisint = (string) clean_param($data['drops'][$i]['ytop'], PARAM_INT) === trim($data['drops'][$i]['ytop']);
            $xleftisint = (string) clean_param($data['drops'][$i]['xleft'], PARAM_INT) === trim($data['drops'][$i]['xleft']);
            $labelpresent = (trim($data['drops'][$i]['droplabel']) !== '');
            $choice = $data['drops'][$i]['choice'];
            $imagechoicepresent = ($choice !== '0');

            if ($imagechoicepresent) {
                if (!$ytoppresent) {
                    $errors["drops[$i]"] = get_string('formerror_noytop', 'qtype_ddimageortext');
                } else if (!$ytopisint) {
                    $errors["drops[$i]"] = get_string('formerror_notintytop', 'qtype_ddimageortext');
                }
                if (!$xleftpresent) {
                    $errors["drops[$i]"] = get_string('formerror_noxleft', 'qtype_ddimageortext');
                } else if (!$xleftisint) {
                    $errors["drops[$i]"] = get_string('formerror_notintxleft', 'qtype_ddimageortext');
                }

                if ($data['drags'][$choice - 1]['dragitemtype'] != 'word' &&
                                        !self::file_uploaded($data['dragitem'][$choice - 1])) {
                    $errors['dragitem['.($choice - 1).']'] =
                                    get_string('formerror_nofile', 'qtype_ddimageortext', $i);
                }

                if (isset($allchoices[$choice]) && !$data['drags'][$choice - 1]['infinite']) {
                    $errors["drops[$i]"] =
                            get_string('formerror_multipledraginstance', 'qtype_ddimageortext', $choice);
                    $errors['drops['.($allchoices[$choice]).']'] =
                            get_string('formerror_multipledraginstance', 'qtype_ddimageortext', $choice);
                    $errors['drags['.($choice - 1).']'] =
                            get_string('formerror_multipledraginstance2', 'qtype_ddimageortext', $choice);
                }
                $allchoices[$choice] = $i;
            } else {
                if ($ytoppresent || $xleftpresent || $labelpresent) {
                    $errors["drops[$i]"] =
                            get_string('formerror_noimageselected', 'qtype_ddimageortext');
                }
            }
        }
        for ($dragindex = 0; $dragindex < $data['noitems']; $dragindex++) {
            $label = $data['draglabel'][$dragindex];
            if ($data['drags'][$dragindex]['dragitemtype'] == 'word') {
                $allowedtags = '<br><sub><sup><b><i><strong><em>';
                $errormessage = get_string('formerror_disallowedtags', 'qtype_ddimageortext');
            } else {
                $allowedtags = '';
                $errormessage = get_string('formerror_noallowedtags', 'qtype_ddimageortext');
            }
            if ($label != strip_tags($label, $allowedtags)) {
                $errors["drags[{$dragindex}]"] = $errormessage;
            }

        }
        return $errors;
    }
}
