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

class DirectoryChromeosdevicesCommandResult extends \Google\Model
{
  /**
   * The command result was unspecified.
   */
  public const RESULT_COMMAND_RESULT_TYPE_UNSPECIFIED = 'COMMAND_RESULT_TYPE_UNSPECIFIED';
  /**
   * The command was ignored as obsolete.
   */
  public const RESULT_IGNORED = 'IGNORED';
  /**
   * The command could not be executed successfully.
   */
  public const RESULT_FAILURE = 'FAILURE';
  /**
   * The command was successfully executed.
   */
  public const RESULT_SUCCESS = 'SUCCESS';
  /**
   * The payload for the command result. The following commands respond with a
   * payload: * `DEVICE_START_CRD_SESSION`: Payload is a stringified JSON object
   * in the form: { "url": url }. The provided URL links to the Chrome Remote
   * Desktop session and requires authentication using only the `email`
   * associated with the command's issuance. * `FETCH_CRD_AVAILABILITY_INFO`:
   * Payload is a stringified JSON object in the form: {
   * "deviceIdleTimeInSeconds": number, "userSessionType": string,
   * "remoteSupportAvailability": string, "remoteAccessAvailability": string }.
   * The "remoteSupportAvailability" field is set to "AVAILABLE" if `shared` CRD
   * session to the device is available. The "remoteAccessAvailability" field is
   * set to "AVAILABLE" if `private` CRD session to the device is available.
   *
   * @var string
   */
  public $commandResultPayload;
  /**
   * The error message with a short explanation as to why the command failed.
   * Only present if the command failed.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The time at which the command was executed or failed to execute.
   *
   * @var string
   */
  public $executeTime;
  /**
   * The result of the command.
   *
   * @var string
   */
  public $result;

  /**
   * The payload for the command result. The following commands respond with a
   * payload: * `DEVICE_START_CRD_SESSION`: Payload is a stringified JSON object
   * in the form: { "url": url }. The provided URL links to the Chrome Remote
   * Desktop session and requires authentication using only the `email`
   * associated with the command's issuance. * `FETCH_CRD_AVAILABILITY_INFO`:
   * Payload is a stringified JSON object in the form: {
   * "deviceIdleTimeInSeconds": number, "userSessionType": string,
   * "remoteSupportAvailability": string, "remoteAccessAvailability": string }.
   * The "remoteSupportAvailability" field is set to "AVAILABLE" if `shared` CRD
   * session to the device is available. The "remoteAccessAvailability" field is
   * set to "AVAILABLE" if `private` CRD session to the device is available.
   *
   * @param string $commandResultPayload
   */
  public function setCommandResultPayload($commandResultPayload)
  {
    $this->commandResultPayload = $commandResultPayload;
  }
  /**
   * @return string
   */
  public function getCommandResultPayload()
  {
    return $this->commandResultPayload;
  }
  /**
   * The error message with a short explanation as to why the command failed.
   * Only present if the command failed.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * The time at which the command was executed or failed to execute.
   *
   * @param string $executeTime
   */
  public function setExecuteTime($executeTime)
  {
    $this->executeTime = $executeTime;
  }
  /**
   * @return string
   */
  public function getExecuteTime()
  {
    return $this->executeTime;
  }
  /**
   * The result of the command.
   *
   * Accepted values: COMMAND_RESULT_TYPE_UNSPECIFIED, IGNORED, FAILURE, SUCCESS
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DirectoryChromeosdevicesCommandResult::class, 'Google_Service_Directory_DirectoryChromeosdevicesCommandResult');
