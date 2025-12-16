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

class LeaseWorkItemRequest extends \Google\Collection
{
  protected $collection_key = 'workerCapabilities';
  /**
   * The current timestamp at the worker.
   *
   * @var string
   */
  public $currentWorkerTime;
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the WorkItem's job.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The project number of the project this worker belongs to.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * The initial lease period.
   *
   * @var string
   */
  public $requestedLeaseDuration;
  /**
   * Untranslated bag-of-bytes WorkRequest from UnifiedWorker.
   *
   * @var array[]
   */
  public $unifiedWorkerRequest;
  /**
   * Filter for WorkItem type.
   *
   * @var string[]
   */
  public $workItemTypes;
  /**
   * Worker capabilities. WorkItems might be limited to workers with specific
   * capabilities.
   *
   * @var string[]
   */
  public $workerCapabilities;
  /**
   * Identifies the worker leasing work -- typically the ID of the virtual
   * machine running the worker.
   *
   * @var string
   */
  public $workerId;

  /**
   * The current timestamp at the worker.
   *
   * @param string $currentWorkerTime
   */
  public function setCurrentWorkerTime($currentWorkerTime)
  {
    $this->currentWorkerTime = $currentWorkerTime;
  }
  /**
   * @return string
   */
  public function getCurrentWorkerTime()
  {
    return $this->currentWorkerTime;
  }
  /**
   * The [regional endpoint]
   * (https://cloud.google.com/dataflow/docs/concepts/regional-endpoints) that
   * contains the WorkItem's job.
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
   * Optional. The project number of the project this worker belongs to.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * The initial lease period.
   *
   * @param string $requestedLeaseDuration
   */
  public function setRequestedLeaseDuration($requestedLeaseDuration)
  {
    $this->requestedLeaseDuration = $requestedLeaseDuration;
  }
  /**
   * @return string
   */
  public function getRequestedLeaseDuration()
  {
    return $this->requestedLeaseDuration;
  }
  /**
   * Untranslated bag-of-bytes WorkRequest from UnifiedWorker.
   *
   * @param array[] $unifiedWorkerRequest
   */
  public function setUnifiedWorkerRequest($unifiedWorkerRequest)
  {
    $this->unifiedWorkerRequest = $unifiedWorkerRequest;
  }
  /**
   * @return array[]
   */
  public function getUnifiedWorkerRequest()
  {
    return $this->unifiedWorkerRequest;
  }
  /**
   * Filter for WorkItem type.
   *
   * @param string[] $workItemTypes
   */
  public function setWorkItemTypes($workItemTypes)
  {
    $this->workItemTypes = $workItemTypes;
  }
  /**
   * @return string[]
   */
  public function getWorkItemTypes()
  {
    return $this->workItemTypes;
  }
  /**
   * Worker capabilities. WorkItems might be limited to workers with specific
   * capabilities.
   *
   * @param string[] $workerCapabilities
   */
  public function setWorkerCapabilities($workerCapabilities)
  {
    $this->workerCapabilities = $workerCapabilities;
  }
  /**
   * @return string[]
   */
  public function getWorkerCapabilities()
  {
    return $this->workerCapabilities;
  }
  /**
   * Identifies the worker leasing work -- typically the ID of the virtual
   * machine running the worker.
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
class_alias(LeaseWorkItemRequest::class, 'Google_Service_Dataflow_LeaseWorkItemRequest');
