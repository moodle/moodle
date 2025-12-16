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

class ReportWorkItemStatusRequest extends \Google\Collection
{
  protected $collection_key = 'workItemStatuses';
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
   * Optional. The project number of the project which owns the WorkItem's job.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * Untranslated bag-of-bytes WorkProgressUpdateRequest from UnifiedWorker.
   *
   * @var array[]
   */
  public $unifiedWorkerRequest;
  protected $workItemStatusesType = WorkItemStatus::class;
  protected $workItemStatusesDataType = 'array';
  /**
   * The ID of the worker reporting the WorkItem status. If this does not match
   * the ID of the worker which the Dataflow service believes currently has the
   * lease on the WorkItem, the report will be dropped (with an error response).
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
   * Optional. The project number of the project which owns the WorkItem's job.
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
   * Untranslated bag-of-bytes WorkProgressUpdateRequest from UnifiedWorker.
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
   * The order is unimportant, except that the order of the WorkItemServiceState
   * messages in the ReportWorkItemStatusResponse corresponds to the order of
   * WorkItemStatus messages here.
   *
   * @param WorkItemStatus[] $workItemStatuses
   */
  public function setWorkItemStatuses($workItemStatuses)
  {
    $this->workItemStatuses = $workItemStatuses;
  }
  /**
   * @return WorkItemStatus[]
   */
  public function getWorkItemStatuses()
  {
    return $this->workItemStatuses;
  }
  /**
   * The ID of the worker reporting the WorkItem status. If this does not match
   * the ID of the worker which the Dataflow service believes currently has the
   * lease on the WorkItem, the report will be dropped (with an error response).
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
class_alias(ReportWorkItemStatusRequest::class, 'Google_Service_Dataflow_ReportWorkItemStatusRequest');
