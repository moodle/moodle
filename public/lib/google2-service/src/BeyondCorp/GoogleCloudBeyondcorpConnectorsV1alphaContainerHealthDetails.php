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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpConnectorsV1alphaContainerHealthDetails extends \Google\Model
{
  /**
   * The version of the current config.
   *
   * @var string
   */
  public $currentConfigVersion;
  /**
   * The latest error message.
   *
   * @var string
   */
  public $errorMsg;
  /**
   * The version of the expected config.
   *
   * @var string
   */
  public $expectedConfigVersion;
  /**
   * The extended status. Such as ExitCode, StartedAt, FinishedAt, etc.
   *
   * @var string[]
   */
  public $extendedStatus;

  /**
   * The version of the current config.
   *
   * @param string $currentConfigVersion
   */
  public function setCurrentConfigVersion($currentConfigVersion)
  {
    $this->currentConfigVersion = $currentConfigVersion;
  }
  /**
   * @return string
   */
  public function getCurrentConfigVersion()
  {
    return $this->currentConfigVersion;
  }
  /**
   * The latest error message.
   *
   * @param string $errorMsg
   */
  public function setErrorMsg($errorMsg)
  {
    $this->errorMsg = $errorMsg;
  }
  /**
   * @return string
   */
  public function getErrorMsg()
  {
    return $this->errorMsg;
  }
  /**
   * The version of the expected config.
   *
   * @param string $expectedConfigVersion
   */
  public function setExpectedConfigVersion($expectedConfigVersion)
  {
    $this->expectedConfigVersion = $expectedConfigVersion;
  }
  /**
   * @return string
   */
  public function getExpectedConfigVersion()
  {
    return $this->expectedConfigVersion;
  }
  /**
   * The extended status. Such as ExitCode, StartedAt, FinishedAt, etc.
   *
   * @param string[] $extendedStatus
   */
  public function setExtendedStatus($extendedStatus)
  {
    $this->extendedStatus = $extendedStatus;
  }
  /**
   * @return string[]
   */
  public function getExtendedStatus()
  {
    return $this->extendedStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpConnectorsV1alphaContainerHealthDetails::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpConnectorsV1alphaContainerHealthDetails');
