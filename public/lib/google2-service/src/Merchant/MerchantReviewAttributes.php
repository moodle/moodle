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

namespace Google\Service\Merchant;

class MerchantReviewAttributes extends \Google\Model
{
  /**
   * Collection method unspecified.
   */
  public const COLLECTION_METHOD_COLLECTION_METHOD_UNSPECIFIED = 'COLLECTION_METHOD_UNSPECIFIED';
  /**
   * The user was not responding to a specific solicitation when they submitted
   * the review.
   */
  public const COLLECTION_METHOD_MERCHANT_UNSOLICITED = 'MERCHANT_UNSOLICITED';
  /**
   * The user submitted the review in response to a solicitation when the user
   * placed an order.
   */
  public const COLLECTION_METHOD_POINT_OF_SALE = 'POINT_OF_SALE';
  /**
   * The user submitted the review in response to a solicitation after
   * fulfillment of the user's order.
   */
  public const COLLECTION_METHOD_AFTER_FULFILLMENT = 'AFTER_FULFILLMENT';
  /**
   * Optional. The method used to collect the review.
   *
   * @var string
   */
  public $collectionMethod;
  /**
   * Required. This should be any freeform text provided by the user and should
   * not be truncated. If multiple responses to different questions are
   * provided, all responses should be included, with the minimal context for
   * the responses to make sense. Context should not be provided if questions
   * were left unanswered.
   *
   * @var string
   */
  public $content;
  /**
   * Optional. Set to true if the reviewer should remain anonymous.
   *
   * @var bool
   */
  public $isAnonymous;
  /**
   * Optional. The maximum possible number for the rating. The value of the max
   * rating must be greater than the value of the min rating.
   *
   * @var string
   */
  public $maxRating;
  /**
   * Optional. Human-readable display name for the merchant.
   *
   * @var string
   */
  public $merchantDisplayName;
  /**
   * Required. Must be unique and stable across all requests. In other words, if
   * a request today and another 90 days ago refer to the same merchant, they
   * must have the same id.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Optional. URL to the merchant's main website. Do not use a redirect URL for
   * this value. In other words, the value should point directly to the
   * merchant's site.
   *
   * @var string
   */
  public $merchantLink;
  /**
   * Optional. URL to the landing page that hosts the reviews for this merchant.
   * Do not use a redirect URL.
   *
   * @var string
   */
  public $merchantRatingLink;
  /**
   * Optional. The minimum possible number for the rating. This should be the
   * worst possible rating and should not be a value for no rating.
   *
   * @var string
   */
  public $minRating;
  /**
   * Optional. The reviewer's overall rating of the merchant.
   *
   * @var 
   */
  public $rating;
  /**
   * Optional. The country where the reviewer made the order defined by ISO
   * 3166-1 Alpha-2 Country Code.
   *
   * @var string
   */
  public $reviewCountry;
  /**
   * Optional. The language of the review defined by BCP-47 language code.
   *
   * @var string
   */
  public $reviewLanguage;
  /**
   * Required. The timestamp indicating when the review was written.
   *
   * @var string
   */
  public $reviewTime;
  /**
   * Optional. A permanent, unique identifier for the author of the review in
   * the publisher's system.
   *
   * @var string
   */
  public $reviewerId;
  /**
   * Optional. Display name of the review author.
   *
   * @var string
   */
  public $reviewerUsername;
  /**
   * Optional. The title of the review.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. The method used to collect the review.
   *
   * Accepted values: COLLECTION_METHOD_UNSPECIFIED, MERCHANT_UNSOLICITED,
   * POINT_OF_SALE, AFTER_FULFILLMENT
   *
   * @param self::COLLECTION_METHOD_* $collectionMethod
   */
  public function setCollectionMethod($collectionMethod)
  {
    $this->collectionMethod = $collectionMethod;
  }
  /**
   * @return self::COLLECTION_METHOD_*
   */
  public function getCollectionMethod()
  {
    return $this->collectionMethod;
  }
  /**
   * Required. This should be any freeform text provided by the user and should
   * not be truncated. If multiple responses to different questions are
   * provided, all responses should be included, with the minimal context for
   * the responses to make sense. Context should not be provided if questions
   * were left unanswered.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Optional. Set to true if the reviewer should remain anonymous.
   *
   * @param bool $isAnonymous
   */
  public function setIsAnonymous($isAnonymous)
  {
    $this->isAnonymous = $isAnonymous;
  }
  /**
   * @return bool
   */
  public function getIsAnonymous()
  {
    return $this->isAnonymous;
  }
  /**
   * Optional. The maximum possible number for the rating. The value of the max
   * rating must be greater than the value of the min rating.
   *
   * @param string $maxRating
   */
  public function setMaxRating($maxRating)
  {
    $this->maxRating = $maxRating;
  }
  /**
   * @return string
   */
  public function getMaxRating()
  {
    return $this->maxRating;
  }
  /**
   * Optional. Human-readable display name for the merchant.
   *
   * @param string $merchantDisplayName
   */
  public function setMerchantDisplayName($merchantDisplayName)
  {
    $this->merchantDisplayName = $merchantDisplayName;
  }
  /**
   * @return string
   */
  public function getMerchantDisplayName()
  {
    return $this->merchantDisplayName;
  }
  /**
   * Required. Must be unique and stable across all requests. In other words, if
   * a request today and another 90 days ago refer to the same merchant, they
   * must have the same id.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * Optional. URL to the merchant's main website. Do not use a redirect URL for
   * this value. In other words, the value should point directly to the
   * merchant's site.
   *
   * @param string $merchantLink
   */
  public function setMerchantLink($merchantLink)
  {
    $this->merchantLink = $merchantLink;
  }
  /**
   * @return string
   */
  public function getMerchantLink()
  {
    return $this->merchantLink;
  }
  /**
   * Optional. URL to the landing page that hosts the reviews for this merchant.
   * Do not use a redirect URL.
   *
   * @param string $merchantRatingLink
   */
  public function setMerchantRatingLink($merchantRatingLink)
  {
    $this->merchantRatingLink = $merchantRatingLink;
  }
  /**
   * @return string
   */
  public function getMerchantRatingLink()
  {
    return $this->merchantRatingLink;
  }
  /**
   * Optional. The minimum possible number for the rating. This should be the
   * worst possible rating and should not be a value for no rating.
   *
   * @param string $minRating
   */
  public function setMinRating($minRating)
  {
    $this->minRating = $minRating;
  }
  /**
   * @return string
   */
  public function getMinRating()
  {
    return $this->minRating;
  }
  public function setRating($rating)
  {
    $this->rating = $rating;
  }
  public function getRating()
  {
    return $this->rating;
  }
  /**
   * Optional. The country where the reviewer made the order defined by ISO
   * 3166-1 Alpha-2 Country Code.
   *
   * @param string $reviewCountry
   */
  public function setReviewCountry($reviewCountry)
  {
    $this->reviewCountry = $reviewCountry;
  }
  /**
   * @return string
   */
  public function getReviewCountry()
  {
    return $this->reviewCountry;
  }
  /**
   * Optional. The language of the review defined by BCP-47 language code.
   *
   * @param string $reviewLanguage
   */
  public function setReviewLanguage($reviewLanguage)
  {
    $this->reviewLanguage = $reviewLanguage;
  }
  /**
   * @return string
   */
  public function getReviewLanguage()
  {
    return $this->reviewLanguage;
  }
  /**
   * Required. The timestamp indicating when the review was written.
   *
   * @param string $reviewTime
   */
  public function setReviewTime($reviewTime)
  {
    $this->reviewTime = $reviewTime;
  }
  /**
   * @return string
   */
  public function getReviewTime()
  {
    return $this->reviewTime;
  }
  /**
   * Optional. A permanent, unique identifier for the author of the review in
   * the publisher's system.
   *
   * @param string $reviewerId
   */
  public function setReviewerId($reviewerId)
  {
    $this->reviewerId = $reviewerId;
  }
  /**
   * @return string
   */
  public function getReviewerId()
  {
    return $this->reviewerId;
  }
  /**
   * Optional. Display name of the review author.
   *
   * @param string $reviewerUsername
   */
  public function setReviewerUsername($reviewerUsername)
  {
    $this->reviewerUsername = $reviewerUsername;
  }
  /**
   * @return string
   */
  public function getReviewerUsername()
  {
    return $this->reviewerUsername;
  }
  /**
   * Optional. The title of the review.
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
class_alias(MerchantReviewAttributes::class, 'Google_Service_Merchant_MerchantReviewAttributes');
