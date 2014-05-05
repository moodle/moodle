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
 * Airnotifier message processor to send messages to the APNS provider: airnotfier. (https://github.com/dongsheng/airnotifier)
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */


require_once($CFG->dirroot . '/message/output/lib.php');

/**
 * Message processor class
 *
 * @package   message_airnotifier
 * @copyright 2012 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_airnotifier extends message_output {

    /**
     * Processes the message and sends a notification via airnotifier
     *
     * @param stdClass $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     * @return true if ok, false if error
     */
    public function send_message($eventdata) {
        global $CFG;

        if (!empty($CFG->noemailever)) {
            // Hidden setting for development sites, set in config.php if needed.
            debugging('$CFG->noemailever active, no airnotifier message sent.', DEBUG_MINIMAL);
            return true;
        }

        // Skip any messaging suspended and deleted users.
        if ($eventdata->userto->auth === 'nologin' or
            $eventdata->userto->suspended or
            $eventdata->userto->deleted) {
            return true;
        }

        // Site id, to map with Moodle Mobile stored sites.
        $siteid = md5($CFG->wwwroot . $eventdata->userto->username);

        // Mandatory notification data that need to be sent in the payload. They have variable length.
        // We need to take them in consideration to calculate the maximum message size.
        $notificationdata = array(
            "site" => $siteid,
            "type" => $eventdata->component . '_' . $eventdata->name,
            "device" => "xxxxxxxxxx",   // Since at this point we don't know the device, we use a 10 chars device platform.
            "notif" => "x",             // 1 or 0 wheter is a notification or not (it may be a private message).
            "userfrom" => (!empty($eventdata->userfrom)) ? fullname($eventdata->userfrom) : ''
        );

        // Calculate the size of the message knowing Apple payload must be lower than 256 bytes.
        // Airnotifier using few bytes of the payload, we must limit our message to even less characters.
        $maxmsgsize = 205 - core_text::strlen(json_encode($notificationdata));
        $message = s($eventdata->smallmessage);
        // If the message size is too big make it shorter.
        if (core_text::strlen($message) >= $maxmsgsize) {

            // Cut the message to the maximum possible size. -4 for the the ending 3 dots (...).
            $message = core_text::substr($message, 0 , $maxmsgsize - 4);

            // We need to check when the message is "escaped" then the message is not too long.
            $encodedmsgsize = core_text::strlen(json_encode($message));
            if ($encodedmsgsize > $maxmsgsize) {
                $totalescapedchar = $encodedmsgsize - core_text::strlen($message);
                // Cut the message to the maximum possible size (taking the escaped character in account).
                $message = core_text::substr($message, 0 , $maxmsgsize - 4 - $totalescapedchar);
            }

            $message = $message . '...';
        }

        // We are sending to message to all devices.
        $airnotifiermanager = new message_airnotifier_manager();
        $devicetokens = $airnotifiermanager->get_user_devices($CFG->airnotifiermobileappname, $eventdata->userto->id);

        foreach ($devicetokens as $devicetoken) {

            if (!$devicetoken->enable) {
                continue;
            }

            // Sending the message to the device.
            $serverurl = $CFG->airnotifierurl . ':' . $CFG->airnotifierport . '/notification/';
            $header = array('Accept: application/json', 'X-AN-APP-NAME: ' . $CFG->airnotifierappname,
                'X-AN-APP-KEY: ' . $CFG->airnotifieraccesskey);
            $curl = new curl;
            $curl->setHeader($header);
            $params = array(
                'alert'     => $message,
                'date'      => (!empty($eventdata->timecreated)) ? $eventdata->timecreated : time(),
                'site'      => $siteid,
                'type'      => $eventdata->component . '_' . $eventdata->name,
                'userfrom'  => (!empty($eventdata->userfrom)) ? fullname($eventdata->userfrom) : '',
                'device'    => $devicetoken->platform,
                'notif'     => (!empty($eventdata->notification)) ? '1' : '0',
                'token'     => $devicetoken->pushid);
            $resp = $curl->post($serverurl, $params);
        }

        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    public function config_form($preferences) {
        global $CFG, $OUTPUT, $USER, $PAGE;

        $systemcontext = context_system::instance();
        if (!has_capability('message/airnotifier:managedevice', $systemcontext)) {
            return get_string('nopermissiontomanagedevices', 'message_airnotifier');
        }

        if (!$this->is_system_configured()) {
            return get_string('notconfigured', 'message_airnotifier');
        } else {

            $PAGE->requires->css('/message/output/airnotifier/style.css');

            $airnotifiermanager = new message_airnotifier_manager();
            $devicetokens = $airnotifiermanager->get_user_devices($CFG->airnotifiermobileappname, $USER->id);

            if (!empty($devicetokens)) {
                $output = '';

                foreach ($devicetokens as $devicetoken) {

                    if ($devicetoken->enable) {
                        $hideshowiconname = 't/hide';
                        $dimmed = '';
                    } else {
                        $hideshowiconname = 't/show';
                        $dimmed = 'dimmed_text';
                    }

                    $hideshowicon = $OUTPUT->pix_icon($hideshowiconname, get_string('showhide', 'message_airnotifier'));
                    $name = "{$devicetoken->name} {$devicetoken->model} {$devicetoken->platform} {$devicetoken->version}";
                    $hideurl = new moodle_url('message/output/airnotifier/action.php',
                                    array('hide' => !$devicetoken->enable, 'deviceid' => $devicetoken->id,
                                        'sesskey' => sesskey()));

                    $output .= html_writer::start_tag('li', array('id' => $devicetoken->id,
                                                                    'class' => 'airnotifierdevice ' . $dimmed)) . "\n";
                    $output .= html_writer::label($name, 'deviceid-' . $devicetoken->id, array('class' => 'devicelabel ')) . ' ' .
                        html_writer::link($hideurl, $hideshowicon, array('class' => 'hidedevice', 'alt' => 'show/hide')) . "\n";
                    $output .= html_writer::end_tag('li') . "\n";
                }

                // Include the AJAX script to automatically trigger the action.
                $airnotifiermanager->include_device_ajax();

                $output = html_writer::tag('ul', $output, array('id' => 'airnotifierdevices'));
            } else {
                $output = get_string('nodevices', 'message_airnotifier');
            }
            return $output;
        }
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    public function process_form($form, &$preferences) {
        return true;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    public function load_data(&$preferences, $userid) {
        return true;
    }

    /**
     * Tests whether the airnotifier settings have been configured
     * @return boolean true if airnotifier is configured
     */
    public function is_system_configured() {
        $airnotifiermanager = new message_airnotifier_manager();
        return $airnotifiermanager->is_system_configured();
    }
}

