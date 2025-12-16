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

class AcceleratorConfig extends \Google\Model
{
  /**
   * Accelerator type is not specified.
   */
  public const TYPE_ACCELERATOR_TYPE_UNSPECIFIED = 'ACCELERATOR_TYPE_UNSPECIFIED';
  /**
   * Accelerator type is Nvidia Tesla P100.
   */
  public const TYPE_NVIDIA_TESLA_P100 = 'NVIDIA_TESLA_P100';
  /**
   * Accelerator type is Nvidia Tesla V100.
   */
  public const TYPE_NVIDIA_TESLA_V100 = 'NVIDIA_TESLA_V100';
  /**
   * Accelerator type is Nvidia Tesla P4.
   */
  public const TYPE_NVIDIA_TESLA_P4 = 'NVIDIA_TESLA_P4';
  /**
   * Accelerator type is Nvidia Tesla T4.
   */
  public const TYPE_NVIDIA_TESLA_T4 = 'NVIDIA_TESLA_T4';
  /**
   * Accelerator type is Nvidia Tesla A100 - 40GB.
   */
  public const TYPE_NVIDIA_TESLA_A100 = 'NVIDIA_TESLA_A100';
  /**
   * Accelerator type is Nvidia Tesla A100 - 80GB.
   */
  public const TYPE_NVIDIA_A100_80GB = 'NVIDIA_A100_80GB';
  /**
   * Accelerator type is Nvidia Tesla L4.
   */
  public const TYPE_NVIDIA_L4 = 'NVIDIA_L4';
  /**
   * Accelerator type is Nvidia Tesla H100 - 80GB.
   */
  public const TYPE_NVIDIA_H100_80GB = 'NVIDIA_H100_80GB';
  /**
   * Accelerator type is Nvidia Tesla H100 - MEGA 80GB.
   */
  public const TYPE_NVIDIA_H100_MEGA_80GB = 'NVIDIA_H100_MEGA_80GB';
  /**
   * Accelerator type is Nvidia Tesla H200 - 141GB.
   */
  public const TYPE_NVIDIA_H200_141GB = 'NVIDIA_H200_141GB';
  /**
   * Accelerator type is NVIDIA Tesla T4 Virtual Workstations.
   */
  public const TYPE_NVIDIA_TESLA_T4_VWS = 'NVIDIA_TESLA_T4_VWS';
  /**
   * Accelerator type is NVIDIA Tesla P100 Virtual Workstations.
   */
  public const TYPE_NVIDIA_TESLA_P100_VWS = 'NVIDIA_TESLA_P100_VWS';
  /**
   * Accelerator type is NVIDIA Tesla P4 Virtual Workstations.
   */
  public const TYPE_NVIDIA_TESLA_P4_VWS = 'NVIDIA_TESLA_P4_VWS';
  /**
   * Accelerator type is NVIDIA B200.
   */
  public const TYPE_NVIDIA_B200 = 'NVIDIA_B200';
  /**
   * Optional. Count of cores of this accelerator.
   *
   * @var string
   */
  public $coreCount;
  /**
   * Optional. Type of this accelerator.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Count of cores of this accelerator.
   *
   * @param string $coreCount
   */
  public function setCoreCount($coreCount)
  {
    $this->coreCount = $coreCount;
  }
  /**
   * @return string
   */
  public function getCoreCount()
  {
    return $this->coreCount;
  }
  /**
   * Optional. Type of this accelerator.
   *
   * Accepted values: ACCELERATOR_TYPE_UNSPECIFIED, NVIDIA_TESLA_P100,
   * NVIDIA_TESLA_V100, NVIDIA_TESLA_P4, NVIDIA_TESLA_T4, NVIDIA_TESLA_A100,
   * NVIDIA_A100_80GB, NVIDIA_L4, NVIDIA_H100_80GB, NVIDIA_H100_MEGA_80GB,
   * NVIDIA_H200_141GB, NVIDIA_TESLA_T4_VWS, NVIDIA_TESLA_P100_VWS,
   * NVIDIA_TESLA_P4_VWS, NVIDIA_B200
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcceleratorConfig::class, 'Google_Service_AIPlatformNotebooks_AcceleratorConfig');
