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

class GPUSharingConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const GPU_SHARING_STRATEGY_GPU_SHARING_STRATEGY_UNSPECIFIED = 'GPU_SHARING_STRATEGY_UNSPECIFIED';
  /**
   * GPUs are time-shared between containers.
   */
  public const GPU_SHARING_STRATEGY_TIME_SHARING = 'TIME_SHARING';
  /**
   * GPUs are shared between containers with NVIDIA MPS.
   */
  public const GPU_SHARING_STRATEGY_MPS = 'MPS';
  /**
   * The type of GPU sharing strategy to enable on the GPU node.
   *
   * @var string
   */
  public $gpuSharingStrategy;
  /**
   * The max number of containers that can share a physical GPU.
   *
   * @var string
   */
  public $maxSharedClientsPerGpu;

  /**
   * The type of GPU sharing strategy to enable on the GPU node.
   *
   * Accepted values: GPU_SHARING_STRATEGY_UNSPECIFIED, TIME_SHARING, MPS
   *
   * @param self::GPU_SHARING_STRATEGY_* $gpuSharingStrategy
   */
  public function setGpuSharingStrategy($gpuSharingStrategy)
  {
    $this->gpuSharingStrategy = $gpuSharingStrategy;
  }
  /**
   * @return self::GPU_SHARING_STRATEGY_*
   */
  public function getGpuSharingStrategy()
  {
    return $this->gpuSharingStrategy;
  }
  /**
   * The max number of containers that can share a physical GPU.
   *
   * @param string $maxSharedClientsPerGpu
   */
  public function setMaxSharedClientsPerGpu($maxSharedClientsPerGpu)
  {
    $this->maxSharedClientsPerGpu = $maxSharedClientsPerGpu;
  }
  /**
   * @return string
   */
  public function getMaxSharedClientsPerGpu()
  {
    return $this->maxSharedClientsPerGpu;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GPUSharingConfig::class, 'Google_Service_Container_GPUSharingConfig');
