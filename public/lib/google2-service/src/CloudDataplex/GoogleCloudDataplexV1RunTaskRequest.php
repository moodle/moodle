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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1RunTaskRequest extends \Google\Model
{
  /**
   * Optional. Execution spec arguments. If the map is left empty, the task will
   * run with existing execution spec args from task definition. If the map
   * contains an entry with a new key, the same will be added to existing set of
   * args. If the map contains an entry with an existing arg key in task
   * definition, the task will run with new arg value for that entry. Clearing
   * an existing arg will require arg value to be explicitly set to a hyphen
   * "-". The arg value cannot be empty.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. User-defined labels for the task. If the map is left empty, the
   * task will run with existing labels from task definition. If the map
   * contains an entry with a new key, the same will be added to existing set of
   * labels. If the map contains an entry with an existing label key in task
   * definition, the task will run with new label value for that entry. Clearing
   * an existing label will require label value to be explicitly set to a hyphen
   * "-". The label value cannot be empty.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Optional. Execution spec arguments. If the map is left empty, the task will
   * run with existing execution spec args from task definition. If the map
   * contains an entry with a new key, the same will be added to existing set of
   * args. If the map contains an entry with an existing arg key in task
   * definition, the task will run with new arg value for that entry. Clearing
   * an existing arg will require arg value to be explicitly set to a hyphen
   * "-". The arg value cannot be empty.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. User-defined labels for the task. If the map is left empty, the
   * task will run with existing labels from task definition. If the map
   * contains an entry with a new key, the same will be added to existing set of
   * labels. If the map contains an entry with an existing label key in task
   * definition, the task will run with new label value for that entry. Clearing
   * an existing label will require label value to be explicitly set to a hyphen
   * "-". The label value cannot be empty.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1RunTaskRequest::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1RunTaskRequest');
