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
 * Returns an ATOM feed of all the issues - useful to run this as a web service
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_xml extends brickfield_accessibility_reporter {

    /**
     * Generates an ATOM feed of accessibility problems
     * @return string|null A nested array of tests and problems with Report Item objects
     */
    public function get_report() {
        $output = "<?xml version='1.0' encoding='utf-8'?>
                    <feed xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
                        xsi:htmlchecker='https://www.brickfield.ie/htmlcheckerxml/2020.xsd'>";
        $results = $this->guideline->getReport();
        if (!is_array($results)) {
            return null;
        }
        foreach ($results as $testname => $test) {
            $translation = $this->guideline->get_translation($testname);
            $output .= "\n\t<htmlchecker:test htmlchecker:testname='$testname' htmlchecker:severity='".
                       $this->guideline->get_severity($testname) ."'>
                       <updated>". date('c') ."</updated>";
            $output .= "\n\t<htmlchecker:title>". $translation['title'] ."</htmlchecker:title>";
            $output .= "\n\t<htmlchecker:description><![CDATA[". $translation['description'] ."]]></htmlchecker:description>";
            $output .= "\n\t<htmlchecker:problems>";
            foreach ($test as $problem) {
                if (is_object($problem)) {
                    $output .= "\n\t<htmlchecker:entities><![CDATA[" . htmlentities($problem->get_html(), ENT_COMPAT) .
                        "]]></htmlchecker:entities>";
                    $output .= "\n\t<htmlchecker:line>". $problem->get_line() ."</htmlchecker:line>";
                    if ($problem->message) {
                        $output .= "\n\t<htmlchecker:message>$problem->message</htmlchecker:message>";
                    }
                    $output .= "\n\t<htmlchecker:pass>$problem->pass</htmlchecker:pass>";
                }
            }
            $output .= "\n\t</htmlchecker:problems>";
            $output .= "</htmlchecker:test>";
        }
        $output .= "</feed>";
        return $output;
    }
}
