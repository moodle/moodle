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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadata extends \Google\Collection
{
  protected $collection_key = 'contentValidationStats';
  protected $contentValidationStatsType = GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats::class;
  protected $contentValidationStatsDataType = 'array';
  /**
   * The ingested data size in bytes.
   *
   * @var string
   */
  public $dataBytesCount;

  /**
   * The validation stats of the content (per file) to be inserted or updated on
   * the Matching Engine Index resource. Populated if contentsDeltaUri is
   * provided as part of Index.metadata. Please note that, currently for those
   * files that are broken or has unsupported file format, we will not have the
   * stats for those files.
   *
   * @param GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats[] $contentValidationStats
   */
  public function setContentValidationStats($contentValidationStats)
  {
    $this->contentValidationStats = $contentValidationStats;
  }
  /**
   * @return GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadataContentValidationStats[]
   */
  public function getContentValidationStats()
  {
    return $this->contentValidationStats;
  }
  /**
   * The ingested data size in bytes.
   *
   * @param string $dataBytesCount
   */
  public function setDataBytesCount($dataBytesCount)
  {
    $this->dataBytesCount = $dataBytesCount;
  }
  /**
   * @return string
   */
  public function getDataBytesCount()
  {
    return $this->dataBytesCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NearestNeighborSearchOperationMetadata');
