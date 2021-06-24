<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element gradeitemname's core interaction API.
 *
 * @package    customcertelement_gradeitemname
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_gradeitemname;

defined('MOODLE_INTERNAL') || die();

/**
 * The customcert element gradeitemname's core interaction API.
 *
 * @package    customcertelement_gradeitemname
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \mod_customcert\element {

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        global $COURSE;

        $mform->addElement('select', 'gradeitem', get_string('gradeitem', 'customcertelement_gradeitemname'),
            \mod_customcert\element_helper::get_grade_items($COURSE));
        $mform->addHelpButton('gradeitem', 'gradeitem', 'customcertelement_gradeitemname');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the text
     */
    public function save_unique_data($data) {
        if (!empty($data->gradeitem)) {
            return $data->gradeitem;
        }

        return '';
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        // Check that the grade item is not empty.
        if (!empty($this->get_data())) {
            \mod_customcert\element_helper::render_content($pdf, $this, $this->get_grade_item_name());
        }
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        // Check that the grade item is not empty.
        if (!empty($this->get_data())) {
            return \mod_customcert\element_helper::render_html_content($this, $this->get_grade_item_name());
        }

        return '';
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('gradeitem');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the grade item name.
     *
     * @return string
     */
    protected function get_grade_item_name() : string {
        global $DB;

        $gradeitem = $this->get_data();

        if (strpos($gradeitem, 'gradeitem:') === 0) {
            $gradeitemid = substr($gradeitem, 10);
            $gradeitem = \grade_item::fetch(['id' => $gradeitemid]);

            return $gradeitem->get_name();
        } else {
            if (!$cm = $DB->get_record('course_modules', array('id' => $gradeitem))) {
                return '';
            }

            if (!$module = $DB->get_record('modules', array('id' => $cm->module))) {
                return '';
            }

            $params = [
                'itemtype' => 'mod',
                'itemmodule' => $module->name,
                'iteminstance' => $cm->instance,
                'courseid' => $cm->course,
                'itemnumber' => 0
            ];

            $gradeitem = \grade_item::fetch($params);

            return $gradeitem->get_name();
        }
    }
}
