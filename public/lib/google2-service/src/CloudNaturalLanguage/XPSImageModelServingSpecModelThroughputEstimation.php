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

namespace Google\Service\CloudNaturalLanguage;

class XPSImageModelServingSpecModelThroughputEstimation extends \Google\Model
{
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Nvidia Tesla K80 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_K80 = 'NVIDIA_TESLA_K80';
  /**
   * Nvidia Tesla P100 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_P100 = 'NVIDIA_TESLA_P100';
  /**
   * Nvidia Tesla V100 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_V100 = 'NVIDIA_TESLA_V100';
  /**
   * Nvidia Tesla P4 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_P4 = 'NVIDIA_TESLA_P4';
  /**
   * Nvidia Tesla T4 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_T4 = 'NVIDIA_TESLA_T4';
  /**
   * Nvidia Tesla A100 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_TESLA_A100 = 'NVIDIA_TESLA_A100';
  /**
   * Nvidia A100 80GB GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_A100_80GB = 'NVIDIA_A100_80GB';
  /**
   * Nvidia L4 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_L4 = 'NVIDIA_L4';
  /**
   * Nvidia H100 80Gb GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_H100_80GB = 'NVIDIA_H100_80GB';
  /**
   * Nvidia H100 80Gb GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_H100_MEGA_80GB = 'NVIDIA_H100_MEGA_80GB';
  /**
   * Nvidia H200 141Gb GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_H200_141GB = 'NVIDIA_H200_141GB';
  /**
   * Nvidia B200 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_B200 = 'NVIDIA_B200';
  /**
   * Nvidia GB200 GPU.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_NVIDIA_GB200 = 'NVIDIA_GB200';
  /**
   * TPU v2 (JellyFish).
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_TPU_V2 = 'TPU_V2';
  /**
   * TPU v3 (DragonFish).
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_TPU_V3 = 'TPU_V3';
  /**
   * TPU_v4 (PufferFish).
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_TPU_V4_POD = 'TPU_V4_POD';
  /**
   * TPU v5 Lite Pods.
   */
  public const COMPUTE_ENGINE_ACCELERATOR_TYPE_TPU_V5_LITEPOD = 'TPU_V5_LITEPOD';
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_TYPE_UNSPECIFIED = 'PARTITION_TYPE_UNSPECIFIED';
  /**
   * The default partition.
   */
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_ZERO = 'PARTITION_ZERO';
  /**
   * It has significantly lower replication than partition-0 and is located in
   * the US only. It also has a larger model size limit and higher default RAM
   * quota than partition-0. Customers with batch traffic, US-based traffic, or
   * very large models should use this partition. Capacity in this partition is
   * significantly cheaper than partition-0.
   */
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_REDUCED_HOMING = 'PARTITION_REDUCED_HOMING';
  /**
   * To be used by customers with Jellyfish-accelerated ops.
   */
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_JELLYFISH = 'PARTITION_JELLYFISH';
  /**
   * The partition used by regionalized servomatic cloud regions.
   */
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_CPU = 'PARTITION_CPU';
  /**
   * The partition used for loading models from custom storage.
   */
  public const SERVOMATIC_PARTITION_TYPE_PARTITION_CUSTOM_STORAGE_CPU = 'PARTITION_CUSTOM_STORAGE_CPU';
  /**
   * @var string
   */
  public $computeEngineAcceleratorType;
  /**
   * Estimated latency.
   *
   * @var 
   */
  public $latencyInMilliseconds;
  /**
   * The approximate qps a deployed node can serve.
   *
   * @var 
   */
  public $nodeQps;
  /**
   * @var string
   */
  public $servomaticPartitionType;

  /**
   * @param self::COMPUTE_ENGINE_ACCELERATOR_TYPE_* $computeEngineAcceleratorType
   */
  public function setComputeEngineAcceleratorType($computeEngineAcceleratorType)
  {
    $this->computeEngineAcceleratorType = $computeEngineAcceleratorType;
  }
  /**
   * @return self::COMPUTE_ENGINE_ACCELERATOR_TYPE_*
   */
  public function getComputeEngineAcceleratorType()
  {
    return $this->computeEngineAcceleratorType;
  }
  public function setLatencyInMilliseconds($latencyInMilliseconds)
  {
    $this->latencyInMilliseconds = $latencyInMilliseconds;
  }
  public function getLatencyInMilliseconds()
  {
    return $this->latencyInMilliseconds;
  }
  public function setNodeQps($nodeQps)
  {
    $this->nodeQps = $nodeQps;
  }
  public function getNodeQps()
  {
    return $this->nodeQps;
  }
  /**
   * @param self::SERVOMATIC_PARTITION_TYPE_* $servomaticPartitionType
   */
  public function setServomaticPartitionType($servomaticPartitionType)
  {
    $this->servomaticPartitionType = $servomaticPartitionType;
  }
  /**
   * @return self::SERVOMATIC_PARTITION_TYPE_*
   */
  public function getServomaticPartitionType()
  {
    return $this->servomaticPartitionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageModelServingSpecModelThroughputEstimation::class, 'Google_Service_CloudNaturalLanguage_XPSImageModelServingSpecModelThroughputEstimation');
