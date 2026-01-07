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

namespace Google\Service\Css;

class Attributes extends \Google\Collection
{
  protected $collection_key = 'sizeTypes';
  /**
   * Additional URL of images of the item.
   *
   * @var string[]
   */
  public $additionalImageLinks;
  /**
   * Set to true if the item is targeted towards adults.
   *
   * @var bool
   */
  public $adult;
  /**
   * Target age group of the item.
   *
   * @var string
   */
  public $ageGroup;
  /**
   * Product Related Attributes.[14-36] Brand of the item.
   *
   * @var string
   */
  public $brand;
  protected $certificationsType = Certification::class;
  protected $certificationsDataType = 'array';
  /**
   * Color of the item.
   *
   * @var string
   */
  public $color;
  /**
   * Allows advertisers to override the item URL when the product is shown
   * within the context of Product Ads.
   *
   * @var string
   */
  public $cppAdsRedirect;
  /**
   * URL directly linking to your the Product Detail Page of the CSS.
   *
   * @var string
   */
  public $cppLink;
  /**
   * URL for the mobile-optimized version of the Product Detail Page of the CSS.
   *
   * @var string
   */
  public $cppMobileLink;
  /**
   * Custom label 0 for custom grouping of items in a Shopping campaign.
   *
   * @var string
   */
  public $customLabel0;
  /**
   * Custom label 1 for custom grouping of items in a Shopping campaign.
   *
   * @var string
   */
  public $customLabel1;
  /**
   * Custom label 2 for custom grouping of items in a Shopping campaign.
   *
   * @var string
   */
  public $customLabel2;
  /**
   * Custom label 3 for custom grouping of items in a Shopping campaign.
   *
   * @var string
   */
  public $customLabel3;
  /**
   * Custom label 4 for custom grouping of items in a Shopping campaign.
   *
   * @var string
   */
  public $customLabel4;
  /**
   * Description of the item.
   *
   * @var string
   */
  public $description;
  /**
   * The list of destinations to exclude for this target (corresponds to
   * unchecked check boxes in Merchant Center).
   *
   * @var string[]
   */
  public $excludedDestinations;
  /**
   * Date on which the item should expire, as specified upon insertion, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format. The actual expiration
   * date is exposed in `productstatuses` as
   * [googleExpirationDate](https://support.google.com/merchants/answer/6324499)
   * and might be earlier if `expirationDate` is too far in the future. Note: It
   * may take 2+ days from the expiration date for the item to actually get
   * deleted.
   *
   * @var string
   */
  public $expirationDate;
  /**
   * Target gender of the item.
   *
   * @var string
   */
  public $gender;
  /**
   * Google's category of the item (see [Google product
   * taxonomy](https://support.google.com/merchants/answer/1705911)). When
   * querying products, this field will contain the user provided value. There
   * is currently no way to get back the auto assigned google product categories
   * through the API.
   *
   * @var string
   */
  public $googleProductCategory;
  /**
   * Global Trade Item Number
   * ([GTIN](https://support.google.com/merchants/answer/188494#gtin)) of the
   * item.
   *
   * @var string
   */
  public $gtin;
  /**
   * Condition of the headline offer.
   *
   * @var string
   */
  public $headlineOfferCondition;
  protected $headlineOfferInstallmentType = HeadlineOfferInstallment::class;
  protected $headlineOfferInstallmentDataType = '';
  /**
   * Link to the headline offer.
   *
   * @var string
   */
  public $headlineOfferLink;
  /**
   * Mobile Link to the headline offer.
   *
   * @var string
   */
  public $headlineOfferMobileLink;
  protected $headlineOfferPriceType = Price::class;
  protected $headlineOfferPriceDataType = '';
  protected $headlineOfferShippingPriceType = Price::class;
  protected $headlineOfferShippingPriceDataType = '';
  protected $headlineOfferSubscriptionCostType = HeadlineOfferSubscriptionCost::class;
  protected $headlineOfferSubscriptionCostDataType = '';
  protected $highPriceType = Price::class;
  protected $highPriceDataType = '';
  /**
   * URL of an image of the item.
   *
   * @var string
   */
  public $imageLink;
  /**
   * The list of destinations to include for this target (corresponds to checked
   * check boxes in Merchant Center). Default destinations are always included
   * unless provided in `excludedDestinations`.
   *
   * @var string[]
   */
  public $includedDestinations;
  /**
   * Whether the item is a merchant-defined bundle. A bundle is a custom
   * grouping of different products sold by a merchant for a single price.
   *
   * @var bool
   */
  public $isBundle;
  /**
   * Shared identifier for all variants of the same product.
   *
   * @var string
   */
  public $itemGroupId;
  protected $lowPriceType = Price::class;
  protected $lowPriceDataType = '';
  /**
   * The material of which the item is made.
   *
   * @var string
   */
  public $material;
  /**
   * Manufacturer Part Number
   * ([MPN](https://support.google.com/merchants/answer/188494#mpn)) of the
   * item.
   *
   * @var string
   */
  public $mpn;
  /**
   * The number of identical products in a merchant-defined multipack.
   *
   * @var string
   */
  public $multipack;
  /**
   * The number of CSS Products.
   *
   * @var string
   */
  public $numberOfOffers;
  /**
   * The item's pattern (e.g. polka dots).
   *
   * @var string
   */
  public $pattern;
  /**
   * Publication of this item will be temporarily paused.
   *
   * @var string
   */
  public $pause;
  protected $productDetailsType = ProductDetail::class;
  protected $productDetailsDataType = 'array';
  protected $productHeightType = ProductDimension::class;
  protected $productHeightDataType = '';
  /**
   * Bullet points describing the most relevant highlights of a product.
   *
   * @var string[]
   */
  public $productHighlights;
  protected $productLengthType = ProductDimension::class;
  protected $productLengthDataType = '';
  /**
   * Categories of the item (formatted as in [products data
   * specification](https://support.google.com/merchants/answer/6324406)).
   *
   * @var string[]
   */
  public $productTypes;
  protected $productWeightType = ProductWeight::class;
  protected $productWeightDataType = '';
  protected $productWidthType = ProductDimension::class;
  protected $productWidthDataType = '';
  /**
   * Size of the item. Only one value is allowed. For variants with different
   * sizes, insert a separate product for each size with the same `itemGroupId`
   * value (see [https://support.google.com/merchants/answer/6324492](size
   * definition)).
   *
   * @var string
   */
  public $size;
  /**
   * System in which the size is specified. Recommended for apparel items.
   *
   * @var string
   */
  public $sizeSystem;
  /**
   * The cut of the item. It can be used to represent combined size types for
   * apparel items. Maximum two of size types can be provided (see
   * [https://support.google.com/merchants/answer/6324497](size type)).
   *
   * @var string[]
   */
  public $sizeTypes;
  /**
   * Title of the item.
   *
   * @var string
   */
  public $title;

