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

namespace Google\Service\ShoppingContent;

class ProductView extends \Google\Collection
{
  /**
   * Undefined aggregated status.
   */
  public const AGGREGATED_DESTINATION_STATUS_AGGREGATED_STATUS_UNSPECIFIED = 'AGGREGATED_STATUS_UNSPECIFIED';
  /**
   * Offer isn't eligible, or is disapproved for all destinations.
   */
  public const AGGREGATED_DESTINATION_STATUS_NOT_ELIGIBLE_OR_DISAPPROVED = 'NOT_ELIGIBLE_OR_DISAPPROVED';
  /**
   * Offer's status is pending in all destinations.
   */
  public const AGGREGATED_DESTINATION_STATUS_PENDING = 'PENDING';
  /**
   * Offer is eligible for some (but not all) destinations.
   */
  public const AGGREGATED_DESTINATION_STATUS_ELIGIBLE_LIMITED = 'ELIGIBLE_LIMITED';
  /**
   * Offer is eligible for all destinations.
   */
  public const AGGREGATED_DESTINATION_STATUS_ELIGIBLE = 'ELIGIBLE';
  /**
   * Indicates that the channel is unspecified.
   */
  public const CHANNEL_CHANNEL_UNSPECIFIED = 'CHANNEL_UNSPECIFIED';
  /**
   * Indicates that the channel is local.
   */
  public const CHANNEL_LOCAL = 'LOCAL';
  /**
   * Indicates that the channel is online.
   */
  public const CHANNEL_ONLINE = 'ONLINE';
  /**
   * Unknown predicted clicks impact.
   */
  public const CLICK_POTENTIAL_CLICK_POTENTIAL_UNSPECIFIED = 'CLICK_POTENTIAL_UNSPECIFIED';
  /**
   * Potential to receive a low number of clicks compared to the highest
   * performing products of the merchant.
   */
  public const CLICK_POTENTIAL_LOW = 'LOW';
  /**
   * Potential to receive a moderate number of clicks compared to the highest
   * performing products of the merchant.
   */
  public const CLICK_POTENTIAL_MEDIUM = 'MEDIUM';
  /**
   * Potential to receive a similar number of clicks as the highest performing
   * products of the merchant.
   */
  public const CLICK_POTENTIAL_HIGH = 'HIGH';
  protected $collection_key = 'itemIssues';
  /**
   * Aggregated destination status.
   *
   * @var string
   */
  public $aggregatedDestinationStatus;
  /**
   * Availability of the product.
   *
   * @var string
   */
  public $availability;
  /**
   * Brand of the product.
   *
   * @var string
   */
  public $brand;
  /**
   * First level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $categoryL1;
  /**
   * Second level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $categoryL2;
  /**
   * Third level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $categoryL3;
  /**
   * Fourth level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $categoryL4;
  /**
   * Fifth level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $categoryL5;
  /**
   * Channel of the product (online versus local).
   *
   * @var string
   */
  public $channel;
  /**
   * Estimated performance potential compared to highest performing products of
   * the merchant.
   *
   * @var string
   */
  public $clickPotential;
  /**
   * Rank of the product based on its click potential. A product with
   * `click_potential_rank` 1 has the highest click potential among the
   * merchant's products that fulfill the search query conditions.
   *
   * @var string
   */
  public $clickPotentialRank;
  /**
   * Condition of the product.
   *
   * @var string
   */
  public $condition;
  /**
   * The time the merchant created the product in timestamp seconds.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Product price currency code (for example, ISO 4217). Absent if product
   * price is not available.
   *
   * @var string
   */
  public $currencyCode;
  protected $expirationDateType = Date::class;
  protected $expirationDateDataType = '';
  /**
   * GTIN of the product.
   *
   * @var string[]
   */
  public $gtin;
  /**
   * The REST ID of the product, in the form of
   * channel:contentLanguage:targetCountry:offerId. Content API methods that
   * operate on products take this as their productId parameter. Should always
   * be included in the SELECT clause.
   *
   * @var string
   */
  public $id;
  /**
   * Item group ID provided by the merchant for grouping variants together.
   *
   * @var string
   */
  public $itemGroupId;
  protected $itemIssuesType = ProductViewItemIssue::class;
  protected $itemIssuesDataType = 'array';
  /**
   * Language code of the product in BCP 47 format.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Merchant-provided id of the product.
   *
   * @var string
   */
  public $offerId;
  /**
   * Product price specified as micros (1 millionth of a standard unit, 1 USD =
   * 1000000 micros) in the product currency. Absent in case the information
   * about the price of the product is not available.
   *
   * @var string
   */
  public $priceMicros;
  /**
   * First level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $productTypeL1;
  /**
   * Second level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $productTypeL2;
  /**
   * Third level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $productTypeL3;
  /**
   * Fourth level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $productTypeL4;
  /**
   * Fifth level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @var string
   */
  public $productTypeL5;
  /**
   * The normalized shipping label specified in the feed
   *
   * @var string
   */
  public $shippingLabel;
  /**
   * Title of the product.
   *
   * @var string
   */
  public $title;

