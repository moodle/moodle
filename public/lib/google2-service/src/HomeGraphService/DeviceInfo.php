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

class DeviceInfo extends \Google\Model
{
  /**
   * Device hardware version.
   *
   * @var string
   */
  public $hwVersion;
  /**
   * Device manufacturer.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Device model.
   *
   * @var string
   */
  public $model;
  /**
   * Device software version.
   *
   * @var string
   */
  public $swVersion;

  /**
   * Device hardware version.
   *
   * @param string $hwVersion
   */
  public function setHwVersion($hwVersion)
  {
    $this->hwVersion = $hwVersion;
  }
  /**
   * @return string
   */
  public function getHwVersion()
  {
    return $this->hwVersion;
  }
  /**
   * Device manufacturer.
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
   * Device model.
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
   * Device software version.
   *
   * @param string $swVersion
   */
  public function setSwVersion($swVersion)
  {
    $this->swVersion = $swVersion;
  }
  /**
   * @return string
   */
  public function getSwVersion()
  {
    return $this->swVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceInfo::class, 'Google_Service_HomeGraphService_DeviceInfo');
