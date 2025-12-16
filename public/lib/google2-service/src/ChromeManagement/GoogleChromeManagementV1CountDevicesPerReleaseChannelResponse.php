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

class GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse extends \Google\Model
{
  /**
   * Number of devices with beta release channel.
   *
   * @var string
   */
  public $betaChannelCount;
  /**
   * Number of devices with canary release channel.
   *
   * @var string
   */
  public $canaryChannelCount;
  /**
   * Number of devices with dev release channel.
   *
   * @var string
   */
  public $devChannelCount;
  /**
   * Number of devices with ltc release channel.
   *
   * @var string
   */
  public $ltcChannelCount;
  /**
   * Number of devices with lts release channel.
   *
   * @var string
   */
  public $ltsChannelCount;
  /**
   * Number of devices with stable release channel.
   *
   * @var string
   */
  public $stableChannelCount;
  /**
   * Number of devices with an unreported release channel.
   *
   * @var string
   */
  public $unreportedChannelCount;
  /**
   * Number of devices with unsupported release channel.
   *
   * @var string
   */
  public $unsupportedChannelCount;

  /**
   * Number of devices with beta release channel.
   *
   * @param string $betaChannelCount
   */
  public function setBetaChannelCount($betaChannelCount)
  {
    $this->betaChannelCount = $betaChannelCount;
  }
  /**
   * @return string
   */
  public function getBetaChannelCount()
  {
    return $this->betaChannelCount;
  }
  /**
   * Number of devices with canary release channel.
   *
   * @param string $canaryChannelCount
   */
  public function setCanaryChannelCount($canaryChannelCount)
  {
    $this->canaryChannelCount = $canaryChannelCount;
  }
  /**
   * @return string
   */
  public function getCanaryChannelCount()
  {
    return $this->canaryChannelCount;
  }
  /**
   * Number of devices with dev release channel.
   *
   * @param string $devChannelCount
   */
  public function setDevChannelCount($devChannelCount)
  {
    $this->devChannelCount = $devChannelCount;
  }
  /**
   * @return string
   */
  public function getDevChannelCount()
  {
    return $this->devChannelCount;
  }
  /**
   * Number of devices with ltc release channel.
   *
   * @param string $ltcChannelCount
   */
  public function setLtcChannelCount($ltcChannelCount)
  {
    $this->ltcChannelCount = $ltcChannelCount;
  }
  /**
   * @return string
   */
  public function getLtcChannelCount()
  {
    return $this->ltcChannelCount;
  }
  /**
   * Number of devices with lts release channel.
   *
   * @param string $ltsChannelCount
   */
  public function setLtsChannelCount($ltsChannelCount)
  {
    $this->ltsChannelCount = $ltsChannelCount;
  }
  /**
   * @return string
   */
  public function getLtsChannelCount()
  {
    return $this->ltsChannelCount;
  }
  /**
   * Number of devices with stable release channel.
   *
   * @param string $stableChannelCount
   */
  public function setStableChannelCount($stableChannelCount)
  {
    $this->stableChannelCount = $stableChannelCount;
  }
  /**
   * @return string
   */
  public function getStableChannelCount()
  {
    return $this->stableChannelCount;
  }
  /**
   * Number of devices with an unreported release channel.
   *
   * @param string $unreportedChannelCount
   */
  public function setUnreportedChannelCount($unreportedChannelCount)
  {
    $this->unreportedChannelCount = $unreportedChannelCount;
  }
  /**
   * @return string
   */
  public function getUnreportedChannelCount()
  {
    return $this->unreportedChannelCount;
  }
  /**
   * Number of devices with unsupported release channel.
   *
   * @param string $unsupportedChannelCount
   */
  public function setUnsupportedChannelCount($unsupportedChannelCount)
  {
    $this->unsupportedChannelCount = $unsupportedChannelCount;
  }
  /**
   * @return string
   */
  public function getUnsupportedChannelCount()
  {
    return $this->unsupportedChannelCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountDevicesPerReleaseChannelResponse');
