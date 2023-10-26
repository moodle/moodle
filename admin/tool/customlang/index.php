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
 * @package    tool
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true); // progress bar is used here

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/customlang/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

require_login(null, false);
require_capability('tool/customlang:view', context_system::instance());

$action  = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$lng     = optional_param('lng', '', PARAM_LANG);
$next     = optional_param('next', 'edit', PARAM_ALPHA);

admin_externalpage_setup('toolcustomlang');
$langs = get_string_manager()->get_list_of_translations();

$PAGE->set_primary_active_tab('siteadminnode');

// pre-output actions
if ($action === 'checkout') {
    require_sesskey();
    require_capability('tool/customlang:edit', context_system::instance());
    if (empty($lng)) {
        throw new \moodle_exception('missingparameter');
    }

    $PAGE->set_cacheable(false);    // progress bar is used here
    $output = $PAGE->get_renderer('tool_customlang');
    echo $output->header();
    echo $output->heading(get_string('pluginname', 'tool_customlang'));
    $progressbar = new progress_bar();
    $progressbar->create();         // prints the HTML code of the progress bar

    // we may need a bit of extra execution time and memory here
    core_php_time_limit::raise(HOURSECS);
    raise_memory_limit(MEMORY_EXTRA);
    tool_customlang_utils::checkout($lng, $progressbar);

    echo $output->continue_button(new moodle_url("/admin/tool/customlang/{$next}.php", array('lng' => $lng)), 'get');
    echo $output->footer();
    exit;
}
if ($action === 'checkin') {
    require_sesskey();
    require_capability('tool/customlang:edit', context_system::instance());
    if (empty($lng)) {
        throw new \moodle_exception('missingparameter');
    }

    if (!$confirm) {
        $output = $PAGE->get_renderer('tool_customlang');
        echo $output->header();
        echo $output->heading(get_string('pluginname', 'tool_customlang'));
        echo $output->heading($langs[$lng], 3);
        $numofmodified = tool_customlang_utils::get_count_of_modified($lng);
        if ($numofmodified != 0) {
            echo $output->heading(get_string('modifiednum', 'tool_customlang', $numofmodified), 3);
            echo $output->confirm(get_string('confirmcheckin', 'tool_customlang'),
                                  new moodle_url($PAGE->url, array('action'=>'checkin', 'lng'=>$lng, 'confirm'=>1)),
                                  new moodle_url($PAGE->url, array('lng'=>$lng)));
        } else {
            echo $output->heading(get_string('modifiedno', 'tool_customlang', $numofmodified), 3);
            echo $output->continue_button(new moodle_url($PAGE->url, array('lng' => $lng)));
        }
        echo $output->footer();
        die();

    } else {
        tool_customlang_utils::checkin($lng);
        redirect($PAGE->url);
    }
}

$output = $PAGE->get_renderer('tool_customlang');

// output starts here
echo $output->header();
echo $output->heading(get_string('pluginname', 'tool_customlang'));

if (empty($lng)) {
    $s = new single_select($PAGE->url, 'lng', $langs);
    $s->label = get_accesshide(get_string('language'));
    $s->class = 'langselector';
    echo $output->box($OUTPUT->render($s), 'langselectorbox');
    echo $OUTPUT->footer();
    exit;
}

echo $output->heading($langs[$lng], 3);

$numofmodified = tool_customlang_utils::get_count_of_modified($lng);

if ($numofmodified != 0) {
    echo $output->heading(get_string('modifiednum', 'tool_customlang', $numofmodified), 3);
}

$menu = array();
if (has_capability('tool/customlang:edit', context_system::instance())) {
    $menu['checkout'] = array(
        'title'     => get_string('checkout', 'tool_customlang'),
        'url'       => new moodle_url($PAGE->url, array('action' => 'checkout', 'lng' => $lng)),
        'method'    => 'post',
    );
    if ($numofmodified != 0) {
        $menu['checkin'] = array(
            'title'     => get_string('checkin', 'tool_customlang'),
            'url'       => new moodle_url($PAGE->url, array('action' => 'checkin', 'lng' => $lng)),
            'method'    => 'post',
        );
    }
    $menu['import'] = array(
        'title'     => get_string('import', 'tool_customlang'),
        'url'       => new moodle_url($PAGE->url, ['action' => 'checkout', 'lng' => $lng, 'next' => 'import']),
        'method'    => 'post',
    );
}
if (has_capability('tool/customlang:export', context_system::instance())) {
    $langdir = tool_customlang_utils::get_localpack_location($lng);
    if (check_dir_exists(dirname($langdir)) && count(glob("$langdir/*"))) {
        $menu['export'] = [
            'title'     => get_string('export', 'tool_customlang'),
            'url'       => new moodle_url("/admin/tool/customlang/export.php", ['lng' => $lng]),
            'method'    => 'post',
        ];
    }
}
echo $output->render(new tool_customlang_menu($menu));

echo $output->footer();
