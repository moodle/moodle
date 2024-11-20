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
use tool_mfa\manager;
use tool_mfa\plugininfo\factor;

require_login(null, false);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('login');
$PAGE->blocks->show_only_fake_blocks();
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);

// Logout if it was requested.
$logout = optional_param('logout', false, PARAM_BOOL);
$sesskey = optional_param('sesskey', '_none_', PARAM_RAW);

if ($logout) {
    if (!confirm_sesskey($sesskey)) {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(
            get_string('logoutconfirm'),
            new moodle_url($PAGE->url, ['logout' => 1, 'sesskey' => sesskey()]),
            new moodle_url('/'),
        );
        echo $OUTPUT->footer();
        die;
    }

    if (!empty($SESSION->wantsurl)) {
        // If we have the wantsurl, we should redirect there, to keep it intact.
        $wantsurl = $SESSION->wantsurl;
    } else {
        // Else redirect home.
        $wantsurl = new \moodle_url($CFG->wwwroot);
    }

    manager::mfa_logout();
    redirect($wantsurl);
}

$currenturl = new moodle_url('/admin/tool/mfa/auth.php');

// Perform state check.
manager::resolve_mfa_status();

// We have a valid landing here, before doing any actions, clear any redir loop progress.
manager::clear_redirect_counter();

// If a specific factor was requested, use it.
$pickedname = optional_param('factorname', false, PARAM_ALPHA);
$pickedfactor = factor::get_factor($pickedname);
$formfactor = optional_param('factor', false, PARAM_ALPHA);

if ($pickedfactor && $pickedfactor->has_input() && $pickedfactor->get_state() == factor::STATE_UNKNOWN) {
    $factor = $pickedfactor;
} else if ($formfactor) {
    // Check if a factor was supplied by the form, such as for a form submission.
    $factor = factor::get_factor($formfactor);
} else {
    // Else, get the next factor that requires input.
    $factor = factor::get_next_user_login_factor();
}

// If ok, perform form actions for input factor.
$form = new login_form($currenturl, ['factor' => $factor], 'post', '', ['class' => 'ignoredirty']);
if ($form->is_submitted()) {
    if (!$form->is_validated() && !$form->is_cancelled()) {
        // Increment the fail counter for the factor,
        // And let the factor handle locking logic.
        $factor->increment_lock_counter();
        manager::resolve_mfa_status(false);
    } else {
        // Set state from user actions.
        if ($form->is_cancelled()) {
            $factor->process_cancel_action();
            // Move to next factor.
            manager::resolve_mfa_status(true);
        } else {
            if ($data = $form->get_data()) {
                // Validation has passed, so before processing, lets action the global form submissions as well.
                $form->globalmanager->submit($data);

                // Did user submit something that causes a fail state?
                if ($factor->get_state() == factor::STATE_FAIL) {
                    manager::resolve_mfa_status(true);
                }

                $factor->set_state(factor::STATE_PASS);
                // Move to next factor.
                manager::resolve_mfa_status(true);
            }
        }
    }
}

$renderer = $PAGE->get_renderer('tool_mfa');
echo $OUTPUT->header();
manager::display_debug_notification();
echo $renderer->verification_form($factor, $form);
echo $OUTPUT->footer();
