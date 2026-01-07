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

class GoogleCloudAiplatformV1BatchDedicatedResources extends \Google\Model
{
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  /**
   * Immutable. The maximum number of machine replicas the batch operation may
   * be scaled to. The default value is 10.
   *
   * @var int
   */
  public $maxReplicaCount;
  /**
   * Immutable. The number of machine replicas used at the start of the batch
   * operation. If not set, Vertex AI decides starting number, not greater than
   * max_replica_count
   *
   * @var int
   */
  public $startingReplicaCount;

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
   * Immutable. The maximum number of machine replicas the batch operation may
   * be scaled to. The default value is 10.
   *
   * @param int $maxReplicaCount
   */
  public function setMaxReplicaCount($maxReplicaCount)
  {
    $this->maxReplicaCount = $maxReplicaCount;
  }
  /**
   * @return int
   */
  public function getMaxReplicaCount()
  {
    return $this->maxReplicaCount;
  }
  /**
   * Immutable. The number of machine replicas used at the start of the batch
   * operation. If not set, Vertex AI decides starting number, not greater than
   * max_replica_count
   *
   * @param int $startingReplicaCount
   */
  public function setStartingReplicaCount($startingReplicaCount)
  {
    $this->startingReplicaCount = $startingReplicaCount;
  }
  /**
   * @return int
   */
  public function getStartingReplicaCount()
  {
    return $this->startingReplicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BatchDedicatedResources::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchDedicatedResources');
