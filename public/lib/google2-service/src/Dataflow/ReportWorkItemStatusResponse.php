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

class ReportWorkItemStatusResponse extends \Google\Collection
{
  protected $collection_key = 'workItemServiceStates';
  /**
   * Untranslated bag-of-bytes WorkProgressUpdateResponse for UnifiedWorker.
   *
   * @var array[]
   */
  public $unifiedWorkerResponse;
  protected $workItemServiceStatesType = WorkItemServiceState::class;
  protected $workItemServiceStatesDataType = 'array';

  /**
   * Untranslated bag-of-bytes WorkProgressUpdateResponse for UnifiedWorker.
   *
   * @param array[] $unifiedWorkerResponse
   */
  public function setUnifiedWorkerResponse($unifiedWorkerResponse)
  {
    $this->unifiedWorkerResponse = $unifiedWorkerResponse;
  }
  /**
   * @return array[]
   */
  public function getUnifiedWorkerResponse()
  {
    return $this->unifiedWorkerResponse;
  }
  /**
   * A set of messages indicating the service-side state for each WorkItem whose
   * status was reported, in the same order as the WorkItemStatus messages in
   * the ReportWorkItemStatusRequest which resulting in this response.
   *
   * @param WorkItemServiceState[] $workItemServiceStates
   */
  public function setWorkItemServiceStates($workItemServiceStates)
  {
    $this->workItemServiceStates = $workItemServiceStates;
  }
  /**
   * @return WorkItemServiceState[]
   */
  public function getWorkItemServiceStates()
  {
    return $this->workItemServiceStates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportWorkItemStatusResponse::class, 'Google_Service_Dataflow_ReportWorkItemStatusResponse');
