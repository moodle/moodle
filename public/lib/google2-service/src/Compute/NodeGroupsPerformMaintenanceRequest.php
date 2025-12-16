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

namespace Google\Service\Compute;

class NodeGroupsPerformMaintenanceRequest extends \Google\Collection
{
  protected $collection_key = 'nodes';
  /**
   * [Required] List of nodes affected by the call.
   *
   * @var string[]
   */
  public $nodes;
  /**
   * The start time of the schedule. The timestamp is an RFC3339 string.
   *
   * @var string
   */
  public $startTime;

  /**
   * [Required] List of nodes affected by the call.
   *
   * @param string[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return string[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * The start time of the schedule. The timestamp is an RFC3339 string.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeGroupsPerformMaintenanceRequest::class, 'Google_Service_Compute_NodeGroupsPerformMaintenanceRequest');
