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

class GetDebugConfigRequest extends \Google\Model
{
  /**
   * The internal component id for which debug configuration is requested.
   *
   * @var string
   */
  public $componentId;
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the job specified by job_id.
   *
   * @var string
   */
  public $location;
  /**
   * The worker id, i.e., VM hostname.
   *
   * @var string
   */
  public $workerId;

  /**
   * The internal component id for which debug configuration is requested.
   *
   * @param string $componentId
   */
  public function setComponentId($componentId)
  {
    $this->componentId = $componentId;
  }
  /**
   * @return string
   */
  public function getComponentId()
  {
    return $this->componentId;
  }
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the job specified by job_id.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The worker id, i.e., VM hostname.
   *
   * @param string $workerId
   */
  public function setWorkerId($workerId)
  {
    $this->workerId = $workerId;
  }
  /**
   * @return string
   */
  public function getWorkerId()
  {
    return $this->workerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetDebugConfigRequest::class, 'Google_Service_Dataflow_GetDebugConfigRequest');
