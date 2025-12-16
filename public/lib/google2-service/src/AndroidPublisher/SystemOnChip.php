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

class SystemOnChip extends \Google\Model
{
  /**
   * Required. The designer of the SoC, eg. "Google" Value of build property
   * "ro.soc.manufacturer"
   * https://developer.android.com/reference/android/os/Build#SOC_MANUFACTURER
   * Required.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * Required. The model of the SoC, eg. "Tensor" Value of build property
   * "ro.soc.model"
   * https://developer.android.com/reference/android/os/Build#SOC_MODEL
   * Required.
   *
   * @var string
   */
  public $model;

  /**
   * Required. The designer of the SoC, eg. "Google" Value of build property
   * "ro.soc.manufacturer"
   * https://developer.android.com/reference/android/os/Build#SOC_MANUFACTURER
   * Required.
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
   * Required. The model of the SoC, eg. "Tensor" Value of build property
   * "ro.soc.model"
   * https://developer.android.com/reference/android/os/Build#SOC_MODEL
   * Required.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SystemOnChip::class, 'Google_Service_AndroidPublisher_SystemOnChip');
