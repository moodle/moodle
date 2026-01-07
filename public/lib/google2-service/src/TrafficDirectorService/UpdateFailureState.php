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

namespace Google\Service\TrafficDirectorService;

class UpdateFailureState extends \Google\Model
{
  /**
   * Details about the last failed update attempt.
   *
   * @var string
   */
  public $details;
  /**
   * What the component configuration would have been if the update had
   * succeeded. This field may not be populated by xDS clients due to storage
   * overhead.
   *
   * @var array[]
   */
  public $failedConfiguration;
  /**
   * Time of the latest failed update attempt.
   *
   * @var string
   */
  public $lastUpdateAttempt;
  /**
   * This is the version of the rejected resource. [#not-implemented-hide:]
   *
   * @var string
   */
  public $versionInfo;

  /**
   * Details about the last failed update attempt.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * What the component configuration would have been if the update had
   * succeeded. This field may not be populated by xDS clients due to storage
   * overhead.
   *
   * @param array[] $failedConfiguration
   */
  public function setFailedConfiguration($failedConfiguration)
  {
    $this->failedConfiguration = $failedConfiguration;
  }
  /**
   * @return array[]
   */
  public function getFailedConfiguration()
  {
    return $this->failedConfiguration;
  }
  /**
   * Time of the latest failed update attempt.
   *
   * @param string $lastUpdateAttempt
   */
  public function setLastUpdateAttempt($lastUpdateAttempt)
  {
    $this->lastUpdateAttempt = $lastUpdateAttempt;
  }
  /**
   * @return string
   */
  public function getLastUpdateAttempt()
  {
    return $this->lastUpdateAttempt;
  }
  /**
   * This is the version of the rejected resource. [#not-implemented-hide:]
   *
   * @param string $versionInfo
   */
  public function setVersionInfo($versionInfo)
  {
    $this->versionInfo = $versionInfo;
  }
  /**
   * @return string
   */
  public function getVersionInfo()
  {
    return $this->versionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateFailureState::class, 'Google_Service_TrafficDirectorService_UpdateFailureState');
