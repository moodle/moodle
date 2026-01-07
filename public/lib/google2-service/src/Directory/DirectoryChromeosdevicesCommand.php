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

class DirectoryChromeosdevicesCommand extends \Google\Model
{
  /**
   * The command status was unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * An unexpired command not yet sent to the client.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The command didn't get executed by the client within the expected time.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * The command is cancelled by admin while in PENDING.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The command has been sent to the client.
   */
  public const STATE_SENT_TO_CLIENT = 'SENT_TO_CLIENT';
  /**
   * The client has responded that it received the command.
   */
  public const STATE_ACKED_BY_CLIENT = 'ACKED_BY_CLIENT';
  /**
   * The client has (un)successfully executed the command.
   */
  public const STATE_EXECUTED_BY_CLIENT = 'EXECUTED_BY_CLIENT';
  /**
   * The command type was unspecified.
   */
  public const TYPE_COMMAND_TYPE_UNSPECIFIED = 'COMMAND_TYPE_UNSPECIFIED';
  /**
   * Reboot the device. Can be issued to Kiosk and managed guest session
   * devices, and regular devices running ChromeOS version 113 or later.
   */
  public const TYPE_REBOOT = 'REBOOT';
  /**
   * Take a screenshot of the device. Only available if the device is in Kiosk
   * Mode.
   */
  public const TYPE_TAKE_A_SCREENSHOT = 'TAKE_A_SCREENSHOT';
  /**
   * Set the volume of the device. Can only be issued to Kiosk and managed guest
   * session devices.
   */
  public const TYPE_SET_VOLUME = 'SET_VOLUME';
  /**
   * Wipe all the users off of the device. Executing this command in the device
   * will remove all user profile data, but it will keep device policy and
   * enrollment.
   */
  public const TYPE_WIPE_USERS = 'WIPE_USERS';
  /**
   * Wipes the device by performing a power wash. Executing this command in the
   * device will remove all data including user policies, device policies and
   * enrollment policies. Warning: This will revert the device back to a factory
   * state with no enrollment unless the device is subject to forced or auto
   * enrollment. Use with caution, as this is an irreversible action!
   */
  public const TYPE_REMOTE_POWERWASH = 'REMOTE_POWERWASH';
  /**
   * Starts a Chrome Remote Desktop session.
   */
  public const TYPE_DEVICE_START_CRD_SESSION = 'DEVICE_START_CRD_SESSION';
  /**
   * Capture the system logs of a kiosk device. The logs can be downloaded from
   * the downloadUrl link present in `deviceFiles` field of [chromeosdevices](ht
   * tps://developers.google.com/workspace/admin/directory/reference/rest/v1/chr
   * omeosdevices)
   */
  public const TYPE_CAPTURE_LOGS = 'CAPTURE_LOGS';
  /**
   * Fetches available type(s) of Chrome Remote Desktop sessions (private or
   * shared) that can be used to remotely connect to the device.
   */
  public const TYPE_FETCH_CRD_AVAILABILITY_INFO = 'FETCH_CRD_AVAILABILITY_INFO';
  /**
   * Fetch support packet from a device remotely. Support packet is a zip
   * archive that contains various system logs and debug data from a ChromeOS
   * device. The support packet can be downloaded from the downloadURL link
   * present in the `deviceFiles` field of [`chromeosdevices`](https://developer
   * s.google.com/workspace/admin/directory/reference/rest/v1/chromeosdevices)
   */
  public const TYPE_FETCH_SUPPORT_PACKET = 'FETCH_SUPPORT_PACKET';
  /**
   * The time at which the command will expire. If the device doesn't execute
   * the command within this time the command will become expired.
   *
   * @var string
   */
  public $commandExpireTime;
  /**
   * Unique ID of a device command.
   *
   * @var string
   */
  public $commandId;
  protected $commandResultType = DirectoryChromeosdevicesCommandResult::class;
  protected $commandResultDataType = '';
  /**
   * The timestamp when the command was issued by the admin.
   *
   * @var string
   */
  public $issueTime;
  /**
   * The payload that the command specified, if any.
   *
   * @var string
   */
  public $payload;
  /**
   * Indicates the command state.
   *
   * @var string
   */
  public $state;
  /**
   * The type of the command.
   *
   * @var string
   */
  public $type;

  /**
   * The time at which the command will expire. If the device doesn't execute
   * the command within this time the command will become expired.
   *
   * @param string $commandExpireTime
   */
  public function setCommandExpireTime($commandExpireTime)
  {
    $this->commandExpireTime = $commandExpireTime;
  }
  /**
   * @return string
   */
  public function getCommandExpireTime()
  {
    return $this->commandExpireTime;
  }
  /**
   * Unique ID of a device command.
   *
   * @param string $commandId
   */
  public function setCommandId($commandId)
  {
    $this->commandId = $commandId;
  }
  /**
   * @return string
   */
  public function getCommandId()
  {
    return $this->commandId;
  }
  /**
   * The result of the command execution.
   *
   * @param DirectoryChromeosdevicesCommandResult $commandResult
   */
  public function setCommandResult(DirectoryChromeosdevicesCommandResult $commandResult)
  {
    $this->commandResult = $commandResult;
  }
  /**
   * @return DirectoryChromeosdevicesCommandResult
   */
  public function getCommandResult()
  {
    return $this->commandResult;
  }
  /**
   * The timestamp when the command was issued by the admin.
   *
   * @param string $issueTime
   */
  public function setIssueTime($issueTime)
  {
    $this->issueTime = $issueTime;
  }
  /**
   * @return string
   */
  public function getIssueTime()
  {
    return $this->issueTime;
  }
  /**
   * The payload that the command specified, if any.
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
  /**
   * Indicates the command state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, EXPIRED, CANCELLED,
   * SENT_TO_CLIENT, ACKED_BY_CLIENT, EXECUTED_BY_CLIENT
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The type of the command.
   *
   * Accepted values: COMMAND_TYPE_UNSPECIFIED, REBOOT, TAKE_A_SCREENSHOT,
   * SET_VOLUME, WIPE_USERS, REMOTE_POWERWASH, DEVICE_START_CRD_SESSION,
   * CAPTURE_LOGS, FETCH_CRD_AVAILABILITY_INFO, FETCH_SUPPORT_PACKET
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectoryChromeosdevicesCommand::class, 'Google_Service_Directory_DirectoryChromeosdevicesCommand');
