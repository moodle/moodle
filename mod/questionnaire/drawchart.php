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
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function draw_chart($feedbacktype, $charttype=null, $labels,
                    $score=null, $allscore=null, $globallabel=null, $groupname, $allresponses) {
    global $PAGE;

    $pageoutput = '';

    if ($allresponses) {
        $nbvalues = count($allscore);
    } else {
        $nbvalues = count($score);
    }
    $nblabels = count($labels);
    $charttitlefont = "Verdana";
    $charttitlesize = 10;
    $charttitlesize2 = 10;
    if ($PAGE->pagetype == 'mod-questionnaire-myreport') {
        $charttitle = get_string('yourresponse', 'questionnaire');
    } else {
        $charttitle = get_string('thisresponse', 'questionnaire');
    }
    $charttitle2 = $groupname;

    // Gradient colors if needed.
    // TODO make gradient colors customizable in the settings.
    $chartcolorsgradient = "['Gradient(white:blue)', 'Gradient(white:red)', 'Gradient(white:green)', 'Gradient(white:pink)',
            'Gradient(white:yellow)', 'Gradient(white:cyan)', 'Gradient(white:navy)',
            'Gradient(white:gray)', 'Gradient(white:black)']";
    $chartcolorsgradient2 = "['Gradient(pink:red)', 'Gradient(white:blue)', 'Gradient(white:green)', 'Gradient(white:pink)',
        'Gradient(white:yellow)', 'Gradient(white:cyan)', 'Gradient(white:navy)', 'Gradient(white:gray)', 'Gradient(white:black)']";

    // We do not have labels other than global in this feedback type.
    if ($feedbacktype == 'global') {
        $labels = array($globallabel);
    }

    switch ($charttype) {

        case 'bipolar':
            // Global feedback with only one score.
            if ($feedbacktype == 'global') {
                if ($score) {
                    if ($allresponses) {
                        $score = null;
                    } else {
                        $score2 = $score;
                        $score = array($score2[0]);
                        $oppositescore = array($score2[1]);
                    }
                }

                if ($allscore) {
                    $allscore2 = $allscore;
                    $allscore = array($allscore2[0]);
                    $alloppositescore = array($allscore2[1]);
                }
                $nblabels = 1.5;     // For a single horizontal bar with 1.5 height.
                $nbvalues = 1;       // Only one hbar.
            } else {
                if ($score) {
                    $oppositescore = array();
                    foreach ($score as $sc) {
                        $oppositescore[] = 100 - $sc;
                    }
                }
                if ($allscore) {
                    $alloppositescore = array();
                    foreach ($allscore as $sc) {
                        $alloppositescore[] = 100 - $sc;
                    }
                }
            }
            foreach ($labels as $key => $label) {
                $lb = explode("|", $label);
                // Just in case there is no pipe separator in label.
                if (count($lb) > 1) {
                    $left = $lb[0];
                    $right = $lb[1];
                    // Lib core_text and diff needed for non-ascii characters.
                    $lenleft = core_text::strlen($left);
                    $diffleft = strlen($left) - $lenleft;
                    $lenright = core_text::strlen($right);
                    $diffright = strlen($right) - $lenright;
                    if ($lenleft < $lenright) {
                        $padlength = $lenright + $diffleft;
                        $left = str_pad($left, $padlength, ' ', STR_PAD_LEFT);
                    }
                    if ($lenleft > $lenright) {
                        $padlength = $lenleft + $diffright;
                        $right = str_pad($right, $padlength, ' ', STR_PAD_RIGHT);
                    }
                    $labels[$key] = $left .' '.$right;
                }
            }
            // Find length of longest label.
            $maxlen = 0;
            foreach ($labels as $label) {
                $labellen = core_text::strlen($label);
                if ($labellen > $maxlen) {
                    $maxlen = $labellen;
                }
            }

            $labels = json_encode($labels, JSON_UNESCAPED_UNICODE);
            // JSON_UNESCAPED_UNICODE available since php 5.4, used to correctly treat French accents etc.

            // The bar colors :: use green for "positive" (left column) and pink for "negative" (right column).
            $chartcolors = array();
            $chartcolors2 = array();
            if ($score) {

                for ($i = 0; $i < $nbvalues; $i++) {
                    if ($score[$i] != 0) {
                        $chartcolors[] = 'lightgreen';
                    }
                }
                for ($i = $nbvalues; $i < $nbvalues * 2; $i++) {
                    $chartcolors[] = 'pink';
                }
            }
            if ($allscore) {

                for ($i = 0; $i < $nbvalues; $i++) {
                    if ($allscore[$i] != 0) {
                        $chartcolors2[] = 'lightgreen';
                    }
                }
                for ($i = $nbvalues; $i < $nbvalues * 2; $i++) {
                    $chartcolors2[] = 'pink';
                }
            }

            // Encode all arrays for javascript compatibility.
            $chartcolors = json_encode($chartcolors);
            if ($allscore) {
                $chartcolors2 = json_encode($chartcolors2);
            }
            if ($score) {
                $score = json_encode($score);
                $oppositescore = json_encode($oppositescore);
            }

            if ($allscore) {
                $allscore = json_encode($allscore);
                $alloppositescore = json_encode($alloppositescore);
            }
            $canvasheight = ($nblabels * 25) + 60;
            $canvaswidth = max(300, (100 + ($maxlen * 7)));
            if (!$allresponses) {
                $pageoutput .= '
                        <canvas id="cvs" width="'.$canvaswidth.'" height="'.$canvasheight.'">[No canvas support]</canvas>
                    ';
            }
            if ($allscore) {
                $pageoutput .= '
                        <canvas id="cvs2" width="'.$canvaswidth.'" height="'.$canvasheight.'">[No canvas support]</canvas>
                    ';
            }
            $pageoutput .= '
                    <script>
                        window.onload = function () {';
            if (!$allresponses) {
                $pageoutput .= '
                            var chart = new RGraph.Bipolar("cvs", '.$score.', '.$oppositescore.');
                            chart.Set("chart.title", "'.$charttitle.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.gutter.center", 0);
                            chart.Set("chart.gutter.left", 10);
                            chart.Set("chart.gutter.top", 40);
                            chart.Set("chart.gutter.bottom", 20);
                            chart.Set("chart.xmax", 100);
                            chart.Set("chart.text.size", 10);
                            chart.Set("chart.text.font", "Courier");
                            chart.Set("chart.colors", '.$chartcolors.');
                            chart.Set("chart.colors.sequential", true);
                            chart.Draw();';
            }
            if ($allscore) {
                $pageoutput .= '
                            var chart = new RGraph.Bipolar("cvs2", '.$allscore.', '.$alloppositescore.');
                            chart.Set("chart.title", "'.$charttitle2.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize2.'");
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.gutter.center", 0);
                            chart.Set("chart.gutter.left", 10);
                            chart.Set("chart.gutter.right", 15);
                            chart.Set("chart.gutter.top", 40);
                            chart.Set("chart.gutter.bottom", 20);
                            chart.Set("chart.xmax", 100);
                            chart.Set("chart.text.size", 10);
                            chart.Set("chart.text.font", "Courier");
                            chart.Set("chart.colors", '.$chartcolors2.');
                            chart.Set("chart.colors.sequential", true);
                            chart.Draw();
                        ';
            }
            $pageoutput .= '
                    }
                    </script>
                ';
            break;

        case 'hbar':
            // The bar colors.
            $chartcolors = array();
            $chartcolors = json_encode($chartcolors);
            $sequential = 'true';
            // Global feedback with only one score.
            if ($feedbacktype == 'global') {
                $lb = explode("|", $globallabel);
                // Just in case there is no pipe separator in label.
                if (count($lb) > 1) {
                    $labels = '';
                    $left = $lb[0];
                    $right = $lb[1];
                    $lenleft = core_text::strlen($left);
                    $lenright = core_text::strlen($right);
                    if ($lenleft < $lenright) {
                        $padlength = $lenright;
                        $left = str_pad($left, $padlength, ' ', STR_PAD_LEFT);
                    }
                    if ($lenleft > $lenright) {
                        $padlength = $lenleft;
                        $right = str_pad($right, $padlength, ' ', STR_PAD_RIGHT);
                    }
                    $labels[0] = $left;
                    $labels[1] = $right;
                }

            } else {
                if ($nblabels > $nbvalues) {
                    for ($i = 1; $i < $nblabels - 1; $i++) {
                        unset($labels[$i]);
                    }
                }
                $sequential = 'false';
            }
            $nblabels = count($labels) + 1;
            $score = json_encode($score);
            if ($allscore) {
                $allscore = json_encode($allscore);
            }
            // Find length of longest label.
            $maxlen = 0;
            foreach ($labels as $label) {
                $labellen = core_text::strlen($label);
                if ($labellen > $maxlen) {
                    $maxlen = $labellen;
                }
            }
            foreach ($labels as $value) {
                $output[] = '"'.$value.'"';
            }
            $labels = '[' . implode(',', $output) . ']';
            $canvasheight = ($nblabels * 20) + 60;
            $charttextfont = 'Courier';
            $gutterleft = ($maxlen * 8) + 5;
            $canvaswidth = 400 + $gutterleft;
            if (!$allresponses) {
                $pageoutput .= '
                    <canvas id="cvs" width="'.$canvaswidth.'" height="'.$canvasheight.'">[No canvas support]</canvas>
                    ';
            }
            if ($allscore) {
                $pageoutput .= '
                        <canvas id="cvs2" width="'.$canvaswidth.'" height="'.$canvasheight.'">[No canvas support]</canvas>
                    ';
            }
            $pageoutput .= '
                    <script>
                        window.onload = function () {';
            if (!$allresponses) {
                $pageoutput .= '
                            var chart = new RGraph.HBar("cvs", '.$score.');
                            chart.Set("chart.title", "'.$charttitle.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("chart.title.x", 400);
                            chart.Set("gutter.left", "'.$gutterleft.'");
                            chart.Set("gutter.right", 2);
                            chart.Set("chart.text.font", "'.$charttextfont.'");
                            chart.Set("labels", '.$labels.');
                            chart.Set("chart.colors", '.$chartcolorsgradient.');
                            chart.Set("chart.colors.sequential", '.$sequential.');
                            chart.Set("xmax",100);
                            chart.Draw();
                            ';
            }
            if ($allscore) {
                $pageoutput .= '
                            var chart = new RGraph.HBar("cvs2", '.$allscore.');
                            chart.Set("chart.title", "'.$charttitle2.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("chart.title.x", 400);
                            chart.Set("gutter.left", "'.$gutterleft.'");
                            chart.Set("gutter.right", 2);
                            chart.Set("chart.text.font", "'.$charttextfont.'");
                            chart.Set("labels", '.$labels.');
                            chart.Set("chart.colors", '.$chartcolorsgradient.');
                            chart.Set("chart.colors.sequential",  '.$sequential.');
                            chart.Set("xmax",100);
                            chart.Draw();
                            ';
            }
            $pageoutput .= '
                    }
                    </script>
                ';
            break;

        case 'radar':
            $score = json_encode($score);
            foreach ($labels as $key => $label) {
                if ($key != 0) {
                    $labels[$key] = wordwrap($label, 20, "\\r\\n");
                } else {
                    $labels[$key] = $label ."\\r\\n";
                }
            }
            foreach ($labels as $value) {
                $output[] = '"'.$value.'"';
            }
            $labels = '[' . implode(',', $output) . ']';
            if ($allscore) {
                $allscore = json_encode($allscore);
            }
            if (!$allresponses) {
                $pageoutput .= '
                        <canvas id="cvs" width="550" height="400">[No canvas support]</canvas>
                        ';
            }
            if ($allscore) {
                $pageoutput .= '
                    <canvas id="cvs2" width="550" height="400">[No canvas support]</canvas>
                    ';
            }
            $pageoutput .= '
                    <script>
                        window.onload = function () {';
            if (!$allresponses) {
                $pageoutput .= '
                            var chart = new RGraph.Radar("cvs", '.$score.');
                            chart.Set("chart.title", "'.$charttitle.'");
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.labels.offset", 15);
                            chart.Set("chart.radius", 150);
                            chart.Set("chart.ymax", 100);
                            chart.Set("chart.labels.axes","n");
                            chart.Draw();
                            ';
            }
            if ($allscore) {
                $pageoutput .= '
                            var chart = new RGraph.Radar("cvs2", '.$allscore.');
                            chart.Set("chart.title", "'.$charttitle2.'");
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.labels.offset", 15);
                            chart.Set("chart.radius", 150);
                            chart.Set("chart.ymax", 100);
                            chart.Set("chart.labels.axes","n");
                            chart.Draw();
                            ';
            }
            $pageoutput .= '
                            }
                            </script>
                    ';
            break;

        case 'rose':
            if ($score != 'null') {
                $score = json_encode($score);
            }

            foreach ($labels as $key => $label) {
                $labels[$key] = wordwrap($label, 8, "\\r\\n");
            }
            foreach ($labels as $value) {
                $output[] = '"'.$value.'"';
            }
            $labels = '[' . implode(',', $output) . ']';

            if ($allscore) {
                $allscore = json_encode($allscore);
            }
            $size = 400;
            if (!$allresponses) {
                $pageoutput .= '
                        <canvas id="cvs" width="'.$size.'" height="'.$size.'">[No canvas support]</canvas>
                    ';
            }
            if ($allscore) {
                $pageoutput .= '&nbsp;&nbsp;&nbsp;
                        <canvas id="cvs2" width="'.$size.'" height="'.$size.'">[No canvas support]</canvas>
                    ';
            }
            $pageoutput .= '
                    <script>
                        window.onload = function () {';
            if (!$allresponses) {
                $pageoutput .= '
                            var chart = new RGraph.Rose("cvs", '.$score.');
                            chart.Set("chart.title", "'.$charttitle.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("chart.title.vpos", 0.2);
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.labels.offset", 10);
                            chart.Set("chart.background.grid.spokes", '.$nblabels.');
                            chart.Set("chart.labels.axes","n");
                            chart.Set("chart.radius", 100);
                            chart.Set("chart.ymax", 100);
                            chart.Set("chart.background.axes", false);
                            chart.Set("chart.colors.sequential", true);
                            chart.Set("chart.colors", ["Gradient(white:red)","Gradient(white:green)","Gradient(white:blue)",
                            "Gradient(white:gray)","Gradient(white:purple)","Gradient(white:pink)",
                                            "Gradient(white:orange)","Gradient(white:black)"]);
                            chart.Draw();
                            ';
            }
            if ($allscore) {
                $pageoutput .= '
                            var chart = new RGraph.Rose("cvs2", '.$allscore.');
                            chart.Set("chart.title", "'.$charttitle2.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("chart.title.vpos", 0.2);
                            chart.Set("chart.labels", '.$labels.');
                            chart.Set("chart.labels.offset", 10);
                            chart.Set("chart.background.grid.spokes", '.$nblabels.');
                            chart.Set("chart.labels.axes","n");
                            chart.Set("chart.radius", 100);
                            chart.Set("chart.ymax", 100);
                            chart.Set("chart.background.axes", false);
                            chart.Set("chart.colors.sequential", true);
                            chart.Set("chart.colors", ["Gradient(white:red)","Gradient(white:green)","Gradient(white:blue)",
                            "Gradient(white:gray)","Gradient(white:purple)","Gradient(white:pink)",
                                            "Gradient(white:orange)","Gradient(white:black)"]);
                            chart.Draw();
                            ';
            }
            $pageoutput .= '
                        }
                    </script>
                ';
            break;

        case 'vprogress':
            if (!$allresponses) {
                $score = $score[0];
            } else {
                $score = null;
            }
            $score = json_encode($score);
            if ($allscore) {
                $allscore = json_encode($allscore[0]);
            }
            // Check presence of pipe separator in label.
            $lb = explode("|", $globallabel);
            $maxlen = 0;
            if (count($lb) > 1) {
                $labels = array_reverse($lb);
                // Find length of longest label.
                $maxlen = 0;
                foreach ($labels as $label) {
                    $labellen = core_text::strlen($label);
                    if ($labellen > $maxlen) {
                        $maxlen = $labellen;
                    }
                }
                foreach ($labels as $value) {
                    $output[] = '"'.$value.'"';
                }
                $labels = '[' . implode(',', $output) . ']';
            } else {
                $labels = '';
            }
            $charttextfont = 'Courier';
            $gutterright = 150 + ($maxlen * 3);
            $canvaswidth = 250 + ($maxlen * 3);

            if (!$allresponses) {
                $pageoutput .= '
                        <canvas id="cvs" width="'.$canvaswidth.'" height="400">[No canvas support]</canvas>
                    ';
            }
            if ($allscore) {
                $pageoutput .= '
                        <canvas id="cvs2" width="250" height="400">[No canvas support]</canvas>
                    ';
            }
            $pageoutput .= '
                    <script>
                        window.onload = function () {';

            if (!$allresponses) {
                $pageoutput .= '
                        var chart = new RGraph.VProgress("cvs", '.$score.',100);
                        chart.Set("chart.gutter.top", 30);
                        chart.Set("chart.gutter.left", 50);
                        chart.Set("chart.gutter.right", "'.$gutterright.'");
                        chart.Set("scale.decimals", 0);
                        chart.Set("chart.text.font", "'.$charttextfont.'");
                        chart.Set("chart.title", "'.$charttitle.'");
                        chart.Set("chart.title.font", "'.$charttitlefont.'");
                        chart.Set("chart.title.size", "'.$charttitlesize.'");
                        ';

                if ($labels) {
                    $pageoutput .= '
                                chart.Set("chart.labels.specific", '.$labels.');
                                ';
                }
                $pageoutput .= '
                                chart.Draw();
                              ';
            }
            if ($allscore) {
                // Display participants graph.
                $pageoutput .= '
                            var chart = new RGraph.VProgress("cvs2", '.$allscore.',100);
                            chart.Set("chart.gutter.top", 30);
                            chart.Set("chart.gutter.left", 50);
                            chart.Set("chart.gutter.right", 150);
                            chart.Set("chart.title", "'.$charttitle2.'");
                            chart.Set("chart.text.font", "'.$charttextfont.'");
                            chart.Set("chart.title.font", "'.$charttitlefont.'");
                            chart.Set("chart.title.size", "'.$charttitlesize.'");
                            chart.Set("scale.decimals", 0);
                            chart.Draw();
                        ';
            }
            $pageoutput .= '
                        }
                    </script>
                ';
            break;
    }

    return $pageoutput;
}