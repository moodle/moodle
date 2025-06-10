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
 * IntelliBoard Help Page.
 *
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 * @package local_intellidata
 */

require_once('../../config.php');

use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;

$context = context_system::instance();

require_login();
require_capability('local/intellidata:viewlti', $context);

$title = get_string('intelliboardhelp', ParamsHelper::PLUGIN);

$PAGE->set_url(new moodle_url("/local/intellidata/help.php"));
$PAGE->set_pagetype('report');
$PAGE->set_context($context);
$PAGE->set_pagelayout(SettingsHelper::get_page_layout());
$PAGE->set_title($title);
$PAGE->set_heading($title);

$renderer = $PAGE->get_renderer(ParamsHelper::PLUGIN);

// Print the page header.
echo $OUTPUT->header();

echo $renderer->render(new \local_intellidata\output\help());

// Finish the page.
echo $OUTPUT->footer();
