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

namespace Google\Service\AndroidManagement;

class NetworkInfo extends \Google\Collection
{
  protected $collection_key = 'telephonyInfos';
  /**
   * IMEI number of the GSM device. For example, A1000031212.
   *
   * @var string
   */
  public $imei;
  /**
   * MEID number of the CDMA device. For example, A00000292788E1.
   *
   * @var string
   */
  public $meid;
  /**
   * Alphabetic name of current registered operator. For example, Vodafone.
   *
   * @deprecated
   * @var string
   */
  public $networkOperatorName;
  protected $telephonyInfosType = TelephonyInfo::class;
  protected $telephonyInfosDataType = 'array';
  /**
   * Wi-Fi MAC address of the device. For example, 7c:11:11:11:11:11.
   *
   * @var string
   */
  public $wifiMacAddress;

  /**
   * IMEI number of the GSM device. For example, A1000031212.
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
   * MEID number of the CDMA device. For example, A00000292788E1.
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
   * Alphabetic name of current registered operator. For example, Vodafone.
   *
   * @deprecated
   * @param string $networkOperatorName
   */
  public function setNetworkOperatorName($networkOperatorName)
  {
    $this->networkOperatorName = $networkOperatorName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNetworkOperatorName()
  {
    return $this->networkOperatorName;
  }
  /**
   * Provides telephony information associated with each SIM card on the device.
   * Only supported on fully managed devices starting from Android API level 23.
   *
   * @param TelephonyInfo[] $telephonyInfos
   */
  public function setTelephonyInfos($telephonyInfos)
  {
    $this->telephonyInfos = $telephonyInfos;
  }
  /**
   * @return TelephonyInfo[]
   */
  public function getTelephonyInfos()
  {
    return $this->telephonyInfos;
  }
  /**
   * Wi-Fi MAC address of the device. For example, 7c:11:11:11:11:11.
   *
   * @param string $wifiMacAddress
   */
  public function setWifiMacAddress($wifiMacAddress)
  {
    $this->wifiMacAddress = $wifiMacAddress;
  }
  /**
   * @return string
   */
  public function getWifiMacAddress()
  {
    return $this->wifiMacAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkInfo::class, 'Google_Service_AndroidManagement_NetworkInfo');
