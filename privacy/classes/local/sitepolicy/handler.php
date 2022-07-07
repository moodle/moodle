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
 * Base class for site policy handlers.
 *
 * @package    core_privacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\sitepolicy;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for site policy handlers.
 *
 * If a plugin wants to act as a site policy handler it has to define class
 * PLUGINNAME\privacy\sitepolicy\handler that extends \core_privacy\sitepolicy\handler
 *
 * @package    core_privacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class handler {
    /**
     * Checks if the site has site policy defined
     *
     * @param bool $forguests
     * @return bool
     */
    public static function is_defined($forguests = false) {
        $url = static::get_redirect_url($forguests);
        return !empty($url);
    }

    /**
     * Returns URL to redirect user to when user needs to agree to site policy
     *
     * This is a regular interactive page for web users. It should have normal Moodle header/footers, it should
     * allow user to view policies and accept them.
     *
     * @param bool $forguests
     * @return moodle_url|null (returns null if site policy is not defined)
     */
    abstract public static function get_redirect_url($forguests = false);

    /**
     * Returns URL of the site policy that needs to be displayed to the user (inside iframe or to use in WS such as mobile app)
     *
     * This page should not have any header/footer, it does not also have any buttons/checkboxes. The caller needs to implement
     * the "Accept" button and call {@link self::accept()} on completion.
     *
     * @param bool $forguests
     * @return moodle_url|null
     */
    abstract public static function get_embed_url($forguests = false);

    /**
     * Accept site policy for the current user
     *
     * @return bool - false if sitepolicy not defined, user is not logged in or user has already agreed to site policy;
     *     true - if we have successfully marked the user as agreed to the site policy
     */
    public static function accept() {
        global $USER, $DB;
        if (!isloggedin()) {
            return false;
        }
        if ($USER->policyagreed || !static::is_defined(isguestuser())) {
            return false;
        }

        if (!isguestuser()) {
            // For the guests agreement in stored in session only, for other users - in DB.
            $DB->set_field('user', 'policyagreed', 1, array('id' => $USER->id));
        }
        $USER->policyagreed = 1;
        return true;
    }

    /**
     * Adds "Agree to site policy" checkbox to the signup form.
     *
     * Sitepolicy handlers can override the simple checkbox with their own controls.
     *
     * @param \MoodleQuickForm $mform
     */
    public static function signup_form($mform) {
        if ($url = static::get_embed_url()) {
            $mform->addElement('header', 'policyagreement', get_string('policyagreement'), '');
            $mform->setExpanded('policyagreement');
            $mform->addElement('static', 'policylink', '', '<a href="' . $url .
                '" onclick="this.target=\'_blank\'">' . get_string('policyagreementclick') . '</a>');
            $mform->addElement('checkbox', 'policyagreed', get_string('policyaccept'));
            $mform->addRule('policyagreed', get_string('policyagree'), 'required', null, 'client');
        }
    }
}
