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

class GoogleCloudContactcenterinsightsV1mainListFeedbackLabelsResponse extends \Google\Collection
{
  protected $collection_key = 'feedbackLabels';
  protected $feedbackLabelsType = GoogleCloudContactcenterinsightsV1mainFeedbackLabel::class;
  protected $feedbackLabelsDataType = 'array';
  /**
   * The next page token.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The feedback labels that match the request.
   *
   * @param GoogleCloudContactcenterinsightsV1mainFeedbackLabel[] $feedbackLabels
   */
  public function setFeedbackLabels($feedbackLabels)
  {
    $this->feedbackLabels = $feedbackLabels;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainFeedbackLabel[]
   */
  public function getFeedbackLabels()
  {
    return $this->feedbackLabels;
  }
  /**
   * The next page token.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainListFeedbackLabelsResponse::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainListFeedbackLabelsResponse');
