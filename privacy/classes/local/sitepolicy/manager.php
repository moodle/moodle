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
 * Site policy management class.
 *
 * @package    core_privacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\sitepolicy;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Site policy management class.
 *
 * @package    core_privacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Returns the list of plugins that can work as sitepolicy handlers (have class PLUGINNAME\privacy\sitepolicy\handler)
     * @return array
     */
    public function get_all_handlers() {
        $sitepolicyhandlers = [];
        foreach (\core_component::get_plugin_types() as $ptype => $unused) {
            $plugins = \core_component::get_plugin_list_with_class($ptype, 'privacy\local\sitepolicy\handler') +
                \core_component::get_plugin_list_with_class($ptype, 'privacy_local_sitepolicy_handler');
            // Allow plugins to have the class either with namespace or without (useful for unittest).
            foreach ($plugins as $pname => $class) {
                $sitepolicyhandlers[$pname] = $class;
            }
        }
        return $sitepolicyhandlers;
    }

    /**
     * Returns the current site policy handler
     *
     * @return handler
     */
    public function get_handler_classname() {
        global $CFG;

        if (!empty($CFG->sitepolicyhandler)) {
            $sitepolicyhandlers = $this->get_all_handlers();

            if (!isset($sitepolicyhandlers[$CFG->sitepolicyhandler])) {
                return default_handler::class;

            } else {
                return $sitepolicyhandlers[$CFG->sitepolicyhandler];
            }

        } else {
            return default_handler::class;
        }
    }

    /**
     * Checks if the site has site policy defined
     *
     * @param bool $forguests
     * @return bool
     */
    public function is_defined($forguests = false) {
        return component_class_callback($this->get_handler_classname(), 'is_defined', [$forguests]);
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
    public function get_redirect_url($forguests = false) {
        $url = component_class_callback($this->get_handler_classname(), 'get_redirect_url', [$forguests]);
        if ($url && !($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        return $url;
    }

    /**
     * Returns URL of the site policy that needs to be displayed to the user (inside iframe or to use in WS such as mobile app)
     *
     * This page should not have any header/footer, it does not also have any buttons/checkboxes. The caller needs to implement
     * the "Accept" button and call {@link self::accept()} on completion.
     *
     * @param bool $forguests
     * @return moodle_url|null
     */
    public function get_embed_url($forguests = false) {
        $url = component_class_callback($this->get_handler_classname(), 'get_embed_url', [$forguests]);
        if ($url && !($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        return $url;
    }

    /**
     * Accept site policy for the current user
     *
     * @return bool - false if sitepolicy not defined, user is not logged in or user has already agreed to site policy;
     *     true - if we have successfully marked the user as agreed to the site policy
     */
    public function accept() {
        return component_class_callback($this->get_handler_classname(), 'accept', []);
    }

    /**
     * Adds "Agree to site policy" checkbox to the signup form.
     *
     * Sitepolicy handlers can override the simple checkbox with their own controls.
     *
     * @param \MoodleQuickForm $mform
     */
    public function signup_form($mform) {
        component_class_callback($this->get_handler_classname(), 'signup_form', [$mform]);
    }
}
