<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Directory;

class DirectoryChromeosdevicesIssueCommandRequest extends \Google\Model
{
  /**
   * The command type was unspecified.
   */
  public const COMMAND_TYPE_COMMAND_TYPE_UNSPECIFIED = 'COMMAND_TYPE_UNSPECIFIED';
  /**
   * Reboot the device. Can be issued to Kiosk and managed guest session
   * devices, and regular devices running ChromeOS version 113 or later.
   */
  public const COMMAND_TYPE_REBOOT = 'REBOOT';
  /**
   * Take a screenshot of the device. Only available if the device is in Kiosk
   * Mode.
   */
  public const COMMAND_TYPE_TAKE_A_SCREENSHOT = 'TAKE_A_SCREENSHOT';
  /**
   * Set the volume of the device. Can only be issued to Kiosk and managed guest
   * session devices.
   */
  public const COMMAND_TYPE_SET_VOLUME = 'SET_VOLUME';
  /**
   * Wipe all the users off of the device. Executing this command in the device
   * will remove all user profile data, but it will keep device policy and
   * enrollment.
   */
  public const COMMAND_TYPE_WIPE_USERS = 'WIPE_USERS';
  /**
   * Wipes the device by performing a power wash. Executing this command in the
   * device will remove all data including user policies, device policies and
   * enrollment policies. Warning: This will revert the device back to a factory
   * state with no enrollment unless the device is subject to forced or auto
   * enrollment. Use with caution, as this is an irreversible action!
   */
  public const COMMAND_TYPE_REMOTE_POWERWASH = 'REMOTE_POWERWASH';
  /**
   * Starts a Chrome Remote Desktop session.
   */
  public const COMMAND_TYPE_DEVICE_START_CRD_SESSION = 'DEVICE_START_CRD_SESSION';
  /**
   * Capture the system logs of a kiosk device. The logs can be downloaded from
   * the downloadUrl link present in `deviceFiles` field of [chromeosdevices](ht
   * tps://developers.google.com/workspace/admin/directory/reference/rest/v1/chr
   * omeosdevices)
   */
  public const COMMAND_TYPE_CAPTURE_LOGS = 'CAPTURE_LOGS';
  /**
   * Fetches available type(s) of Chrome Remote Desktop sessions (private or
   * shared) that can be used to remotely connect to the device.
   */
  public const COMMAND_TYPE_FETCH_CRD_AVAILABILITY_INFO = 'FETCH_CRD_AVAILABILITY_INFO';
  /**
   * Fetch support packet from a device remotely. Support packet is a zip
   * archive that contains various system logs and debug data from a ChromeOS
   * device. The support packet can be downloaded from the downloadURL link
   * present in the `deviceFiles` field of [`chromeosdevices`](https://developer
   * s.google.com/workspace/admin/directory/reference/rest/v1/chromeosdevices)
   */
  public const COMMAND_TYPE_FETCH_SUPPORT_PACKET = 'FETCH_SUPPORT_PACKET';
  /**
   * The type of command.
   *
   * @var string
   */
  public $commandType;
  /**
   * The payload for the command, provide it only if command supports it. The
   * following commands support adding payload: * `SET_VOLUME`: Payload is a
   * stringified JSON object in the form: { "volume": 50 }. The volume has to be
   * an integer in the range [0,100]. * `DEVICE_START_CRD_SESSION`: Payload is
   * optionally a stringified JSON object in the form: { "ackedUserPresence":
   * true, "crdSessionType": string }. `ackedUserPresence` is a boolean. By
   * default, `ackedUserPresence` is set to `false`. To start a Chrome Remote
   * Desktop session for an active device, set `ackedUserPresence` to `true`.
   * `crdSessionType` can only select from values `private` (which grants the
   * remote admin exclusive control of the ChromeOS device) or `shared` (which
   * allows the admin and the local user to share control of the ChromeOS
   * device). If not set, `crdSessionType` defaults to `shared`. The
   * `FETCH_CRD_AVAILABILITY_INFO` command can be used to determine available
   * session types on the device. * `REBOOT`: Payload is a stringified JSON
   * object in the form: { "user_session_delay_seconds": 300 }. The
   * `user_session_delay_seconds` is the amount of seconds to wait before
   * rebooting the device if a user is logged in. It has to be an integer in the
   * range [0,300]. When payload is not present for reboot, 0 delay is the
   * default. Note: This only applies if an actual user is logged in, including
   * a Guest. If the device is in the login screen or in Kiosk mode the value is
   * not respected and the device immediately reboots. * `FETCH_SUPPORT_PACKET`:
   * Payload is optionally a stringified JSON object in the form:
   * {"supportPacketDetails":{ "issueCaseId": optional_support_case_id_string,
   * "issueDescription": optional_issue_description_string,
   * "requestedDataCollectors": []}} The list of available
   * `data_collector_enums` are as following: Chrome System Information (1),
   * Crash IDs (2), Memory Details (3), UI Hierarchy (4), Additional ChromeOS
   * Platform Logs (5), Device Event (6), Intel WiFi NICs Debug Dump (7), Touch
   * Events (8), Lacros (9), Lacros System Information (10), ChromeOS Flex Logs
   * (11), DBus Details (12), ChromeOS Network Routes (13), ChromeOS Shill
   * (Connection Manager) Logs (14), Policies (15), ChromeOS System State and
   * Logs (16), ChromeOS System Logs (17), ChromeOS Chrome User Logs (18),
   * ChromeOS Bluetooth (19), ChromeOS Connected Input Devices (20), ChromeOS
   * Traffic Counters (21), ChromeOS Virtual Keyboard (22), ChromeOS Network
   * Health (23). See more details in [help
   * article](https://support.google.com/chrome/a?p=remote-log).
   *
   * @var string
   */
  public $payload;

