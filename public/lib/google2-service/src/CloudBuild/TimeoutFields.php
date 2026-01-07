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

namespace Google\Service\CloudBuild;

class TimeoutFields extends \Google\Model
{
  /**
   * Finally sets the maximum allowed duration of this pipeline's finally
   *
   * @var string
   */
  public $finally;
  /**
   * Pipeline sets the maximum allowed duration for execution of the entire
   * pipeline. The sum of individual timeouts for tasks and finally must not
   * exceed this value.
   *
   * @var string
   */
  public $pipeline;
  /**
   * Tasks sets the maximum allowed duration of this pipeline's tasks
   *
   * @var string
   */
  public $tasks;

  /**
   * Finally sets the maximum allowed duration of this pipeline's finally
   *
   * @param string $finally
   */
  public function setFinally($finally)
  {
    $this->finally = $finally;
  }
  /**
   * @return string
   */
  public function getFinally()
  {
    return $this->finally;
  }
  /**
   * Pipeline sets the maximum allowed duration for execution of the entire
   * pipeline. The sum of individual timeouts for tasks and finally must not
   * exceed this value.
   *
   * @param string $pipeline
   */
  public function setPipeline($pipeline)
  {
    $this->pipeline = $pipeline;
  }
  /**
   * @return string
   */
  public function getPipeline()
  {
    return $this->pipeline;
  }
  /**
   * Tasks sets the maximum allowed duration of this pipeline's tasks
   *
   * @param string $tasks
   */
  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }
  /**
   * @return string
   */
  public function getTasks()
  {
    return $this->tasks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeoutFields::class, 'Google_Service_CloudBuild_TimeoutFields');
