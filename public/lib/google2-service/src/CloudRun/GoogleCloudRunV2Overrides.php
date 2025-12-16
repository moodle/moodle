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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2Overrides extends \Google\Collection
{
  protected $collection_key = 'containerOverrides';
  protected $containerOverridesType = GoogleCloudRunV2ContainerOverride::class;
  protected $containerOverridesDataType = 'array';
  /**
   * Optional. The desired number of tasks the execution should run. Will
   * replace existing task_count value.
   *
   * @var int
   */
  public $taskCount;
  /**
   * Duration in seconds the task may be active before the system will actively
   * try to mark it failed and kill associated containers. Will replace existing
   * timeout_seconds value.
   *
   * @var string
   */
  public $timeout;

  /**
   * Per container override specification.
   *
   * @param GoogleCloudRunV2ContainerOverride[] $containerOverrides
   */
  public function setContainerOverrides($containerOverrides)
  {
    $this->containerOverrides = $containerOverrides;
  }
  /**
   * @return GoogleCloudRunV2ContainerOverride[]
   */
  public function getContainerOverrides()
  {
    return $this->containerOverrides;
  }
  /**
   * Optional. The desired number of tasks the execution should run. Will
   * replace existing task_count value.
   *
   * @param int $taskCount
   */
  public function setTaskCount($taskCount)
  {
    $this->taskCount = $taskCount;
  }
  /**
   * @return int
   */
  public function getTaskCount()
  {
    return $this->taskCount;
  }
  /**
   * Duration in seconds the task may be active before the system will actively
   * try to mark it failed and kill associated containers. Will replace existing
   * timeout_seconds value.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Overrides::class, 'Google_Service_CloudRun_GoogleCloudRunV2Overrides');
