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

class ProductReviewAttributes extends \Google\Collection
{
  /**
   * Collection method unspecified.
   */
  public const COLLECTION_METHOD_COLLECTION_METHOD_UNSPECIFIED = 'COLLECTION_METHOD_UNSPECIFIED';
  /**
   * The user was not responding to a specific solicitation when they submitted
   * the review.
   */
  public const COLLECTION_METHOD_UNSOLICITED = 'UNSOLICITED';
  /**
   * The user submitted the review in response to a solicitation after
   * fulfillment of the user's order.
   */
  public const COLLECTION_METHOD_POST_FULFILLMENT = 'POST_FULFILLMENT';
  protected $collection_key = 'skus';
  /**
   * Optional. The name of the aggregator of the product reviews. A publisher
   * may use a reviews aggregator to manage reviews and provide the feeds. This
   * element indicates the use of an aggregator and contains information about
   * the aggregator.
   *
   * @var string
   */
  public $aggregatorName;
  /**
   * Optional. Contains ASINs (Amazon Standard Identification Numbers)
   * associated with a product.
   *
   * @var string[]
   */
  public $asins;
  /**
   * Optional. Contains brand names associated with a product.
   *
   * @var string[]
   */
  public $brands;
  /**
   * Optional. The method used to collect the review.
   *
   * @var string
   */
  public $collectionMethod;
  /**
   * Optional. Contains the disadvantages based on the opinion of the reviewer.
   * Omit boilerplate text like "con:" unless it was written by the reviewer.
   *
   * @var string[]
   */
  public $cons;
  /**
   * Optional. The content of the review. If empty, the content might still get
   * populated from pros and cons.
   *
   * @var string
   */
  public $content;
  /**
   * Optional. Contains GTINs (global trade item numbers) associated with a
   * product. Sub-types of GTINs (e.g. UPC, EAN, ISBN, JAN) are supported.
   *
   * @var string[]
   */
  public $gtins;
  /**
   * Optional. Indicates whether the review is incentivized.
   *
   * @var bool
   */
  public $isIncentivizedReview;
  /**
   * Optional. Indicates whether the review is marked as spam in the publisher's
   * system.
   *
   * @var bool
   */
  public $isSpam;
  /**
   * Optional. Indicates whether the reviewer's purchase is verified.
   *
   * @var bool
   */
  public $isVerifiedPurchase;
  /**
   * Optional. The maximum possible number for the rating. The value of the max
   * rating must be greater than the value of the min attribute.
   *
   * @var string
   */
  public $maxRating;
  /**
   * Optional. Contains the ratings associated with the review. The minimum
   * possible number for the rating. This should be the worst possible rating
   * and should not be a value for no rating.
   *
   * @var string
   */
  public $minRating;
  /**
   * Optional. Contains MPNs (manufacturer part numbers) associated with a
   * product.
   *
   * @var string[]
   */
  public $mpns;
  /**
   * Optional. The URI of the product. This URI can have the same value as the
   * `review_link` element, if the review URI and the product URI are the same.
   *
   * @var string[]
   */
  public $productLinks;
  /**
   * Optional. Descriptive name of a product.
   *
   * @var string[]
   */
  public $productNames;
  /**
   * Optional. Contains the advantages based on the opinion of the reviewer.
   * Omit boilerplate text like "pro:" unless it was written by the reviewer.
   *
   * @var string[]
   */
  public $pros;
  /**
   * Optional. A link to the company favicon of the publisher. The image
   * dimensions should be favicon size: 16x16 pixels. The image format should be
   * GIF, JPG or PNG.
   *
   * @var string
   */
  public $publisherFavicon;
  /**
   * Optional. The name of the publisher of the product reviews. The information
   * about the publisher, which may be a retailer, manufacturer, reviews service
   * company, or any entity that publishes product reviews.
   *
   * @var string
   */
  public $publisherName;
  /**
   * Optional. The reviewer's overall rating of the product.
   *
   * @var 
   */
  public $rating;
  /**
   * Optional. The country of the review defined by ISO 3166-1 Alpha-2 Country
   * Code.
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
  protected $reviewLinkType = ReviewLink::class;
  protected $reviewLinkDataType = '';
  /**
   * Required. The timestamp indicating when the review was written.
   *
   * @var string
   */
  public $reviewTime;
  /**
   * Optional. The author of the product review. A permanent, unique identifier
   * for the author of the review in the publisher's system.
   *
   * @var string
   */
  public $reviewerId;
  /**
   * Optional. A URI to an image of the reviewed product created by the review
   * author. The URI does not have to end with an image file extension.
   *
   * @var string[]
   */
  public $reviewerImageLinks;
  /**
   * Optional. Set to true if the reviewer should remain anonymous.
   *
   * @var bool
   */
  public $reviewerIsAnonymous;
  /**
   * Optional. The name of the reviewer of the product review.
   *
   * @var string
   */
  public $reviewerUsername;
  /**
   * Optional. Contains SKUs (stock keeping units) associated with a product.
   * Often this matches the product Offer Id in the product feed.
   *
   * @var string[]
   */
  public $skus;
  /**
   * Optional. The name of the subclient of the product reviews. The subclient
   * is an identifier of the product review source. It should be equivalent to
   * the directory provided in the file data source path.
   *
   * @var string
   */
  public $subclientName;
  /**
   * Optional. The title of the review.
   *
   * @var string
   */
  public $title;
  /**
   * Optional. A permanent, unique identifier for the transaction associated
   * with the review in the publisher's system. This ID can be used to indicate
   * that multiple reviews are associated with the same transaction.
   *
   * @var string
   */
  public $transactionId;

