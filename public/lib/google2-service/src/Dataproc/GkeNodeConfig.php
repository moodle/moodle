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

class GkeNodeConfig extends \Google\Collection
{
  protected $collection_key = 'accelerators';
  protected $acceleratorsType = GkeNodePoolAcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  /**
   * Optional. The Customer Managed Encryption Key (CMEK)
   * (https://cloud.google.com/kubernetes-engine/docs/how-to/using-cmek) used to
   * encrypt the boot disk attached to each node in the node pool. Specify the
   * key using the following format: projects/{project}/locations/{location}/key
   * Rings/{key_ring}/cryptoKeys/{crypto_key}
   *
   * @var string
   */
  public $bootDiskKmsKey;
  /**
   * Optional. The number of local SSD disks to attach to the node, which is
   * limited by the maximum number of disks allowable per zone (see Adding Local
   * SSDs (https://cloud.google.com/compute/docs/disks/local-ssd)).
   *
   * @var int
   */
  public $localSsdCount;
  /**
   * Optional. The name of a Compute Engine machine type
   * (https://cloud.google.com/compute/docs/machine-types).
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. Minimum CPU platform
   * (https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform)
   * to be used by this instance. The instance may be scheduled on the specified
   * or a newer CPU platform. Specify the friendly names of CPU platforms, such
   * as "Intel Haswell"` or Intel Sandy Bridge".
   *
   * @var string
   */
  public $minCpuPlatform;
  /**
   * Optional. Whether the nodes are created as legacy preemptible VM instances
   * (https://cloud.google.com/compute/docs/instances/preemptible). Also see
   * Spot VMs, preemptible VM instances without a maximum lifetime. Legacy and
   * Spot preemptible nodes cannot be used in a node pool with the CONTROLLER
   * role or in the DEFAULT node pool if the CONTROLLER role is not assigned
   * (the DEFAULT node pool will assume the CONTROLLER role).
   *
   * @var bool
   */
  public $preemptible;
  /**
   * Optional. Whether the nodes are created as Spot VM instances
   * (https://cloud.google.com/compute/docs/instances/spot). Spot VMs are the
   * latest update to legacy preemptible VMs. Spot VMs do not have a maximum
   * lifetime. Legacy and Spot preemptible nodes cannot be used in a node pool
   * with the CONTROLLER role or in the DEFAULT node pool if the CONTROLLER role
   * is not assigned (the DEFAULT node pool will assume the CONTROLLER role).
   *
   * @var bool
   */
  public $spot;

  /**
   * Optional. A list of hardware accelerators
   * (https://cloud.google.com/compute/docs/gpus) to attach to each node.
   *
   * @param GkeNodePoolAcceleratorConfig[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return GkeNodePoolAcceleratorConfig[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * Optional. The Customer Managed Encryption Key (CMEK)
   * (https://cloud.google.com/kubernetes-engine/docs/how-to/using-cmek) used to
   * encrypt the boot disk attached to each node in the node pool. Specify the
   * key using the following format: projects/{project}/locations/{location}/key
   * Rings/{key_ring}/cryptoKeys/{crypto_key}
   *
   * @param string $bootDiskKmsKey
   */
  public function setBootDiskKmsKey($bootDiskKmsKey)
  {
    $this->bootDiskKmsKey = $bootDiskKmsKey;
  }
  /**
   * @return string
   */
  public function getBootDiskKmsKey()
  {
    return $this->bootDiskKmsKey;
  }
  /**
   * Optional. The number of local SSD disks to attach to the node, which is
   * limited by the maximum number of disks allowable per zone (see Adding Local
   * SSDs (https://cloud.google.com/compute/docs/disks/local-ssd)).
   *
   * @param int $localSsdCount
   */
  public function setLocalSsdCount($localSsdCount)
  {
    $this->localSsdCount = $localSsdCount;
  }
  /**
   * @return int
   */
  public function getLocalSsdCount()
  {
    return $this->localSsdCount;
  }
  /**
   * Optional. The name of a Compute Engine machine type
   * (https://cloud.google.com/compute/docs/machine-types).
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. Minimum CPU platform
   * (https://cloud.google.com/compute/docs/instances/specify-min-cpu-platform)
   * to be used by this instance. The instance may be scheduled on the specified
   * or a newer CPU platform. Specify the friendly names of CPU platforms, such
   * as "Intel Haswell"` or Intel Sandy Bridge".
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Optional. Whether the nodes are created as legacy preemptible VM instances
   * (https://cloud.google.com/compute/docs/instances/preemptible). Also see
   * Spot VMs, preemptible VM instances without a maximum lifetime. Legacy and
   * Spot preemptible nodes cannot be used in a node pool with the CONTROLLER
   * role or in the DEFAULT node pool if the CONTROLLER role is not assigned
   * (the DEFAULT node pool will assume the CONTROLLER role).
   *
   * @param bool $preemptible
   */
  public function setPreemptible($preemptible)
  {
    $this->preemptible = $preemptible;
  }
  /**
   * @return bool
   */
  public function getPreemptible()
  {
    return $this->preemptible;
  }
  /**
   * Optional. Whether the nodes are created as Spot VM instances
   * (https://cloud.google.com/compute/docs/instances/spot). Spot VMs are the
   * latest update to legacy preemptible VMs. Spot VMs do not have a maximum
   * lifetime. Legacy and Spot preemptible nodes cannot be used in a node pool
   * with the CONTROLLER role or in the DEFAULT node pool if the CONTROLLER role
   * is not assigned (the DEFAULT node pool will assume the CONTROLLER role).
   *
   * @param bool $spot
   */
  public function setSpot($spot)
  {
    $this->spot = $spot;
  }
  /**
   * @return bool
   */
  public function getSpot()
  {
    return $this->spot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeNodeConfig::class, 'Google_Service_Dataproc_GkeNodeConfig');
