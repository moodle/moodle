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

namespace Google\Service\Dataproc;

class GkeNodePoolAcceleratorConfig extends \Google\Model
{
  /**
   * The number of accelerator cards exposed to an instance.
   *
   * @var string
   */
  public $acceleratorCount;
  /**
   * The accelerator type resource namename (see GPUs on Compute Engine).
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Size of partitions to create on the GPU. Valid values are described in the
   * NVIDIA mig user guide (https://docs.nvidia.com/datacenter/tesla/mig-user-
   * guide/#partitioning).
   *
   * @var string
   */
  public $gpuPartitionSize;

  /**
   * The number of accelerator cards exposed to an instance.
   *
   * @param string $acceleratorCount
   */
  public function setAcceleratorCount($acceleratorCount)
  {
    $this->acceleratorCount = $acceleratorCount;
  }
  /**
   * @return string
   */
  public function getAcceleratorCount()
  {
    return $this->acceleratorCount;
  }
  /**
   * The accelerator type resource namename (see GPUs on Compute Engine).
   *
   * @param string $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return string
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * Size of partitions to create on the GPU. Valid values are described in the
   * NVIDIA mig user guide (https://docs.nvidia.com/datacenter/tesla/mig-user-
   * guide/#partitioning).
   *
   * @param string $gpuPartitionSize
   */
  public function setGpuPartitionSize($gpuPartitionSize)
  {
    $this->gpuPartitionSize = $gpuPartitionSize;
  }
  /**
   * @return string
   */
  public function getGpuPartitionSize()
  {
    return $this->gpuPartitionSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeNodePoolAcceleratorConfig::class, 'Google_Service_Dataproc_GkeNodePoolAcceleratorConfig');
