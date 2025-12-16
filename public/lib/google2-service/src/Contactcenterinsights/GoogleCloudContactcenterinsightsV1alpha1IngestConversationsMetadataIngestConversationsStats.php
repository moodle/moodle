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

class GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats extends \Google\Model
{
  /**
   * Output only. The number of objects skipped because another conversation
   * with the same transcript uri had already been ingested.
   *
   * @var int
   */
  public $duplicatesSkippedCount;
  /**
   * Output only. The number of objects which were unable to be ingested due to
   * errors. The errors are populated in the partial_errors field.
   *
   * @var int
   */
  public $failedIngestCount;
  /**
   * Output only. The number of objects processed during the ingest operation.
   *
   * @var int
   */
  public $processedObjectCount;
  /**
   * Output only. The number of new conversations added during this ingest
   * operation.
   *
   * @var int
   */
  public $successfulIngestCount;

  /**
   * Output only. The number of objects skipped because another conversation
   * with the same transcript uri had already been ingested.
   *
   * @param int $duplicatesSkippedCount
   */
  public function setDuplicatesSkippedCount($duplicatesSkippedCount)
  {
    $this->duplicatesSkippedCount = $duplicatesSkippedCount;
  }
  /**
   * @return int
   */
  public function getDuplicatesSkippedCount()
  {
    return $this->duplicatesSkippedCount;
  }
  /**
   * Output only. The number of objects which were unable to be ingested due to
   * errors. The errors are populated in the partial_errors field.
   *
   * @param int $failedIngestCount
   */
  public function setFailedIngestCount($failedIngestCount)
  {
    $this->failedIngestCount = $failedIngestCount;
  }
  /**
   * @return int
   */
  public function getFailedIngestCount()
  {
    return $this->failedIngestCount;
  }
  /**
   * Output only. The number of objects processed during the ingest operation.
   *
   * @param int $processedObjectCount
   */
  public function setProcessedObjectCount($processedObjectCount)
  {
    $this->processedObjectCount = $processedObjectCount;
  }
  /**
   * @return int
   */
  public function getProcessedObjectCount()
  {
    return $this->processedObjectCount;
  }
  /**
   * Output only. The number of new conversations added during this ingest
   * operation.
   *
   * @param int $successfulIngestCount
   */
  public function setSuccessfulIngestCount($successfulIngestCount)
  {
    $this->successfulIngestCount = $successfulIngestCount;
  }
  /**
   * @return int
   */
  public function getSuccessfulIngestCount()
  {
    return $this->successfulIngestCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1IngestConversationsMetadataIngestConversationsStats');
