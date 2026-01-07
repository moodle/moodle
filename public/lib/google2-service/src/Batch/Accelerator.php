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

namespace Google\Service\Batch;

class Accelerator extends \Google\Model
{
  /**
   * The number of accelerators of this type.
   *
   * @var string
   */
  public $count;
  /**
   * Optional. The NVIDIA GPU driver version that should be installed for this
   * type. You can define the specific driver version such as "470.103.01",
   * following the driver version requirements in
   * https://cloud.google.com/compute/docs/gpus/install-drivers-gpu#minimum-
   * driver. Batch will install the specific accelerator driver if qualified.
   *
   * @var string
   */
  public $driverVersion;
  /**
   * Deprecated: please use instances[0].install_gpu_drivers instead.
   *
   * @deprecated
   * @var bool
   */
  public $installGpuDrivers;
  /**
   * The accelerator type. For example, "nvidia-tesla-t4". See `gcloud compute
   * accelerator-types list`.
   *
   * @var string
   */
  public $type;

  /**
   * The number of accelerators of this type.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Optional. The NVIDIA GPU driver version that should be installed for this
   * type. You can define the specific driver version such as "470.103.01",
   * following the driver version requirements in
   * https://cloud.google.com/compute/docs/gpus/install-drivers-gpu#minimum-
   * driver. Batch will install the specific accelerator driver if qualified.
   *
   * @param string $driverVersion
   */
  public function setDriverVersion($driverVersion)
  {
    $this->driverVersion = $driverVersion;
  }
  /**
   * @return string
   */
  public function getDriverVersion()
  {
    return $this->driverVersion;
  }
  /**
   * Deprecated: please use instances[0].install_gpu_drivers instead.
   *
   * @deprecated
   * @param bool $installGpuDrivers
   */
  public function setInstallGpuDrivers($installGpuDrivers)
  {
    $this->installGpuDrivers = $installGpuDrivers;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getInstallGpuDrivers()
  {
    return $this->installGpuDrivers;
  }
  /**
   * The accelerator type. For example, "nvidia-tesla-t4". See `gcloud compute
   * accelerator-types list`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Accelerator::class, 'Google_Service_Batch_Accelerator');
