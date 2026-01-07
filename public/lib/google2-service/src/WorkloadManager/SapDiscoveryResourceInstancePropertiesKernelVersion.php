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

namespace Google\Service\WorkloadManager;

class SapDiscoveryResourceInstancePropertiesKernelVersion extends \Google\Model
{
  protected $distroKernelType = SapDiscoveryResourceInstancePropertiesKernelVersionVersion::class;
  protected $distroKernelDataType = '';
  protected $osKernelType = SapDiscoveryResourceInstancePropertiesKernelVersionVersion::class;
  protected $osKernelDataType = '';
  /**
   * Optional. Raw string of the kernel version.
   *
   * @var string
   */
  public $rawString;

  /**
   * Optional. Captures the distro-specific kernel version, the portion of the
   * string following the first dash.
   *
   * @param SapDiscoveryResourceInstancePropertiesKernelVersionVersion $distroKernel
   */
  public function setDistroKernel(SapDiscoveryResourceInstancePropertiesKernelVersionVersion $distroKernel)
  {
    $this->distroKernel = $distroKernel;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesKernelVersionVersion
   */
  public function getDistroKernel()
  {
    return $this->distroKernel;
  }
  /**
   * Optional. Captures the OS-specific kernel version, the portion of the
   * string up to the first dash.
   *
   * @param SapDiscoveryResourceInstancePropertiesKernelVersionVersion $osKernel
   */
  public function setOsKernel(SapDiscoveryResourceInstancePropertiesKernelVersionVersion $osKernel)
  {
    $this->osKernel = $osKernel;
  }
  /**
   * @return SapDiscoveryResourceInstancePropertiesKernelVersionVersion
   */
  public function getOsKernel()
  {
    return $this->osKernel;
  }
  /**
   * Optional. Raw string of the kernel version.
   *
   * @param string $rawString
   */
  public function setRawString($rawString)
  {
    $this->rawString = $rawString;
  }
  /**
   * @return string
   */
  public function getRawString()
  {
    return $this->rawString;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryResourceInstancePropertiesKernelVersion::class, 'Google_Service_WorkloadManager_SapDiscoveryResourceInstancePropertiesKernelVersion');
