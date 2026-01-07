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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1AcceleratorConfig extends \Google\Model
{
  /**
   * Unspecified accelerator type. Default to no GPU.
   */
  public const TYPE_ACCELERATOR_TYPE_UNSPECIFIED = 'ACCELERATOR_TYPE_UNSPECIFIED';
  /**
   * Nvidia Tesla K80 GPU.
   */
  public const TYPE_NVIDIA_TESLA_K80 = 'NVIDIA_TESLA_K80';
  /**
   * Nvidia Tesla P100 GPU.
   */
  public const TYPE_NVIDIA_TESLA_P100 = 'NVIDIA_TESLA_P100';
  /**
   * Nvidia V100 GPU.
   */
  public const TYPE_NVIDIA_TESLA_V100 = 'NVIDIA_TESLA_V100';
  /**
   * Nvidia Tesla P4 GPU.
   */
  public const TYPE_NVIDIA_TESLA_P4 = 'NVIDIA_TESLA_P4';
  /**
   * Nvidia T4 GPU.
   */
  public const TYPE_NVIDIA_TESLA_T4 = 'NVIDIA_TESLA_T4';
  /**
   * Nvidia A100 GPU.
   */
  public const TYPE_NVIDIA_TESLA_A100 = 'NVIDIA_TESLA_A100';
  /**
   * TPU v2.
   */
  public const TYPE_TPU_V2 = 'TPU_V2';
  /**
   * TPU v3.
   */
  public const TYPE_TPU_V3 = 'TPU_V3';
  /**
   * TPU v2 POD.
   */
  public const TYPE_TPU_V2_POD = 'TPU_V2_POD';
  /**
   * TPU v3 POD.
   */
  public const TYPE_TPU_V3_POD = 'TPU_V3_POD';
  /**
   * TPU v4 POD.
   */
  public const TYPE_TPU_V4_POD = 'TPU_V4_POD';
  /**
   * The number of accelerators to attach to each machine running the job.
   *
   * @var string
   */
  public $count;
  /**
   * The type of accelerator to use.
   *
   * @var string
   */
  public $type;

  /**
   * The number of accelerators to attach to each machine running the job.
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
   * The type of accelerator to use.
   *
   * Accepted values: ACCELERATOR_TYPE_UNSPECIFIED, NVIDIA_TESLA_K80,
   * NVIDIA_TESLA_P100, NVIDIA_TESLA_V100, NVIDIA_TESLA_P4, NVIDIA_TESLA_T4,
   * NVIDIA_TESLA_A100, TPU_V2, TPU_V3, TPU_V2_POD, TPU_V3_POD, TPU_V4_POD
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
class_alias(GoogleCloudMlV1AcceleratorConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1AcceleratorConfig');
