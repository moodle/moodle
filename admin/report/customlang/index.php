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
 * Performs checkout of the strings into the translation table
 *
 * @package    report
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/report/customlang/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

require_login(SITEID, false);
require_capability('report/customlang:view', get_system_context());

$action  = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', false, PARAM_BOOL);

admin_externalpage_setup('reportcustomlang');

// pre-output actions
if ($action === 'checkout') {
    require_sesskey();
    require_capability('report/customlang:edit', get_system_context());
    report_customlang_utils::checkout(current_language());
    redirect(new moodle_url('/admin/report/customlang/edit.php'));
}

if ($action === 'checkin') {
    require_sesskey();
    require_capability('report/customlang:edit', get_system_context());

    if (!$confirm) {
        $output = $PAGE->get_renderer('report_customlang');
        echo $output->header();
        echo $output->heading(get_string('pluginname', 'report_customlang'));
        $numofmodified = report_customlang_utils::get_count_of_modified(current_language());
        if ($numofmodified != 0) {
            echo $output->heading(get_string('modifiednum', 'report_customlang', $numofmodified), 3);
            echo $output->confirm(get_string('confirmcheckin', 'report_customlang'),
                                  new moodle_url($PAGE->url, array('action'=>'checkin', 'confirm'=>1)), $PAGE->url);
        } else {
            echo $output->heading(get_string('modifiedno', 'report_customlang', $numofmodified), 3);
            echo $output->continue_button($PAGE->url);
        }
        echo $output->footer();
        die();

    } else {
        report_customlang_utils::checkin(current_language());
        redirect($PAGE->url);
    }
}

$output = $PAGE->get_renderer('report_customlang');

// output starts here
echo $output->header();
echo $output->heading(get_string('pluginname', 'report_customlang'));

echo $output->box($output->lang_menu(), 'langmenubox');

$numofmodified = report_customlang_utils::get_count_of_modified(current_language());

if ($numofmodified != 0) {
    echo $output->heading(get_string('modifiednum', 'report_customlang', $numofmodified), 3);
}

$menu = array();
if (has_capability('report/customlang:edit', get_system_context())) {
    $menu['checkout'] = array(
        'title'     => get_string('checkout', 'report_customlang'),
        'url'       => new moodle_url($PAGE->url, array('action' => 'checkout')),
        'method'    => 'post',
    );
    if ($numofmodified != 0) {
        $menu['checkin'] = array(
            'title'     => get_string('checkin', 'report_customlang'),
            'url'       => new moodle_url($PAGE->url, array('action' => 'checkin')),
            'method'    => 'post',
        );
    }
}
echo $output->render(new report_customlang_menu($menu));

echo $output->footer();
