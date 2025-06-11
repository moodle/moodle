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

/*
 * Comments management interface
 *
 * @package   core_comment
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

use core_reportbuilder\system_report_factory;
use core_comment\reportbuilder\local\systemreports\comments;

admin_externalpage_setup('comments', '', null, '', array('pagelayout'=>'report'));

$PAGE->requires->js_call_amd('core_comment/admin', 'init');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('comments'));

$report = system_report_factory::create(comments::class, context_system::instance());
$report->set_default_per_page($CFG->commentsperpage);

echo $report->output();

// Render delete selected button.
if ($DB->record_exists('comments', [])) {
    echo $OUTPUT->render(new single_button(
        new moodle_url('#'),
        get_string('deleteselected'),
        'post',
        single_button::BUTTON_PRIMARY,
        ['data-action' => 'comment-delete-selected']
    ));
}

echo $OUTPUT->footer();
