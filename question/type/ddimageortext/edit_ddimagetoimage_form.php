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
 * @package    qtype
 * @subpackage ddimagetoimage
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Drag-and-drop images onto images  editing form definition.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimagetoimage_edit_form extends question_edit_form {
    const MAX_GROUPS = 8;
    const START_NUM_IMAGES = 6;
    const ADD_NUM_IMAGES = 3;

    public function qtype() {
        return 'ddimagetoimage';
    }

    /**
     *
     * Options shared by all file pickers in the form.
     */
    public static function file_picker_options() {
        $filepickeroptions = array();
        //$filepickeroptions['accepted_types'] = array('web_image');
        $filepickeroptions['accepted_types'] = array('*');
        $filepickeroptions['maxbytes'] = 0;
        $filepickeroptions['maxfiles'] = 1;
        $filepickeroptions['subdirs'] = 0;
        return $filepickeroptions;
    }

    protected function get_drag_image_repeats() {
        $countimages = 0;
        if (isset($this->question->id)) {
            foreach ($this->question->options->drags as $drag) {
                $countimages = max($countimages, $drag->no);
            }
        }
        if ($this->question->formoptions->repeatelements) {
            $imagerepeatsatstart = max(self::START_NUM_IMAGES, $countimages + self::ADD_NUM_IMAGES);
        } else {
            $imagerepeatsatstart = $countimages;
        }
        $imagerepeats = optional_param('noimages', $imagerepeatsatstart, PARAM_INT);
        $addfields = optional_param('addimages', '', PARAM_TEXT);
        if (!empty($addfields)){
            $imagerepeats += self::ADD_NUM_IMAGES;
        }
        return array($imagerepeatsatstart, $imagerepeats);
    }

    /**
     * definition_inner adds all specific fields to the form.
     * @param object $mform (the form being built).
     */
    protected function definition_inner($mform) {

        $previewareaheaderelement = $mform->createElement('header', 'previewareaheader',
                            get_string('previewareaheader', 'qtype_ddimagetoimage'));
        $mform->insertElementBefore($previewareaheaderelement, 'generalheader');
        $previewareaelement = $mform->createElement('static', 'previewarea',
                            get_string('previewarea', 'qtype_ddimagetoimage'),
                            get_string('previewareamessage', 'qtype_ddimagetoimage'));
        $mform->insertElementBefore($previewareaelement, 'generalheader');


        list($imagerepeatsatstart, $imagerepeats) = $this->get_drag_image_repeats();
        $this->definition_drop_zones($mform, $imagerepeats);
        $mform->addElement('checkbox', 'shuffleanswers', ' ',
                                        get_string('shuffleimages', 'qtype_ddimagetoimage'));
        $mform->setDefault('shuffleanswers', 0);
        $mform->closeHeaderBefore('shuffleanswers');
        //add the draggable image fields to the form
        $this->definition_draggable_images($mform, $imagerepeatsatstart);

        $this->add_combined_feedback_fields(true);
        $this->add_interactive_settings(true, true);
    }

    protected function definition_drop_zones($mform, $imagerepeats) {
        $mform->addElement('header', 'dropzoneheader',
                                    get_string('dropzoneheader', 'qtype_ddimagetoimage'));

        $mform->addElement('filepicker', 'bgimage', get_string('bgimage', 'qtype_ddimagetoimage'),
                                                               null, self::file_picker_options());


        $countdropzones = 0;
        if (isset($this->question->id)) {
            foreach ($this->question->options->drops as $drop) {
                $countdropzones = max($countdropzones, $drop->no);
            }
        }
        if ($this->question->formoptions->repeatelements) {
            $dropzonerepeatsatstart = max(self::START_NUM_IMAGES,
                                                    $countdropzones + self::ADD_NUM_IMAGES);
        } else {
            $dropzonerepeatsatstart = $countdropzones;
        }

        $this->repeat_elements($this->drop_zone($mform, $imagerepeats), $dropzonerepeatsatstart,
                $this->drop_zones_repeated_options(),
                'nodropzone', 'adddropzone', self::ADD_NUM_IMAGES,
                get_string('addmoredropzones', 'qtype_ddimagetoimage'));
    }

    protected function drop_zone($mform, $imagerepeats) {
        $dropzoneitem = array();


        $grouparray = array();
        $grouparray[] = $mform->createElement('static', 'xleftlabel', '',
                ' '.get_string('xleft', 'qtype_ddimagetoimage').' ');
        $grouparray[] = $mform->createElement('text', 'xleft',
                                                get_string('xleft', 'qtype_ddimagetoimage'),
                                                array('size'=>5, 'class'=>'tweakcss'));
        $mform->setType('xleft', PARAM_NOTAGS);
        $grouparray[] = $mform->createElement('static', 'ytoplabel', '',
                ' '.get_string('ytop', 'qtype_ddimagetoimage').' ');
        $grouparray[] = $mform->createElement('text', 'ytop',
                                                get_string('ytop', 'qtype_ddimagetoimage'),
                                                array('size'=>5, 'class'=>'tweakcss'));
        $mform->setType('ytop', PARAM_NOTAGS);
        $options = array();

        $options[0] = '';
        for ($i = 1; $i <= $imagerepeats; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                                        get_string('draggableimage', 'qtype_ddimagetoimage').' ');
        $grouparray[] = $mform->createElement('select', 'choice',
                                    get_string('draggableimage', 'qtype_ddimagetoimage'), $options);
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                                        get_string('label', 'qtype_ddimagetoimage').' ');
        $grouparray[] = $mform->createElement('text', 'droplabel',
                                                get_string('label', 'qtype_ddimagetoimage'),
                                                array('size'=>10, 'class'=>'tweakcss'));
        $mform->setType('droplabel', PARAM_NOTAGS);
        $dropzone = $mform->createElement('group', 'drops',
                get_string('dropzone', 'qtype_ddimagetoimage', '{no}'), $grouparray);
        return array($dropzone);
    }

    protected function drop_zones_repeated_options() {
        $repeatedoptions = array();
        $repeatedoptions['choice']['default'] = '0';
        return $repeatedoptions;
    }

    protected function definition_draggable_images($mform, $imagerepeatsatstart) {

        $this->repeat_elements($this->draggable_image($mform), $imagerepeatsatstart,
                $this->draggable_images_repeated_options(),
                'noimages', 'addimages', self::ADD_NUM_IMAGES,
                get_string('addmoreimages', 'qtype_ddimagetoimage'));
    }

    protected function draggable_image($mform) {
        $draggableimageitem = array();

        $draggableimageitem[] = $mform->createElement('header', 'draggableimageheader',
                                get_string('draggableimageheader', 'qtype_ddimagetoimage', '{no}'));
        $draggableimageitem[] = $mform->createElement('filepicker', 'dragitem', '', null,
                                                                    self::file_picker_options());

        $grouparray = array();
        $grouparray[] = $mform->createElement('text', 'draglabel',
                                                get_string('label', 'qtype_ddimagetoimage'),
                                                array('size'=>30, 'class'=>'tweakcss'));
        $mform->setType('draglabel', PARAM_NOTAGS);
        $options = array();
        for ($i = 1; $i <= self::MAX_GROUPS; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                get_string('group', 'qtype_gapselect').' ');
        $grouparray[] = $mform->createElement('select', 'draggroup',
                                                get_string('group', 'qtype_gapselect'), $options);
        $grouparray[] = $mform->createElement('checkbox', 'infinite', ' ',
                get_string('infinite', 'qtype_ddimagetoimage'), null,
                array('size' => 1, 'class' => 'tweakcss'));
        $draggableimageitem[] = $mform->createElement('group', 'drags',
                get_string('label', 'qtype_ddimagetoimage'), $grouparray);
        return $draggableimageitem;
    }

    protected function draggable_images_repeated_options() {
        $repeatedoptions = array();
        $repeatedoptions['draggroup']['default'] = '1';
        return $repeatedoptions;
    }

    public function data_preprocessing($question) {
        global $PAGE;

        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_combined_feedback($question, true);
        $question = $this->data_preprocessing_hints($question, true, true);

        if (!empty($question->options)) {
            $question->shuffleanswers = $question->options->shuffleanswers;
            $question->drags = array();
            foreach ($question->options->drags as $drag) {
                $dragindex = $drag->no -1;
                $question->drags[$dragindex] = array();
                $question->drags[$dragindex]['draglabel'] = $drag->label;
                $question->drags[$dragindex]['infinite'] = $drag->infinite;
                $question->drags[$dragindex]['draggroup'] = $drag->draggroup;
            }
            list(, $imagerepeats) = $this->get_drag_image_repeats();
            for ($imageindex = 0; $imageindex < $imagerepeats; $imageindex++) {
                $draftitemid = file_get_submitted_draft_itemid("dragitem[$imageindex]");
                //numbers not allowed in filearea name
                $filearea = str_replace(range('0', '9'), range('a', 'j'), "drag_$imageindex");
                file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_ddimagetoimage',
                                    $filearea, !empty($question->id) ? (int) $question->id : null,
                                    self::file_picker_options());
                $question->dragitem[$imageindex] = $draftitemid;
            }
            $question->drops = array();
            foreach ($question->options->drops as $drop) {
                $question->drops[$drop->no -1] = array();
                $question->drops[$drop->no -1]['choice'] = $drop->choice;
                $question->drops[$drop->no -1]['droplabel'] = $drop->label;
                $question->drops[$drop->no -1]['xleft'] = $drop->xleft;
                $question->drops[$drop->no -1]['ytop'] = $drop->ytop;
            }
            $draftitemid = file_get_submitted_draft_itemid('bgimage');
            file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_ddimagetoimage',
                                    'bgimage', !empty($question->id) ? (int) $question->id : null,
                                    self::file_picker_options());
            $question->bgimage = $draftitemid;
        }

        $jsmodule = array(
            'name'     => 'qtype_ddimagetoimage',
            'fullpath' => '/question/type/ddimagetoimage/module.js',
            'requires' => array('node', 'dd-drop', 'dd-constrain', 'form_filepicker')
        );
        $PAGE->requires->js_init_call('M.qtype_ddimagetoimage.init_form',
                                                                null, true, $jsmodule);

        return $question;
    }

    public static function file_get_draft_area_files($draftitemid) {
        $toreturn = new stdClass();
        $toreturn->draftitemid = $draftitemid;
        $draftareafiles = file_get_drafarea_files($draftitemid);
        $draftareafile = reset($draftareafiles->list);
        $toreturn->url = $draftareafile->url;
        return $toreturn;
    }


    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }



}
