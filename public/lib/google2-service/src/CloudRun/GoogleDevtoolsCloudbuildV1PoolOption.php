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

class GoogleDevtoolsCloudbuildV1PoolOption extends \Google\Model
{
  /**
   * The `WorkerPool` resource to execute the build on. You must have
   * `cloudbuild.workerpools.use` on the project hosting the WorkerPool. Format
   * projects/{project}/locations/{location}/workerPools/{workerPoolId}
   *
   * @var string
   */
  public $name;

  /**
   * The `WorkerPool` resource to execute the build on. You must have
   * `cloudbuild.workerpools.use` on the project hosting the WorkerPool. Format
   * projects/{project}/locations/{location}/workerPools/{workerPoolId}
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1PoolOption::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1PoolOption');
