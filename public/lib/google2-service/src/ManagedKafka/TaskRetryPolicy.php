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

namespace Google\Service\ManagedKafka;

class TaskRetryPolicy extends \Google\Model
{
  /**
   * Optional. The maximum amount of time to wait before retrying a failed task.
   * This sets an upper bound for the backoff delay.
   *
   * @var string
   */
  public $maximumBackoff;
  /**
   * Optional. The minimum amount of time to wait before retrying a failed task.
   * This sets a lower bound for the backoff delay.
   *
   * @var string
   */
  public $minimumBackoff;
  /**
   * Optional. If true, task retry is disabled.
   *
   * @var bool
   */
  public $taskRetryDisabled;

  /**
   * Optional. The maximum amount of time to wait before retrying a failed task.
   * This sets an upper bound for the backoff delay.
   *
   * @param string $maximumBackoff
   */
  public function setMaximumBackoff($maximumBackoff)
  {
    $this->maximumBackoff = $maximumBackoff;
  }
  /**
   * @return string
   */
  public function getMaximumBackoff()
  {
    return $this->maximumBackoff;
  }
  /**
   * Optional. The minimum amount of time to wait before retrying a failed task.
   * This sets a lower bound for the backoff delay.
   *
   * @param string $minimumBackoff
   */
  public function setMinimumBackoff($minimumBackoff)
  {
    $this->minimumBackoff = $minimumBackoff;
  }
  /**
   * @return string
   */
  public function getMinimumBackoff()
  {
    return $this->minimumBackoff;
  }
  /**
   * Optional. If true, task retry is disabled.
   *
   * @param bool $taskRetryDisabled
   */
  public function setTaskRetryDisabled($taskRetryDisabled)
  {
    $this->taskRetryDisabled = $taskRetryDisabled;
  }
  /**
   * @return bool
   */
  public function getTaskRetryDisabled()
  {
    return $this->taskRetryDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskRetryPolicy::class, 'Google_Service_ManagedKafka_TaskRetryPolicy');
