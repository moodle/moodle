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

class EmbeddedTask extends \Google\Model
{
  /**
   * User annotations. See https://google.aip.dev/128#annotations
   *
   * @var string[]
   */
  public $annotations;
  protected $taskSpecType = TaskSpec::class;
  protected $taskSpecDataType = '';

  /**
   * User annotations. See https://google.aip.dev/128#annotations
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Spec to instantiate this TaskRun.
   *
   * @param TaskSpec $taskSpec
   */
  public function setTaskSpec(TaskSpec $taskSpec)
  {
    $this->taskSpec = $taskSpec;
  }
  /**
   * @return TaskSpec
   */
  public function getTaskSpec()
  {
    return $this->taskSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EmbeddedTask::class, 'Google_Service_CloudBuild_EmbeddedTask');
