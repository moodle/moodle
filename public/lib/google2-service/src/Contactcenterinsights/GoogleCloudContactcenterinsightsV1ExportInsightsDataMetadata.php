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

class GoogleCloudContactcenterinsightsV1ExportInsightsDataMetadata extends \Google\Collection
{
  protected $collection_key = 'partialErrors';
  /**
   * The number of conversations that were exported successfully.
   *
   * @var int
   */
  public $completedExportCount;
  /**
   * Output only. The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time the operation finished running.
   *
   * @var string
   */
  public $endTime;
  /**
   * The number of conversations that failed to be exported.
   *
   * @var int
   */
  public $failedExportCount;
  protected $partialErrorsType = GoogleRpcStatus::class;
  protected $partialErrorsDataType = 'array';
  protected $requestType = GoogleCloudContactcenterinsightsV1ExportInsightsDataRequest::class;
  protected $requestDataType = '';

  /**
   * The number of conversations that were exported successfully.
   *
   * @param int $completedExportCount
   */
  public function setCompletedExportCount($completedExportCount)
  {
    $this->completedExportCount = $completedExportCount;
  }
  /**
   * @return int
   */
  public function getCompletedExportCount()
  {
    return $this->completedExportCount;
  }
  /**
   * Output only. The time the operation was created.
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
   * Output only. The time the operation finished running.
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
   * The number of conversations that failed to be exported.
   *
   * @param int $failedExportCount
   */
  public function setFailedExportCount($failedExportCount)
  {
    $this->failedExportCount = $failedExportCount;
  }
  /**
   * @return int
   */
  public function getFailedExportCount()
  {
    return $this->failedExportCount;
  }
  /**
   * Partial errors during export operation that might cause the operation
   * output to be incomplete.
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
   * The original request for export.
   *
   * @param GoogleCloudContactcenterinsightsV1ExportInsightsDataRequest $request
   */
  public function setRequest(GoogleCloudContactcenterinsightsV1ExportInsightsDataRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1ExportInsightsDataRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1ExportInsightsDataMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1ExportInsightsDataMetadata');
