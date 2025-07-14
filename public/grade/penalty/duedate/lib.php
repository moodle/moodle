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
 * The gradepenalty_duedate lib file.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\url;

/**
 * Extend the course navigation with a penalty rule settings.
 *
 * @param navigation_node $navigation The settings navigation object
 * @param stdClass $course The course
 * @param context $context Course context
 * @return void
 */
function gradepenalty_duedate_extend_navigation_course(navigation_node $navigation, stdClass $course, context $context): void {
    if (has_capability('gradepenalty/duedate:manage', $context)) {
        $url = new url('/grade/penalty/duedate/manage_penalty_rule.php', ['contextid' => $context->id]);
        $name = get_string('penaltyrule', 'gradepenalty_duedate');
        $navigation->add($name, $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
    }
}

/**
 * Extend the module navigation with a penalty rule settings.
 *
 * @param navigation_node $navigation The settings navigation object
 * @param cm_info $cm The course module
 * @return void
 */
function gradepenalty_duedate_extend_navigation_module(navigation_node $navigation, cm_info $cm): void {
    $context = context_module::instance($cm->id);
    if (has_capability('gradepenalty/duedate:manage', $context)) {
        $url = new url('/grade/penalty/duedate/manage_penalty_rule.php', ['contextid' => $context->id]);
        $name = get_string('penaltyrule', 'gradepenalty_duedate');
        $navigation->add($name, $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));
    }
}

/**
 * Load penalty rule form.
 *
 * @param array $args parameters to load the form
 * @return string html and js of the form
 */
function gradepenalty_duedate_output_fragment_penalty_rule_form(array $args): string {
    $context = $args['context'];

    $params = [
        'contextid' => $context->id,
        'action' => new url('/grade/penalty/duedate/manage_penalty_rule.php', ['contextid' => $context->id]),
        'penaltyrules' => json_decode($args['penaltyrules'], true),
        'finalpenaltyrule' => $args['finalpenaltyrule'],
    ];

    // Load edit penalty form.
    $form = new gradepenalty_duedate\output\form\edit_penalty_form($params['action'], $params);

    // Return html and js.
    return $form->render();
}

/**
 * Define the setting page for the penalty rule.
 */
function gradepenalty_duedate_get_settings_url(): url {
    return new url('/grade/penalty/duedate/manage_penalty_rule.php', [
        'contextid' => \core\context\system::instance()->id,
    ]);
}