  /**
   * The type of command.
   *
   * Accepted values: COMMAND_TYPE_UNSPECIFIED, REBOOT, TAKE_A_SCREENSHOT,
   * SET_VOLUME, WIPE_USERS, REMOTE_POWERWASH, DEVICE_START_CRD_SESSION,
   * CAPTURE_LOGS, FETCH_CRD_AVAILABILITY_INFO, FETCH_SUPPORT_PACKET
   *
   * @param self::COMMAND_TYPE_* $commandType
   */
  public function setCommandType($commandType)
  {
    $this->commandType = $commandType;
  }
  /**
   * @return self::COMMAND_TYPE_*
   */
  public function getCommandType()
  {
    return $this->commandType;
  }
  /**
   * The payload for the command, provide it only if command supports it. The
   * following commands support adding payload: * `SET_VOLUME`: Payload is a
   * stringified JSON object in the form: { "volume": 50 }. The volume has to be
   * an integer in the range [0,100]. * `DEVICE_START_CRD_SESSION`: Payload is
   * optionally a stringified JSON object in the form: { "ackedUserPresence":
   * true, "crdSessionType": string }. `ackedUserPresence` is a boolean. By
   * default, `ackedUserPresence` is set to `false`. To start a Chrome Remote
   * Desktop session for an active device, set `ackedUserPresence` to `true`.
   * `crdSessionType` can only select from values `private` (which grants the
   * remote admin exclusive control of the ChromeOS device) or `shared` (which
   * allows the admin and the local user to share control of the ChromeOS
   * device). If not set, `crdSessionType` defaults to `shared`. The
   * `FETCH_CRD_AVAILABILITY_INFO` command can be used to determine available
   * session types on the device. * `REBOOT`: Payload is a stringified JSON
   * object in the form: { "user_session_delay_seconds": 300 }. The
   * `user_session_delay_seconds` is the amount of seconds to wait before
   * rebooting the device if a user is logged in. It has to be an integer in the
   * range [0,300]. When payload is not present for reboot, 0 delay is the
   * default. Note: This only applies if an actual user is logged in, including
   * a Guest. If the device is in the login screen or in Kiosk mode the value is
   * not respected and the device immediately reboots. * `FETCH_SUPPORT_PACKET`:
   * Payload is optionally a stringified JSON object in the form:
   * {"supportPacketDetails":{ "issueCaseId": optional_support_case_id_string,
   * "issueDescription": optional_issue_description_string,
   * "requestedDataCollectors": []}} The list of available
   * `data_collector_enums` are as following: Chrome System Information (1),
   * Crash IDs (2), Memory Details (3), UI Hierarchy (4), Additional ChromeOS
   * Platform Logs (5), Device Event (6), Intel WiFi NICs Debug Dump (7), Touch
   * Events (8), Lacros (9), Lacros System Information (10), ChromeOS Flex Logs
   * (11), DBus Details (12), ChromeOS Network Routes (13), ChromeOS Shill
   * (Connection Manager) Logs (14), Policies (15), ChromeOS System State and
   * Logs (16), ChromeOS System Logs (17), ChromeOS Chrome User Logs (18),
   * ChromeOS Bluetooth (19), ChromeOS Connected Input Devices (20), ChromeOS
   * Traffic Counters (21), ChromeOS Virtual Keyboard (22), ChromeOS Network
   * Health (23). See more details in [help
   * article](https://support.google.com/chrome/a?p=remote-log).
   *
   * @param string $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return string
   */
  public function getPayload()
  {
    return $this->payload;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectoryChromeosdevicesIssueCommandRequest::class, 'Google_Service_Directory_DirectoryChromeosdevicesIssueCommandRequest');
