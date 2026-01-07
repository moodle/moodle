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

namespace Google\Service\AndroidPublisher;

class DeviceMetadata extends \Google\Model
{
  /**
   * Device CPU make, e.g. "Qualcomm"
   *
   * @var string
   */
  public $cpuMake;
  /**
   * Device CPU model, e.g. "MSM8974"
   *
   * @var string
   */
  public $cpuModel;
  /**
   * Device class (e.g. tablet)
   *
   * @var string
   */
  public $deviceClass;
  /**
   * OpenGL version
   *
   * @var int
   */
  public $glEsVersion;
  /**
   * Device manufacturer (e.g. Motorola)
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Comma separated list of native platforms (e.g. "arm", "arm7")
   *
   * @var string
   */
  public $nativePlatform;
  /**
   * Device model name (e.g. Droid)
   *
   * @var string
   */
  public $productName;
  /**
   * Device RAM in Megabytes, e.g. "2048"
   *
   * @var int
   */
  public $ramMb;
  /**
   * Screen density in DPI
   *
   * @var int
   */
  public $screenDensityDpi;
  /**
   * Screen height in pixels
   *
   * @var int
   */
  public $screenHeightPx;
  /**
   * Screen width in pixels
   *
   * @var int
   */
  public $screenWidthPx;

  /**
   * Device CPU make, e.g. "Qualcomm"
   *
   * @param string $cpuMake
   */
  public function setCpuMake($cpuMake)
  {
    $this->cpuMake = $cpuMake;
  }
  /**
   * @return string
   */
  public function getCpuMake()
  {
    return $this->cpuMake;
  }
  /**
   * Device CPU model, e.g. "MSM8974"
   *
   * @param string $cpuModel
   */
  public function setCpuModel($cpuModel)
  {
    $this->cpuModel = $cpuModel;
  }
  /**
   * @return string
   */
  public function getCpuModel()
  {
    return $this->cpuModel;
  }
  /**
   * Device class (e.g. tablet)
   *
   * @param string $deviceClass
   */
  public function setDeviceClass($deviceClass)
  {
    $this->deviceClass = $deviceClass;
  }
  /**
   * @return string
   */
  public function getDeviceClass()
  {
    return $this->deviceClass;
  }
  /**
   * OpenGL version
   *
   * @param int $glEsVersion
   */
  public function setGlEsVersion($glEsVersion)
  {
    $this->glEsVersion = $glEsVersion;
  }
  /**
   * @return int
   */
  public function getGlEsVersion()
  {
    return $this->glEsVersion;
  }
  /**
   * Device manufacturer (e.g. Motorola)
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
   * Comma separated list of native platforms (e.g. "arm", "arm7")
   *
   * @param string $nativePlatform
   */
  public function setNativePlatform($nativePlatform)
  {
    $this->nativePlatform = $nativePlatform;
  }
  /**
   * @return string
   */
  public function getNativePlatform()
  {
    return $this->nativePlatform;
  }
  /**
   * Device model name (e.g. Droid)
   *
   * @param string $productName
   */
  public function setProductName($productName)
  {
    $this->productName = $productName;
  }
  /**
   * @return string
   */
  public function getProductName()
  {
    return $this->productName;
  }
  /**
   * Device RAM in Megabytes, e.g. "2048"
   *
   * @param int $ramMb
   */
  public function setRamMb($ramMb)
  {
    $this->ramMb = $ramMb;
  }
  /**
   * @return int
   */
  public function getRamMb()
  {
    return $this->ramMb;
  }
  /**
   * Screen density in DPI
   *
   * @param int $screenDensityDpi
   */
  public function setScreenDensityDpi($screenDensityDpi)
  {
    $this->screenDensityDpi = $screenDensityDpi;
  }
  /**
   * @return int
   */
  public function getScreenDensityDpi()
  {
    return $this->screenDensityDpi;
  }
  /**
   * Screen height in pixels
   *
   * @param int $screenHeightPx
   */
  public function setScreenHeightPx($screenHeightPx)
  {
    $this->screenHeightPx = $screenHeightPx;
  }
  /**
   * @return int
   */
  public function getScreenHeightPx()
  {
    return $this->screenHeightPx;
  }
  /**
   * Screen width in pixels
   *
   * @param int $screenWidthPx
   */
  public function setScreenWidthPx($screenWidthPx)
  {
    $this->screenWidthPx = $screenWidthPx;
  }
  /**
   * @return int
   */
  public function getScreenWidthPx()
  {
    return $this->screenWidthPx;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceMetadata::class, 'Google_Service_AndroidPublisher_DeviceMetadata');
