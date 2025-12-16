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

class HardwareInfo extends \Google\Collection
{
  protected $collection_key = 'skinThrottlingTemperatures';
  /**
   * Battery shutdown temperature thresholds in Celsius for each battery on the
   * device.
   *
   * @var float[]
   */
  public $batteryShutdownTemperatures;
  /**
   * Battery throttling temperature thresholds in Celsius for each battery on
   * the device.
   *
   * @var float[]
   */
  public $batteryThrottlingTemperatures;
  /**
   * Brand of the device. For example, Google.
   *
   * @var string
   */
  public $brand;
  /**
   * CPU shutdown temperature thresholds in Celsius for each CPU on the device.
   *
   * @var float[]
   */
  public $cpuShutdownTemperatures;
  /**
   * CPU throttling temperature thresholds in Celsius for each CPU on the
   * device.
   *
   * @var float[]
   */
  public $cpuThrottlingTemperatures;
  /**
   * Baseband version. For example, MDM9625_104662.22.05.34p.
   *
   * @var string
   */
  public $deviceBasebandVersion;
  /**
   * Output only. ID that uniquely identifies a personally-owned device in a
   * particular organization. On the same physical device when enrolled with the
   * same organization, this ID persists across setups and even factory resets.
   * This ID is available on personally-owned devices with a work profile on
   * devices running Android 12 and above.
   *
   * @var string
   */
  public $enterpriseSpecificId;
  protected $euiccChipInfoType = EuiccChipInfo::class;
  protected $euiccChipInfoDataType = 'array';
  /**
   * GPU shutdown temperature thresholds in Celsius for each GPU on the device.
   *
   * @var float[]
   */
  public $gpuShutdownTemperatures;
  /**
   * GPU throttling temperature thresholds in Celsius for each GPU on the
   * device.
   *
   * @var float[]
   */
  public $gpuThrottlingTemperatures;
  /**
   * Name of the hardware. For example, Angler.
   *
   * @var string
   */
  public $hardware;
  /**
   * Manufacturer. For example, Motorola.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * The model of the device. For example, Asus Nexus 7.
   *
   * @var string
   */
  public $model;
  /**
   * The device serial number. However, for personally-owned devices running
   * Android 12 and above, this is the same as the enterpriseSpecificId.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * Device skin shutdown temperature thresholds in Celsius.
   *
   * @var float[]
   */
  public $skinShutdownTemperatures;
  /**
   * Device skin throttling temperature thresholds in Celsius.
   *
   * @var float[]
   */
  public $skinThrottlingTemperatures;

