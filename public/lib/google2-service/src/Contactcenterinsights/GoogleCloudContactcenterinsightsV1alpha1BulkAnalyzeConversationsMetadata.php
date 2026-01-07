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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsMetadata extends \Google\Collection
{
  protected $collection_key = 'partialErrors';
  /**
   * The number of requested analyses that have completed successfully so far.
   *
   * @var int
   */
  public $completedAnalysesCount;
  /**
   * The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  /**
   * The number of requested analyses that have failed so far.
   *
   * @var int
   */
  public $failedAnalysesCount;
  protected $partialErrorsType = GoogleRpcStatus::class;
  protected $partialErrorsDataType = 'array';
  protected $requestType = GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest::class;
  protected $requestDataType = '';
  /**
   * Total number of analyses requested. Computed by the number of conversations
   * returned by `filter` multiplied by `analysis_percentage` in the request.
   *
   * @var int
   */
  public $totalRequestedAnalysesCount;

  /**
   * The number of requested analyses that have completed successfully so far.
   *
   * @param int $completedAnalysesCount
   */
  public function setCompletedAnalysesCount($completedAnalysesCount)
  {
    $this->completedAnalysesCount = $completedAnalysesCount;
  }
  /**
   * @return int
   */
  public function getCompletedAnalysesCount()
  {
    return $this->completedAnalysesCount;
  }
  /**
   * The time the operation was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time the operation finished running.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The number of requested analyses that have failed so far.
   *
   * @param int $failedAnalysesCount
   */
  public function setFailedAnalysesCount($failedAnalysesCount)
  {
    $this->failedAnalysesCount = $failedAnalysesCount;
  }
  /**
   * @return int
   */
  public function getFailedAnalysesCount()
  {
    return $this->failedAnalysesCount;
  }
  /**
   * Output only. Partial errors during bulk analyze operation that might cause
   * the operation output to be incomplete.
   *
   * @param GoogleRpcStatus[] $partialErrors
   */
  public function setPartialErrors($partialErrors)
  {
    $this->partialErrors = $partialErrors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getPartialErrors()
  {
    return $this->partialErrors;
  }
  /**
   * The original request for bulk analyze.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest $request
   */
  public function setRequest(GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Total number of analyses requested. Computed by the number of conversations
   * returned by `filter` multiplied by `analysis_percentage` in the request.
   *
   * @param int $totalRequestedAnalysesCount
   */
  public function setTotalRequestedAnalysesCount($totalRequestedAnalysesCount)
  {
    $this->totalRequestedAnalysesCount = $totalRequestedAnalysesCount;
  }
  /**
   * @return int
   */
  public function getTotalRequestedAnalysesCount()
  {
    return $this->totalRequestedAnalysesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1BulkAnalyzeConversationsMetadata');
