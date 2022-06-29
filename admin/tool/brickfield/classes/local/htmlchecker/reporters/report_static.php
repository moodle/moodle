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
 * A static reporter.
 *
 * Generates a list of errors which do not pass and their severity.
 *
 * This is just a demonstration of what you can do with a reporter.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_static extends brickfield_accessibility_reporter {
    /**
     * Generates a static list of errors within a div.
     * @return array A fully-formatted report
     */
    public function get_report(): array {
        $output = [];
        foreach ($this->guideline->get_report() as $testname => $test) {
            $severity    = $this->guideline->get_severity($testname);
            $translation = $this->guideline->get_translation($testname);

            if (isset($translation['title'])) {
                $title = $translation['title'];
            } else {
                $title = null;
            }
            if (isset($translation['description'])) {
                $description = $translation['description'];
            } else {
                $description = null;
            }

            switch ($severity) {
                case \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SEVERE:
                    $severitylevel  = 'Error';
                    $severitynumber = 1;
                    break;
                case \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_MODERATE:
                    $severitylevel  = 'Warning';
                    $severitynumber = 2;
                    break;
                case \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SUGGESTION:
                    $severitylevel  = 'Suggestion';
                    $severitynumber = 3;
                    break;
            }

            if (is_array($test)) {
                $testcount = 0;
                foreach ($test as $problem) {
                    $testresult = [];
                    if (is_object($problem)) {
                        $testresult['text_type'] = $problem->message;
                        if ($testname === "cssTextHasContrast" || $testname === "cssTextStyleEmphasize") {
                            $stylevalue = $problem->message;
                            $hexcolors  = [];
                            $stylematches = [];
                            $weightmatches = [];

                            preg_match_all("/(#[0-9a-f]{6}|#[0-9a-f]{3})/", $stylevalue, $hexcolors);
                            preg_match("/font-style:\s([a-z]*);/", $stylevalue, $stylematches);
                            preg_match("/font-weight:\s([a-z]*);/", $stylevalue, $weightmatches);
                            $hexcolors = array_unique($hexcolors[0]);

                            $testresult['colors'] = $hexcolors;
                            $testresult['back_color'] = $hexcolors[0];
                            $testresult['fore_color'] = $hexcolors[1];
                            $testresult['font_style'] = $stylematches[1];
                            $testresult['font_weight'] = $weightmatches[1];
                            if ($testresult['font_weight'] === "bolder") {
                                $testresult['font_weight'] = "bold";
                            }
                            $testresult['text_type'] = preg_replace('/(?=:).+/', '', $problem->message);

                        }

                        $testresult['type'] = $testname;
                        $testresult['lineNo'] = $problem->line;

                        if (isset($testresult['element'])) {
                            $testresult['element'] = $problem->element->tagName;
                        }

                        // Edit description for certain cases.
                        switch($testname) {
                            case 'videosEmbeddedOrLinkedNeedCaptions':
                                if ($problem->manual == true || $testcount > 0) {
                                    if ($problem->manual == true) {
                                        $testcount++;
                                    }
                                    $testresult['description']  = $description."<p>⚠️ ".$testcount.
                                        ' items require manual verification because unable to detect captions.' .
                                        ' This is most likely due to the video being unlisted, private, or deleted.</p>';
                                } else {
                                    $testresult['description']  = $description;
                                }
                                break;

                            default:
                                $testresult['description']  = $description;
                                break;
                        }

                        $testresult['severity'] = $severitylevel;
                        $testresult['severity_num'] = $severitynumber;
                        $testresult['title'] = $title;
                        $testresult['path'] = count($this->path) > 1 ? $this->path[1] : "None";
                        $testresult['html'] = $problem->get_html();
                        $testresult['state'] = $problem->state;
                        $testresult['manual'] = $problem->manual;
                    }

                    $output[] = $testresult;
                }
            }
        }
        return $output;
    }
}
