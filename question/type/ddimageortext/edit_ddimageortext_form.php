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

require_once($CFG->dirroot.'/question/type/ddimageortext/edit_ddtoimage_form_base.php');

/**
 * Defines the editing form for the drag-and-drop images onto images question type.
 *
 * @package    qtype
 * @subpackage ddimageortext
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
class qtype_ddimageortext_edit_form extends qtype_ddtoimage_edit_form_base {

    public function qtype() {
        return 'ddimageortext';
    }

    public function js_call() {
        global $PAGE;
        $maxsizes =new stdClass();
        $maxsizes->bgimage = new stdClass();
        $maxsizes->bgimage->width = QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXWIDTH;
        $maxsizes->bgimage->height = QTYPE_DDIMAGEORTEXT_BGIMAGE_MAXHEIGHT;
        $maxsizes->dragimage = new stdClass();
        $maxsizes->dragimage->width = QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXWIDTH;
        $maxsizes->dragimage->height = QTYPE_DDIMAGEORTEXT_DRAGIMAGE_MAXHEIGHT;

        $params = array('maxsizes' => $maxsizes,
                        'topnode' => 'fieldset#previewareaheader');

        $PAGE->requires->yui_module('moodle-qtype_ddimageortext-form',
                                        'M.qtype_ddimageortext.init_form',
                                        array($params));
    }

    //drag items

    protected function definition_draggable_items($mform, $itemrepeatsatstart) {

        $this->repeat_elements($this->draggable_item($mform), $itemrepeatsatstart,
                $this->draggable_items_repeated_options(),
                'noitems', 'additems', self::ADD_NUM_ITEMS,
                get_string('addmoreimages', 'qtype_ddimageortext'));
    }

    protected function draggable_item($mform) {
        $draggableimageitem = array();

        $draggableimageitem[] = $mform->createElement('header', 'draggableitemheader',
                                get_string('draggableitemheader', 'qtype_ddimageortext', '{no}'));
        $dragitemtypes = array('image' => get_string('draggableimage', 'qtype_ddimageortext'),
                                'word' => get_string('draggableword', 'qtype_ddimageortext'));
        $draggableimageitem[] = $mform->createElement('select', 'dragitemtype',
                                            get_string('draggableitemtype', 'qtype_ddimageortext'),
                                            $dragitemtypes,
                                            array('class' => 'dragitemtype'));
        $draggableimageitem[] = $mform->createElement('filepicker', 'dragitem', '', null,
                                    self::file_picker_options());

        $grouparray = array();
        $grouparray[] = $mform->createElement('text', 'draglabel',
                                                get_string('label', 'qtype_ddimageortext'),
                                                array('size'=>30, 'class'=>'tweakcss'));
        $mform->setType('draglabel', PARAM_NOTAGS);
        $options = array();
        for ($i = 1; $i <= self::MAX_GROUPS; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                get_string('group', 'qtype_gapselect').' ');
        $grouparray[] = $mform->createElement('select', 'draggroup',
                                                get_string('group', 'qtype_gapselect'),
                                                $options,
                                                array('class' => 'draggroup'));
        $grouparray[] = $mform->createElement('advcheckbox', 'infinite', ' ',
                get_string('infinite', 'qtype_ddimageortext'));
        $draggableimageitem[] = $mform->createElement('group', 'drags',
                get_string('label', 'qtype_ddimageortext'), $grouparray);
        return $draggableimageitem;
    }

    protected function draggable_items_repeated_options() {
        $repeatedoptions = array();
        $repeatedoptions['draggroup']['default'] = '1';
        return $repeatedoptions;
    }

    //drop zones

    protected function drop_zone($mform, $imagerepeats) {
        $dropzoneitem = array();

        $grouparray = array();
        $grouparray[] = $mform->createElement('static', 'xleftlabel', '',
                ' '.get_string('xleft', 'qtype_ddimageortext').' ');
        $grouparray[] = $mform->createElement('text', 'xleft',
                                                get_string('xleft', 'qtype_ddimageortext'),
                                                array('size'=>5, 'class'=>'tweakcss'));
        $mform->setType('xleft', PARAM_NOTAGS);
        $grouparray[] = $mform->createElement('static', 'ytoplabel', '',
                ' '.get_string('ytop', 'qtype_ddimageortext').' ');
        $grouparray[] = $mform->createElement('text', 'ytop',
                                                get_string('ytop', 'qtype_ddimageortext'),
                                                array('size'=>5, 'class'=>'tweakcss'));
        $mform->setType('ytop', PARAM_NOTAGS);
        $options = array();

        $options[0] = '';
        for ($i = 1; $i <= $imagerepeats; $i += 1) {
            $options[$i] = $i;
        }
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                                        get_string('draggableitem', 'qtype_ddimageortext').' ');
        $grouparray[] = $mform->createElement('select', 'choice',
                                    get_string('draggableitem', 'qtype_ddimageortext'), $options);
        $grouparray[] = $mform->createElement('static', '', '', ' ' .
                                        get_string('label', 'qtype_ddimageortext').' ');
        $grouparray[] = $mform->createElement('text', 'droplabel',
                                                get_string('label', 'qtype_ddimageortext'),
                                                array('size'=>10, 'class'=>'tweakcss'));
        $mform->setType('droplabel', PARAM_NOTAGS);
        $dropzone = $mform->createElement('group', 'drops',
                get_string('dropzone', 'qtype_ddimageortext', '{no}'), $grouparray);
        return array($dropzone);
    }

    protected function drop_zones_repeated_options() {
        $repeatedoptions = array();
        $repeatedoptions['choice']['default'] = '0';
        return $repeatedoptions;
    }
}
