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

namespace auth_lti\output;

use core\output\notification;

/**
 * Renderer class for auth_lti.
 *
 * @package    auth_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {
    /**
     * Render the account options view, displayed to instructors on first launch if no account binding exists.
     *
     * @param int $provisioningmode the desired account provisioning mode, see auth_plugin_lti constants for details.
     * @return string the html.
     */
    public function render_account_binding_options_page(int $provisioningmode): string {
        $formaction = new \moodle_url('/auth/lti/login.php');
        $notification = new notification(get_string('firstlaunchnotice', 'auth_lti'), \core\notification::INFO, false);
        $noauthnotice = new notification(get_string('firstlaunchnoauthnotice', 'auth_lti', get_docs_url('Publish_as_LTI_tool')),
            \core\notification::WARNING, false);
        $cancreateaccounts = !get_config('moodle', 'authpreventaccountcreation');
        if ($provisioningmode == \auth_plugin_lti::PROVISIONING_MODE_PROMPT_EXISTING_ONLY) {
            $cancreateaccounts = false;
        }

        $accountinfo = ['isloggedin' => isloggedin()];
        if (isloggedin()) {
            global $USER;
            $accountinfo = array_merge($accountinfo, [
                'firstname' => $USER->firstname,
                'lastname' => $USER->lastname,
                'email' => $USER->email,
                'picturehtml' => $this->output->user_picture($USER,  ['size' => 35, 'class' => 'round']),
            ]);
        }

        $context = [
            'info' => $notification->export_for_template($this),
            'formaction' => $formaction->out(),
            'sesskey' => sesskey(),
            'accountinfo' => $accountinfo,
            'cancreateaccounts' => $cancreateaccounts,
            'noauthnotice' => $noauthnotice->export_for_template($this)
        ];
        return parent::render_from_template('auth_lti/local/ltiadvantage/login', $context);
    }

    /**
     * Render the page displayed when the account binding is complete, letting the user continue to the launch.
     *
     * Callers can provide different messages depending on which type of binding took place. For example, a newly
     * provisioned account may require a slightly different message to an existing account being linked.
     *
     * The return URL is the page the user will be taken back to when they click 'Continue'. This is likely the launch
     * or deeplink launch endpoint but could be any calling code in LTI which wants to use the account binding workflow.
     *
     * @param notification $notification the notification containing the message describing the binding success.
     * @param \moodle_url $returnurl the URL to return to when the user clicks continue on the rendered page.
     * @return string the rendered HTML
     */
    public function render_account_binding_complete(notification $notification, \moodle_url $returnurl): string {
        $context = (object) [
            'notification' => $notification->export_for_template($this),
            'returnurl' => $returnurl->out()
        ];
        return parent::render_from_template('auth_lti/local/ltiadvantage/account_binding_complete', $context);
    }
}
