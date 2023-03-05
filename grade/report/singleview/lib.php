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
 * Singleview report generic functions
 *
 * @package gradereport_singleview
 * @copyright 2023 Ilya Tregubov
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns link to singleview report for the current element
 *
 * @param context_course $context Course context
 * @param int $courseid Course ID
 * @param array  $element An array representing an element in the grade_tree
 * @param grade_plugin_return $gpr A grade_plugin_return object
 * @param string $mode Mode - gradeitem or user
 * @param ?stdClass $templatecontext Template context
 * @return stdClass|null
 */
function gradereport_singleview_get_report_link(context_course $context, int $courseid,
        array $element, grade_plugin_return $gpr, string $mode, ?stdClass $templatecontext): ?stdClass {

    $reportstring = grade_helper::get_lang_string('singleviewreport_' . $mode, 'gradereport_singleview');
    if (!isset($templatecontext)) {
        $templatecontext = new stdClass();
    }

    if ($mode == 'gradeitem') {
        // View all grades items.
        // FIXME: MDL-52678 This is extremely hacky we should have an API for inserting grade column links.
        if (get_capability_info('gradereport/singleview:view')) {
            if (has_all_capabilities(['gradereport/singleview:view', 'moodle/grade:viewall',
                'moodle/grade:edit'], $context)) {

                $url = new moodle_url('/grade/report/singleview/index.php', [
                    'id' => $courseid,
                    'item' => 'grade',
                    'itemid' => $element['object']->id
                ]);
                $gpr->add_url_params($url);
                $templatecontext->reporturl0 = html_writer::link($url, $reportstring,
                    ['class' => 'dropdown-item', 'aria-label' => $reportstring, 'role' => 'menuitem']);
                return $templatecontext;
            }
        }
    } else if ($mode == 'user') {
        // FIXME: MDL-52678 This get_capability_info is hacky and we should have an API for inserting grade row links instead.
        $canseesingleview = false;
        if (get_capability_info('gradereport/singleview:view')) {
            $canseesingleview = has_all_capabilities(['gradereport/singleview:view',
                'moodle/grade:viewall', 'moodle/grade:edit'], $context);
        }

        if ($canseesingleview) {
            $url = new moodle_url('/grade/report/singleview/index.php',
                ['id' => $courseid, 'itemid' => $element['userid'], 'item' => 'user']);
            $gpr->add_url_params($url);
            $templatecontext->reporturl0 = html_writer::link($url, $reportstring,
                ['class' => 'dropdown-item', 'aria-label' => $reportstring, 'role' => 'menuitem']);
            return $templatecontext;
        }
    }
    return null;
}
