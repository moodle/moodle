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
 * MFA page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/admin/tool/mfa/lib.php');
require_once($CFG->libdir.'/adminlib.php');

use tool_mfa\local\form\login_form;

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('secure');
$PAGE->blocks->show_only_fake_blocks();
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);

// The only page action allowed here is a logout if it was requested.
$logout = optional_param('logout', false, PARAM_BOOL);
if ($logout) {
    if (!empty($SESSION->wantsurl)) {
        // If we have the wantsurl, we should redirect there, to keep it intact.
        $wantsurl = $SESSION->wantsurl;
    } else {
        // Else redirect home.
        $wantsurl = new \moodle_url($CFG->wwwroot);
    }

    \tool_mfa\manager::mfa_logout();
    redirect($wantsurl);
}

$currenturl = new moodle_url('/admin/tool/mfa/auth.php');

// Perform state check.
\tool_mfa\manager::resolve_mfa_status();

// We have a valid landing here, before doing any actions, clear any redir loop progress.
\tool_mfa\manager::clear_redirect_counter();

$factor = \tool_mfa\plugininfo\factor::get_next_user_factor();
// If ok, perform form actions for input factor.
$form = new login_form($currenturl, ['factor' => $factor]);
if ($form->is_submitted()) {
    if (!$form->is_validated() && !$form->is_cancelled()) {
        // Increment the fail counter for the factor,
        // And let the factor handle locking logic.
        $factor->increment_lock_counter();
        \tool_mfa\manager::resolve_mfa_status(false);
    } else {
        // Set state from user actions.
        if ($form->is_cancelled()) {
            $factor->process_cancel_action();
            // Move to next factor.
            \tool_mfa\manager::resolve_mfa_status(true);
        } else {
            if ($data = $form->get_data()) {
                // Validation has passed, so before processing, lets action the global form submissions as well.
                $form->globalmanager->submit($data);

                // Did user submit something that causes a fail state?
                if ($factor->get_state() == \tool_mfa\plugininfo\factor::STATE_FAIL) {
                    \tool_mfa\manager::resolve_mfa_status(true);
                }

                $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
                // Move to next factor.
                \tool_mfa\manager::resolve_mfa_status(true);
            }
        }
    }
}
$renderer = $PAGE->get_renderer('tool_mfa');
echo $OUTPUT->header();

\tool_mfa\manager::display_debug_notification();

echo $OUTPUT->heading(get_string('pluginname', 'factor_'.$factor->name));
// Check if a notification is required for factor lockouts.
$remattempts = $factor->get_remaining_attempts();
if ($remattempts < get_config('tool_mfa', 'lockout')) {
    echo $OUTPUT->notification(get_string('lockoutnotification', 'tool_mfa', $remattempts), 'notifyerror');
}
$form->display();

echo $renderer->guide_link();
echo $OUTPUT->footer();
