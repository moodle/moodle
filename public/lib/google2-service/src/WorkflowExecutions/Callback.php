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

namespace Google\Service\WorkflowExecutions;

class Callback extends \Google\Collection
{
  protected $collection_key = 'availablePayloads';
  /**
   * Output only. The payloads received by the callback that have not been
   * processed by a waiting execution step.
   *
   * @var string[]
   */
  public $availablePayloads;
  /**
   * Output only. The method accepted by the callback. For example: GET, POST,
   * PUT.
   *
   * @var string
   */
  public $method;
  /**
   * Output only. The resource name of the callback. Format: projects/{project}/
   * locations/{location}/workflows/{workflow}/executions/{execution}/callback/{
   * callback}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of execution steps waiting on this callback.
   *
   * @var string
   */
  public $waiters;

  /**
   * Output only. The payloads received by the callback that have not been
   * processed by a waiting execution step.
   *
   * @param string[] $availablePayloads
   */
  public function setAvailablePayloads($availablePayloads)
  {
    $this->availablePayloads = $availablePayloads;
  }
  /**
   * @return string[]
   */
  public function getAvailablePayloads()
  {
    return $this->availablePayloads;
  }
  /**
   * Output only. The method accepted by the callback. For example: GET, POST,
   * PUT.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Output only. The resource name of the callback. Format: projects/{project}/
   * locations/{location}/workflows/{workflow}/executions/{execution}/callback/{
   * callback}
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
   * Output only. Number of execution steps waiting on this callback.
   *
   * @param string $waiters
   */
  public function setWaiters($waiters)
  {
    $this->waiters = $waiters;
  }
  /**
   * @return string
   */
  public function getWaiters()
  {
    return $this->waiters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Callback::class, 'Google_Service_WorkflowExecutions_Callback');
