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

namespace Google\Service\SaaSServiceManagement;

class ReplicationStats extends \Google\Collection
{
  protected $collection_key = 'retryCount';
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  /**
   * The resources that are failed replication.
   *
   * @var string[]
   */
  public $failedResources;
  /**
   * The resources that are finished replication.
   *
   * @var string[]
   */
  public $finishedResources;
  /**
   * The resources that are pending replication.
   *
   * @var string[]
   */
  public $pendingResources;
  /**
   * The number of retries for the failed resources.
   *
   * @var int[]
   */
  public $retryCount;

  /**
   * The errors that occurred during replication, one error for each failed
   * resource.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The resources that are failed replication.
   *
   * @param string[] $failedResources
   */
  public function setFailedResources($failedResources)
  {
    $this->failedResources = $failedResources;
  }
  /**
   * @return string[]
   */
  public function getFailedResources()
  {
    return $this->failedResources;
  }
  /**
   * The resources that are finished replication.
   *
   * @param string[] $finishedResources
   */
  public function setFinishedResources($finishedResources)
  {
    $this->finishedResources = $finishedResources;
  }
  /**
   * @return string[]
   */
  public function getFinishedResources()
  {
    return $this->finishedResources;
  }
  /**
   * The resources that are pending replication.
   *
   * @param string[] $pendingResources
   */
  public function setPendingResources($pendingResources)
  {
    $this->pendingResources = $pendingResources;
  }
  /**
   * @return string[]
   */
  public function getPendingResources()
  {
    return $this->pendingResources;
  }
  /**
   * The number of retries for the failed resources.
   *
   * @param int[] $retryCount
   */
  public function setRetryCount($retryCount)
  {
    $this->retryCount = $retryCount;
  }
  /**
   * @return int[]
   */
  public function getRetryCount()
  {
    return $this->retryCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicationStats::class, 'Google_Service_SaaSServiceManagement_ReplicationStats');
