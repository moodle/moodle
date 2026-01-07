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

class GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec extends \Google\Model
{
  /**
   * Optional. max replicas in the node pool, must be ≥ replica_count and >
   * min_replica_count or will throw error
   *
   * @var string
   */
  public $maxReplicaCount;
  /**
   * Optional. min replicas in the node pool, must be ≤ replica_count and <
   * max_replica_count or will throw error. For autoscaling enabled Ray-on-
   * Vertex, we allow min_replica_count of a resource_pool to be 0 to match the
   * OSS Ray behavior(https://docs.ray.io/en/latest/cluster/vms/user-
   * guides/configuring-autoscaling.html#cluster-config-parameters). As for
   * Persistent Resource, the min_replica_count must be > 0, we added a
   * corresponding validation inside
   * CreatePersistentResourceRequestValidator.java.
   *
   * @var string
   */
  public $minReplicaCount;

  /**
   * Optional. max replicas in the node pool, must be ≥ replica_count and >
   * min_replica_count or will throw error
   *
   * @param string $maxReplicaCount
   */
  public function setMaxReplicaCount($maxReplicaCount)
  {
    $this->maxReplicaCount = $maxReplicaCount;
  }
  /**
   * @return string
   */
  public function getMaxReplicaCount()
  {
    return $this->maxReplicaCount;
  }
  /**
   * Optional. min replicas in the node pool, must be ≤ replica_count and <
   * max_replica_count or will throw error. For autoscaling enabled Ray-on-
   * Vertex, we allow min_replica_count of a resource_pool to be 0 to match the
   * OSS Ray behavior(https://docs.ray.io/en/latest/cluster/vms/user-
   * guides/configuring-autoscaling.html#cluster-config-parameters). As for
   * Persistent Resource, the min_replica_count must be > 0, we added a
   * corresponding validation inside
   * CreatePersistentResourceRequestValidator.java.
   *
   * @param string $minReplicaCount
   */
  public function setMinReplicaCount($minReplicaCount)
  {
    $this->minReplicaCount = $minReplicaCount;
  }
  /**
   * @return string
   */
  public function getMinReplicaCount()
  {
    return $this->minReplicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ResourcePoolAutoscalingSpec');
