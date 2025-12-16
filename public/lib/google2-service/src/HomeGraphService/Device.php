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

namespace Google\Service\HomeGraphService;

class Device extends \Google\Collection
{
  protected $collection_key = 'traits';
  /**
   * Attributes for the traits supported by the device.
   *
   * @var array[]
   */
  public $attributes;
  /**
   * Custom device attributes stored in Home Graph and provided to your smart
   * home Action in each [QUERY](https://developers.home.google.com/cloud-to-
   * cloud/intents/query) and
   * [EXECUTE](https://developers.home.google.com/cloud-to-
   * cloud/intents/execute) intent. Data in this object has a few constraints:
   * No sensitive information, including but not limited to Personally
   * Identifiable Information.
   *
   * @var array[]
   */
  public $customData;
  protected $deviceInfoType = DeviceInfo::class;
  protected $deviceInfoDataType = '';
  /**
   * Third-party device ID.
   *
   * @var string
   */
  public $id;
  protected $nameType = DeviceNames::class;
  protected $nameDataType = '';
  /**
   * Indicates whether your smart home Action will report notifications to
   * Google for this device via ReportStateAndNotification. If your smart home
   * Action enables users to control device notifications, you should update
   * this field and call RequestSyncDevices.
   *
   * @var bool
   */
  public $notificationSupportedByAgent;
  protected $otherDeviceIdsType = AgentOtherDeviceId::class;
  protected $otherDeviceIdsDataType = 'array';
  /**
   * Suggested name for the room where this device is installed. Google attempts
   * to use this value during user setup.
   *
   * @var string
   */
  public $roomHint;
  /**
   * Suggested name for the structure where this device is installed. Google
   * attempts to use this value during user setup.
   *
   * @var string
   */
  public $structureHint;
  /**
   * Traits supported by the device. See [device
   * traits](https://developers.home.google.com/cloud-to-cloud/traits).
   *
   * @var string[]
   */
  public $traits;
  /**
   * Hardware type of the device. See [device
   * types](https://developers.home.google.com/cloud-to-cloud/guides).
   *
   * @var string
   */
  public $type;
  /**
   * Indicates whether your smart home Action will report state of this device
   * to Google via ReportStateAndNotification.
   *
   * @var bool
   */
  public $willReportState;

  /**
   * Attributes for the traits supported by the device.
   *
   * @param array[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return array[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Custom device attributes stored in Home Graph and provided to your smart
   * home Action in each [QUERY](https://developers.home.google.com/cloud-to-
   * cloud/intents/query) and
   * [EXECUTE](https://developers.home.google.com/cloud-to-
   * cloud/intents/execute) intent. Data in this object has a few constraints:
   * No sensitive information, including but not limited to Personally
   * Identifiable Information.
   *
   * @param array[] $customData
   */
  public function setCustomData($customData)
  {
    $this->customData = $customData;
  }
  /**
   * @return array[]
   */
  public function getCustomData()
  {
    return $this->customData;
  }
  /**
   * Device manufacturer, model, hardware version, and software version.
   *
   * @param DeviceInfo $deviceInfo
   */
  public function setDeviceInfo(DeviceInfo $deviceInfo)
  {
    $this->deviceInfo = $deviceInfo;
  }
  /**
   * @return DeviceInfo
   */
  public function getDeviceInfo()
  {
    return $this->deviceInfo;
  }
  /**
   * Third-party device ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Names given to this device by your smart home Action.
   *
   * @param DeviceNames $name
   */
  public function setName(DeviceNames $name)
  {
    $this->name = $name;
  }
  /**
   * @return DeviceNames
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Indicates whether your smart home Action will report notifications to
   * Google for this device via ReportStateAndNotification. If your smart home
   * Action enables users to control device notifications, you should update
   * this field and call RequestSyncDevices.
   *
   * @param bool $notificationSupportedByAgent
   */
  public function setNotificationSupportedByAgent($notificationSupportedByAgent)
  {
    $this->notificationSupportedByAgent = $notificationSupportedByAgent;
  }
  /**
   * @return bool
   */
  public function getNotificationSupportedByAgent()
  {
    return $this->notificationSupportedByAgent;
  }
  /**
   * Alternate IDs associated with this device. This is used to identify cloud
   * synced devices enabled for [local
   * fulfillment](https://developers.home.google.com/local-home/overview).
   *
   * @param AgentOtherDeviceId[] $otherDeviceIds
   */
  public function setOtherDeviceIds($otherDeviceIds)
  {
    $this->otherDeviceIds = $otherDeviceIds;
  }
  /**
   * @return AgentOtherDeviceId[]
   */
  public function getOtherDeviceIds()
  {
    return $this->otherDeviceIds;
  }
  /**
   * Suggested name for the room where this device is installed. Google attempts
   * to use this value during user setup.
   *
   * @param string $roomHint
   */
  public function setRoomHint($roomHint)
  {
    $this->roomHint = $roomHint;
  }
  /**
   * @return string
   */
  public function getRoomHint()
  {
    return $this->roomHint;
  }
  /**
   * Suggested name for the structure where this device is installed. Google
   * attempts to use this value during user setup.
   *
   * @param string $structureHint
   */
  public function setStructureHint($structureHint)
  {
    $this->structureHint = $structureHint;
  }
  /**
   * @return string
   */
  public function getStructureHint()
  {
    return $this->structureHint;
  }
  /**
   * Traits supported by the device. See [device
   * traits](https://developers.home.google.com/cloud-to-cloud/traits).
   *
   * @param string[] $traits
   */
  public function setTraits($traits)
  {
    $this->traits = $traits;
  }
  /**
   * @return string[]
   */
  public function getTraits()
  {
    return $this->traits;
  }
  /**
   * Hardware type of the device. See [device
   * types](https://developers.home.google.com/cloud-to-cloud/guides).
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Indicates whether your smart home Action will report state of this device
   * to Google via ReportStateAndNotification.
   *
   * @param bool $willReportState
   */
  public function setWillReportState($willReportState)
  {
    $this->willReportState = $willReportState;
  }
  /**
   * @return bool
   */
  public function getWillReportState()
  {
    return $this->willReportState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Device::class, 'Google_Service_HomeGraphService_Device');
