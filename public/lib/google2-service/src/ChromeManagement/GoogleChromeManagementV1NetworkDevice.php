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

class GoogleChromeManagementV1NetworkDevice extends \Google\Model
{
  /**
   * Network device type not specified.
   */
  public const TYPE_NETWORK_DEVICE_TYPE_UNSPECIFIED = 'NETWORK_DEVICE_TYPE_UNSPECIFIED';
  /**
   * Cellular device.
   */
  public const TYPE_CELLULAR_DEVICE = 'CELLULAR_DEVICE';
  /**
   * Ethernet device.
   */
  public const TYPE_ETHERNET_DEVICE = 'ETHERNET_DEVICE';
  /**
   * Wifi device.
   */
  public const TYPE_WIFI_DEVICE = 'WIFI_DEVICE';
  /**
   * Output only. The integrated circuit card ID associated with the device's
   * sim card.
   *
   * @var string
   */
  public $iccid;
  /**
   * Output only. IMEI (if applicable) of the corresponding network device.
   *
   * @var string
   */
  public $imei;
  /**
   * Output only. MAC address (if applicable) of the corresponding network
   * device.
   *
   * @var string
   */
  public $macAddress;
  /**
   * Output only. The mobile directory number associated with the device's sim
   * card.
   *
   * @var string
   */
  public $mdn;
  /**
   * Output only. MEID (if applicable) of the corresponding network device.
   *
   * @var string
   */
  public $meid;
  /**
   * Output only. Network device type.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The integrated circuit card ID associated with the device's
   * sim card.
   *
   * @param string $iccid
   */
  public function setIccid($iccid)
  {
    $this->iccid = $iccid;
  }
  /**
   * @return string
   */
  public function getIccid()
  {
    return $this->iccid;
  }
  /**
   * Output only. IMEI (if applicable) of the corresponding network device.
   *
   * @param string $imei
   */
  public function setImei($imei)
  {
    $this->imei = $imei;
  }
  /**
   * @return string
   */
  public function getImei()
  {
    return $this->imei;
  }
  /**
   * Output only. MAC address (if applicable) of the corresponding network
   * device.
   *
   * @param string $macAddress
   */
  public function setMacAddress($macAddress)
  {
    $this->macAddress = $macAddress;
  }
  /**
   * @return string
   */
  public function getMacAddress()
  {
    return $this->macAddress;
  }
  /**
   * Output only. The mobile directory number associated with the device's sim
   * card.
   *
   * @param string $mdn
   */
  public function setMdn($mdn)
  {
    $this->mdn = $mdn;
  }
  /**
   * @return string
   */
  public function getMdn()
  {
    return $this->mdn;
  }
  /**
   * Output only. MEID (if applicable) of the corresponding network device.
   *
   * @param string $meid
   */
  public function setMeid($meid)
  {
    $this->meid = $meid;
  }
  /**
   * @return string
   */
  public function getMeid()
  {
    return $this->meid;
  }
  /**
   * Output only. Network device type.
   *
   * Accepted values: NETWORK_DEVICE_TYPE_UNSPECIFIED, CELLULAR_DEVICE,
   * ETHERNET_DEVICE, WIFI_DEVICE
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
class_alias(GoogleChromeManagementV1NetworkDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1NetworkDevice');
