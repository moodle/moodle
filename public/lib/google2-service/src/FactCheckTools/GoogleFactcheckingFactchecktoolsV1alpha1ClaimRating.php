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

namespace Google\Service\FactCheckTools;

class GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating extends \Google\Model
{
  /**
   * For numeric ratings, the best value possible in the scale from worst to
   * best. Corresponds to `ClaimReview.reviewRating.bestRating`.
   *
   * @var int
   */
  public $bestRating;
  /**
   * Corresponds to `ClaimReview.reviewRating.image`.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * Corresponds to `ClaimReview.reviewRating.ratingExplanation`.
   *
   * @var string
   */
  public $ratingExplanation;
  /**
   * A numeric rating of this claim, in the range worstRating — bestRating
   * inclusive. Corresponds to `ClaimReview.reviewRating.ratingValue`.
   *
   * @var int
   */
  public $ratingValue;
  /**
   * The truthfulness rating as a human-readible short word or phrase.
   * Corresponds to `ClaimReview.reviewRating.alternateName`.
   *
   * @var string
   */
  public $textualRating;
  /**
   * For numeric ratings, the worst value possible in the scale from worst to
   * best. Corresponds to `ClaimReview.reviewRating.worstRating`.
   *
   * @var int
   */
  public $worstRating;

  /**
   * For numeric ratings, the best value possible in the scale from worst to
   * best. Corresponds to `ClaimReview.reviewRating.bestRating`.
   *
   * @param int $bestRating
   */
  public function setBestRating($bestRating)
  {
    $this->bestRating = $bestRating;
  }
  /**
   * @return int
   */
  public function getBestRating()
  {
    return $this->bestRating;
  }
  /**
   * Corresponds to `ClaimReview.reviewRating.image`.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * Corresponds to `ClaimReview.reviewRating.ratingExplanation`.
   *
   * @param string $ratingExplanation
   */
  public function setRatingExplanation($ratingExplanation)
  {
    $this->ratingExplanation = $ratingExplanation;
  }
  /**
   * @return string
   */
  public function getRatingExplanation()
  {
    return $this->ratingExplanation;
  }
  /**
   * A numeric rating of this claim, in the range worstRating — bestRating
   * inclusive. Corresponds to `ClaimReview.reviewRating.ratingValue`.
   *
   * @param int $ratingValue
   */
  public function setRatingValue($ratingValue)
  {
    $this->ratingValue = $ratingValue;
  }
  /**
   * @return int
   */
  public function getRatingValue()
  {
    return $this->ratingValue;
  }
  /**
   * The truthfulness rating as a human-readible short word or phrase.
   * Corresponds to `ClaimReview.reviewRating.alternateName`.
   *
   * @param string $textualRating
   */
  public function setTextualRating($textualRating)
  {
    $this->textualRating = $textualRating;
  }
  /**
   * @return string
   */
  public function getTextualRating()
  {
    return $this->textualRating;
  }
  /**
   * For numeric ratings, the worst value possible in the scale from worst to
   * best. Corresponds to `ClaimReview.reviewRating.worstRating`.
   *
   * @param int $worstRating
   */
  public function setWorstRating($worstRating)
  {
    $this->worstRating = $worstRating;
  }
  /**
   * @return int
   */
  public function getWorstRating()
  {
    return $this->worstRating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating::class, 'Google_Service_FactCheckTools_GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating');
