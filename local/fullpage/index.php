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
 * Version information
 * @package    local_fullpage
 * @copyright  Huseyin Yemen  - http://themesalmond.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once(__DIR__ . '/lib.php');
global $USER, $PAGE;
$pageid = optional_param('id', '', PARAM_RAW);
$id = substr($pageid, 1);
$as = substr($pageid, 0, 1);
// If we require user to be logged in.
// Only advanced pages have a login option.
if ($as == "a") {
    if (get_config('local_fullpage', 'loginrequired'.$id)) {
        // Log them in and then redirect them back to the form.
        if (!isloggedin() || isguestuser()) {
            require_login();
        }
    }
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagetype('site-index');
$PAGE->set_pagelayout('standard');
$PAGE->set_title("Information Pages");
$PAGE->set_heading('Information Pages');
$templatecontext = local_fullpage_render($pageid);

$PAGE->set_url("{$CFG->wwwroot}/local/fullpage", ['id' => $pageid]);

if (empty($templatecontext['pageenabled'])) {
    redirect("$CFG->wwwroot/");
}
if (!empty($templatecontext['pagetitle'])) {
    $PAGE->navbar->add($templatecontext['pagetitle']);
    $PAGE->set_title($templatecontext['pagetitle']);
    $PAGE->set_heading($templatecontext['pagetitle']);
} else {
    $PAGE->navbar->add(get_string('page', 'local_fullpage'));
}

$PAGE->requires->css('/local/fullpage/style/main.css');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_fullpage/main', $templatecontext);
echo $OUTPUT->footer();

die();
exit;
