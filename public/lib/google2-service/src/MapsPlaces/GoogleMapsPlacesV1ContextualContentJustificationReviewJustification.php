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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1ContextualContentJustificationReviewJustification extends \Google\Model
{
  protected $highlightedTextType = GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText::class;
  protected $highlightedTextDataType = '';
  protected $reviewType = GoogleMapsPlacesV1Review::class;
  protected $reviewDataType = '';

  /**
   * @param GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText $highlightedText
   */
  public function setHighlightedText(GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText $highlightedText)
  {
    $this->highlightedText = $highlightedText;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContentJustificationReviewJustificationHighlightedText
   */
  public function getHighlightedText()
  {
    return $this->highlightedText;
  }
  /**
   * The review that the highlighted text is generated from.
   *
   * @param GoogleMapsPlacesV1Review $review
   */
  public function setReview(GoogleMapsPlacesV1Review $review)
  {
    $this->review = $review;
  }
  /**
   * @return GoogleMapsPlacesV1Review
   */
  public function getReview()
  {
    return $this->review;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1ContextualContentJustificationReviewJustification::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContextualContentJustificationReviewJustification');
