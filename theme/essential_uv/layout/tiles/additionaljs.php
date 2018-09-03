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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard in respect to modifications of the Bootstrap theme.
 * @author      G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$PAGE->requires->js_call_amd('theme_essential_uv/header', 'init');
$PAGE->requires->js_call_amd('theme_essential_uv/footer', 'init');
if (\theme_essential_uv\toolbox::not_lte_ie9()) {
    $oldnavbar = \theme_essential_uv\toolbox::get_setting('oldnavbar');
    $PAGE->requires->js_call_amd('theme_essential_uv/navbar', 'init', array('data' => array('oldnavbar' => $oldnavbar)));
    if ($oldnavbar) {
        // Only need this to change the classes when scrolling when the navbar is in the old position.
        $PAGE->requires->js_call_amd('theme_essential_uv/affix', 'init');
    }
    $breadcrumbstyle = \theme_essential_uv\toolbox::get_setting('breadcrumbstyle');
    if ($PAGE->pagelayout == 'course') {
        $PAGE->requires->js_call_amd('theme_essential_uv/course_navigation', 'init');
    }
    if ($breadcrumbstyle == '1') {
        $PAGE->requires->js_call_amd('theme_essential_uv/jBreadCrumb', 'init');
    }
    if (\theme_essential_uv\toolbox::get_setting('fitvids')) {
        $PAGE->requires->js_call_amd('theme_essential_uv/fitvids', 'init');
    }
}
if ($PAGE->pagelayout == 'mydashboard') {
    if (\theme_essential_uv\toolbox::course_content_search()) {
        $essential_uvsearch = new moodle_url('/theme/essential_uv/inspector.ajax.php');
        $essential_uvsearch->param('sesskey', sesskey());
        $inspectorscourerdata = array('data' => array('theme' => $essential_uvsearch->out(false)));
        $PAGE->requires->js_call_amd('theme_essential_uv/inspector_scourer', 'init', $inspectorscourerdata);
    }
}