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

class UpdateDatabaseMetadata extends \Google\Model
{
  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is best-effort).
   *
   * @var string
   */
  public $cancelTime;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  protected $requestType = UpdateDatabaseRequest::class;
  protected $requestDataType = '';

  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is best-effort).
   *
   * @param string $cancelTime
   */
  public function setCancelTime($cancelTime)
  {
    $this->cancelTime = $cancelTime;
  }
  /**
   * @return string
   */
  public function getCancelTime()
  {
    return $this->cancelTime;
  }
  /**
   * The progress of the UpdateDatabase operation.
   *
   * @param OperationProgress $progress
   */
  public function setProgress(OperationProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return OperationProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * The request for UpdateDatabase.
   *
   * @param UpdateDatabaseRequest $request
   */
  public function setRequest(UpdateDatabaseRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return UpdateDatabaseRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateDatabaseMetadata::class, 'Google_Service_Spanner_UpdateDatabaseMetadata');
