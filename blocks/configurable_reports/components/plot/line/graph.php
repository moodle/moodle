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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../../../config.php");
require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");

require_login();

// error_reporting(E_ALL);
// ini_set('display_erros', true);

$id = required_param('id', PARAM_ALPHANUM);
$reportid = required_param('reportid', PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);

if (!$report = $DB->get_record('block_configurable_reports', ['id' => $reportid])) {
    throw new moodle_exception('reportdoesnotexists');
}

if (!$courseid || !$report->global) {
    $courseid = $report->courseid;
}

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new moodle_exception('No such course id');
}

// Force user login in course (SITE or Course).
if ((int) $course->id === SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

$reportclassname = 'report_' . $report->type;
$reportclass = new $reportclassname($report);

if (!$reportclass->check_permissions($USER->id, $context)) {
    throw new moodle_exception("No permissions");
}

$components = cr_unserialize($report->components);
$graphs = $components['plot']['elements'];
if (!empty($graphs)) {
    $series = [];
    foreach ($graphs as $g) {
        require_once($CFG->dirroot . '/blocks/configurable_reports/components/plot/' . $g['pluginname'] . '/plugin.class.php');
        if ($g['id'] == $id) {
            $classname = 'plugin_' . $g['pluginname'];
            $class = new $classname($report);
            $series = $class->get_series($g['formdata']);
            break;
        }
    }

    if ($g['id'] == $id) {
        $min = optional_param('min', 0, PARAM_INT);
        $max = optional_param('max', 0, PARAM_INT);
        $abcise = optional_param('abcise', -1, PARAM_INT);

        $abciselabel = [];
        if ($abcise != -1) {
            $abciselabel = $series[$abcise]['serie'];
            unset($series[$abcise]);
        }

        // Standard inclusions.
        include($CFG->dirroot . "/blocks/configurable_reports/lib/pChart/pData.class.php");
        include($CFG->dirroot . "/blocks/configurable_reports/lib/pChart/pChart.class.php");

        // Dataset definition.
        $dataset = new pData;
        $lastid = 0;

        foreach ($series as $key => $val) {

            try {
                $dataset->AddPoint($val['serie'], "Serie$key");
                $dataset->AddAllSeries();
                $lastid = $key;
            } catch (Throwable $e) {
                continue;
            }
        }

        if (!empty($abciselabel)) {
            $nk = $lastid + 1;
            $dataset->AddPoint($abciselabel, "Serie$nk");
            $dataset->SetAbsciseLabelSerie("Serie$nk");
        } else {
            $dataset->SetAbsciseLabelSerie();
        }

        foreach ($series as $key => $val) {
            $value = $val['name'];

            if (!is_countable($value)) {
                continue;
            }

            $ishebrew = preg_match("/[\xE0-\xFA]/", iconv("UTF-8", "ISO-8859-8", $value));
            $fixedvalue = ($ishebrew == 1) ? $reportclass->utf8_strrev($value) : $value;
            $dataset->SetSerieName($fixedvalue, "Serie$key");
        }

        // Initialise the graph.
        $test = new pChart(700, 230);
        $test->setFixedScale($min, $max);

        $test->setFontProperties($CFG->dirroot . "/blocks/configurable_reports/lib/Fonts/tahoma.ttf", 8);
        $test->setGraphArea(70, 30, 680, 200);
        $test->drawFilledRoundedRectangle(7, 7, 693, 223, 5, 240, 240, 240);
        $test->drawRoundedRectangle(5, 5, 695, 225, 5, 230, 230, 230);
        $test->drawGraphArea(255, 255, 255, true);

        if (!empty($dataset->GetData())) {
            $test->drawScale($dataset->GetData(), $dataset->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, true, 0, 2);
        }

        $test->drawGrid(4, true, 230, 230, 230, 50);

        // Draw the 0 line.
        $test->setFontProperties($CFG->dirroot . "/blocks/configurable_reports/lib/Fonts/tahoma.ttf", 10);
        $test->drawTreshold(0, 143, 55, 72, true, true);

        // Draw the line graph.
        if (!empty($dataset->GetData())) {
            $test->drawLineGraph($dataset->GetData(), $dataset->GetDataDescription());
            $test->drawPlotGraph($dataset->GetData(), $dataset->GetDataDescription(), 3, 2, 255, 255, 255);

            // Finish the graph.
            $test->setFontProperties($CFG->dirroot . "/blocks/configurable_reports/lib/Fonts/tahoma.ttf", 8);
            $test->drawLegend(75, 35, $dataset->GetDataDescription(), 255, 255, 255);
        }

        ob_clean(); // Hack to clear output and send only IMAGE data to browser.
        $test->Stroke();
    }
}
