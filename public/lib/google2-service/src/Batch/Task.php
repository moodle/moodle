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

class Task extends \Google\Model
{
  /**
   * Task name. The name is generated from the parent TaskGroup name and 'id'
   * field. For example: "projects/123456/locations/us-
   * west1/jobs/job01/taskGroups/group01/tasks/task01".
   *
   * @var string
   */
  public $name;
  protected $statusType = TaskStatus::class;
  protected $statusDataType = '';

  /**
   * Task name. The name is generated from the parent TaskGroup name and 'id'
   * field. For example: "projects/123456/locations/us-
   * west1/jobs/job01/taskGroups/group01/tasks/task01".
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Task Status.
   *
   * @param TaskStatus $status
   */
  public function setStatus(TaskStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return TaskStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Task::class, 'Google_Service_Batch_Task');
