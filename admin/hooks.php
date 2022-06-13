<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Hooks overview page.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('hooksoverview');
require_capability('moodle/site:config', \core\context\system::instance());

$hookmanager = \core\hook\manager::get_instance();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('hooksoverview', 'core_admin'));

$table = new html_table();
$table->head = [get_string('hookname', 'core_admin'), get_string('hookcallbacks', 'core_admin'),
    get_string('hookdescription', 'core_admin'), get_string('hookdeprecates', 'core_admin')];
$table->align = ['left', 'left', 'left', 'left'];
$table->id = 'hookslist';
$table->attributes['class'] = 'admintable generaltable';
$table->data = [];

// All hooks referenced from db/hooks.php files.
$allhooks = $hookmanager->get_all_callbacks();

// Add unused hooks.
$candidates = $hookmanager->discover_known_hooks();
foreach ($candidates as $classname) {
    if (isset($allhooks[$classname])) {
        continue;
    }
    $allhooks[$classname] = [];
}

foreach ($allhooks as $hookclass => $callbacks) {
    $cbinfo = [];
    foreach ($callbacks as $definition) {
        $iscallable = is_callable($definition['callback'], true, $callbackname);
        $isoverridden = isset($CFG->hooks_callback_overrides[$hookclass][$definition['callback']]);
        $info = $callbackname . '&nbsp(' . $definition['priority'] . ')';
        if (!$iscallable) {
            $info .= ' <span class="badge badge-danger">' . get_string('error') . '</span>';
        }
        if ($isoverridden) {
            // The lang string meaning should be close enough here.
            $info .= ' <span class="badge badge-warning">' . get_string('configoverride', 'core_admin') . '</span>';
        }

        $cbinfo[] = $info;
    }
    if ($cbinfo) {
        foreach ($cbinfo as $k => $v) {
            $class = '';
            if ($definition['disabled']) {
                $class = 'dimmed_text';
            }
            $cbinfo[$k] = "<li class='$class'>" . $v . '</li>';
        }
        $cbinfo = '<ol>' . implode("\n", $cbinfo) . '</ol>';
    } else {
        $cbinfo = '';
    }

    if (!class_exists($hookclass)) {
        // This could be from a contrib plugin that is compatible with multiple Moodle branches.
        $description = '<span class="badge badge-warning">' . get_string('hookmissing', 'core_admin') . '</span>';
    } else {
        $rc = new \ReflectionClass($hookclass);
        if ($rc->implementsInterface(\core\hook\described_hook::class)) {
            $description = call_user_func([$hookclass, 'get_hook_description']);
            $description = clean_text(markdown_to_html($description), FORMAT_HTML);
        } else {
            $description = '<small>' . get_string('hookdescriptionmissing', 'core_admin') . '</small>';
        }
    }

    $deprecates = '';
    if (class_exists($hookclass) && $rc->implementsInterface(\core\hook\deprecated_callback_replacement::class)) {
        $deprecates = call_user_func([$hookclass, 'get_deprecated_plugin_callbacks']);
        if ($deprecates) {
            foreach ($deprecates as $k => $v) {
                $deprecates[$k] = '<li>' . $v . '</li>';
            }
            $deprecates = '<ul>' . implode("\n", $deprecates) . '</ul>';
        }
    }

    $table->data[] = new html_table_row([$hookclass, $cbinfo, $description, $deprecates]);
}

echo html_writer::table($table);

echo $OUTPUT->footer();
