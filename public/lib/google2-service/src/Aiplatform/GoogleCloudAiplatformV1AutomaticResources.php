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

class GoogleCloudAiplatformV1AutomaticResources extends \Google\Model
{
  /**
   * Immutable. The maximum number of replicas that may be deployed on when the
   * traffic against it increases. If the requested value is too large, the
   * deployment will error, but if deployment succeeds then the ability to scale
   * to that many replicas is guaranteed (barring service outages). If traffic
   * increases beyond what its replicas at maximum may handle, a portion of the
   * traffic will be dropped. If this value is not provided, a no upper bound
   * for scaling under heavy traffic will be assume, though Vertex AI may be
   * unable to scale beyond certain replica number.
   *
   * @var int
   */
  public $maxReplicaCount;
  /**
   * Immutable. The minimum number of replicas that will be always deployed on.
   * If traffic against it increases, it may dynamically be deployed onto more
   * replicas up to max_replica_count, and as traffic decreases, some of these
   * extra replicas may be freed. If the requested value is too large, the
   * deployment will error.
   *
   * @var int
   */
  public $minReplicaCount;

  /**
   * Immutable. The maximum number of replicas that may be deployed on when the
   * traffic against it increases. If the requested value is too large, the
   * deployment will error, but if deployment succeeds then the ability to scale
   * to that many replicas is guaranteed (barring service outages). If traffic
   * increases beyond what its replicas at maximum may handle, a portion of the
   * traffic will be dropped. If this value is not provided, a no upper bound
   * for scaling under heavy traffic will be assume, though Vertex AI may be
   * unable to scale beyond certain replica number.
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
   * Immutable. The minimum number of replicas that will be always deployed on.
   * If traffic against it increases, it may dynamically be deployed onto more
   * replicas up to max_replica_count, and as traffic decreases, some of these
   * extra replicas may be freed. If the requested value is too large, the
   * deployment will error.
   *
   * @param int $minReplicaCount
   */
  public function setMinReplicaCount($minReplicaCount)
  {
    $this->minReplicaCount = $minReplicaCount;
  }
  /**
   * @return int
   */
  public function getMinReplicaCount()
  {
    return $this->minReplicaCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AutomaticResources::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AutomaticResources');
