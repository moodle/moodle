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

class GoogleCloudContactcenterinsightsV1mainSampleConversationsMetadataSampleConversationsStats extends \Google\Model
{
  /**
   * Output only. The number of objects which were unable to be sampled due to
   * errors. The errors are populated in the partial_errors field.
   *
   * @var int
   */
  public $failedSampleCount;
  /**
   * Output only. The number of new conversations added during this sample
   * operation.
   *
   * @var int
   */
  public $successfulSampleCount;

  /**
   * Output only. The number of objects which were unable to be sampled due to
   * errors. The errors are populated in the partial_errors field.
   *
   * @param int $failedSampleCount
   */
  public function setFailedSampleCount($failedSampleCount)
  {
    $this->failedSampleCount = $failedSampleCount;
  }
  /**
   * @return int
   */
  public function getFailedSampleCount()
  {
    return $this->failedSampleCount;
  }
  /**
   * Output only. The number of new conversations added during this sample
   * operation.
   *
   * @param int $successfulSampleCount
   */
  public function setSuccessfulSampleCount($successfulSampleCount)
  {
    $this->successfulSampleCount = $successfulSampleCount;
  }
  /**
   * @return int
   */
  public function getSuccessfulSampleCount()
  {
    return $this->successfulSampleCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainSampleConversationsMetadataSampleConversationsStats::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainSampleConversationsMetadataSampleConversationsStats');
