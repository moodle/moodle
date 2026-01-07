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

class GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse extends \Google\Model
{
  /**
   * Number of ChromeOS devices have not synced policies in the past 28 days.
   *
   * @var string
   */
  public $noRecentPolicySyncCount;
  /**
   * Number of ChromeOS devices that have not seen any user activity in the past
   * 28 days.
   *
   * @var string
   */
  public $noRecentUserActivityCount;
  /**
   * Number of devices whose OS version is not compliant.
   *
   * @var string
   */
  public $osVersionNotCompliantCount;
  /**
   * Number of devices that are pending an OS update.
   *
   * @var string
   */
  public $pendingUpdate;
  /**
   * Number of devices that are unable to apply a policy due to an OS version
   * mismatch.
   *
   * @var string
   */
  public $unsupportedPolicyCount;

  /**
   * Number of ChromeOS devices have not synced policies in the past 28 days.
   *
   * @param string $noRecentPolicySyncCount
   */
  public function setNoRecentPolicySyncCount($noRecentPolicySyncCount)
  {
    $this->noRecentPolicySyncCount = $noRecentPolicySyncCount;
  }
  /**
   * @return string
   */
  public function getNoRecentPolicySyncCount()
  {
    return $this->noRecentPolicySyncCount;
  }
  /**
   * Number of ChromeOS devices that have not seen any user activity in the past
   * 28 days.
   *
   * @param string $noRecentUserActivityCount
   */
  public function setNoRecentUserActivityCount($noRecentUserActivityCount)
  {
    $this->noRecentUserActivityCount = $noRecentUserActivityCount;
  }
  /**
   * @return string
   */
  public function getNoRecentUserActivityCount()
  {
    return $this->noRecentUserActivityCount;
  }
  /**
   * Number of devices whose OS version is not compliant.
   *
   * @param string $osVersionNotCompliantCount
   */
  public function setOsVersionNotCompliantCount($osVersionNotCompliantCount)
  {
    $this->osVersionNotCompliantCount = $osVersionNotCompliantCount;
  }
  /**
   * @return string
   */
  public function getOsVersionNotCompliantCount()
  {
    return $this->osVersionNotCompliantCount;
  }
  /**
   * Number of devices that are pending an OS update.
   *
   * @param string $pendingUpdate
   */
  public function setPendingUpdate($pendingUpdate)
  {
    $this->pendingUpdate = $pendingUpdate;
  }
  /**
   * @return string
   */
  public function getPendingUpdate()
  {
    return $this->pendingUpdate;
  }
  /**
   * Number of devices that are unable to apply a policy due to an OS version
   * mismatch.
   *
   * @param string $unsupportedPolicyCount
   */
  public function setUnsupportedPolicyCount($unsupportedPolicyCount)
  {
    $this->unsupportedPolicyCount = $unsupportedPolicyCount;
  }
  /**
   * @return string
   */
  public function getUnsupportedPolicyCount()
  {
    return $this->unsupportedPolicyCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountChromeDevicesThatNeedAttentionResponse');
