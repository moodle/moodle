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

namespace Google\Service\Dataflow;

class Step extends \Google\Model
{
  /**
   * The kind of step in the Cloud Dataflow job.
   *
   * @var string
   */
  public $kind;
  /**
   * The name that identifies the step. This must be unique for each step with
   * respect to all other steps in the Cloud Dataflow job.
   *
   * @var string
   */
  public $name;
  /**
   * Named properties associated with the step. Each kind of predefined step has
   * its own required set of properties. Must be provided on Create. Only
   * retrieved with JOB_VIEW_ALL.
   *
   * @var array[]
   */
  public $properties;

  /**
   * The kind of step in the Cloud Dataflow job.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The name that identifies the step. This must be unique for each step with
   * respect to all other steps in the Cloud Dataflow job.
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
   * Named properties associated with the step. Each kind of predefined step has
   * its own required set of properties. Must be provided on Create. Only
   * retrieved with JOB_VIEW_ALL.
   *
   * @param array[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return array[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Step::class, 'Google_Service_Dataflow_Step');