  /**
   * Optional. The name of the aggregator of the product reviews. A publisher
   * may use a reviews aggregator to manage reviews and provide the feeds. This
   * element indicates the use of an aggregator and contains information about
   * the aggregator.
   *
   * @param string $aggregatorName
   */
  public function setAggregatorName($aggregatorName)
  {
    $this->aggregatorName = $aggregatorName;
  }
  /**
   * @return string
   */
  public function getAggregatorName()
  {
    return $this->aggregatorName;
  }
  /**
   * Optional. Contains ASINs (Amazon Standard Identification Numbers)
   * associated with a product.
   *
   * @param string[] $asins
   */
  public function setAsins($asins)
  {
    $this->asins = $asins;
  }
  /**
   * @return string[]
   */
  public function getAsins()
  {
    return $this->asins;
  }
  /**
   * Optional. Contains brand names associated with a product.
   *
   * @param string[] $brands
   */
  public function setBrands($brands)
  {
    $this->brands = $brands;
  }
  /**
   * @return string[]
   */
  public function getBrands()
  {
    return $this->brands;
  }
  /**
   * Optional. The method used to collect the review.
   *
   * Accepted values: COLLECTION_METHOD_UNSPECIFIED, UNSOLICITED,
   * POST_FULFILLMENT
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
   * Optional. Contains the disadvantages based on the opinion of the reviewer.
   * Omit boilerplate text like "con:" unless it was written by the reviewer.
   *
   * @param string[] $cons
   */
  public function setCons($cons)
  {
    $this->cons = $cons;
  }
  /**
   * @return string[]
   */
  public function getCons()
  {
    return $this->cons;
  }
  /**
   * Optional. The content of the review. If empty, the content might still get
   * populated from pros and cons.
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
   * Optional. Contains GTINs (global trade item numbers) associated with a
   * product. Sub-types of GTINs (e.g. UPC, EAN, ISBN, JAN) are supported.
   *
   * @param string[] $gtins
   */
  public function setGtins($gtins)
  {
    $this->gtins = $gtins;
  }
  /**
   * @return string[]
   */
  public function getGtins()
  {
    return $this->gtins;
  }
  /**
   * Optional. Indicates whether the review is incentivized.
   *
   * @param bool $isIncentivizedReview
   */
  public function setIsIncentivizedReview($isIncentivizedReview)
  {
    $this->isIncentivizedReview = $isIncentivizedReview;
  }
  /**
   * @return bool
   */
  public function getIsIncentivizedReview()
  {
    return $this->isIncentivizedReview;
  }
  /**
   * Optional. Indicates whether the review is marked as spam in the publisher's
   * system.
   *
   * @param bool $isSpam
   */
  public function setIsSpam($isSpam)
  {
    $this->isSpam = $isSpam;
  }
  /**
   * @return bool
   */
  public function getIsSpam()
  {
    return $this->isSpam;
  }
  /**
   * Optional. Indicates whether the reviewer's purchase is verified.
   *
   * @param bool $isVerifiedPurchase
   */
  public function setIsVerifiedPurchase($isVerifiedPurchase)
  {
    $this->isVerifiedPurchase = $isVerifiedPurchase;
  }
  /**
   * @return bool
   */
  public function getIsVerifiedPurchase()
  {
    return $this->isVerifiedPurchase;
  }
  /**
   * Optional. The maximum possible number for the rating. The value of the max
   * rating must be greater than the value of the min attribute.
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
   * Optional. Contains the ratings associated with the review. The minimum
   * possible number for the rating. This should be the worst possible rating
   * and should not be a value for no rating.
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
  /**
   * Optional. Contains MPNs (manufacturer part numbers) associated with a
   * product.
   *
   * @param string[] $mpns
   */
  public function setMpns($mpns)
  {
    $this->mpns = $mpns;
  }
  /**
   * @return string[]
   */
  public function getMpns()
  {
    return $this->mpns;
  }
  /**
   * Optional. The URI of the product. This URI can have the same value as the
   * `review_link` element, if the review URI and the product URI are the same.
   *
   * @param string[] $productLinks
   */
  public function setProductLinks($productLinks)
  {
    $this->productLinks = $productLinks;
  }
  /**
   * @return string[]
   */
  public function getProductLinks()
  {
    return $this->productLinks;
  }
  /**
   * Optional. Descriptive name of a product.
   *
   * @param string[] $productNames
   */
  public function setProductNames($productNames)
  {
    $this->productNames = $productNames;
  }
  /**
   * @return string[]
   */
  public function getProductNames()
  {
    return $this->productNames;
  }
  /**
   * Optional. Contains the advantages based on the opinion of the reviewer.
   * Omit boilerplate text like "pro:" unless it was written by the reviewer.
   *
   * @param string[] $pros
   */
  public function setPros($pros)
  {
    $this->pros = $pros;
  }
  /**
   * @return string[]
   */
  public function getPros()
  {
    return $this->pros;
  }
  /**
   * Optional. A link to the company favicon of the publisher. The image
   * dimensions should be favicon size: 16x16 pixels. The image format should be
   * GIF, JPG or PNG.
   *
   * @param string $publisherFavicon
   */
  public function setPublisherFavicon($publisherFavicon)
  {
    $this->publisherFavicon = $publisherFavicon;
  }
  /**
   * @return string
   */
  public function getPublisherFavicon()
  {
    return $this->publisherFavicon;
  }
  /**
   * Optional. The name of the publisher of the product reviews. The information
   * about the publisher, which may be a retailer, manufacturer, reviews service
   * company, or any entity that publishes product reviews.
   *
   * @param string $publisherName
   */
  public function setPublisherName($publisherName)
  {
    $this->publisherName = $publisherName;
  }
  /**
   * @return string
   */
  public function getPublisherName()
  {
    return $this->publisherName;
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
   * Optional. The country of the review defined by ISO 3166-1 Alpha-2 Country
   * Code.
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
   * Optional. The URI of the review landing page.
   *
   * @param ReviewLink $reviewLink
   */
  public function setReviewLink(ReviewLink $reviewLink)
  {
    $this->reviewLink = $reviewLink;
  }
  /**
   * @return ReviewLink
   */
  public function getReviewLink()
  {
    return $this->reviewLink;
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
   * Optional. The author of the product review. A permanent, unique identifier
   * for the author of the review in the publisher's system.
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
   * Optional. A URI to an image of the reviewed product created by the review
   * author. The URI does not have to end with an image file extension.
   *
   * @param string[] $reviewerImageLinks
   */
  public function setReviewerImageLinks($reviewerImageLinks)
  {
    $this->reviewerImageLinks = $reviewerImageLinks;
  }
  /**
   * @return string[]
   */
  public function getReviewerImageLinks()
  {
    return $this->reviewerImageLinks;
  }
  /**
   * Optional. Set to true if the reviewer should remain anonymous.
   *
   * @param bool $reviewerIsAnonymous
   */
  public function setReviewerIsAnonymous($reviewerIsAnonymous)
  {
    $this->reviewerIsAnonymous = $reviewerIsAnonymous;
  }
  /**
   * @return bool
   */
  public function getReviewerIsAnonymous()
  {
    return $this->reviewerIsAnonymous;
  }
  /**
   * Optional. The name of the reviewer of the product review.
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
   * Optional. Contains SKUs (stock keeping units) associated with a product.
   * Often this matches the product Offer Id in the product feed.
   *
   * @param string[] $skus
   */
  public function setSkus($skus)
  {
    $this->skus = $skus;
  }
  /**
   * @return string[]
   */
  public function getSkus()
  {
    return $this->skus;
  }
  /**
   * Optional. The name of the subclient of the product reviews. The subclient
   * is an identifier of the product review source. It should be equivalent to
   * the directory provided in the file data source path.
   *
   * @param string $subclientName
   */
  public function setSubclientName($subclientName)
  {
    $this->subclientName = $subclientName;
  }
  /**
   * @return string
   */
  public function getSubclientName()
  {
    return $this->subclientName;
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
  /**
   * Optional. A permanent, unique identifier for the transaction associated
   * with the review in the publisher's system. This ID can be used to indicate
   * that multiple reviews are associated with the same transaction.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductReviewAttributes::class, 'Google_Service_Merchant_ProductReviewAttributes');
