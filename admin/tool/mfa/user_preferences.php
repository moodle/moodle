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
 * User preferences page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('error:isguestuser', 'tool_mfa');
}

$action = optional_param('action', '', PARAM_TEXT);
$factorid = optional_param('factorid', 0, PARAM_INT);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/user_preferences.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('preferences:header', 'tool_mfa'));
$PAGE->set_cacheable(false);

if ($node = $PAGE->settingsnav->find('usercurrentsettings', null)) {
    $PAGE->navbar->add($node->get_content(), $node->action());
}
$PAGE->navbar->add(get_string('preferences:header', 'tool_mfa'), new \moodle_url('/admin/tool/mfa/user_preferences.php'));
$OUTPUT = $PAGE->get_renderer('tool_mfa');

echo $OUTPUT->header();
if (!empty($action)) {
    if ($factorid != 0) {
        $instance = \tool_mfa\plugininfo\factor::get_instance_from_id($factorid);
        // Confirm factor is valid for the accessing user.
        if ($USER->id == $instance->userid) {
            $factor = \tool_mfa\plugininfo\factor::get_factor($instance->factor);
            $string = $factor->get_display_name().' - '.$instance->label;
            echo $OUTPUT->notification(get_string('factor'.$action, 'tool_mfa', $string), 'notifysuccess');
        }
    }
}

echo $OUTPUT->active_factors();
echo $OUTPUT->available_factors();

$renderer = $PAGE->get_renderer('tool_mfa');
echo $renderer->get_support_link();

\tool_mfa\manager::display_debug_notification();

echo $OUTPUT->footer();

if (!empty($SESSION->tool_mfa_setwantsurl) && $SESSION->tool_mfa_setwantsurl
    && \tool_mfa\manager::get_total_weight() >= 100) {
    unset($SESSION->wantsurl);
}