  /**
   * Aggregated destination status.
   *
   * Accepted values: AGGREGATED_STATUS_UNSPECIFIED,
   * NOT_ELIGIBLE_OR_DISAPPROVED, PENDING, ELIGIBLE_LIMITED, ELIGIBLE
   *
   * @param self::AGGREGATED_DESTINATION_STATUS_* $aggregatedDestinationStatus
   */
  public function setAggregatedDestinationStatus($aggregatedDestinationStatus)
  {
    $this->aggregatedDestinationStatus = $aggregatedDestinationStatus;
  }
  /**
   * @return self::AGGREGATED_DESTINATION_STATUS_*
   */
  public function getAggregatedDestinationStatus()
  {
    return $this->aggregatedDestinationStatus;
  }
  /**
   * Availability of the product.
   *
   * @param string $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return string
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * Brand of the product.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * First level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $categoryL1
   */
  public function setCategoryL1($categoryL1)
  {
    $this->categoryL1 = $categoryL1;
  }
  /**
   * @return string
   */
  public function getCategoryL1()
  {
    return $this->categoryL1;
  }
  /**
   * Second level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $categoryL2
   */
  public function setCategoryL2($categoryL2)
  {
    $this->categoryL2 = $categoryL2;
  }
  /**
   * @return string
   */
  public function getCategoryL2()
  {
    return $this->categoryL2;
  }
  /**
   * Third level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $categoryL3
   */
  public function setCategoryL3($categoryL3)
  {
    $this->categoryL3 = $categoryL3;
  }
  /**
   * @return string
   */
  public function getCategoryL3()
  {
    return $this->categoryL3;
  }
  /**
   * Fourth level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $categoryL4
   */
  public function setCategoryL4($categoryL4)
  {
    $this->categoryL4 = $categoryL4;
  }
  /**
   * @return string
   */
  public function getCategoryL4()
  {
    return $this->categoryL4;
  }
  /**
   * Fifth level of the product category in [Google's product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $categoryL5
   */
  public function setCategoryL5($categoryL5)
  {
    $this->categoryL5 = $categoryL5;
  }
  /**
   * @return string
   */
  public function getCategoryL5()
  {
    return $this->categoryL5;
  }
  /**
   * Channel of the product (online versus local).
   *
   * Accepted values: CHANNEL_UNSPECIFIED, LOCAL, ONLINE
   *
   * @param self::CHANNEL_* $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return self::CHANNEL_*
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Estimated performance potential compared to highest performing products of
   * the merchant.
   *
   * Accepted values: CLICK_POTENTIAL_UNSPECIFIED, LOW, MEDIUM, HIGH
   *
   * @param self::CLICK_POTENTIAL_* $clickPotential
   */
  public function setClickPotential($clickPotential)
  {
    $this->clickPotential = $clickPotential;
  }
  /**
   * @return self::CLICK_POTENTIAL_*
   */
  public function getClickPotential()
  {
    return $this->clickPotential;
  }
  /**
   * Rank of the product based on its click potential. A product with
   * `click_potential_rank` 1 has the highest click potential among the
   * merchant's products that fulfill the search query conditions.
   *
   * @param string $clickPotentialRank
   */
  public function setClickPotentialRank($clickPotentialRank)
  {
    $this->clickPotentialRank = $clickPotentialRank;
  }
  /**
   * @return string
   */
  public function getClickPotentialRank()
  {
    return $this->clickPotentialRank;
  }
  /**
   * Condition of the product.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The time the merchant created the product in timestamp seconds.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Product price currency code (for example, ISO 4217). Absent if product
   * price is not available.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Expiration date for the product. Specified on insertion.
   *
   * @param Date $expirationDate
   */
  public function setExpirationDate(Date $expirationDate)
  {
    $this->expirationDate = $expirationDate;
  }
  /**
   * @return Date
   */
  public function getExpirationDate()
  {
    return $this->expirationDate;
  }
  /**
   * GTIN of the product.
   *
   * @param string[] $gtin
   */
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  /**
   * @return string[]
   */
  public function getGtin()
  {
    return $this->gtin;
  }
  /**
   * The REST ID of the product, in the form of
   * channel:contentLanguage:targetCountry:offerId. Content API methods that
   * operate on products take this as their productId parameter. Should always
   * be included in the SELECT clause.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Item group ID provided by the merchant for grouping variants together.
   *
   * @param string $itemGroupId
   */
  public function setItemGroupId($itemGroupId)
  {
    $this->itemGroupId = $itemGroupId;
  }
  /**
   * @return string
   */
  public function getItemGroupId()
  {
    return $this->itemGroupId;
  }
  /**
   * List of item issues for the product.
   *
   * @param ProductViewItemIssue[] $itemIssues
   */
  public function setItemIssues($itemIssues)
  {
    $this->itemIssues = $itemIssues;
  }
  /**
   * @return ProductViewItemIssue[]
   */
  public function getItemIssues()
  {
    return $this->itemIssues;
  }
  /**
   * Language code of the product in BCP 47 format.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Merchant-provided id of the product.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * Product price specified as micros (1 millionth of a standard unit, 1 USD =
   * 1000000 micros) in the product currency. Absent in case the information
   * about the price of the product is not available.
   *
   * @param string $priceMicros
   */
  public function setPriceMicros($priceMicros)
  {
    $this->priceMicros = $priceMicros;
  }
  /**
   * @return string
   */
  public function getPriceMicros()
  {
    return $this->priceMicros;
  }
  /**
   * First level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $productTypeL1
   */
  public function setProductTypeL1($productTypeL1)
  {
    $this->productTypeL1 = $productTypeL1;
  }
  /**
   * @return string
   */
  public function getProductTypeL1()
  {
    return $this->productTypeL1;
  }
  /**
   * Second level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $productTypeL2
   */
  public function setProductTypeL2($productTypeL2)
  {
    $this->productTypeL2 = $productTypeL2;
  }
  /**
   * @return string
   */
  public function getProductTypeL2()
  {
    return $this->productTypeL2;
  }
  /**
   * Third level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $productTypeL3
   */
  public function setProductTypeL3($productTypeL3)
  {
    $this->productTypeL3 = $productTypeL3;
  }
  /**
   * @return string
   */
  public function getProductTypeL3()
  {
    return $this->productTypeL3;
  }
  /**
   * Fourth level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $productTypeL4
   */
  public function setProductTypeL4($productTypeL4)
  {
    $this->productTypeL4 = $productTypeL4;
  }
  /**
   * @return string
   */
  public function getProductTypeL4()
  {
    return $this->productTypeL4;
  }
  /**
   * Fifth level of the product type in merchant's own [product
   * taxonomy](https://support.google.com/merchants/answer/6324436).
   *
   * @param string $productTypeL5
   */
  public function setProductTypeL5($productTypeL5)
  {
    $this->productTypeL5 = $productTypeL5;
  }
  /**
   * @return string
   */
  public function getProductTypeL5()
  {
    return $this->productTypeL5;
  }
  /**
   * The normalized shipping label specified in the feed
   *
   * @param string $shippingLabel
   */
  public function setShippingLabel($shippingLabel)
  {
    $this->shippingLabel = $shippingLabel;
  }
  /**
   * @return string
   */
  public function getShippingLabel()
  {
    return $this->shippingLabel;
  }
  /**
   * Title of the product.
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
class_alias(ProductView::class, 'Google_Service_ShoppingContent_ProductView');
