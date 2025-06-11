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
 * Airnotifier message processor to send messages to the APNS provider: airnotfier. (https://github.com/dcai/airnotifier)
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
        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');

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

        // If username is empty we try to retrieve it, since it's required to generate the siteid.
        if (empty($eventdata->userto->username)) {
            $eventdata->userto->username = $DB->get_field('user', 'username', array('id' => $eventdata->userto->id));
        }

        // Site id, to map with Moodle Mobile stored sites.
        $siteid = md5($CFG->wwwroot . $eventdata->userto->username);

        // Airnotifier can handle custom requests using processors (that are Airnotifier plugins).
        // In the extra parameter we indicate which processor to use and also additional data to be handled by the processor.
        // Here we clone the eventdata object because will be deleting/adding attributes.
        $extra = clone $eventdata;

        // Delete attributes that may content private information.
        if (!empty($eventdata->userfrom)) {
            $extra->userfromid = $eventdata->userfrom->id;
            $extra->userfromfullname = fullname($eventdata->userfrom);
            unset($extra->userfrom);
        }
        $extra->usertoid = $eventdata->userto->id;
        unset($extra->userto);

        $extra->processor       = 'moodle';
        $extra->site            = $siteid;
        $extra->date            = (!empty($eventdata->timecreated)) ? $eventdata->timecreated : time();
        $extra->notification    = (!empty($eventdata->notification)) ? 1 : 0;
        $encryptnotifications = get_config('message_airnotifier', 'encryptnotifications') == 1;
        $encryptprocessing = get_config('message_airnotifier', 'encryptprocessing');

        // Site name.
        $site = get_site();
        $extra->sitefullname = clean_param(format_string($site->fullname), PARAM_NOTAGS);
        $extra->siteshortname = clean_param(format_string($site->shortname), PARAM_NOTAGS);

        // Clean HTML and ony allow data not to be ignored by Airnotifier to reduce the payload size.
        if (empty($extra->smallmessage)) {
            $extra->smallmessage = $extra->fullmessage;
        }
        $extra->smallmessage = clean_param($extra->smallmessage, PARAM_NOTAGS);
        unset($extra->fullmessage);
        unset($extra->fullmessagehtml);
        unset($extra->fullmessageformat);
        unset($extra->fullmessagetrust);

        // Send wwwroot to airnotifier.
        $extra->wwwroot = $CFG->wwwroot;

        // We are sending to message to all devices.
        $airnotifiermanager = new message_airnotifier_manager();
        $devicetokens = $airnotifiermanager->get_user_devices($CFG->airnotifiermobileappname, $eventdata->userto->id);

        foreach ($devicetokens as $devicetoken) {
            if (!$devicetoken->enable) {
                continue;
            }

            // Check if we should skip sending the notification.
            if ($encryptnotifications && empty($devicetoken->publickey) &&
                    $encryptprocessing == message_airnotifier_manager::ENCRYPT_UNSUPPORTED_NOT_SEND) {

                continue;   // Avoid sending notifications to devices not supporting encryption.
            }

            // Sending the message to the device.
            $serverurl = $CFG->airnotifierurl . ':' . $CFG->airnotifierport . '/api/v2/push/';
            $header = array('Accept: application/json', 'X-AN-APP-NAME: ' . $CFG->airnotifierappname,
                'X-AN-APP-KEY: ' . $CFG->airnotifieraccesskey);
            $curl = new curl;
            // Push notifications are supposed to be instant, do not wait to long blocking the execution.
            $curl->setopt(array('CURLOPT_TIMEOUT' => 2, 'CURLOPT_CONNECTTIMEOUT' => 2));
            $curl->setHeader($header);

            // Clone the data to avoid modifying the original.
            $deviceextra = clone $extra;

            $deviceextra->encrypted = $encryptnotifications;
            $deviceextra = $this->encrypt_payload($deviceextra, $devicetoken);

            // We use Firebase to deliver all Push Notifications, and for all device types.
            // Firebase has a 4KB payload limit.
            // https://firebase.google.com/docs/cloud-messaging/concept-options#notifications_and_data_messages
            // If the message is over that limit we remove unneeded fields and replace the title with a simple message.
            if (\core_text::strlen(json_encode($deviceextra), '8bit') > 4000) {
                $deviceextra->smallmessage = get_string('view_notification', 'message_airnotifier');
            }

            $params = array(
                'device'    => $devicetoken->platform,
                'token'     => $devicetoken->pushid,
                'extra'     => $deviceextra
            );
            if ($deviceextra->encrypted) {
                // Setting alert to null makes air notifier send the notification as a data payload,
                // this forces Android phones to call the app onMessageReceived function to decrypt the notification.
                // Otherwise notifications are created by the Android system and will not be decrypted.
                $params['alert'] = null;
            }

            // JSON POST raw body request.
            $resp = $curl->post($serverurl, json_encode($params));
        }

        return true;
    }

    /**
     * Encrypt the notification payload.
     *
     * @param stdClass $payload The notification payload.
     * @param stdClass $devicetoken The device token record
     * @return stdClass
     */
    protected function encrypt_payload(stdClass $payload, stdClass $devicetoken): stdClass {
        if (empty($payload->encrypted)) {
            return $payload;
        }

        if (empty($devicetoken->publickey)) {
            $payload->encrypted = false;
            return $payload;
        }

        $publickey = sodium_base642bin($devicetoken->publickey, SODIUM_BASE64_VARIANT_ORIGINAL);
        $fields = [
            'userfromfullname',
            'userfromid',
            'sitefullname',
            'smallmessage',
            'subject',
            'contexturl',
        ];
        foreach ($fields as $field) {
            if (!isset($payload->$field)) {
                continue;
            }
            $payload->$field = sodium_bin2base64(sodium_crypto_box_seal(
                $payload->$field,
                $publickey
            ), SODIUM_BASE64_VARIANT_ORIGINAL);
        }

        // Remove extra fields which may contain personal data.
        // They cannot be encrypted otherwise we would go over the 4KB payload size limit.
        unset($payload->usertoid);
        unset($payload->replyto);
        unset($payload->replytoname);
        unset($payload->siteshortname);
        unset($payload->customdata);
        unset($payload->contexturlname);
        unset($payload->replytoname);
        unset($payload->attachment);
        unset($payload->attachname);

        return $payload;
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

                    $output .= html_writer::start_tag('li', array('id' => $devicetoken->id,
                                                                    'class' => 'airnotifierdevice ' . $dimmed)) . "\n";
                    $output .= html_writer::label($name, 'deviceid-' . $devicetoken->id, array('class' => 'devicelabel ')) . ' ' .
                        html_writer::link('#', $hideshowicon, array('class' => 'hidedevice', 'alt' => 'show/hide')) . "\n";
                    $output .= html_writer::end_tag('li') . "\n";
                }

                // Include the AJAX script to automatically trigger the action.
                $airnotifiermanager->include_device_ajax();

                $output = html_writer::tag('ul', $output, array('class' => 'list-unstyled unstyled',
                    'id' => 'airnotifierdevices'));
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
