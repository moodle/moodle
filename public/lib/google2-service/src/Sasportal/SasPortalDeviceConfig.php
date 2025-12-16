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

namespace Google\Service\Sasportal;

class SasPortalDeviceConfig extends \Google\Collection
{
  /**
   * Unspecified device category.
   */
  public const CATEGORY_DEVICE_CATEGORY_UNSPECIFIED = 'DEVICE_CATEGORY_UNSPECIFIED';
  /**
   * Category A.
   */
  public const CATEGORY_DEVICE_CATEGORY_A = 'DEVICE_CATEGORY_A';
  /**
   * Category B.
   */
  public const CATEGORY_DEVICE_CATEGORY_B = 'DEVICE_CATEGORY_B';
  public const STATE_DEVICE_CONFIG_STATE_UNSPECIFIED = 'DEVICE_CONFIG_STATE_UNSPECIFIED';
  public const STATE_DRAFT = 'DRAFT';
  public const STATE_FINAL = 'FINAL';
  protected $collection_key = 'measurementCapabilities';
  protected $airInterfaceType = SasPortalDeviceAirInterface::class;
  protected $airInterfaceDataType = '';
  /**
   * The call sign of the device operator.
   *
   * @var string
   */
  public $callSign;
  /**
   * FCC category of the device.
   *
   * @var string
   */
  public $category;
  protected $installationParamsType = SasPortalInstallationParams::class;
  protected $installationParamsDataType = '';
  /**
   * Output only. Whether the configuration has been signed by a CPI.
   *
   * @var bool
   */
  public $isSigned;
  /**
   * Measurement reporting capabilities of the device.
   *
   * @var string[]
   */
  public $measurementCapabilities;
  protected $modelType = SasPortalDeviceModel::class;
  protected $modelDataType = '';
  /**
   * State of the configuration.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last time the device configuration was edited.
   *
   * @var string
   */
  public $updateTime;
  /**
   * The identifier of a device user.
   *
   * @var string
   */
  public $userId;

  /**
   * Information about this device's air interface.
   *
   * @param SasPortalDeviceAirInterface $airInterface
   */
  public function setAirInterface(SasPortalDeviceAirInterface $airInterface)
  {
    $this->airInterface = $airInterface;
  }
  /**
   * @return SasPortalDeviceAirInterface
   */
  public function getAirInterface()
  {
    return $this->airInterface;
  }
  /**
   * The call sign of the device operator.
   *
   * @param string $callSign
   */
  public function setCallSign($callSign)
  {
    $this->callSign = $callSign;
  }
  /**
   * @return string
   */
  public function getCallSign()
  {
    return $this->callSign;
  }
  /**
   * FCC category of the device.
   *
   * Accepted values: DEVICE_CATEGORY_UNSPECIFIED, DEVICE_CATEGORY_A,
   * DEVICE_CATEGORY_B
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Installation parameters for the device.
   *
   * @param SasPortalInstallationParams $installationParams
   */
  public function setInstallationParams(SasPortalInstallationParams $installationParams)
  {
    $this->installationParams = $installationParams;
  }
  /**
   * @return SasPortalInstallationParams
   */
  public function getInstallationParams()
  {
    return $this->installationParams;
  }
  /**
   * Output only. Whether the configuration has been signed by a CPI.
   *
   * @param bool $isSigned
   */
  public function setIsSigned($isSigned)
  {
    $this->isSigned = $isSigned;
  }
  /**
   * @return bool
   */
  public function getIsSigned()
  {
    return $this->isSigned;
  }
  /**
   * Measurement reporting capabilities of the device.
   *
   * @param string[] $measurementCapabilities
   */
  public function setMeasurementCapabilities($measurementCapabilities)
  {
    $this->measurementCapabilities = $measurementCapabilities;
  }
  /**
   * @return string[]
   */
  public function getMeasurementCapabilities()
  {
    return $this->measurementCapabilities;
  }
  /**
   * Information about this device model.
   *
   * @param SasPortalDeviceModel $model
   */
  public function setModel(SasPortalDeviceModel $model)
  {
    $this->model = $model;
  }
  /**
   * @return SasPortalDeviceModel
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * State of the configuration.
   *
   * Accepted values: DEVICE_CONFIG_STATE_UNSPECIFIED, DRAFT, FINAL
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The last time the device configuration was edited.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The identifier of a device user.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalDeviceConfig::class, 'Google_Service_Sasportal_SasPortalDeviceConfig');
