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

class GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup extends \Google\Collection
{
  protected $collection_key = 'claimAppearances';
  /**
   * A list of links to works in which this claim appears, aside from the one
   * specified in `claim_first_appearance`. Corresponds to
   * `ClaimReview.itemReviewed[@type=Claim].appearance.url`.
   *
   * @var string[]
   */
  public $claimAppearances;
  protected $claimAuthorType = GoogleFactcheckingFactchecktoolsV1alpha1ClaimAuthor::class;
  protected $claimAuthorDataType = '';
  /**
   * The date when the claim was made or entered public discourse. Corresponds
   * to `ClaimReview.itemReviewed.datePublished`.
   *
   * @var string
   */
  public $claimDate;
  /**
   * A link to a work in which this claim first appears. Corresponds to
   * `ClaimReview.itemReviewed[@type=Claim].firstAppearance.url`.
   *
   * @var string
   */
  public $claimFirstAppearance;
  /**
   * The location where this claim was made. Corresponds to
   * `ClaimReview.itemReviewed.name`.
   *
   * @var string
   */
  public $claimLocation;
  /**
   * A short summary of the claim being evaluated. Corresponds to
   * `ClaimReview.claimReviewed`.
   *
   * @var string
   */
  public $claimReviewed;
  protected $ratingType = GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating::class;
  protected $ratingDataType = '';
  /**
   * This field is optional, and will default to the page URL. We provide this
   * field to allow you the override the default value, but the only permitted
   * override is the page URL plus an optional anchor link ("page jump").
   * Corresponds to `ClaimReview.url`
   *
   * @var string
   */
  public $url;

  /**
   * A list of links to works in which this claim appears, aside from the one
   * specified in `claim_first_appearance`. Corresponds to
   * `ClaimReview.itemReviewed[@type=Claim].appearance.url`.
   *
   * @param string[] $claimAppearances
   */
  public function setClaimAppearances($claimAppearances)
  {
    $this->claimAppearances = $claimAppearances;
  }
  /**
   * @return string[]
   */
  public function getClaimAppearances()
  {
    return $this->claimAppearances;
  }
  /**
   * Info about the author of this claim.
   *
   * @param GoogleFactcheckingFactchecktoolsV1alpha1ClaimAuthor $claimAuthor
   */
  public function setClaimAuthor(GoogleFactcheckingFactchecktoolsV1alpha1ClaimAuthor $claimAuthor)
  {
    $this->claimAuthor = $claimAuthor;
  }
  /**
   * @return GoogleFactcheckingFactchecktoolsV1alpha1ClaimAuthor
   */
  public function getClaimAuthor()
  {
    return $this->claimAuthor;
  }
  /**
   * The date when the claim was made or entered public discourse. Corresponds
   * to `ClaimReview.itemReviewed.datePublished`.
   *
   * @param string $claimDate
   */
  public function setClaimDate($claimDate)
  {
    $this->claimDate = $claimDate;
  }
  /**
   * @return string
   */
  public function getClaimDate()
  {
    return $this->claimDate;
  }
  /**
   * A link to a work in which this claim first appears. Corresponds to
   * `ClaimReview.itemReviewed[@type=Claim].firstAppearance.url`.
   *
   * @param string $claimFirstAppearance
   */
  public function setClaimFirstAppearance($claimFirstAppearance)
  {
    $this->claimFirstAppearance = $claimFirstAppearance;
  }
  /**
   * @return string
   */
  public function getClaimFirstAppearance()
  {
    return $this->claimFirstAppearance;
  }
  /**
   * The location where this claim was made. Corresponds to
   * `ClaimReview.itemReviewed.name`.
   *
   * @param string $claimLocation
   */
  public function setClaimLocation($claimLocation)
  {
    $this->claimLocation = $claimLocation;
  }
  /**
   * @return string
   */
  public function getClaimLocation()
  {
    return $this->claimLocation;
  }
  /**
   * A short summary of the claim being evaluated. Corresponds to
   * `ClaimReview.claimReviewed`.
   *
   * @param string $claimReviewed
   */
  public function setClaimReviewed($claimReviewed)
  {
    $this->claimReviewed = $claimReviewed;
  }
  /**
   * @return string
   */
  public function getClaimReviewed()
  {
    return $this->claimReviewed;
  }
  /**
   * Info about the rating of this claim review.
   *
   * @param GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating $rating
   */
  public function setRating(GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating $rating)
  {
    $this->rating = $rating;
  }
  /**
   * @return GoogleFactcheckingFactchecktoolsV1alpha1ClaimRating
   */
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * This field is optional, and will default to the page URL. We provide this
   * field to allow you the override the default value, but the only permitted
   * override is the page URL plus an optional anchor link ("page jump").
   * Corresponds to `ClaimReview.url`
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup::class, 'Google_Service_FactCheckTools_GoogleFactcheckingFactchecktoolsV1alpha1ClaimReviewMarkup');
