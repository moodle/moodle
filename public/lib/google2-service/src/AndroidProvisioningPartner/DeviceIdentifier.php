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

namespace Google\Service\AndroidProvisioningPartner;

class DeviceIdentifier extends \Google\Model
{
  /**
   * Device type is not specified.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_UNSPECIFIED = 'DEVICE_TYPE_UNSPECIFIED';
  /**
   * Android device
   */
  public const DEVICE_TYPE_DEVICE_TYPE_ANDROID = 'DEVICE_TYPE_ANDROID';
  /**
   * Chrome OS device
   */
  public const DEVICE_TYPE_DEVICE_TYPE_CHROME_OS = 'DEVICE_TYPE_CHROME_OS';
  /**
   * An identifier provided by OEMs, carried through the production and sales
   * process. Only applicable to Chrome OS devices.
   *
   * @var string
   */
  public $chromeOsAttestedDeviceId;
  /**
   * The type of the device
   *
   * @var string
   */
  public $deviceType;
  /**
   * The device’s IMEI number. Validated on input.
   *
   * @var string
   */
  public $imei;
  /**
   * The device’s second IMEI number.
   *
   * @var string
   */
  public $imei2;
  /**
   * The device manufacturer’s name. Matches the device's built-in value
   * returned from `android.os.Build.MANUFACTURER`. Allowed values are listed in
   * [Android manufacturers](/zero-touch/resources/manufacturer-
   * names#manufacturers-names).
   *
   * @var string
   */
  public $manufacturer;
  /**
   * The device’s MEID number.
   *
   * @var string
   */
  public $meid;
  /**
   * The device’s second MEID number.
   *
   * @var string
   */
  public $meid2;
  /**
   * The device model's name. Allowed values are listed in [Android
   * models](/zero-touch/resources/manufacturer-names#model-names) and [Chrome
   * OS models](https://support.google.com/chrome/a/answer/10130175#identify_com
   * patible).
   *
   * @var string
   */
  public $model;
  /**
   * The manufacturer's serial number for the device. This value might not be
   * unique across different device models.
   *
   * @var string
   */
  public $serialNumber;

  /**
   * An identifier provided by OEMs, carried through the production and sales
   * process. Only applicable to Chrome OS devices.
   *
   * @param string $chromeOsAttestedDeviceId
   */
  public function setChromeOsAttestedDeviceId($chromeOsAttestedDeviceId)
  {
    $this->chromeOsAttestedDeviceId = $chromeOsAttestedDeviceId;
  }
  /**
   * @return string
   */
  public function getChromeOsAttestedDeviceId()
  {
    return $this->chromeOsAttestedDeviceId;
  }
  /**
   * The type of the device
   *
   * Accepted values: DEVICE_TYPE_UNSPECIFIED, DEVICE_TYPE_ANDROID,
   * DEVICE_TYPE_CHROME_OS
   *
   * @param self::DEVICE_TYPE_* $deviceType
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return self::DEVICE_TYPE_*
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * The device’s IMEI number. Validated on input.
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
   * The device’s second IMEI number.
   *
   * @param string $imei2
   */
  public function setImei2($imei2)
  {
    $this->imei2 = $imei2;
  }
  /**
   * @return string
   */
  public function getImei2()
  {
    return $this->imei2;
  }
  /**
   * The device manufacturer’s name. Matches the device's built-in value
   * returned from `android.os.Build.MANUFACTURER`. Allowed values are listed in
   * [Android manufacturers](/zero-touch/resources/manufacturer-
   * names#manufacturers-names).
   *
   * @param string $manufacturer
   */
  public function setManufacturer($manufacturer)
  {
    $this->manufacturer = $manufacturer;
  }
  /**
   * @return string
   */
  public function getManufacturer()
  {
    return $this->manufacturer;
  }
  /**
   * The device’s MEID number.
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
   * The device’s second MEID number.
   *
   * @param string $meid2
   */
  public function setMeid2($meid2)
  {
    $this->meid2 = $meid2;
  }
  /**
   * @return string
   */
  public function getMeid2()
  {
    return $this->meid2;
  }
  /**
   * The device model's name. Allowed values are listed in [Android
   * models](/zero-touch/resources/manufacturer-names#model-names) and [Chrome
   * OS models](https://support.google.com/chrome/a/answer/10130175#identify_com
   * patible).
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * The manufacturer's serial number for the device. This value might not be
   * unique across different device models.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceIdentifier::class, 'Google_Service_AndroidProvisioningPartner_DeviceIdentifier');
