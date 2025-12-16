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

namespace Google\Service\GKEOnPrem;

class VmwareNodePoolAutoscalingConfig extends \Google\Model
{
  /**
   * Maximum number of replicas in the NodePool.
   *
   * @var int
   */
  public $maxReplicas;
  /**
   * Minimum number of replicas in the NodePool.
   *
   * @var int
   */
  public $minReplicas;

  /**
   * Maximum number of replicas in the NodePool.
   *
   * @param int $maxReplicas
   */
  public function setMaxReplicas($maxReplicas)
  {
    $this->maxReplicas = $maxReplicas;
  }
  /**
   * @return int
   */
  public function getMaxReplicas()
  {
    return $this->maxReplicas;
  }
  /**
   * Minimum number of replicas in the NodePool.
   *
   * @param int $minReplicas
   */
  public function setMinReplicas($minReplicas)
  {
    $this->minReplicas = $minReplicas;
  }
  /**
   * @return int
   */
  public function getMinReplicas()
  {
    return $this->minReplicas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareNodePoolAutoscalingConfig::class, 'Google_Service_GKEOnPrem_VmwareNodePoolAutoscalingConfig');
