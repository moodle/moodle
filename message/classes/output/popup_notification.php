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
 * Contains class used to prepare a popup notification for display.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output;

require_once($CFG->dirroot . '/message/lib.php');

use renderable;
use templatable;
use moodle_url;
use core_user;

/**
 * Class to prepare a popup notification for display.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class popup_notification implements templatable, renderable {

    /**
     * The notification.
     */
    protected $notification;

    /**
     * Indicates if the receiver of the notification should have their
     * details embedded in the output.
     */
    protected $embeduserto;

    /**
     * Indicates if the sender of the notification should have their
     * details embedded in the output.
     */
    protected $embeduserfrom;

    /**
     * Indicates if the receiver of the notification should have their
     * notification preferences embedded in the output.
     */
    protected $embedpreference;

    /**
     * A cache for the receiver's full name, if it's already known, so that
     * a DB lookup isn't required.
     */
    protected $usertofullname;

    /**
     * Constructor.
     *
     * @param \stdClass $notification
     */
    public function __construct($notification, $embeduserto,
        $embeduserfrom, $embedpreference, $usertofullname = '') {

        $this->notification = $notification;
        $this->embeduserto = $embeduserto;
        $this->embeduserfrom = $embeduserfrom;
        $this->embedpreference = $embedpreference;
        $this->usertofullname = $usertofullname;
    }

    public function export_for_template(\renderer_base $output) {
        global $USER;

        $context = clone $this->notification;

        if ($context->useridto == $USER->id && $context->timeusertodeleted) {
            $context->deleted = true;
        } else {
            $context->deleted = false;
        }

        // We need to get the user from the query.
        if ($this->embeduserfrom) {
            // Check for non-reply and support users.
            if (core_user::is_real_user($context->useridfrom)) {
                $user = new \stdClass();
                $user = username_load_fields_from_object($user, $context, 'userfrom');
                $profileurl = new moodle_url('/user/profile.php', array('id' => $context->useridfrom));
                $context->userfromfullname = fullname($user);
                $context->userfromprofileurl = $profileurl->out();
            } else {
                $context->userfromfullname = get_string('coresystem');
            }
        }

        // We need to get the user from the query.
        if ($this->embeduserto) {
            if (empty($this->usertofullname)) {
                $user = new \stdClass();
                $user = username_load_fields_from_object($user, $context, 'userto');
                $context->usertofullname = fullname($user);
            } else {
                $context->usertofullname = $this->usertofullname;
            }
        }

        $context->timecreatedpretty = get_string('ago', 'message', format_time(time() - $context->timecreated));
        $context->text = message_format_message_text($context);
        $context->read = $context->timeread ? true : false;

        if (!empty($context->component) && substr($context->component, 0, 4) == 'mod_') {
            $iconurl = $output->pix_url('icon', $context->component);
        } else {
            $iconurl = $output->pix_url('i/marker', 'core');
        }

        $context->iconurl = $iconurl->out();

        // We only return the logged in user's preferences, so if it isn't the sender or receiver
        // of this notification then skip embedding the preferences.
        if ($this->embedpreference && !empty($context->component) && !empty($context->eventtype)
                && $USER->id == $context->useridto) {
            $key = 'message_provider_' . $context->component . '_' . $context->eventtype;
            $context->preference = array(
                'key' => $key,
                'loggedin' => get_user_preferences($key . '_loggedin', $USER->id),
                'loggedoff' => get_user_preferences($key . '_loggedoff', $USER->id),
            );
        }

        return $context;
    }
}
