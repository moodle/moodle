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

namespace tool_brickfield\local\htmlchecker\reporters;

use tool_brickfield\local\htmlchecker\brickfield_accessibility_reporter;

/**
 * An array reporter that simply returns an unformatted and nested PHP array of tests and report objects
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_array extends brickfield_accessibility_reporter {

    /**
     * Generates a static list of errors within a div.
     * @return array|null A nested array of tests and problems with Report Item objects
     */
    public function get_report() {
        $results = $this->guideline->get_report();
        if (!is_array($results)) {
            return null;
        }
        foreach ($results as $testname => $test) {
            $translation = $this->guideline->get_translation($testname);
            $output[$testname]['severity'] = $this->guideline->get_severity($testname);
            $output[$testname]['title'] = $translation['title'];
            $output[$testname]['body'] = $translation['description'];
            foreach ($test as $k => $problem) {
                if (is_object($problem)) {
                    $output[$testname]['problems'][$k]['element'] = htmlentities($problem->get_html());
                    $output[$testname]['problems'][$k]['line'] = $problem->get_line();
                    if ($problem->message) {
                        $output[$testname]['problems']['message'] = $problem->message;
                    }
                    $output[$testname]['problems']['pass'] = $problem->pass;
                }
            }
        }
        return $output;
    }
}