  /**
   * Battery shutdown temperature thresholds in Celsius for each battery on the
   * device.
   *
   * @param float[] $batteryShutdownTemperatures
   */
  public function setBatteryShutdownTemperatures($batteryShutdownTemperatures)
  {
    $this->batteryShutdownTemperatures = $batteryShutdownTemperatures;
  }
  /**
   * @return float[]
   */
  public function getBatteryShutdownTemperatures()
  {
    return $this->batteryShutdownTemperatures;
  }
  /**
   * Battery throttling temperature thresholds in Celsius for each battery on
   * the device.
   *
   * @param float[] $batteryThrottlingTemperatures
   */
  public function setBatteryThrottlingTemperatures($batteryThrottlingTemperatures)
  {
    $this->batteryThrottlingTemperatures = $batteryThrottlingTemperatures;
  }
  /**
   * @return float[]
   */
  public function getBatteryThrottlingTemperatures()
  {
    return $this->batteryThrottlingTemperatures;
  }
  /**
   * Brand of the device. For example, Google.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * CPU shutdown temperature thresholds in Celsius for each CPU on the device.
   *
   * @param float[] $cpuShutdownTemperatures
   */
  public function setCpuShutdownTemperatures($cpuShutdownTemperatures)
  {
    $this->cpuShutdownTemperatures = $cpuShutdownTemperatures;
  }
  /**
   * @return float[]
   */
  public function getCpuShutdownTemperatures()
  {
    return $this->cpuShutdownTemperatures;
  }
  /**
   * CPU throttling temperature thresholds in Celsius for each CPU on the
   * device.
   *
   * @param float[] $cpuThrottlingTemperatures
   */
  public function setCpuThrottlingTemperatures($cpuThrottlingTemperatures)
  {
    $this->cpuThrottlingTemperatures = $cpuThrottlingTemperatures;
  }
  /**
   * @return float[]
   */
  public function getCpuThrottlingTemperatures()
  {
    return $this->cpuThrottlingTemperatures;
  }
  /**
   * Baseband version. For example, MDM9625_104662.22.05.34p.
   *
   * @param string $deviceBasebandVersion
   */
  public function setDeviceBasebandVersion($deviceBasebandVersion)
  {
    $this->deviceBasebandVersion = $deviceBasebandVersion;
  }
  /**
   * @return string
   */
  public function getDeviceBasebandVersion()
  {
    return $this->deviceBasebandVersion;
  }
  /**
   * Output only. ID that uniquely identifies a personally-owned device in a
   * particular organization. On the same physical device when enrolled with the
   * same organization, this ID persists across setups and even factory resets.
   * This ID is available on personally-owned devices with a work profile on
   * devices running Android 12 and above.
   *
   * @param string $enterpriseSpecificId
   */
  public function setEnterpriseSpecificId($enterpriseSpecificId)
  {
    $this->enterpriseSpecificId = $enterpriseSpecificId;
  }
  /**
   * @return string
   */
  public function getEnterpriseSpecificId()
  {
    return $this->enterpriseSpecificId;
  }
  /**
   * Output only. Information related to the eUICC chip.
   *
   * @param EuiccChipInfo[] $euiccChipInfo
   */
  public function setEuiccChipInfo($euiccChipInfo)
  {
    $this->euiccChipInfo = $euiccChipInfo;
  }
  /**
   * @return EuiccChipInfo[]
   */
  public function getEuiccChipInfo()
  {
    return $this->euiccChipInfo;
  }
  /**
   * GPU shutdown temperature thresholds in Celsius for each GPU on the device.
   *
   * @param float[] $gpuShutdownTemperatures
   */
  public function setGpuShutdownTemperatures($gpuShutdownTemperatures)
  {
    $this->gpuShutdownTemperatures = $gpuShutdownTemperatures;
  }
  /**
   * @return float[]
   */
  public function getGpuShutdownTemperatures()
  {
    return $this->gpuShutdownTemperatures;
  }
  /**
   * GPU throttling temperature thresholds in Celsius for each GPU on the
   * device.
   *
   * @param float[] $gpuThrottlingTemperatures
   */
  public function setGpuThrottlingTemperatures($gpuThrottlingTemperatures)
  {
    $this->gpuThrottlingTemperatures = $gpuThrottlingTemperatures;
  }
  /**
   * @return float[]
   */
  public function getGpuThrottlingTemperatures()
  {
    return $this->gpuThrottlingTemperatures;
  }
  /**
   * Name of the hardware. For example, Angler.
   *
   * @param string $hardware
   */
  public function setHardware($hardware)
  {
    $this->hardware = $hardware;
  }
  /**
   * @return string
   */
  public function getHardware()
  {
    return $this->hardware;
  }
  /**
   * Manufacturer. For example, Motorola.
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
   * The model of the device. For example, Asus Nexus 7.
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
   * The device serial number. However, for personally-owned devices running
   * Android 12 and above, this is the same as the enterpriseSpecificId.
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
  /**
   * Device skin shutdown temperature thresholds in Celsius.
   *
   * @param float[] $skinShutdownTemperatures
   */
  public function setSkinShutdownTemperatures($skinShutdownTemperatures)
  {
    $this->skinShutdownTemperatures = $skinShutdownTemperatures;
  }
  /**
   * @return float[]
   */
  public function getSkinShutdownTemperatures()
  {
    return $this->skinShutdownTemperatures;
  }
  /**
   * Device skin throttling temperature thresholds in Celsius.
   *
   * @param float[] $skinThrottlingTemperatures
   */
  public function setSkinThrottlingTemperatures($skinThrottlingTemperatures)
  {
    $this->skinThrottlingTemperatures = $skinThrottlingTemperatures;
  }
  /**
   * @return float[]
   */
  public function getSkinThrottlingTemperatures()
  {
    return $this->skinThrottlingTemperatures;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HardwareInfo::class, 'Google_Service_AndroidManagement_HardwareInfo');
