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
 * This file contains the customcert element grade's core interaction API.
 *
 * @package    customcertelement_grade
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_grade;

defined('MOODLE_INTERNAL') || die();

/**
 * Grade - Course
 */
define('CUSTOMCERT_GRADE_COURSE', '0');

/**
 * The customcert element grade's core interaction API.
 *
 * @package    customcertelement_grade
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

        // Get the grade items we can display.
        $gradeitems = array();
        $gradeitems[CUSTOMCERT_GRADE_COURSE] = get_string('coursegrade', 'customcertelement_grade');
        $gradeitems = $gradeitems + \mod_customcert\element_helper::get_grade_items($COURSE);

        // The grade items.
        $mform->addElement('select', 'gradeitem', get_string('gradeitem', 'customcertelement_grade'), $gradeitems);
        $mform->addHelpButton('gradeitem', 'gradeitem', 'customcertelement_grade');

        // The grade format.
        $mform->addElement('select', 'gradeformat', get_string('gradeformat', 'customcertelement_grade'),
            self::get_grade_format_options());
        $mform->setType('gradeformat', PARAM_INT);
        $mform->addHelpButton('gradeformat', 'gradeformat', 'customcertelement_grade');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data.
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        // Array of data we will be storing in the database.
        $arrtostore = array(
            'gradeitem' => $data->gradeitem,
            'gradeformat' => $data->gradeformat
        );

        // Encode these variables before saving into the DB.
        return json_encode($arrtostore);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $courseid = \mod_customcert\element_helper::get_courseid($this->id);

        // Decode the information stored in the database.
        $gradeinfo = json_decode($this->get_data());
        $gradeitem = $gradeinfo->gradeitem;
        $gradeformat = $gradeinfo->gradeformat;

        // If we are previewing this certificate then just show a demonstration grade.
        if ($preview) {
            $courseitem = \grade_item::fetch_course_item($courseid);
            $grade = grade_format_gradevalue('100', $courseitem, true, $gradeinfo->gradeformat);;
        } else {
            if ($gradeitem == CUSTOMCERT_GRADE_COURSE) {
                $grade = \mod_customcert\element_helper::get_course_grade_info(
                    $courseid,
                    $gradeformat,
                    $user->id
                );
            } else if (strpos($gradeitem, 'gradeitem:') === 0) {
                $gradeitemid = substr($gradeitem, 10);
                $grade = \mod_customcert\element_helper::get_grade_item_info(
                    $gradeitemid,
                    $gradeformat,
                    $user->id
                );
            } else {
                $grade = \mod_customcert\element_helper::get_mod_grade_info(
                    $gradeitem,
                    $gradeformat,
                    $user->id
                );
            }

            if ($grade) {
                $grade = $grade->get_displaygrade();
            }
        }

        \mod_customcert\element_helper::render_content($pdf, $this, $grade);
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
        global $COURSE;

        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        // Decode the information stored in the database.
        $gradeinfo = json_decode($this->get_data());

        $courseitem = \grade_item::fetch_course_item($COURSE->id);

        $grade = grade_format_gradevalue('100', $courseitem, true, $gradeinfo->gradeformat);

        return \mod_customcert\element_helper::render_html_content($this, $grade);
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        // Set the item and format for this element.
        if (!empty($this->get_data())) {
            $gradeinfo = json_decode($this->get_data());

            $element = $mform->getElement('gradeitem');
            $element->setValue($gradeinfo->gradeitem);

            $element = $mform->getElement('gradeformat');
            $element->setValue($gradeinfo->gradeformat);
        }

        parent::definition_after_data($mform);
    }

    /**
     * This function is responsible for handling the restoration process of the element.
     *
     * We will want to update the course module the grade element is pointing to as it will
     * have changed in the course restore.
     *
     * @param \restore_customcert_activity_task $restore
     */
    public function after_restore($restore) {
        global $DB;

        $gradeinfo = json_decode($this->get_data());
        if ($newitem = \restore_dbops::get_backup_ids_record($restore->get_restoreid(), 'course_module', $gradeinfo->gradeitem)) {
            $gradeinfo->gradeitem = $newitem->newitemid;
            $DB->set_field('customcert_elements', 'data', $this->save_unique_data($gradeinfo), array('id' => $this->get_id()));
        }
    }

    /**
     * Helper function to return all the possible grade formats.
     *
     * @return array returns an array of grade formats
     */
    public static function get_grade_format_options() {
        $gradeformat = array();
        $gradeformat[GRADE_DISPLAY_TYPE_REAL] = get_string('gradepoints', 'customcertelement_grade');
        $gradeformat[GRADE_DISPLAY_TYPE_PERCENTAGE] = get_string('gradepercent', 'customcertelement_grade');
        $gradeformat[GRADE_DISPLAY_TYPE_LETTER] = get_string('gradeletter', 'customcertelement_grade');

        return $gradeformat;
    }
}
