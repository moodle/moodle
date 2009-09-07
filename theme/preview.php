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
 * This page alows you to preview an arbitrary theme before selecting it.
 */

require_once("../config.php");

$preview = optional_param('preview','standard',PARAM_FILE); // which theme to show

if (!file_exists($CFG->themedir .'/'. $preview)) {
    $preview = 'standard';
}

require_login();

require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$PAGE->force_theme($preview);

$strthemes = get_string('themes');
$strpreview = get_string('preview');

$PAGE->navbar->add($strthemes);
$PAGE->navbar->add($strpreview);
$PAGE->set_title("$SITE->shortname: $strpreview");
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->box_start();
echo $OUTPUT->heading($preview);
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
