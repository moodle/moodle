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

class GoogleCloudDataplexV1TaskInfrastructureSpecBatchComputeResources extends \Google\Model
{
  /**
   * Optional. Total number of job executors. Executor Count should be between 2
   * and 100. Default=2
   *
   * @var int
   */
  public $executorsCount;
  /**
   * Optional. Max configurable executors. If max_executors_count >
   * executors_count, then auto-scaling is enabled. Max Executor Count should be
   * between 2 and 1000. Default=1000
   *
   * @var int
   */
  public $maxExecutorsCount;

  /**
   * Optional. Total number of job executors. Executor Count should be between 2
   * and 100. Default=2
   *
   * @param int $executorsCount
   */
  public function setExecutorsCount($executorsCount)
  {
    $this->executorsCount = $executorsCount;
  }
  /**
   * @return int
   */
  public function getExecutorsCount()
  {
    return $this->executorsCount;
  }
  /**
   * Optional. Max configurable executors. If max_executors_count >
   * executors_count, then auto-scaling is enabled. Max Executor Count should be
   * between 2 and 1000. Default=1000
   *
   * @param int $maxExecutorsCount
   */
  public function setMaxExecutorsCount($maxExecutorsCount)
  {
    $this->maxExecutorsCount = $maxExecutorsCount;
  }
  /**
   * @return int
   */
  public function getMaxExecutorsCount()
  {
    return $this->maxExecutorsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskInfrastructureSpecBatchComputeResources::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskInfrastructureSpecBatchComputeResources');
