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
 * Provides useful functions related to elements.
 *
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/grade/constants.php');
require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/querylib.php');

/**
 * Class helper.
 *
 * Provides useful functions related to elements.
 *
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element_helper {

    /**
     * @var int the top-left of element
     */
    const CUSTOMCERT_REF_POINT_TOPLEFT = 0;

    /**
     * @var int the top-center of element
     */
    const CUSTOMCERT_REF_POINT_TOPCENTER = 1;

    /**
     * @var int the top-left of element
     */
    const CUSTOMCERT_REF_POINT_TOPRIGHT = 2;

    /**
     * Common behaviour for rendering specified content on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param \mod_customcert\element $element the customcert element
     * @param string $content the content to render
     */
    public static function render_content($pdf, $element, $content) {
        list($font, $attr) = self::get_font($element);
        $pdf->setFont($font, $attr, $element->get_fontsize());
        $fontcolour = \TCPDF_COLORS::convertHTMLColorToDec($element->get_colour(), $fontcolour);
        $pdf->SetTextColor($fontcolour['R'], $fontcolour['G'], $fontcolour['B']);

        $x = $element->get_posx();
        $y = $element->get_posy();
        $w = $element->get_width();
        $refpoint = $element->get_refpoint();
        $actualwidth = $pdf->GetStringWidth($content);

        if ($w and $w < $actualwidth) {
            $actualwidth = $w;
        }

        switch ($refpoint) {
            case self::CUSTOMCERT_REF_POINT_TOPRIGHT:
                $x = $element->get_posx() - $actualwidth;
                if ($x < 0) {
                    $x = 0;
                    $w = $element->get_posx();
                } else {
                    $w = $actualwidth;
                }
                break;
            case self::CUSTOMCERT_REF_POINT_TOPCENTER:
                $x = $element->get_posx() - $actualwidth / 2;
                if ($x < 0) {
                    $x = 0;
                    $w = $element->get_posx() * 2;
                } else {
                    $w = $actualwidth;
                }
                break;
        }

        if ($w) {
            $w += 0.0001;
        }
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->writeHTMLCell($w, 0, $x, $y, $content, 0, 0, false, true);
    }

    /**
     * Common behaviour for rendering specified content on the drag and drop page.
     *
     * @param \mod_customcert\element $element the customcert element
     * @param string $content the content to render
     * @return string the html
     */
    public static function render_html_content($element, $content) {
        list($font, $attr) = self::get_font($element);
        $fontstyle = 'font-family: ' . $font;
        if (strpos($attr, 'B') !== false) {
            $fontstyle .= '; font-weight: bold';
        }
        if (strpos($attr, 'I') !== false) {
            $fontstyle .= '; font-style: italic';
        }

        $style = $fontstyle . '; color: ' . $element->get_colour() . '; font-size: ' . $element->get_fontsize() . 'pt;';
        if ($element->get_width()) {
            $style .= ' width: ' . $element->get_width() . 'mm';
        }
        return \html_writer::div($content, '', array('style' => $style));
    }

    /**
     * Helper function to render the font elements.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public static function render_form_element_font($mform) {
        $mform->addElement('select', 'font', get_string('font', 'customcert'), \mod_customcert\certificate::get_fonts());
        $mform->setType('font', PARAM_TEXT);
        $mform->setDefault('font', 'times');
        $mform->addHelpButton('font', 'font', 'customcert');
        $mform->addElement('select', 'fontsize', get_string('fontsize', 'customcert'),
            \mod_customcert\certificate::get_font_sizes());
        $mform->setType('fontsize', PARAM_INT);
        $mform->setDefault('fontsize', 12);
        $mform->addHelpButton('fontsize', 'fontsize', 'customcert');
    }

    /**
     * Helper function to render the colour elements.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public static function render_form_element_colour($mform) {
        $mform->addElement('customcert_colourpicker', 'colour', get_string('fontcolour', 'customcert'));
        $mform->setType('colour', PARAM_RAW); // Need to validate that this is a valid colour.
        $mform->setDefault('colour', '#000000');
        $mform->addHelpButton('colour', 'fontcolour', 'customcert');
    }

    /**
     * Helper function to render the position elements.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public static function render_form_element_position($mform) {
        $mform->addElement('text', 'posx', get_string('posx', 'customcert'), array('size' => 10));
        $mform->setType('posx', PARAM_INT);
        $mform->setDefault('posx', 0);
        $mform->addHelpButton('posx', 'posx', 'customcert');
        $mform->addElement('text', 'posy', get_string('posy', 'customcert'), array('size' => 10));
        $mform->setType('posy', PARAM_INT);
        $mform->setDefault('posy', 0);
        $mform->addHelpButton('posy', 'posy', 'customcert');
    }

    /**
     * Helper function to render the width element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public static function render_form_element_width($mform) {
        $mform->addElement('text', 'width', get_string('elementwidth', 'customcert'), array('size' => 10));
        $mform->setType('width', PARAM_INT);
        $mform->setDefault('width', 0);
        $mform->addHelpButton('width', 'elementwidth', 'customcert');
    }

    /**
     * Helper function to render the refpoint element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public static function render_form_element_refpoint($mform) {
        $refpointoptions = array();
        $refpointoptions[self::CUSTOMCERT_REF_POINT_TOPLEFT] = get_string('topleft', 'customcert');
        $refpointoptions[self::CUSTOMCERT_REF_POINT_TOPCENTER] = get_string('topcenter', 'customcert');
        $refpointoptions[self::CUSTOMCERT_REF_POINT_TOPRIGHT] = get_string('topright', 'customcert');

        $mform->addElement('select', 'refpoint', get_string('refpoint', 'customcert'), $refpointoptions);
        $mform->setType('refpoint', PARAM_INT);
        $mform->setDefault('refpoint', self::CUSTOMCERT_REF_POINT_TOPCENTER);
        $mform->addHelpButton('refpoint', 'refpoint', 'customcert');
    }

    /**
     * Helper function to performs validation on the colour element.
     *
     * @param array $data the submitted data
     * @return array the validation errors
     */
    public static function validate_form_element_colour($data) {
        $errors = array();
        // Validate the colour.
        if (!self::validate_colour($data['colour'])) {
            $errors['colour'] = get_string('invalidcolour', 'customcert');
        }
        return $errors;
    }

    /**
     * Helper function to performs validation on the position elements.
     *
     * @param array $data the submitted data
     * @return array the validation errors
     */
    public static function validate_form_element_position($data) {
        $errors = array();

        // Check if posx is not set, or not numeric or less than 0.
        if ((!isset($data['posx'])) || (!is_numeric($data['posx'])) || ($data['posx'] < 0)) {
            $errors['posx'] = get_string('invalidposition', 'customcert', 'X');
        }
        // Check if posy is not set, or not numeric or less than 0.
        if ((!isset($data['posy'])) || (!is_numeric($data['posy'])) || ($data['posy'] < 0)) {
            $errors['posy'] = get_string('invalidposition', 'customcert', 'Y');
        }

        return $errors;
    }

    /**
     * Helper function to perform validation on the width element.
     *
     * @param array $data the submitted data
     * @return array the validation errors
     */
    public static function validate_form_element_width($data) {
        $errors = array();

        // Check if width is less than 0.
        if (isset($data['width']) && $data['width'] < 0) {
            $errors['width'] = get_string('invalidelementwidth', 'customcert');
        }

        return $errors;
    }

    /**
     * Returns the font used for this element.
     *
     * @param \mod_customcert\element $element the customcert element
     * @return array the font and font attributes
     */
    public static function get_font($element) {
        // Variable for the font.
        $font = $element->get_font();
        // Get the last two characters of the font name.
        $fontlength = strlen($font);
        $lastchar = $font[$fontlength - 1];
        $secondlastchar = $font[$fontlength - 2];
        // The attributes of the font.
        $attr = '';
        // Check if the last character is 'i'.
        if ($lastchar == 'i') {
            // Remove the 'i' from the font name.
            $font = substr($font, 0, -1);
            // Check if the second last char is b.
            if ($secondlastchar == 'b') {
                // Remove the 'b' from the font name.
                $font = substr($font, 0, -1);
                $attr .= 'B';
            }
            $attr .= 'I';
        } else if ($lastchar == 'b') {
            // Remove the 'b' from the font name.
            $font = substr($font, 0, -1);
            $attr .= 'B';
        }
        return array($font, $attr);
    }

    /**
     * Validates the colour selected.
     *
     * @param string $colour
     * @return bool returns true if the colour is valid, false otherwise
     */
    public static function validate_colour($colour) {
        // List of valid HTML colour names.
        $colournames = array(
            'aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure',
            'beige', 'bisque', 'black', 'blanchedalmond', 'blue',
            'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse',
            'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson',
            'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray',
            'darkgrey', 'darkgreen', 'darkkhaki', 'darkmagenta',
            'darkolivegreen', 'darkorange', 'darkorchid', 'darkred',
            'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray',
            'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink',
            'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick',
            'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro',
            'ghostwhite', 'gold', 'goldenrod', 'gray', 'grey', 'green',
            'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo',
            'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen',
            'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan',
            'lightgoldenrodyellow', 'lightgray', 'lightgrey', 'lightgreen',
            'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue',
            'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow',
            'lime', 'limegreen', 'linen', 'magenta', 'maroon',
            'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple',
            'mediumseagreen', 'mediumslateblue', 'mediumspringgreen',
            'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream',
            'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive',
            'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod',
            'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip',
            'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red',
            'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown',
            'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue',
            'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan',
            'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white',
            'whitesmoke', 'yellow', 'yellowgreen'
        );

        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $colour)) {
            return true;
        } else if (in_array(strtolower($colour), $colournames)) {
            return true;
        }

        return false;
    }

    /**
     * Helper function that returns the sequence on a specified customcert page for a
     * newly created element.
     *
     * @param int $pageid the id of the page we are adding this element to
     * @return int the element number
     */
    public static function get_element_sequence($pageid) {
        global $DB;

        // Set the sequence of the element we are creating.
        $sequence = 1;
        // Check if there already elements that exist, if so, overwrite value.
        $sql = "SELECT MAX(sequence) as maxsequence
                  FROM {customcert_elements}
                 WHERE pageid = :id";
        // Get the current max sequence on this page and add 1 to get the new sequence.
        if ($maxseq = $DB->get_record_sql($sql, array('id' => $pageid))) {
            $sequence = $maxseq->maxsequence + 1;
        }

        return $sequence;
    }

    /**
     * Helper function that returns the course id for this element.
     *
     * @param int $elementid The element id
     * @return int The course id
     */
    public static function get_courseid($elementid) {
        global $DB, $SITE;

        $sql = "SELECT course
                  FROM {customcert} c
            INNER JOIN {customcert_pages} cp
                    ON c.templateid = cp.templateid
            INNER JOIN {customcert_elements} ce
                    ON cp.id = ce.pageid
                 WHERE ce.id = :elementid";

        // Check if there is a course associated with this element.
        if ($course = $DB->get_record_sql($sql, array('elementid' => $elementid))) {
            return $course->course;
        } else { // Must be in a site template.
            return $SITE->id;
        }
    }

    /**
     * Helper function that returns the context for this element.
     *
     * @param int $elementid The element id
     * @return \context The context
     */
    public static function get_context(int $elementid) : \context {
        global $DB;

        $sql = "SELECT ct.contextid
                  FROM {customcert_templates} ct
            INNER JOIN {customcert_pages} cp
                    ON ct.id = cp.templateid
            INNER JOIN {customcert_elements} ce
                    ON cp.id = ce.pageid
                 WHERE ce.id = :elementid";
        $contextid = $DB->get_field_sql($sql, array('elementid' => $elementid), MUST_EXIST);

        return \context::instance_by_id($contextid);
    }

    /**
     * Return the list of possible elements to add.
     *
     * @return array the list of element types that can be used.
     */
    public static function get_available_element_types() {
        global $CFG;

        // Array to store the element types.
        $options = array();

        // Check that the directory exists.
        $elementdir = "$CFG->dirroot/mod/customcert/element";
        if (file_exists($elementdir)) {
            // Get directory contents.
            $elementfolders = new \DirectoryIterator($elementdir);
            // Loop through the elements folder.
            foreach ($elementfolders as $elementfolder) {
                // If it is not a directory or it is '.' or '..', skip it.
                if (!$elementfolder->isDir() || $elementfolder->isDot()) {
                    continue;
                }
                // Check that the standard class exists, if not we do
                // not want to display it as an option as it will not work.
                $foldername = $elementfolder->getFilename();
                // Get the class name.
                $classname = '\\customcertelement_' . $foldername . '\\element';
                // Ensure the necessary class exists.
                if (class_exists($classname)) {
                    // Additionally, check if the user is allowed to add the element at all.
                    if ($classname::can_add()) {
                        $component = "customcertelement_{$foldername}";
                        $options[$foldername] = get_string('pluginname', $component);
                    }
                }
            }
        }

        \core_collator::asort($options);
        return $options;
    }

    /**
     * Helper function to return all the grades items for a given course.
     *
     * @param \stdClass $course The course we want to return the grade items for
     * @return array the array of gradeable items in the course
     */
    public static function get_grade_items($course) {
        // Array to store the grade items.
        $arrgradeitems = array();

        // Get other non-module related grade items.
        if ($gradeitems = \grade_item::fetch_all(['courseid' => $course->id])) {
            foreach ($gradeitems as $gi) {
                if ($gi->is_course_item()) {
                    continue; // Skipping for legacy reasons - this was added to individual elements.
                }

                if ($gi->is_external_item()) {
                    $cm = get_coursemodule_from_instance($gi->itemmodule, $gi->iteminstance, $course->id);
                    $modcontext = \context_module::instance($cm->id);
                    $modname = format_string($cm->name, true, array('context' => $modcontext));
                }

                if ($gi->is_external_item() && !$gi->is_outcome_item()) {
                    // Due to legacy reasons we are storing the course module ID here rather than the grade item id.
                    // If we were to change we would need to provide upgrade steps to convert cm->id to gi->id.
                    $arrgradeitems[$cm->id] = get_string('activity', 'mod_customcert') . ' : ' . $gi->get_name();
                } else if ($gi->is_external_item() && $gi->is_outcome_item()) {
                    // Get the name of the activity.
                    $optionname = get_string('gradeoutcome', 'mod_customcert') . ' : '  . $modname . " - " . $gi->get_name();
                    $arrgradeitems['gradeitem:' . $gi->id] = $optionname;
                } else {
                    $arrgradeitems['gradeitem:' . $gi->id] = get_string('gradeitem', 'grades') . ' : ' . $gi->get_name(true);
                }
            }

            // Alphabetise this.
            asort($arrgradeitems);
        }

        return $arrgradeitems;
    }

    /**
     * Helper function to return the grade information for a course for a specified user.
     *
     * @param int $courseid
     * @param int $gradeformat
     * @param int $userid
     * @return grade_information|bool the grade information, or false if there is none.
     */
    public static function get_course_grade_info($courseid, $gradeformat, $userid) {
        $courseitem = \grade_item::fetch_course_item($courseid);

        if (!$courseitem) {
            return false;
        }

        $grade = new \grade_grade(array('itemid' => $courseitem->id, 'userid' => $userid));

        return new grade_information(
            $courseitem->get_name(),
            $grade->finalgrade,
            grade_format_gradevalue($grade->finalgrade, $courseitem, true, $gradeformat),
            $grade->get_dategraded()
        );
    }

    /**
     * Helper function to return the grade information for a module for a specified user.
     *
     * @param int $cmid
     * @param int $gradeformat
     * @param int $userid
     * @return grade_information|bool the grade information, or false if there is none.
     */
    public static function get_mod_grade_info($cmid, $gradeformat, $userid) {
        global $DB;

        if (!$cm = $DB->get_record('course_modules', array('id' => $cmid))) {
            return false;
        }

        if (!$module = $DB->get_record('modules', array('id' => $cm->module))) {
            return false;
        }

        $params = [
            'itemtype' => 'mod',
            'itemmodule' => $module->name,
            'iteminstance' => $cm->instance,
            'courseid' => $cm->course,
            'itemnumber' => 0
        ];
        $gradeitem = \grade_item::fetch($params);

        if (empty($gradeitem)) {
            return false;
        }

        $grade = grade_get_grades(
            $cm->course,
            'mod',
            $module->name,
            $cm->instance,
            $userid
        );

        if (!isset($grade->items[0]->grades[$userid])) {
            return false;
        }

        $gradebookgrade = $grade->items[0]->grades[$userid];

        $dategraded = null;
        if (!empty($gradebookgrade->dategraded)) {
            $dategraded = $gradebookgrade->dategraded;
        }

        $displaygrade = grade_format_gradevalue($gradebookgrade->grade, $gradeitem, true, $gradeformat);

        return new grade_information(
            $gradeitem->get_name(),
            $gradebookgrade->grade,
            $displaygrade,
            $dategraded
        );
    }

    /**
     * Helper function to return the grade information for a grade item for a specified user.
     *
     * @param int $gradeitemid
     * @param int $gradeformat
     * @param int $userid
     * @return grade_information|bool the grade information, or false if there is none.
     */
    public static function get_grade_item_info($gradeitemid, $gradeformat, $userid) {
        if (!$gradeitem = \grade_item::fetch(['id' => $gradeitemid])) {
            return false;
        }

        $grade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $userid));

        return new grade_information(
            $gradeitem->get_name(),
            $grade->finalgrade,
            grade_format_gradevalue($grade->finalgrade, $gradeitem, true, $gradeformat),
            $grade->get_dategraded()
        );
    }
}
