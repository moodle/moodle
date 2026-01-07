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

class GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet extends \Google\Model
{
  /**
   * A link to show the review on Google Maps.
   *
   * @var string
   */
  public $googleMapsUri;
  /**
   * The ID of the review that is being referenced.
   *
   * @var string
   */
  public $reviewId;
  /**
   * The title of the review.
   *
   * @var string
   */
  public $title;

  /**
   * A link to show the review on Google Maps.
   *
   * @param string $googleMapsUri
   */
  public function setGoogleMapsUri($googleMapsUri)
  {
    $this->googleMapsUri = $googleMapsUri;
  }
  /**
   * @return string
   */
  public function getGoogleMapsUri()
  {
    return $this->googleMapsUri;
  }
  /**
   * The ID of the review that is being referenced.
   *
   * @param string $reviewId
   */
  public function setReviewId($reviewId)
  {
    $this->reviewId = $reviewId;
  }
  /**
   * @return string
   */
  public function getReviewId()
  {
    return $this->reviewId;
  }
  /**
   * The title of the review.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunkMapsPlaceAnswerSourcesReviewSnippet');
