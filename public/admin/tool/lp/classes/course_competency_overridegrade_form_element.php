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
 * Course competency override grade element.
 *
 * @package   tool_lp
 * @copyright 2022 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/form/advcheckbox.php');

/**
 * Course competency override grade element.
 *
 * @package   tool_lp
 * @copyright 2022 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_course_competency_overridegrade_form_element extends MoodleQuickForm_advcheckbox {

    /**
     * Constructor
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     */
    public function __construct($elementname=null, $elementlabel=null, $options=[]) {
        if ($elementname == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }

        if (!empty($options['cmid'])) {
            $cmid = $options['cmid'];

            $current = \core_competency\api::list_course_module_competencies_in_course_module($cmid);

            // Note: We just pick the override grade value set on the first course_module_competency.
            // Because in the UI we force them to be the same for all.
            if (!empty($current)) {
                $one = array_pop($current);
                $this->setValue($one->get('overridegrade'));
            }
        }

        parent::__construct($elementname, $elementlabel);
    }
}
