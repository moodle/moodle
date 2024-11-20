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
 * Strings for component 'message_airnotifier', language 'en'
 *
 * @package    message_airnotifier
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['airnotifieraccesskey'] = 'Airnotifier access key';
$string['airnotifierappname'] = 'Airnotifier app name';
$string['airnotifierfielderror'] = 'Please remove any empty spaces or unnecessary characters from the following field: {$a}';
$string['airnotifiermobileappname'] = 'Mobile app name';
$string['airnotifierport'] = 'Airnotifier port';
$string['airnotifierurl'] = 'Airnotifier URL';
$string['checkconfiguration'] = 'Check and test push notification configuration';
$string['configairnotifierurl'] = 'The server URL to connect to for sending push notifications.';
$string['configairnotifierport'] = 'The port to use when connecting to the airnotifier server.';
$string['configairnotifieraccesskey'] = 'The access key for connecting to the Airnotifier server. You can obtain an access key by clicking the "Request access key" link below (registered sites only) or by creating an account on the <a href="https://apps.moodle.com">Moodle Apps Portal</a>.';
$string['configairnotifierappname'] = 'The app name identifier in Airnotifier.';
$string['configairnotifiermobileappname'] = 'The Mobile app unique identifier (usually something like com.moodle.moodlemobile).';
$string['configured'] = 'Configured';
$string['deletecheckdevicename'] = 'Delete your device: {$a->name}';
$string['deletedevice'] = 'Delete the device. Note that an app can register the device again. If the device keeps reappearing, disable it.';
$string['devicetoken'] = 'Device token';
$string['donotsendnotification'] = 'Do not send notifications at all';
$string['enableprocessor'] = 'Enable mobile notifications';
$string['encryptnotifications'] = 'Encrypt notifications';
$string['encryptnotifications_help'] = 'Enable end-to-end encryption of app notifications. Some data may be removed from notifications if it can’t be encrypted.';
$string['encryptprocessing'] = 'For devices not supporting encryption';
$string['encryptprocessing_desc'] = 'Encrypted notifications require at least Android 8 or iOS 13, and Moodle App 4.2 or later.';
$string['errorretrievingkey'] = 'An error occurred while retrieving the access key. Your site must be registered to use this service. If your site is already registered, please try updating your registration. Alternatively, you can obtain an access key by creating an account on the <a href="https://apps.moodle.com">Moodle Apps Portal</a>.';
$string['keyretrievedsuccessfully'] = 'The access key was retrieved successfully. To access Moodle app usage statistics, please create an account on the <a href="https://apps.moodle.com">Moodle Apps Portal</a>.';
$string['messageprovidersempty'] = 'There are no mobile notifications enabled in default notification preferences.';
$string['messageproviderslow'] = 'Only a few mobile notifications are enabled in default notification preferences.';
$string['moodleappsportallimitswarning'] = 'Please note that the number of user devices allowed to receive notifications depends on your Moodle app subscription. For details, visit the <a href="{$a}" target="_blank">Moodle Apps Portal</a>.';
$string['nodevices'] = 'No registered devices. Devices will automatically appear after you install the Moodle app and add this site.';
$string['noemailevernotset'] = '$CFG->noemailever disabled';
$string['noemaileverset'] = '$CFG->noemailever is enabled in config.php. You need to set this setting to false or remove it.';
$string['nopermissiontomanagedevices'] = 'You don\'t have permission to manage devices.';
$string['notconfigured'] = 'The Airnotifier server has not been configured so push notifications cannot be sent.';
$string['notificationsserverconfiguration'] = 'Notifications server (Airnotifier) configuration';
$string['pluginname'] = 'Mobile';
$string['privacy:appiddescription'] = 'This is an identifier to the application being used.';
$string['privacy:enableddescription'] = 'If this device is enabled for airnotifier.';
$string['privacy:metadata:enabled'] = 'Whether the airnotifier device is enabled.';
$string['privacy:metadata:date'] = 'The date that the message was sent.';
$string['privacy:metadata:externalpurpose'] = 'This information is sent to an external site to be ultimately delivered to the mobile device of the user.';
$string['privacy:metadata:fullmessage'] = 'The full message.';
$string['privacy:metadata:notification'] = 'If this message is a notification.';
$string['privacy:metadata:smallmessage'] = 'A section of the message.';
$string['privacy:metadata:subject'] = 'The subject line of the message.';
$string['privacy:metadata:tableexplanation'] = 'Airnotifier device information is stored here.';
$string['privacy:metadata:userdeviceid'] = 'The ID linking to the user\'s mobile device';
$string['privacy:metadata:userfromfullname'] = 'The full name of the user who sent the message.';
$string['privacy:metadata:userfromid'] = 'The user ID of the author of the message.';
$string['privacy:metadata:userid'] = 'The ID of the user who sent the message.';
$string['privacy:metadata:username'] = 'The username of the user.';
$string['privacy:metadata:usersubsystem'] = 'This plugin is connected to the user subsystem.';
$string['privacy:subcontext'] = 'Message Airnotifier';
$string['sitemustberegistered'] = 'In order to use the public Airnotifier instance, your site must be registered. Alternatively, you can obtain an access key by creating an account on the <a href="https://apps.moodle.com">Moodle Apps Portal</a>.';
$string['showhide'] = 'Enable/disable the device.';
$string['requestaccesskey'] = 'Request access key';
$string['sendnotificationnotenc'] = 'Send notifications without encryption';
$string['sendtest'] = 'Send test push notification to my devices';
$string['sendtestconfirmation'] = 'A test push notification will be sent to the devices you use to connect to this site. Please ensure that your devices are connected to the Internet and that the mobile app is not open (since push notifications are only displayed when received in the background).';
$string['serverconnectivityerror'] = 'This site is not able to connect to the notifications server {$a}';
$string['unknowndevice'] = 'Unknown device';
$string['userdevices'] = 'User devices';
$string['airnotifier:managedevice'] = 'Manage devices';
$string['view_notification'] = 'Tap to view';
