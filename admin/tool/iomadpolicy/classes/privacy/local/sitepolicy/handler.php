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
 * Site iomadpolicy handler class.
 *
 * @package    tool_iomadpolicy
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\privacy\local\sitepolicy;

defined('MOODLE_INTERNAL') || die();

use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;

/**
 * Class implementation for a site iomadpolicy handler.
 *
 * @package    tool_iomadpolicy
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler extends \core_privacy\local\sitepolicy\handler {

    /**
     * Returns URL to redirect user to when user needs to agree to site iomadpolicy
     *
     * This is a regular interactive page for web users. It should have normal Moodle header/footers, it should
     * allow user to view policies and accept them.
     *
     * @param bool $forguests
     * @return moodle_url|null (returns null if site iomadpolicy is not defined)
     */
    public static function get_redirect_url($forguests = false) {
        // There is no redirect for guests, policies are shown in the popup, only return redirect url for the logged in users.
        if (!$forguests && api::get_current_versions_ids(iomadpolicy_version::AUDIENCE_LOGGEDIN)) {
            return new \moodle_url('/admin/tool/iomadpolicy/index.php');
        }
        return null;
    }

    /**
     * Returns URL of the site iomadpolicy that needs to be displayed to the user (inside iframe or to use in WS such as mobile app)
     *
     * This page should not have any header/footer, it does not also have any buttons/checkboxes. The caller needs to implement
     * the "Accept" button and call {@link self::accept()} on completion.
     *
     * @param bool $forguests
     * @return moodle_url|null
     */
    public static function get_embed_url($forguests = false) {
        if (api::get_current_versions_ids($forguests ? iomadpolicy_version::AUDIENCE_GUESTS : iomadpolicy_version::AUDIENCE_LOGGEDIN)) {
            return new \moodle_url('/admin/tool/iomadpolicy/viewall.php');
        }
        return null;
    }

    /**
     * Accept site iomadpolicy for the current user
     *
     * @return bool - false if siteiomadpolicy not defined, user is not logged in or user has already agreed to site iomadpolicy;
     *     true - if we have successfully marked the user as agreed to the site iomadpolicy
     */
    public static function accept() {
        global $USER, $DB;

        if (!isloggedin()) {
            return false;
        }

        if ($USER->policyagreed) {
            return false;
        }

        if (isguestuser()) {
            // For guests, agreement is stored in the session only.
            $USER->policyagreed = 1;
            return true;
        }

        // Find all compulsory policies and mark them as accepted.
        $compulsory = [];
        foreach (api::list_current_versions(iomadpolicy_version::AUDIENCE_LOGGEDIN) as $iomadpolicyversion) {
            if ($iomadpolicyversion->optional == iomadpolicy_version::AGREEMENT_COMPULSORY) {
                $compulsory[] = $iomadpolicyversion->id;
            }
        }

        if ($compulsory) {
            api::accept_policies($compulsory);
        }

        // Mark policies as agreed.
        $DB->set_field('user', 'iomadpolicyagreed', 1, array('id' => $USER->id));
        $USER->policyagreed = 1;

        return true;
    }

    /**
     * Adds "Agree to site iomadpolicy" checkbox to the signup form.
     *
     * @param \MoodleQuickForm $mform
     */
    public static function signup_form($mform) {
        if (static::is_defined()) {
            // This plugin displays policies to the user who is signing up before the signup form is shown.
            // By the time user has access to signup form they have already agreed to all compulsory policies.
            $mform->addElement('hidden', 'iomadpolicyagreed', 1);
            $mform->setType('iomadpolicyagreed', PARAM_INT);
        }
    }
}
