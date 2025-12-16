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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ResourcePool extends \Google\Model
{
  protected $autoscalingSpecType = GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec::class;
  protected $autoscalingSpecDataType = '';
  protected $diskSpecType = GoogleCloudAiplatformV1DiskSpec::class;
  protected $diskSpecDataType = '';
  /**
   * Immutable. The unique ID in a PersistentResource for referring to this
   * resource pool. User can specify it if necessary. Otherwise, it's generated
   * automatically.
   *
   * @var string
   */
  public $id;
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  /**
   * Optional. The total number of machines to use for this resource pool.
   *
   * @var string
   */
  public $replicaCount;
  /**
   * Output only. The number of machines currently in use by training jobs for
   * this resource pool. Will replace idle_replica_count.
   *
   * @var string
   */
  public $usedReplicaCount;

  /**
   * Optional. Optional spec to configure GKE or Ray-on-Vertex autoscaling
   *
   * @param GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec $autoscalingSpec
   */
  public function setAutoscalingSpec(GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec $autoscalingSpec)
  {
    $this->autoscalingSpec = $autoscalingSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec
   */
  public function getAutoscalingSpec()
  {
    return $this->autoscalingSpec;
  }
  /**
   * Optional. Disk spec for the machine in this node pool.
   *
   * @param GoogleCloudAiplatformV1DiskSpec $diskSpec
   */
  public function setDiskSpec(GoogleCloudAiplatformV1DiskSpec $diskSpec)
  {
    $this->diskSpec = $diskSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1DiskSpec
   */
  public function getDiskSpec()
  {
    return $this->diskSpec;
  }
  /**
   * Immutable. The unique ID in a PersistentResource for referring to this
   * resource pool. User can specify it if necessary. Otherwise, it's generated
   * automatically.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. Immutable. The specification of a single machine.
   *
   * @param GoogleCloudAiplatformV1MachineSpec $machineSpec
   */
  public function setMachineSpec(GoogleCloudAiplatformV1MachineSpec $machineSpec)
  {
    $this->machineSpec = $machineSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1MachineSpec
   */
  public function getMachineSpec()
  {
    return $this->machineSpec;
  }
  /**
   * Optional. The total number of machines to use for this resource pool.
   *
   * @param string $replicaCount
   */
  public function setReplicaCount($replicaCount)
  {
    $this->replicaCount = $replicaCount;
  }
  /**
   * @return string
   */
  public function getReplicaCount()
  {
    return $this->replicaCount;
  }
  /**
   * Output only. The number of machines currently in use by training jobs for
   * this resource pool. Will replace idle_replica_count.
   *
   * @param string $usedReplicaCount
   */
  public function setUsedReplicaCount($usedReplicaCount)
  {
    $this->usedReplicaCount = $usedReplicaCount;
  }
  /**
   * @return string
   */
  public function getUsedReplicaCount()
  {
    return $this->usedReplicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ResourcePool::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ResourcePool');
