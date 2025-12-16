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

namespace Google\Service\Batch;

class TaskGroupStatus extends \Google\Collection
{
  protected $collection_key = 'instances';
  /**
   * Count of task in each state in the TaskGroup. The map key is task state
   * name.
   *
   * @var string[]
   */
  public $counts;
  protected $instancesType = InstanceStatus::class;
  protected $instancesDataType = 'array';

  /**
   * Count of task in each state in the TaskGroup. The map key is task state
   * name.
   *
   * @param string[] $counts
   */
  public function setCounts($counts)
  {
    $this->counts = $counts;
  }
  /**
   * @return string[]
   */
  public function getCounts()
  {
    return $this->counts;
  }
  /**
   * Status of instances allocated for the TaskGroup.
   *
   * @param InstanceStatus[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return InstanceStatus[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskGroupStatus::class, 'Google_Service_Batch_TaskGroupStatus');
