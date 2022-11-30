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

use tool_brickfield\local\htmlchecker\brickfield_accessibility;

/**
 * Returns a formatted HTML view of the problems
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_codehighlight extends \tool_brickfield\local\htmlchecker\brickfield_accessibility_reporter {

    /**
     * @var array An array of the classnames to be associated with items
     */
    public $classnames = [brickfield_accessibility::BA_TEST_SEVERE => 'testlevel_severe',
                          brickfield_accessibility::BA_TEST_MODERATE => 'testlevel_moderate',
                          brickfield_accessibility::BA_TEST_SUGGESTION => 'testlevel_suggestion',
                         ];

    /**
     * The getReport method - we iterate through every test item and
     * add additional attributes to build the report UI.
     * @return string A fully-formed HTML document.
     */
    public function get_report(): string {
        $problems = $this->guideline->get_report();
        if (is_array($problems)) {
            foreach ($problems as $testname => $test) {
                if (!isset($this->options->display_level) ||
                    ($this->options->display_level >= $test['severity'] && is_array($test))) {
                    foreach ($test as $problem) {
                        if (is_object($problem)
                           && property_exists($problem, 'element')
                           && is_object($problem->element)) {
                            // Wrap each error with a "wrapper" node who's tag name is the severity
                            // level class. We'll fix this later and change them back to 'span' elements
                            // after we have converted the HTML code to entities.
                            $severitywrapper = $this->dom->createElement($this->classnames[$test['severity']]);
                            $severitywrapper->setAttribute('class', $this->classnames[$test['severity']] .' '. $testname);
                            $severitywrapper->setAttribute('test', $testname);
                            $severitywrapper->appendChild($problem->element->cloneNode(true));
                            $parent = $problem->element->parentNode;
                            if (is_object($parent)) {
                                $parent->replaceChild($severitywrapper, $problem->element);
                            }
                        }
                    }
                }
            }
        }
        $this->dom->formatOutput = true;
        $html = htmlspecialchars($this->dom->saveHTML(), ENT_COMPAT);
        $html = str_replace('&quot;', '"', $html);
        foreach ($this->classnames as $name) {
            $html = preg_replace('/&lt;'. $name .'([^&]+)+\&gt;/', '<span \\1>', $html);
            $html = str_replace('&lt;/'. $name .'&gt;', '</span>', $html);
        }
        return $html;
    }
}
