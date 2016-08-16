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
 * Course competencies element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_competency\api;
use core_competency\external\competency_exporter;
require_once($CFG->libdir . '/form/autocomplete.php');

/**
 * Course competencies element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_course_competencies_form_element extends MoodleQuickForm_autocomplete {

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName=null, $elementLabel=null, $options=array(), $attributes=null) {
        global $OUTPUT;

        if ($elementName == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }

        if (!isset($options['courseid'])) {
            throw new coding_exception('Course id is required for the course_competencies form element');
        }
        $courseid = $options['courseid'];

        if (!empty($options['cmid'])) {
            $current = \core_competency\api::list_course_module_competencies_in_course_module($options['cmid']);
            $ids = array();
            foreach ($current as $coursemodulecompetency) {
                array_push($ids, $coursemodulecompetency->get_competencyid());
            }
            $this->setValue($ids);
        }

        $competencies = api::list_course_competencies($courseid);
        $validoptions = array();

        $context = context_course::instance($courseid);
        foreach ($competencies as $competency) {
            // We don't need to show the description as part of the options, so just set this to null.
            $competency['competency']->set_description(null);
            $exporter = new competency_exporter($competency['competency'], array('context' => $context));
            $templatecontext = array('competency' => $exporter->export($OUTPUT));
            $id = $competency['competency']->get_id();
            $validoptions[$id] = $OUTPUT->render_from_template('tool_lp/competency_summary', $templatecontext);
        }
        $attributes['tags'] = false;
        $attributes['multiple'] = 'multiple';
        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }
}
