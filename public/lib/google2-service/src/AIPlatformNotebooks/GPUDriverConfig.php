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

namespace Google\Service\AIPlatformNotebooks;

class GPUDriverConfig extends \Google\Model
{
  /**
   * Optional. Specify a custom Cloud Storage path where the GPU driver is
   * stored. If not specified, we'll automatically choose from official GPU
   * drivers.
   *
   * @var string
   */
  public $customGpuDriverPath;
  /**
   * Optional. Whether the end user authorizes Google Cloud to install GPU
   * driver on this VM instance. If this field is empty or set to false, the GPU
   * driver won't be installed. Only applicable to instances with GPUs.
   *
   * @var bool
   */
  public $enableGpuDriver;

  /**
   * Optional. Specify a custom Cloud Storage path where the GPU driver is
   * stored. If not specified, we'll automatically choose from official GPU
   * drivers.
   *
   * @param string $customGpuDriverPath
   */
  public function setCustomGpuDriverPath($customGpuDriverPath)
  {
    $this->customGpuDriverPath = $customGpuDriverPath;
  }
  /**
   * @return string
   */
  public function getCustomGpuDriverPath()
  {
    return $this->customGpuDriverPath;
  }
  /**
   * Optional. Whether the end user authorizes Google Cloud to install GPU
   * driver on this VM instance. If this field is empty or set to false, the GPU
   * driver won't be installed. Only applicable to instances with GPUs.
   *
   * @param bool $enableGpuDriver
   */
  public function setEnableGpuDriver($enableGpuDriver)
  {
    $this->enableGpuDriver = $enableGpuDriver;
  }
  /**
   * @return bool
   */
  public function getEnableGpuDriver()
  {
    return $this->enableGpuDriver;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GPUDriverConfig::class, 'Google_Service_AIPlatformNotebooks_GPUDriverConfig');
