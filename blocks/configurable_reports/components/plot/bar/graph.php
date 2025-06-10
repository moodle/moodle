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
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../../../config.php");
require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");

require_login();

// TODO check if this is correct should be int only?
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
if ((int) $courseid === SITEID) {
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
        require_once($CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/class/pDraw.php");
        require_once($CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/class/pData.php");
        require_once($CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/class/pColor.php");
        require_once($CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/class/pException.php");
        require_once($CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/class/pCharts.php");

        // Dataset definition.

        $dataset = new \pChart\pData();
        $labels = array_shift($series);

        // Invert/Reverse Hebrew labels so it can be rendered using PHP imagettftext()
        // Also find the longest value, to aid with sizing the image.
        $longestlabel = 0;
        foreach ($labels as $key => $value) {
            $labellen = strlen($value);
            $labellen > $longestlabel && $longestlabel = $labellen;
            $invertedlabels[$key] = strip_tags(
                (preg_match("/[\xE0-\xFA]/", iconv("UTF-8", "ISO-8859-8", $value))) ? $reportclass->utf8_strrev($value) : $value
            );
        }

        $dataset->addPoints($invertedlabels, "Labels");
        $dataset->setAbscissa("Labels");

        $longestlegend = 0;
        foreach ($series as $name => $valueset) {
            $legendlen = strlen($name);
            $legendlen > $longestlegend && $longestlegend = $legendlen;
            $dataset->addPoints($valueset, $name);
        }

        $width = property_exists($g['formdata'], "width") ? $g['formdata']->width : 900;
        $height = property_exists($g['formdata'], "height") ? $g['formdata']->height : 500;

        $colorr = property_exists($g['formdata'], "color_r") ? $g['formdata']->color_r : 170;
        $colorg = property_exists($g['formdata'], "color_g") ? $g['formdata']->color_g : 183;
        $colorb = property_exists($g['formdata'], "color_b") ? $g['formdata']->color_b : 87;

        $padding = 30;
        $fontsize = 8;
        $fontpath = $CFG->dirroot . "/blocks/configurable_reports/lib/pChart2/fonts";
        $labeloffset = $longestlabel * ($fontsize / 2);
        $minlabeloffset = $padding + 100;
        $maxlabeloffset = $height / 2 + $padding;
        if ($labeloffset < $minlabeloffset) {
            $labeloffset = $minlabeloffset;
        } else if ($labeloffset > $maxlabeloffset) {
            $labeloffset = $maxlabeloffset;
        }
        $legendoffset = ($longestlegend * ($fontsize / 2));
        $maxlegendoffset = $width / 3 + $padding;
        if ($legendoffset > $maxlegendoffset) {
            $legendoffset = $maxlegendoffset;
        }

        $mypicture = new \pChart\pDraw($width, $height);
        $mypicture->myData = $dataset;

        $mypicture->setFontProperties([
            "FontName" => "$fontpath/Cairo-Regular.ttf",
            "FontSize" => $fontsize,
            "Color" => new \pChart\pColor(0, 0, 0),
        ]);

        [$legendwidth, $legendheight] = array_values($mypicture->getLegendSize());
        $legendx = $width - $legendwidth - $padding;
        $legendy = $padding;
        $colnames = array_keys($series);
        $firstcol = $colnames[0];
        $graphx = $padding + (strlen($firstcol) * ($fontsize / 2));
        $graphy = $padding;
        $graphwidth = $legendx - $padding;
        $graphheight = $height - $labeloffset;
        $mypicture->setGraphArea($graphx, $graphy, $graphwidth, $graphheight);

        $scalesettings = [
            "TickR" => 0,
            "TickG" => 0,
            "TickB" => 0,
            "LabelRotation" => 45,
            "DrawSubTicks" => true,
        ];
        $mypicture->drawScale($scalesettings);
        $mypicture->setShadow(true, [
            "X" => 1,
            "Y" => 1,
            "R" => 0,
            "G" => 0,
            "B" => 0,
            "Alpha" => 10,
        ]);

        $chartsettings = [
            "DisplayValues" => true,
            "Rounded" => true,
            "Surrounding" => 60,
            "DisplayR" => 0,
            "DisplayG" => 0,
            "DisplayB" => 0,
            "DisplayOffset" => 5,
            "DrawSubTicks" => true,
        ];

        $pcharts = new \pChart\pCharts($mypicture);
        $pcharts->drawBarChart($chartsettings);

        $mypicture->setShadow(false);
        $mypicture->drawLegend($legendx, $legendy);
        $mypicture->stroke();

        $mypicture->autoOutput();
    }
}
