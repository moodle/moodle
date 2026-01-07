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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand extends \Google\Model
{
  /**
   * Represents an unspecified command state.
   */
  public const COMMAND_STATE_COMMAND_STATE_UNSPECIFIED = 'COMMAND_STATE_UNSPECIFIED';
  /**
   * Represents a command in a pending state.
   */
  public const COMMAND_STATE_PENDING = 'PENDING';
  /**
   * Represents a command that has expired.
   */
  public const COMMAND_STATE_EXPIRED = 'EXPIRED';
  /**
   * Represents a command that has been executed by the client.
   */
  public const COMMAND_STATE_EXECUTED_BY_CLIENT = 'EXECUTED_BY_CLIENT';
  protected $commandResultType = GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult::class;
  protected $commandResultDataType = '';
  /**
   * Output only. State of the remote command.
   *
   * @var string
   */
  public $commandState;
  /**
   * Required. Type of the remote command. The only supported command_type is
   * "clearBrowsingData".
   *
   * @var string
   */
  public $commandType;
  /**
   * Output only. Timestamp of the issurance of the remote command.
   *
   * @var string
   */
  public $issueTime;
  /**
   * Identifier. Format: customers/{customer_id}/profiles/{profile_permanent_id}
   * /commands/{command_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Payload of the remote command. The payload for
   * "clearBrowsingData" command supports: - fields "clearCache" and
   * "clearCookies" - values of boolean type.
   *
   * @var array[]
   */
  public $payload;
  /**
   * Output only. Valid duration of the remote command.
   *
   * @var string
   */
  public $validDuration;

  /**
   * Output only. Result of the remote command.
   *
   * @param GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult $commandResult
   */
  public function setCommandResult(GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult $commandResult)
  {
    $this->commandResult = $commandResult;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult
   */
  public function getCommandResult()
  {
    return $this->commandResult;
  }
  /**
   * Output only. State of the remote command.
   *
   * Accepted values: COMMAND_STATE_UNSPECIFIED, PENDING, EXPIRED,
   * EXECUTED_BY_CLIENT
   *
   * @param self::COMMAND_STATE_* $commandState
   */
  public function setCommandState($commandState)
  {
    $this->commandState = $commandState;
  }
  /**
   * @return self::COMMAND_STATE_*
   */
  public function getCommandState()
  {
    return $this->commandState;
  }
  /**
   * Required. Type of the remote command. The only supported command_type is
   * "clearBrowsingData".
   *
   * @param string $commandType
   */
  public function setCommandType($commandType)
  {
    $this->commandType = $commandType;
  }
  /**
   * @return string
   */
  public function getCommandType()
  {
    return $this->commandType;
  }
  /**
   * Output only. Timestamp of the issurance of the remote command.
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
   * Identifier. Format: customers/{customer_id}/profiles/{profile_permanent_id}
   * /commands/{command_id}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Payload of the remote command. The payload for
   * "clearBrowsingData" command supports: - fields "clearCache" and
   * "clearCookies" - values of boolean type.
   *
   * @param array[] $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array[]
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Output only. Valid duration of the remote command.
   *
   * @param string $validDuration
   */
  public function setValidDuration($validDuration)
  {
    $this->validDuration = $validDuration;
  }
  /**
   * @return string
   */
  public function getValidDuration()
  {
    return $this->validDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ChromeBrowserProfileCommand');
