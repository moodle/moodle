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
 * List of all board templates.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');

$syscontext = context_system::instance();

require_login();
require_capability('mod/board:managetemplates', $syscontext);

$pageurl = new moodle_url('/mod/board/template/index.php');
$title = get_string('templates', 'mod_board');

if (has_capability('moodle/site:config', $syscontext)) {
    require_once("$CFG->libdir/adminlib.php");
    admin_externalpage_setup('modboardtemplates', '', null, $pageurl, ['pagelayout' => 'admin', 'nosearch' => true]);
} else {
    $PAGE->set_url($pageurl);
    $PAGE->set_context($syscontext);
}

$PAGE->set_secondary_navigation(false);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$buttons = [];

$url = new moodle_url('/mod/board/template/create_ajax.php');
$button = new \mod_board\output\ajax_form\modal\button($url, get_string('template_create', 'mod_board'));
$button->set_form_size('lg');
$buttons[] = $OUTPUT->render($button);

$url = new moodle_url('/mod/board/template/import_ajax.php');
$button = new \mod_board\output\ajax_form\modal\button($url, get_string('template_import', 'mod_board'));
$button->set_form_size('lg');
$buttons[] = $OUTPUT->render($button);

$PAGE->set_button(implode(' ', $buttons) . $PAGE->button);

echo $OUTPUT->header();

$report = \core_reportbuilder\system_report_factory::create(
    \mod_board\reportbuilder\local\systemreports\templates::class,
    $syscontext
);
echo $report->output();

echo $OUTPUT->footer();
