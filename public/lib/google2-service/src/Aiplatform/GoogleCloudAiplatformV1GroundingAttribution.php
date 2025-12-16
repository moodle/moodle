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

class GoogleCloudAiplatformV1GroundingAttribution extends \Google\Model
{
  /**
   * @var float
   */
  public $confidenceScore;
  protected $segmentType = GoogleCloudAiplatformV1Segment::class;
  protected $segmentDataType = '';
  protected $webType = GoogleCloudAiplatformV1GroundingAttributionWeb::class;
  protected $webDataType = '';

  /**
   * @param float
   */
  public function setConfidenceScore($confidenceScore)
  {
    $this->confidenceScore = $confidenceScore;
  }
  /**
   * @return float
   */
  public function getConfidenceScore()
  {
    return $this->confidenceScore;
  }
  /**
   * @param GoogleCloudAiplatformV1Segment
   */
  public function setSegment(GoogleCloudAiplatformV1Segment $segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return GoogleCloudAiplatformV1Segment
   */
  public function getSegment()
  {
    return $this->segment;
  }
  /**
   * @param GoogleCloudAiplatformV1GroundingAttributionWeb
   */
  public function setWeb(GoogleCloudAiplatformV1GroundingAttributionWeb $web)
  {
    $this->web = $web;
  }
  /**
   * @return GoogleCloudAiplatformV1GroundingAttributionWeb
   */
  public function getWeb()
  {
    return $this->web;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingAttribution::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingAttribution');
