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

class GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult extends \Google\Model
{
  /**
   * Represents an unspecified command result.
   */
  public const RESULT_TYPE_COMMAND_RESULT_TYPE_UNSPECIFIED = 'COMMAND_RESULT_TYPE_UNSPECIFIED';
  /**
   * Represents a command with an ignored result.
   */
  public const RESULT_TYPE_IGNORED = 'IGNORED';
  /**
   * Represents a failed command.
   */
  public const RESULT_TYPE_FAILURE = 'FAILURE';
  /**
   * Represents a succeeded command.
   */
  public const RESULT_TYPE_SUCCESS = 'SUCCESS';
  /**
   * Output only. Timestamp of the client execution of the remote command.
   *
   * @var string
   */
  public $clientExecutionTime;
  /**
   * Output only. Result code that indicates the type of error or success of the
   * command.
   *
   * @var string
   */
  public $resultCode;
  /**
   * Output only. Result type of the remote command.
   *
   * @var string
   */
  public $resultType;

  /**
   * Output only. Timestamp of the client execution of the remote command.
   *
   * @param string $clientExecutionTime
   */
  public function setClientExecutionTime($clientExecutionTime)
  {
    $this->clientExecutionTime = $clientExecutionTime;
  }
  /**
   * @return string
   */
  public function getClientExecutionTime()
  {
    return $this->clientExecutionTime;
  }
  /**
   * Output only. Result code that indicates the type of error or success of the
   * command.
   *
   * @param string $resultCode
   */
  public function setResultCode($resultCode)
  {
    $this->resultCode = $resultCode;
  }
  /**
   * @return string
   */
  public function getResultCode()
  {
    return $this->resultCode;
  }
  /**
   * Output only. Result type of the remote command.
   *
   * Accepted values: COMMAND_RESULT_TYPE_UNSPECIFIED, IGNORED, FAILURE, SUCCESS
   *
   * @param self::RESULT_TYPE_* $resultType
   */
  public function setResultType($resultType)
  {
    $this->resultType = $resultType;
  }
  /**
   * @return self::RESULT_TYPE_*
   */
  public function getResultType()
  {
    return $this->resultType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ChromeBrowserProfileCommandCommandResult');
