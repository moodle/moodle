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

class BestEffortProvisioning extends \Google\Model
{
  /**
   * When this is enabled, cluster/node pool creations will ignore non-fatal
   * errors like stockout to best provision as many nodes as possible right now
   * and eventually bring up all target number of nodes
   *
   * @var bool
   */
  public $enabled;
  /**
   * Minimum number of nodes to be provisioned to be considered as succeeded,
   * and the rest of nodes will be provisioned gradually and eventually when
   * stockout issue has been resolved.
   *
   * @var int
   */
  public $minProvisionNodes;

  /**
   * When this is enabled, cluster/node pool creations will ignore non-fatal
   * errors like stockout to best provision as many nodes as possible right now
   * and eventually bring up all target number of nodes
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Minimum number of nodes to be provisioned to be considered as succeeded,
   * and the rest of nodes will be provisioned gradually and eventually when
   * stockout issue has been resolved.
   *
   * @param int $minProvisionNodes
   */
  public function setMinProvisionNodes($minProvisionNodes)
  {
    $this->minProvisionNodes = $minProvisionNodes;
  }
  /**
   * @return int
   */
  public function getMinProvisionNodes()
  {
    return $this->minProvisionNodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BestEffortProvisioning::class, 'Google_Service_Container_BestEffortProvisioning');