  /**
   * Additional URL of images of the item.
   *
   * @param string[] $additionalImageLinks
   */
  public function setAdditionalImageLinks($additionalImageLinks)
  {
    $this->additionalImageLinks = $additionalImageLinks;
  }
  /**
   * @return string[]
   */
  public function getAdditionalImageLinks()
  {
    return $this->additionalImageLinks;
  }
  /**
   * Set to true if the item is targeted towards adults.
   *
   * @param bool $adult
   */
  public function setAdult($adult)
  {
    $this->adult = $adult;
  }
  /**
   * @return bool
   */
  public function getAdult()
  {
    return $this->adult;
  }
  /**
   * Target age group of the item.
   *
   * @param string $ageGroup
   */
  public function setAgeGroup($ageGroup)
  {
    $this->ageGroup = $ageGroup;
  }
  /**
   * @return string
   */
  public function getAgeGroup()
  {
    return $this->ageGroup;
  }
  /**
   * Product Related Attributes.[14-36] Brand of the item.
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
   * A list of certificates claimed by the CSS for the given product.
   *
   * @param Certification[] $certifications
   */
  public function setCertifications($certifications)
  {
    $this->certifications = $certifications;
  }
  /**
   * @return Certification[]
   */
  public function getCertifications()
  {
    return $this->certifications;
  }
  /**
   * Color of the item.
   *
   * @param string $color
   */
  public function setColor($color)
  {
    $this->color = $color;
  }
  /**
   * @return string
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * Allows advertisers to override the item URL when the product is shown
   * within the context of Product Ads.
   *
   * @param string $cppAdsRedirect
   */
  public function setCppAdsRedirect($cppAdsRedirect)
  {
    $this->cppAdsRedirect = $cppAdsRedirect;
  }
  /**
   * @return string
   */
  public function getCppAdsRedirect()
  {
    return $this->cppAdsRedirect;
  }
  /**
   * URL directly linking to your the Product Detail Page of the CSS.
   *
   * @param string $cppLink
   */
  public function setCppLink($cppLink)
  {
    $this->cppLink = $cppLink;
  }
  /**
   * @return string
   */
  public function getCppLink()
  {
    return $this->cppLink;
  }
  /**
   * URL for the mobile-optimized version of the Product Detail Page of the CSS.
   *
   * @param string $cppMobileLink
   */
  public function setCppMobileLink($cppMobileLink)
  {
    $this->cppMobileLink = $cppMobileLink;
  }
  /**
   * @return string
   */
  public function getCppMobileLink()
  {
    return $this->cppMobileLink;
  }
  /**
   * Custom label 0 for custom grouping of items in a Shopping campaign.
   *
   * @param string $customLabel0
   */
  public function setCustomLabel0($customLabel0)
  {
    $this->customLabel0 = $customLabel0;
  }
  /**
   * @return string
   */
  public function getCustomLabel0()
  {
    return $this->customLabel0;
  }
  /**
   * Custom label 1 for custom grouping of items in a Shopping campaign.
   *
   * @param string $customLabel1
   */
  public function setCustomLabel1($customLabel1)
  {
    $this->customLabel1 = $customLabel1;
  }
  /**
   * @return string
   */
  public function getCustomLabel1()
  {
    return $this->customLabel1;
  }
  /**
   * Custom label 2 for custom grouping of items in a Shopping campaign.
   *
   * @param string $customLabel2
   */
  public function setCustomLabel2($customLabel2)
  {
    $this->customLabel2 = $customLabel2;
  }
  /**
   * @return string
   */
  public function getCustomLabel2()
  {
    return $this->customLabel2;
  }
  /**
   * Custom label 3 for custom grouping of items in a Shopping campaign.
   *
   * @param string $customLabel3
   */
  public function setCustomLabel3($customLabel3)
  {
    $this->customLabel3 = $customLabel3;
  }
  /**
   * @return string
   */
  public function getCustomLabel3()
  {
    return $this->customLabel3;
  }
  /**
   * Custom label 4 for custom grouping of items in a Shopping campaign.
   *
   * @param string $customLabel4
   */
  public function setCustomLabel4($customLabel4)
  {
    $this->customLabel4 = $customLabel4;
  }
  /**
   * @return string
   */
  public function getCustomLabel4()
  {
    return $this->customLabel4;
  }
  /**
   * Description of the item.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The list of destinations to exclude for this target (corresponds to
   * unchecked check boxes in Merchant Center).
   *
   * @param string[] $excludedDestinations
   */
  public function setExcludedDestinations($excludedDestinations)
  {
    $this->excludedDestinations = $excludedDestinations;
  }
  /**
   * @return string[]
   */
  public function getExcludedDestinations()
  {
    return $this->excludedDestinations;
  }
  /**
   * Date on which the item should expire, as specified upon insertion, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format. The actual expiration
   * date is exposed in `productstatuses` as
   * [googleExpirationDate](https://support.google.com/merchants/answer/6324499)
   * and might be earlier if `expirationDate` is too far in the future. Note: It
   * may take 2+ days from the expiration date for the item to actually get
   * deleted.
   *
   * @param string $expirationDate
   */
  public function setExpirationDate($expirationDate)
  {
    $this->expirationDate = $expirationDate;
  }
  /**
   * @return string
   */
  public function getExpirationDate()
  {
    return $this->expirationDate;
  }
  /**
   * Target gender of the item.
   *
   * @param string $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return string
   */
  public function getGender()
  {
    return $this->gender;
  }
  /**
   * Google's category of the item (see [Google product
   * taxonomy](https://support.google.com/merchants/answer/1705911)). When
   * querying products, this field will contain the user provided value. There
   * is currently no way to get back the auto assigned google product categories
   * through the API.
   *
   * @param string $googleProductCategory
   */
  public function setGoogleProductCategory($googleProductCategory)
  {
    $this->googleProductCategory = $googleProductCategory;
  }
  /**
   * @return string
   */
  public function getGoogleProductCategory()
  {
    return $this->googleProductCategory;
  }
  /**
   * Global Trade Item Number
   * ([GTIN](https://support.google.com/merchants/answer/188494#gtin)) of the
   * item.
   *
   * @param string $gtin
   */
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  /**
   * @return string
   */
  public function getGtin()
  {
    return $this->gtin;
  }
  /**
   * Condition of the headline offer.
   *
   * @param string $headlineOfferCondition
   */
  public function setHeadlineOfferCondition($headlineOfferCondition)
  {
    $this->headlineOfferCondition = $headlineOfferCondition;
  }
  /**
   * @return string
   */
  public function getHeadlineOfferCondition()
  {
    return $this->headlineOfferCondition;
  }
  /**
   * Number and amount of installments to pay for an item.
   *
   * @param HeadlineOfferInstallment $headlineOfferInstallment
   */
  public function setHeadlineOfferInstallment(HeadlineOfferInstallment $headlineOfferInstallment)
  {
    $this->headlineOfferInstallment = $headlineOfferInstallment;
  }
  /**
   * @return HeadlineOfferInstallment
   */
  public function getHeadlineOfferInstallment()
  {
    return $this->headlineOfferInstallment;
  }
  /**
   * Link to the headline offer.
   *
   * @param string $headlineOfferLink
   */
  public function setHeadlineOfferLink($headlineOfferLink)
  {
    $this->headlineOfferLink = $headlineOfferLink;
  }
  /**
   * @return string
   */
  public function getHeadlineOfferLink()
  {
    return $this->headlineOfferLink;
  }
  /**
   * Mobile Link to the headline offer.
   *
   * @param string $headlineOfferMobileLink
   */
  public function setHeadlineOfferMobileLink($headlineOfferMobileLink)
  {
    $this->headlineOfferMobileLink = $headlineOfferMobileLink;
  }
  /**
   * @return string
   */
  public function getHeadlineOfferMobileLink()
  {
    return $this->headlineOfferMobileLink;
  }
  /**
   * Headline Price of the CSS Product.
   *
   * @param Price $headlineOfferPrice
   */
  public function setHeadlineOfferPrice(Price $headlineOfferPrice)
  {
    $this->headlineOfferPrice = $headlineOfferPrice;
  }
  /**
   * @return Price
   */
  public function getHeadlineOfferPrice()
  {
    return $this->headlineOfferPrice;
  }
  /**
   * Headline Price of the CSS Product.
   *
   * @param Price $headlineOfferShippingPrice
   */
  public function setHeadlineOfferShippingPrice(Price $headlineOfferShippingPrice)
  {
    $this->headlineOfferShippingPrice = $headlineOfferShippingPrice;
  }
  /**
   * @return Price
   */
  public function getHeadlineOfferShippingPrice()
  {
    return $this->headlineOfferShippingPrice;
  }
  /**
   * Number of periods (months or years) and amount of payment per period for an
   * item with an associated subscription contract.
   *
   * @param HeadlineOfferSubscriptionCost $headlineOfferSubscriptionCost
   */
  public function setHeadlineOfferSubscriptionCost(HeadlineOfferSubscriptionCost $headlineOfferSubscriptionCost)
  {
    $this->headlineOfferSubscriptionCost = $headlineOfferSubscriptionCost;
  }
  /**
   * @return HeadlineOfferSubscriptionCost
   */
  public function getHeadlineOfferSubscriptionCost()
  {
    return $this->headlineOfferSubscriptionCost;
  }
  /**
   * High Price of the CSS Product.
   *
   * @param Price $highPrice
   */
  public function setHighPrice(Price $highPrice)
  {
    $this->highPrice = $highPrice;
  }
  /**
   * @return Price
   */
  public function getHighPrice()
  {
    return $this->highPrice;
  }
  /**
   * URL of an image of the item.
   *
   * @param string $imageLink
   */
  public function setImageLink($imageLink)
  {
    $this->imageLink = $imageLink;
  }
  /**
   * @return string
   */
  public function getImageLink()
  {
    return $this->imageLink;
  }
  /**
   * The list of destinations to include for this target (corresponds to checked
   * check boxes in Merchant Center). Default destinations are always included
   * unless provided in `excludedDestinations`.
   *
   * @param string[] $includedDestinations
   */
  public function setIncludedDestinations($includedDestinations)
  {
    $this->includedDestinations = $includedDestinations;
  }
  /**
   * @return string[]
   */
  public function getIncludedDestinations()
  {
    return $this->includedDestinations;
  }
  /**
   * Whether the item is a merchant-defined bundle. A bundle is a custom
   * grouping of different products sold by a merchant for a single price.
   *
   * @param bool $isBundle
   */
  public function setIsBundle($isBundle)
  {
    $this->isBundle = $isBundle;
  }
  /**
   * @return bool
   */
  public function getIsBundle()
  {
    return $this->isBundle;
  }
  /**
   * Shared identifier for all variants of the same product.
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
   * Low Price of the CSS Product.
   *
   * @param Price $lowPrice
   */
  public function setLowPrice(Price $lowPrice)
  {
    $this->lowPrice = $lowPrice;
  }
  /**
   * @return Price
   */
  public function getLowPrice()
  {
    return $this->lowPrice;
  }
  /**
   * The material of which the item is made.
   *
   * @param string $material
   */
  public function setMaterial($material)
  {
    $this->material = $material;
  }
  /**
   * @return string
   */
  public function getMaterial()
  {
    return $this->material;
  }
  /**
   * Manufacturer Part Number
   * ([MPN](https://support.google.com/merchants/answer/188494#mpn)) of the
   * item.
   *
   * @param string $mpn
   */
  public function setMpn($mpn)
  {
    $this->mpn = $mpn;
  }
  /**
   * @return string
   */
  public function getMpn()
  {
    return $this->mpn;
  }
  /**
   * The number of identical products in a merchant-defined multipack.
   *
   * @param string $multipack
   */
  public function setMultipack($multipack)
  {
    $this->multipack = $multipack;
  }
  /**
   * @return string
   */
  public function getMultipack()
  {
    return $this->multipack;
  }
  /**
   * The number of CSS Products.
   *
   * @param string $numberOfOffers
   */
  public function setNumberOfOffers($numberOfOffers)
  {
    $this->numberOfOffers = $numberOfOffers;
  }
  /**
   * @return string
   */
  public function getNumberOfOffers()
  {
    return $this->numberOfOffers;
  }
  /**
   * The item's pattern (e.g. polka dots).
   *
   * @param string $pattern
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  /**
   * @return string
   */
  public function getPattern()
  {
    return $this->pattern;
  }
  /**
   * Publication of this item will be temporarily paused.
   *
   * @param string $pause
   */
  public function setPause($pause)
  {
    $this->pause = $pause;
  }
  /**
   * @return string
   */
  public function getPause()
  {
    return $this->pause;
  }
  /**
   * Technical specification or additional product details.
   *
   * @param ProductDetail[] $productDetails
   */
  public function setProductDetails($productDetails)
  {
    $this->productDetails = $productDetails;
  }
  /**
   * @return ProductDetail[]
   */
  public function getProductDetails()
  {
    return $this->productDetails;
  }
  /**
   * The height of the product in the units provided. The value must be between
   * 0 (exclusive) and 3000 (inclusive).
   *
   * @param ProductDimension $productHeight
   */
  public function setProductHeight(ProductDimension $productHeight)
  {
    $this->productHeight = $productHeight;
  }
  /**
   * @return ProductDimension
   */
  public function getProductHeight()
  {
    return $this->productHeight;
  }
  /**
   * Bullet points describing the most relevant highlights of a product.
   *
   * @param string[] $productHighlights
   */
  public function setProductHighlights($productHighlights)
  {
    $this->productHighlights = $productHighlights;
  }
  /**
   * @return string[]
   */
  public function getProductHighlights()
  {
    return $this->productHighlights;
  }
  /**
   * The length of the product in the units provided. The value must be between
   * 0 (exclusive) and 3000 (inclusive).
   *
   * @param ProductDimension $productLength
   */
  public function setProductLength(ProductDimension $productLength)
  {
    $this->productLength = $productLength;
  }
  /**
   * @return ProductDimension
   */
  public function getProductLength()
  {
    return $this->productLength;
  }
  /**
   * Categories of the item (formatted as in [products data
   * specification](https://support.google.com/merchants/answer/6324406)).
   *
   * @param string[] $productTypes
   */
  public function setProductTypes($productTypes)
  {
    $this->productTypes = $productTypes;
  }
  /**
   * @return string[]
   */
  public function getProductTypes()
  {
    return $this->productTypes;
  }
  /**
   * The weight of the product in the units provided. The value must be between
   * 0 (exclusive) and 2000 (inclusive).
   *
   * @param ProductWeight $productWeight
   */
  public function setProductWeight(ProductWeight $productWeight)
  {
    $this->productWeight = $productWeight;
  }
  /**
   * @return ProductWeight
   */
  public function getProductWeight()
  {
    return $this->productWeight;
  }
  /**
   * The width of the product in the units provided. The value must be between 0
   * (exclusive) and 3000 (inclusive).
   *
   * @param ProductDimension $productWidth
   */
  public function setProductWidth(ProductDimension $productWidth)
  {
    $this->productWidth = $productWidth;
  }
  /**
   * @return ProductDimension
   */
  public function getProductWidth()
  {
    return $this->productWidth;
  }
  /**
   * Size of the item. Only one value is allowed. For variants with different
   * sizes, insert a separate product for each size with the same `itemGroupId`
   * value (see [https://support.google.com/merchants/answer/6324492](size
   * definition)).
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * System in which the size is specified. Recommended for apparel items.
   *
   * @param string $sizeSystem
   */
  public function setSizeSystem($sizeSystem)
  {
    $this->sizeSystem = $sizeSystem;
  }
  /**
   * @return string
   */
  public function getSizeSystem()
  {
    return $this->sizeSystem;
  }
  /**
   * The cut of the item. It can be used to represent combined size types for
   * apparel items. Maximum two of size types can be provided (see
   * [https://support.google.com/merchants/answer/6324497](size type)).
   *
   * @param string[] $sizeTypes
   */
  public function setSizeTypes($sizeTypes)
  {
    $this->sizeTypes = $sizeTypes;
  }
  /**
   * @return string[]
   */
  public function getSizeTypes()
  {
    return $this->sizeTypes;
  }
  /**
   * Title of the item.
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
class_alias(Attributes::class, 'Google_Service_Css_Attributes');
