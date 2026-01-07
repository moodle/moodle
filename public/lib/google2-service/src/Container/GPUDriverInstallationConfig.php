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

namespace Google\Service\Container;

class GPUDriverInstallationConfig extends \Google\Model
{
  /**
   * Default value is to not install any GPU driver.
   */
  public const GPU_DRIVER_VERSION_GPU_DRIVER_VERSION_UNSPECIFIED = 'GPU_DRIVER_VERSION_UNSPECIFIED';
  /**
   * Disable GPU driver auto installation and needs manual installation
   */
  public const GPU_DRIVER_VERSION_INSTALLATION_DISABLED = 'INSTALLATION_DISABLED';
  /**
   * "Default" GPU driver in COS and Ubuntu.
   */
  public const GPU_DRIVER_VERSION_DEFAULT = 'DEFAULT';
  /**
   * "Latest" GPU driver in COS.
   */
  public const GPU_DRIVER_VERSION_LATEST = 'LATEST';
  /**
   * Mode for how the GPU driver is installed.
   *
   * @var string
   */
  public $gpuDriverVersion;

  /**
   * Mode for how the GPU driver is installed.
   *
   * Accepted values: GPU_DRIVER_VERSION_UNSPECIFIED, INSTALLATION_DISABLED,
   * DEFAULT, LATEST
   *
   * @param self::GPU_DRIVER_VERSION_* $gpuDriverVersion
   */
  public function setGpuDriverVersion($gpuDriverVersion)
  {
    $this->gpuDriverVersion = $gpuDriverVersion;
  }
  /**
   * @return self::GPU_DRIVER_VERSION_*
   */
  public function getGpuDriverVersion()
  {
    return $this->gpuDriverVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GPUDriverInstallationConfig::class, 'Google_Service_Container_GPUDriverInstallationConfig');
