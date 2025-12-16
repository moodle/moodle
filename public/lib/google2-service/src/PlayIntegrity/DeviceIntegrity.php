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

namespace Google\Service\PlayIntegrity;

class DeviceIntegrity extends \Google\Collection
{
  protected $collection_key = 'legacyDeviceRecognitionVerdict';
  protected $deviceAttributesType = DeviceAttributes::class;
  protected $deviceAttributesDataType = '';
  protected $deviceRecallType = DeviceRecall::class;
  protected $deviceRecallDataType = '';
  /**
   * Details about the integrity of the device the app is running on.
   *
   * @var string[]
   */
  public $deviceRecognitionVerdict;
  /**
   * Contains legacy details about the integrity of the device the app is
   * running on. Only for devices with Android version T or higher and only for
   * apps opted in to the new verdicts. Only available during the transition
   * period to the new verdicts system and will be removed afterwards.
   *
   * @var string[]
   */
  public $legacyDeviceRecognitionVerdict;
  protected $recentDeviceActivityType = RecentDeviceActivity::class;
  protected $recentDeviceActivityDataType = '';

  /**
   * Attributes of the device where the integrity token was generated.
   *
   * @param DeviceAttributes $deviceAttributes
   */
  public function setDeviceAttributes(DeviceAttributes $deviceAttributes)
  {
    $this->deviceAttributes = $deviceAttributes;
  }
  /**
   * @return DeviceAttributes
   */
  public function getDeviceAttributes()
  {
    return $this->deviceAttributes;
  }
  /**
   * Details about the device recall bits set by the developer.
   *
   * @param DeviceRecall $deviceRecall
   */
  public function setDeviceRecall(DeviceRecall $deviceRecall)
  {
    $this->deviceRecall = $deviceRecall;
  }
  /**
   * @return DeviceRecall
   */
  public function getDeviceRecall()
  {
    return $this->deviceRecall;
  }
  /**
   * Details about the integrity of the device the app is running on.
   *
   * @param string[] $deviceRecognitionVerdict
   */
  public function setDeviceRecognitionVerdict($deviceRecognitionVerdict)
  {
    $this->deviceRecognitionVerdict = $deviceRecognitionVerdict;
  }
  /**
   * @return string[]
   */
  public function getDeviceRecognitionVerdict()
  {
    return $this->deviceRecognitionVerdict;
  }
  /**
   * Contains legacy details about the integrity of the device the app is
   * running on. Only for devices with Android version T or higher and only for
   * apps opted in to the new verdicts. Only available during the transition
   * period to the new verdicts system and will be removed afterwards.
   *
   * @param string[] $legacyDeviceRecognitionVerdict
   */
  public function setLegacyDeviceRecognitionVerdict($legacyDeviceRecognitionVerdict)
  {
    $this->legacyDeviceRecognitionVerdict = $legacyDeviceRecognitionVerdict;
  }
  /**
   * @return string[]
   */
  public function getLegacyDeviceRecognitionVerdict()
  {
    return $this->legacyDeviceRecognitionVerdict;
  }
  /**
   * Details about the device activity of the device the app is running on.
   *
   * @param RecentDeviceActivity $recentDeviceActivity
   */
  public function setRecentDeviceActivity(RecentDeviceActivity $recentDeviceActivity)
  {
    $this->recentDeviceActivity = $recentDeviceActivity;
  }
  /**
   * @return RecentDeviceActivity
   */
  public function getRecentDeviceActivity()
  {
    return $this->recentDeviceActivity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceIntegrity::class, 'Google_Service_PlayIntegrity_DeviceIntegrity');
