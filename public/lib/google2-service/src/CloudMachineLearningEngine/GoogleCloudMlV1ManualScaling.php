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

class GoogleCloudMlV1ManualScaling extends \Google\Model
{
  /**
   * The number of nodes to allocate for this model. These nodes are always up,
   * starting from the time the model is deployed, so the cost of operating this
   * model will be proportional to `nodes` * number of hours since last billing
   * cycle plus the cost for each prediction performed.
   *
   * @var int
   */
  public $nodes;

  /**
   * The number of nodes to allocate for this model. These nodes are always up,
   * starting from the time the model is deployed, so the cost of operating this
   * model will be proportional to `nodes` * number of hours since last billing
   * cycle plus the cost for each prediction performed.
   *
   * @param int $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return int
   */
  public function getNodes()
  {
    return $this->nodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1ManualScaling::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1ManualScaling');
