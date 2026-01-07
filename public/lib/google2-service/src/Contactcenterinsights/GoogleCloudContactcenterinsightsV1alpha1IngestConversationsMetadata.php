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

class GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadata extends \Google\Collection
{
  protected $collection_key = 'sampledConversations';
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
  protected $ingestConversationsStatsType = GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats::class;
  protected $ingestConversationsStatsDataType = '';
  protected $partialErrorsType = GoogleRpcStatus::class;
  protected $partialErrorsDataType = 'array';
  protected $requestType = GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest::class;
  protected $requestDataType = '';
  /**
   * Output only. Stores the conversation resources produced by ingest sampling
   * operations.
   *
   * @var string[]
   */
  public $sampledConversations;

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
   * Output only. Statistics for IngestConversations operation.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats $ingestConversationsStats
   */
  public function setIngestConversationsStats(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats $ingestConversationsStats)
  {
    $this->ingestConversationsStats = $ingestConversationsStats;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats
   */
  public function getIngestConversationsStats()
  {
    return $this->ingestConversationsStats;
  }
  /**
   * Output only. Partial errors during ingest operation that might cause the
   * operation output to be incomplete.
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
   * Output only. The original request for ingest.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest $request
   */
  public function setRequest(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1IngestConversationsRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Output only. Stores the conversation resources produced by ingest sampling
   * operations.
   *
   * @param string[] $sampledConversations
   */
  public function setSampledConversations($sampledConversations)
  {
    $this->sampledConversations = $sampledConversations;
  }
  /**
   * @return string[]
   */
  public function getSampledConversations()
  {
    return $this->sampledConversations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadata::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadata');
