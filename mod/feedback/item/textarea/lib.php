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

defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_textarea extends feedback_item_base {
    protected $type = "textarea";

    public function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('textarea_form.php');

        //get the lastposition number of the feedback_items
        $position = $item->position;
        $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
        if ($position == -1) {
            $i_formselect_last = $lastposition + 1;
            $i_formselect_value = $lastposition + 1;
            $item->position = $lastposition + 1;
        } else {
            $i_formselect_last = $lastposition;
            $i_formselect_value = $item->position;
        }
        //the elements for position dropdownlist
        $positionlist = array_slice(range(0, $i_formselect_last), 1, $i_formselect_last, true);

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;

        $width_and_height = explode('|', $item->presentation);

        if (isset($width_and_height[0]) AND $width_and_height[0] >= 5) {
            $itemwidth = $width_and_height[0];
        } else {
            $itemwidth = 30;
        }

        if (isset($width_and_height[1])) {
            $itemheight = $width_and_height[1];
        } else {
            $itemheight = 5;
        }
        $item->itemwidth = $itemwidth;
        $item->itemheight = $itemheight;

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : null,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $customdata = array('item' => $item,
                            'common' => $commonparams,
                            'positionlist' => $positionlist,
                            'position' => $position);

        $this->item_form = new feedback_textarea_form('edit_item.php', $customdata);
    }

    public function save_item() {
        global $DB;

        if (!$item = $this->item_form->get_data()) {
            return false;
        }

        if (isset($item->clone_item) AND $item->clone_item) {
            $item->id = ''; //to clone this item
            $item->position++;
        }

        $item->hasvalue = $this->get_hasvalue();
        if (!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        } else {
            $DB->update_record('feedback_item', $item);
        }

        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }

    /**
     * Helper function for collected data for exporting to excel
     *
     * @param stdClass $item the db-object from feedback_item
     * @param int $groupid
     * @param int $courseid
     * @return stdClass
     */
    protected function get_analysed($item, $groupid = false, $courseid = false) {
        global $DB;

        $analysed_val = new stdClass();
        $analysed_val->data = array();
        $analysed_val->name = $item->name;

        $values = feedback_get_group_values($item, $groupid, $courseid);
        if ($values) {
            $data = array();
            foreach ($values as $value) {
                $data[] = str_replace("\n", '<br />', $value->value);
            }
            $analysed_val->data = $data;
        }
        return $analysed_val;
    }

    public function get_printval($item, $value) {

        if (!isset($value->value)) {
            return '';
        }

        return $value->value;
    }

    public function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if ($values) {
            echo "<table class=\"analysis itemtype_{$item->typ}\">";
            echo '<tr><th colspan="2" align="left">';
            echo $itemnr . ' ';
            if (strval($item->label) !== '') {
                echo '('. format_string($item->label).') ';
            }
            echo format_text($item->name, FORMAT_HTML, array('noclean' => true, 'para' => false));
            echo '</th></tr>';
            foreach ($values as $value) {
                $class = strlen(trim($value->value)) ? '' : ' class="isempty"';
                echo '<tr'.$class.'>';
                echo '<td colspan="2" class="singlevalue">';
                echo str_replace("\n", '<br />', $value->value);
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    public function excelprint_item(&$worksheet, $row_offset,
                             $xls_formats, $item,
                             $groupid, $courseid = false) {

        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $worksheet->write_string($row_offset, 0, $item->label, $xls_formats->head2);
        $worksheet->write_string($row_offset, 1, $item->name, $xls_formats->head2);
        $data = $analysed_item->data;
        if (is_array($data)) {
            if (isset($data[0])) {
                $worksheet->write_string($row_offset, 2, htmlspecialchars_decode($data[0], ENT_QUOTES), $xls_formats->value_bold);
            }
            $row_offset++;
            $sizeofdata = count($data);
            for ($i = 1; $i < $sizeofdata; $i++) {
                $worksheet->write_string($row_offset, 2, htmlspecialchars_decode($data[$i], ENT_QUOTES), $xls_formats->default);
                $row_offset++;
            }
        }
        $row_offset++;
        return $row_offset;
    }

    /**
     * Adds an input element to the complete form
     *
     * @param stdClass $item
     * @param mod_feedback_complete_form $form
     */
    public function complete_form_element($item, $form) {
        $name = $this->get_display_name($item);
        $inputname = $item->typ . '_' . $item->id;
        list($cols, $rows) = explode ("|", $item->presentation);
        $form->add_form_element($item,
            ['textarea', $inputname, $name, array('rows' => $rows, 'cols' => $cols)]);
        $form->set_element_type($inputname, PARAM_NOTAGS);
    }

    public function create_value($data) {
        return s($data);
    }
}
