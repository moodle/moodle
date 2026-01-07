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

namespace Google\Service\Spanner;

class CreateInstanceRequest extends \Google\Model
{
  protected $instanceType = Instance::class;
  protected $instanceDataType = '';
  /**
   * Required. The ID of the instance to create. Valid identifiers are of the
   * form `a-z*[a-z0-9]` and must be between 2 and 64 characters in length.
   *
   * @var string
   */
  public $instanceId;

  /**
   * Required. The instance to create. The name may be omitted, but if specified
   * must be `/instances/`.
   *
   * @param Instance $instance
   */
  public function setInstance(Instance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return Instance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. The ID of the instance to create. Valid identifiers are of the
   * form `a-z*[a-z0-9]` and must be between 2 and 64 characters in length.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateInstanceRequest::class, 'Google_Service_Spanner_CreateInstanceRequest');
