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

namespace Google\Service\CloudMemorystoreforMemcached;

class ApplyParametersRequest extends \Google\Collection
{
  protected $collection_key = 'nodeIds';
  /**
   * Whether to apply instance-level parameter group to all nodes. If set to
   * true, users are restricted from specifying individual nodes, and
   * `ApplyParameters` updates all nodes within the instance.
   *
   * @var bool
   */
  public $applyAll;
  /**
   * Nodes to which the instance-level parameter group is applied.
   *
   * @var string[]
   */
  public $nodeIds;

  /**
   * Whether to apply instance-level parameter group to all nodes. If set to
   * true, users are restricted from specifying individual nodes, and
   * `ApplyParameters` updates all nodes within the instance.
   *
   * @param bool $applyAll
   */
  public function setApplyAll($applyAll)
  {
    $this->applyAll = $applyAll;
  }
  /**
   * @return bool
   */
  public function getApplyAll()
  {
    return $this->applyAll;
  }
  /**
   * Nodes to which the instance-level parameter group is applied.
   *
   * @param string[] $nodeIds
   */
  public function setNodeIds($nodeIds)
  {
    $this->nodeIds = $nodeIds;
  }
  /**
   * @return string[]
   */
  public function getNodeIds()
  {
    return $this->nodeIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplyParametersRequest::class, 'Google_Service_CloudMemorystoreforMemcached_ApplyParametersRequest');
