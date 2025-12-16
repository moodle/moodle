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

class Product extends \Google\Collection
{
  protected $collection_key = 'taxes';
  /**
   * Additional URLs of images of the item.
   *
   * @var string[]
   */
  public $additionalImageLinks;
  /**
   * Additional cut of the item. Used together with size_type to represent
   * combined size types for apparel items.
   *
   * @var string
   */
  public $additionalSizeType;
  /**
   * Used to group items in an arbitrary way. Only for CPA%, discouraged
   * otherwise.
   *
   * @var string
   */
  public $adsGrouping;
  /**
   * Similar to ads_grouping, but only works on CPC.
   *
   * @var string[]
   */
  public $adsLabels;
  /**
   * Allows advertisers to override the item URL when the product is shown
   * within the context of Product Ads.
   *
   * @var string
   */
  public $adsRedirect;
  /**
   * Should be set to true if the item is targeted towards adults.
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
  protected $autoPricingMinPriceType = Price::class;
  protected $autoPricingMinPriceDataType = '';
  /**
   * Availability status of the item.
   *
   * @var string
   */
  public $availability;
  /**
   * The day a pre-ordered product becomes available for delivery, in ISO 8601
   * format.
   *
   * @var string
   */
  public $availabilityDate;
  /**
   * Brand of the item.
   *
   * @var string
   */
  public $brand;
  /**
   * URL for the canonical version of your item's landing page.
   *
   * @var string
   */
  public $canonicalLink;
  protected $certificationsType = ProductCertification::class;
  protected $certificationsDataType = 'array';
  /**
   * Required. The item's channel (online or local). Acceptable values are: -
   * "`local`" - "`online`"
   *
   * @var string
   */
  public $channel;
  protected $cloudExportAdditionalPropertiesType = CloudExportAdditionalProperties::class;
  protected $cloudExportAdditionalPropertiesDataType = 'array';
  /**
   * Color of the item.
   *
   * @var string
   */
  public $color;
  /**
   * Condition or state of the item.
   *
   * @var string
   */
  public $condition;
  /**
   * Required. The two-letter ISO 639-1 language code for the item.
   *
   * @var string
   */
  public $contentLanguage;
  protected $costOfGoodsSoldType = Price::class;
  protected $costOfGoodsSoldDataType = '';
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
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
   * The date time when an offer becomes visible in search results across
   * Google’s YouTube surfaces, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format. See [Disclosure
   * date](https://support.google.com/merchants/answer/13034208) for more
   * information.
   *
   * @var string
   */
  public $disclosureDate;
  /**
   * An identifier for an item for dynamic remarketing campaigns.
   *
   * @var string
   */
  public $displayAdsId;
  /**
   * URL directly to your item's landing page for dynamic remarketing campaigns.
   *
   * @var string
   */
  public $displayAdsLink;
  /**
   * Advertiser-specified recommendations.
   *
   * @var string[]
   */
  public $displayAdsSimilarIds;
  /**
   * Title of an item for dynamic remarketing campaigns.
   *
   * @var string
   */
  public $displayAdsTitle;
  /**
   * Offer margin for dynamic remarketing campaigns.
   *
   * @var 
   */
  public $displayAdsValue;
  /**
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @var string
   */
  public $energyEfficiencyClass;
  /**
   * The list of [destinations to
   * exclude](//support.google.com/merchants/answer/6324486) for this target
   * (corresponds to cleared check boxes in Merchant Center). Products that are
   * excluded from all destinations for more than 7 days are automatically
   * deleted.
   *
   * @var string[]
   */
  public $excludedDestinations;
  /**
   * Date on which the item should expire, as specified upon insertion, in ISO
   * 8601 format. The actual expiration date in Google Shopping is exposed in
   * `productstatuses` as `googleExpirationDate` and might be earlier if
   * `expirationDate` is too far in the future.
   *
   * @var string
   */
  public $expirationDate;
  /**
   * Required for multi-seller accounts. Use this attribute if you're a
   * marketplace uploading products for various sellers to your multi-seller
   * account.
   *
   * @var string
   */
  public $externalSellerId;
  /**
   * Feed label for the item. Either `targetCountry` or `feedLabel` is required.
   * Must be less than or equal to 20 uppercase letters (A-Z), numbers (0-9),
   * and dashes (-).
   *
   * @var string
   */
  public $feedLabel;
  protected $freeShippingThresholdType = FreeShippingThreshold::class;
  protected $freeShippingThresholdDataType = 'array';
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
   * Global Trade Item Number (GTIN) of the item.
   *
   * @var string
   */
  public $gtin;
  /**
   * The REST ID of the product. Content API methods that operate on products
   * take this as their `productId` parameter. The REST ID for a product has one
   * of the 2 forms channel:contentLanguage: targetCountry: offerId or
   * channel:contentLanguage:feedLabel: offerId.
   *
   * @var string
   */
  public $id;
  /**
   * False when the item does not have unique product identifiers appropriate to
   * its category, such as GTIN, MPN, and brand. Required according to the
   * Unique Product Identifier Rules for all target countries except for Canada.
   *
   * @var bool
   */
  public $identifierExists;
  /**
   * URL of an image of the item.
   *
   * @var string
   */
  public $imageLink;
  /**
   * The list of [destinations to
   * include](//support.google.com/merchants/answer/7501026) for this target
   * (corresponds to checked check boxes in Merchant Center). Default
   * destinations are always included unless provided in `excludedDestinations`.
   *
   * @var string[]
   */
  public $includedDestinations;
  protected $installmentType = Installment::class;
  protected $installmentDataType = '';
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
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#product`"
   *
   * @var string
   */
  public $kind;
  /**
   * Additional URLs of lifestyle images of the item. Used to explicitly
   * identify images that showcase your item in a real-world context. See the
   * Help Center article for more information.
   *
   * @var string[]
   */
  public $lifestyleImageLinks;
  /**
   * URL directly linking to your item's page on your website.
   *
   * @var string
   */
  public $link;
  /**
   * URL template for merchant hosted local storefront.
   *
   * @var string
   */
  public $linkTemplate;
  protected $loyaltyProgramType = LoyaltyProgram::class;
  protected $loyaltyProgramDataType = '';
  protected $loyaltyProgramsType = LoyaltyProgram::class;
  protected $loyaltyProgramsDataType = 'array';
  /**
   * The material of which the item is made.
   *
   * @var string
   */
  public $material;
  /**
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @var string
   */
  public $maxEnergyEfficiencyClass;
  /**
   * Maximal product handling time (in business days).
   *
   * @var string
   */
  public $maxHandlingTime;
  protected $maximumRetailPriceType = Price::class;
  protected $maximumRetailPriceDataType = '';
  /**
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @var string
   */
  public $minEnergyEfficiencyClass;
  /**
   * Minimal product handling time (in business days).
   *
   * @var string
   */
  public $minHandlingTime;
  /**
   * URL for the mobile-optimized version of your item's landing page.
   *
   * @var string
   */
  public $mobileLink;
  /**
   * URL template for merchant hosted local storefront optimized for mobile
   * devices.
   *
   * @var string
   */
  public $mobileLinkTemplate;
  /**
   * Manufacturer Part Number (MPN) of the item.
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
   * Required. A unique identifier for the item. Leading and trailing
   * whitespaces are stripped and multiple whitespaces are replaced by a single
   * whitespace upon submission. Only valid unicode characters are accepted. See
   * the products feed specification for details. *Note:* Content API methods
   * that operate on products take the REST ID of the product, *not* this
   * identifier.
   *
   * @var string
   */
  public $offerId;
  /**
   * The item's pattern (for example, polka dots).
   *
   * @var string
   */
  public $pattern;
  /**
   * Publication of this item should be temporarily paused. Acceptable values
   * are: - "`ads`"
   *
   * @var string
   */
  public $pause;
  /**
   * The pick up option for the item. Acceptable values are: - "`buy`" -
   * "`reserve`" - "`ship to store`" - "`not supported`"
   *
   * @var string
   */
  public $pickupMethod;
  /**
   * Item store pickup timeline. Acceptable values are: - "`same day`" - "`next
   * day`" - "`2-day`" - "`3-day`" - "`4-day`" - "`5-day`" - "`6-day`" -
   * "`7-day`" - "`multi-week`"
   *
   * @var string
   */
  public $pickupSla;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  protected $productDetailsType = ProductProductDetail::class;
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
   * Categories of the item (formatted as in product data specification).
   *
   * @var string[]
   */
  public $productTypes;
  protected $productWeightType = ProductWeight::class;
  protected $productWeightDataType = '';
  protected $productWidthType = ProductDimension::class;
  protected $productWidthDataType = '';
  /**
   * The unique ID of a promotion.
   *
   * @var string[]
   */
  public $promotionIds;
  protected $salePriceType = Price::class;
  protected $salePriceDataType = '';
  /**
   * Date range during which the item is on sale (see product data specification
   * ).
   *
   * @var string
   */
  public $salePriceEffectiveDate;
  /**
   * The quantity of the product that is available for selling on Google.
   * Supported only for online products.
   *
   * @var string
   */
  public $sellOnGoogleQuantity;
  protected $shippingType = ProductShipping::class;
  protected $shippingDataType = 'array';
  protected $shippingHeightType = ProductShippingDimension::class;
  protected $shippingHeightDataType = '';
  /**
   * The shipping label of the product, used to group product in account-level
   * shipping rules.
   *
   * @var string
   */
  public $shippingLabel;
  protected $shippingLengthType = ProductShippingDimension::class;
  protected $shippingLengthDataType = '';
  protected $shippingWeightType = ProductShippingWeight::class;
  protected $shippingWeightDataType = '';
  protected $shippingWidthType = ProductShippingDimension::class;
  protected $shippingWidthDataType = '';
  /**
   * List of country codes (ISO 3166-1 alpha-2) to exclude the offer from
   * Shopping Ads destination. Countries from this list are removed from
   * countries configured in MC feed settings.
   *
   * @var string[]
   */
  public $shoppingAdsExcludedCountries;
  /**
   * System in which the size is specified. Recommended for apparel items.
   *
   * @var string
   */
  public $sizeSystem;
  /**
   * The cut of the item. Recommended for apparel items.
   *
   * @var string
   */
  public $sizeType;
  /**
   * Size of the item. Only one value is allowed. For variants with different
   * sizes, insert a separate product for each size with the same `itemGroupId`
   * value (see size definition).
   *
   * @var string[]
   */
  public $sizes;
  /**
   * Output only. The source of the offer, that is, how the offer was created.
   * Acceptable values are: - "`api`" - "`crawl`" - "`feed`"
   *
   * @var string
   */
  public $source;
  protected $structuredDescriptionType = ProductStructuredDescription::class;
  protected $structuredDescriptionDataType = '';
  protected $structuredTitleType = ProductStructuredTitle::class;
  protected $structuredTitleDataType = '';
  protected $subscriptionCostType = ProductSubscriptionCost::class;
  protected $subscriptionCostDataType = '';
  protected $sustainabilityIncentivesType = ProductSustainabilityIncentive::class;
  protected $sustainabilityIncentivesDataType = 'array';
  /**
   * Required. The CLDR territory code for the item's country of sale.
   *
   * @var string
   */
  public $targetCountry;
  /**
   * The tax category of the product, used to configure detailed tax nexus in
   * account-level tax settings.
   *
   * @var string
   */
  public $taxCategory;
  protected $taxesType = ProductTax::class;
  protected $taxesDataType = 'array';
  /**
   * Title of the item.
   *
   * @var string
   */
  public $title;
  /**
   * The transit time label of the product, used to group product in account-
   * level transit time tables.
   *
   * @var string
   */
  public $transitTimeLabel;
  protected $unitPricingBaseMeasureType = ProductUnitPricingBaseMeasure::class;
  protected $unitPricingBaseMeasureDataType = '';
  protected $unitPricingMeasureType = ProductUnitPricingMeasure::class;
  protected $unitPricingMeasureDataType = '';
  /**
   * URL of the 3D model of the item to provide more visuals.
   *
   * @var string
   */
  public $virtualModelLink;

  /**
   * Additional URLs of images of the item.
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
   * Additional cut of the item. Used together with size_type to represent
   * combined size types for apparel items.
   *
   * @param string $additionalSizeType
   */
  public function setAdditionalSizeType($additionalSizeType)
  {
    $this->additionalSizeType = $additionalSizeType;
  }
  /**
   * @return string
   */
  public function getAdditionalSizeType()
  {
    return $this->additionalSizeType;
  }
  /**
   * Used to group items in an arbitrary way. Only for CPA%, discouraged
   * otherwise.
   *
   * @param string $adsGrouping
   */
  public function setAdsGrouping($adsGrouping)
  {
    $this->adsGrouping = $adsGrouping;
  }
  /**
   * @return string
   */
  public function getAdsGrouping()
  {
    return $this->adsGrouping;
  }
  /**
   * Similar to ads_grouping, but only works on CPC.
   *
   * @param string[] $adsLabels
   */
  public function setAdsLabels($adsLabels)
  {
    $this->adsLabels = $adsLabels;
  }
  /**
   * @return string[]
   */
  public function getAdsLabels()
  {
    return $this->adsLabels;
  }
  /**
   * Allows advertisers to override the item URL when the product is shown
   * within the context of Product Ads.
   *
   * @param string $adsRedirect
   */
  public function setAdsRedirect($adsRedirect)
  {
    $this->adsRedirect = $adsRedirect;
  }
  /**
   * @return string
   */
  public function getAdsRedirect()
  {
    return $this->adsRedirect;
  }
  /**
   * Should be set to true if the item is targeted towards adults.
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
   * A safeguard in the [Automated
   * Discounts](//support.google.com/merchants/answer/10295759) and [Dynamic
   * Promotions](//support.google.com/merchants/answer/13949249) projects,
   * ensuring that discounts on merchants' offers do not fall below this value,
   * thereby preserving the offer's value and profitability.
   *
   * @param Price $autoPricingMinPrice
   */
  public function setAutoPricingMinPrice(Price $autoPricingMinPrice)
  {
    $this->autoPricingMinPrice = $autoPricingMinPrice;
  }
  /**
   * @return Price
   */
  public function getAutoPricingMinPrice()
  {
    return $this->autoPricingMinPrice;
  }
  /**
   * Availability status of the item.
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
   * The day a pre-ordered product becomes available for delivery, in ISO 8601
   * format.
   *
   * @param string $availabilityDate
   */
  public function setAvailabilityDate($availabilityDate)
  {
    $this->availabilityDate = $availabilityDate;
  }
  /**
   * @return string
   */
  public function getAvailabilityDate()
  {
    return $this->availabilityDate;
  }
  /**
   * Brand of the item.
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
   * URL for the canonical version of your item's landing page.
   *
   * @param string $canonicalLink
   */
  public function setCanonicalLink($canonicalLink)
  {
    $this->canonicalLink = $canonicalLink;
  }
  /**
   * @return string
   */
  public function getCanonicalLink()
  {
    return $this->canonicalLink;
  }
  /**
   * Product
   * [certification](https://support.google.com/merchants/answer/13528839),
   * introduced for EU energy efficiency labeling compliance using the [EU
   * EPREL](https://eprel.ec.europa.eu/screen/home) database.
   *
   * @param ProductCertification[] $certifications
   */
  public function setCertifications($certifications)
  {
    $this->certifications = $certifications;
  }
  /**
   * @return ProductCertification[]
   */
  public function getCertifications()
  {
    return $this->certifications;
  }
  /**
   * Required. The item's channel (online or local). Acceptable values are: -
   * "`local`" - "`online`"
   *
   * @param string $channel
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  /**
   * @return string
   */
  public function getChannel()
  {
    return $this->channel;
  }
  /**
   * Extra fields to export to the Cloud Retail program.
   *
   * @param CloudExportAdditionalProperties[] $cloudExportAdditionalProperties
   */
  public function setCloudExportAdditionalProperties($cloudExportAdditionalProperties)
  {
    $this->cloudExportAdditionalProperties = $cloudExportAdditionalProperties;
  }
  /**
   * @return CloudExportAdditionalProperties[]
   */
  public function getCloudExportAdditionalProperties()
  {
    return $this->cloudExportAdditionalProperties;
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
   * Condition or state of the item.
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
   * Required. The two-letter ISO 639-1 language code for the item.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * Cost of goods sold. Used for gross profit reporting.
   *
   * @param Price $costOfGoodsSold
   */
  public function setCostOfGoodsSold(Price $costOfGoodsSold)
  {
    $this->costOfGoodsSold = $costOfGoodsSold;
  }
  /**
   * @return Price
   */
  public function getCostOfGoodsSold()
  {
    return $this->costOfGoodsSold;
  }
  /**
   * A list of custom (merchant-provided) attributes. It can also be used for
   * submitting any attribute of the feed specification in its generic form (for
   * example, `{ "name": "size type", "value": "regular" }`). This is useful for
   * submitting attributes not explicitly exposed by the API, such as additional
   * attributes used for Buy on Google (formerly known as Shopping Actions).
   *
   * @param CustomAttribute[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return CustomAttribute[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
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
   * The date time when an offer becomes visible in search results across
   * Google’s YouTube surfaces, in [ISO
   * 8601](http://en.wikipedia.org/wiki/ISO_8601) format. See [Disclosure
   * date](https://support.google.com/merchants/answer/13034208) for more
   * information.
   *
   * @param string $disclosureDate
   */
  public function setDisclosureDate($disclosureDate)
  {
    $this->disclosureDate = $disclosureDate;
  }
  /**
   * @return string
   */
  public function getDisclosureDate()
  {
    return $this->disclosureDate;
  }
  /**
   * An identifier for an item for dynamic remarketing campaigns.
   *
   * @param string $displayAdsId
   */
  public function setDisplayAdsId($displayAdsId)
  {
    $this->displayAdsId = $displayAdsId;
  }
  /**
   * @return string
   */
  public function getDisplayAdsId()
  {
    return $this->displayAdsId;
  }
  /**
   * URL directly to your item's landing page for dynamic remarketing campaigns.
   *
   * @param string $displayAdsLink
   */
  public function setDisplayAdsLink($displayAdsLink)
  {
    $this->displayAdsLink = $displayAdsLink;
  }
  /**
   * @return string
   */
  public function getDisplayAdsLink()
  {
    return $this->displayAdsLink;
  }
  /**
   * Advertiser-specified recommendations.
   *
   * @param string[] $displayAdsSimilarIds
   */
  public function setDisplayAdsSimilarIds($displayAdsSimilarIds)
  {
    $this->displayAdsSimilarIds = $displayAdsSimilarIds;
  }
  /**
   * @return string[]
   */
  public function getDisplayAdsSimilarIds()
  {
    return $this->displayAdsSimilarIds;
  }
  /**
   * Title of an item for dynamic remarketing campaigns.
   *
   * @param string $displayAdsTitle
   */
  public function setDisplayAdsTitle($displayAdsTitle)
  {
    $this->displayAdsTitle = $displayAdsTitle;
  }
  /**
   * @return string
   */
  public function getDisplayAdsTitle()
  {
    return $this->displayAdsTitle;
  }
  public function setDisplayAdsValue($displayAdsValue)
  {
    $this->displayAdsValue = $displayAdsValue;
  }
  public function getDisplayAdsValue()
  {
    return $this->displayAdsValue;
  }
  /**
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @param string $energyEfficiencyClass
   */
  public function setEnergyEfficiencyClass($energyEfficiencyClass)
  {
    $this->energyEfficiencyClass = $energyEfficiencyClass;
  }
  /**
   * @return string
   */
  public function getEnergyEfficiencyClass()
  {
    return $this->energyEfficiencyClass;
  }
  /**
   * The list of [destinations to
   * exclude](//support.google.com/merchants/answer/6324486) for this target
   * (corresponds to cleared check boxes in Merchant Center). Products that are
   * excluded from all destinations for more than 7 days are automatically
   * deleted.
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
   * Date on which the item should expire, as specified upon insertion, in ISO
   * 8601 format. The actual expiration date in Google Shopping is exposed in
   * `productstatuses` as `googleExpirationDate` and might be earlier if
   * `expirationDate` is too far in the future.
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
   * Required for multi-seller accounts. Use this attribute if you're a
   * marketplace uploading products for various sellers to your multi-seller
   * account.
   *
   * @param string $externalSellerId
   */
  public function setExternalSellerId($externalSellerId)
  {
    $this->externalSellerId = $externalSellerId;
  }
  /**
   * @return string
   */
  public function getExternalSellerId()
  {
    return $this->externalSellerId;
  }
  /**
   * Feed label for the item. Either `targetCountry` or `feedLabel` is required.
   * Must be less than or equal to 20 uppercase letters (A-Z), numbers (0-9),
   * and dashes (-).
   *
   * @param string $feedLabel
   */
  public function setFeedLabel($feedLabel)
  {
    $this->feedLabel = $feedLabel;
  }
  /**
   * @return string
   */
  public function getFeedLabel()
  {
    return $this->feedLabel;
  }
  /**
   * Optional. Conditions to be met for a product to have free shipping.
   *
   * @param FreeShippingThreshold[] $freeShippingThreshold
   */
  public function setFreeShippingThreshold($freeShippingThreshold)
  {
    $this->freeShippingThreshold = $freeShippingThreshold;
  }
  /**
   * @return FreeShippingThreshold[]
   */
  public function getFreeShippingThreshold()
  {
    return $this->freeShippingThreshold;
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
   * Global Trade Item Number (GTIN) of the item.
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
   * The REST ID of the product. Content API methods that operate on products
   * take this as their `productId` parameter. The REST ID for a product has one
   * of the 2 forms channel:contentLanguage: targetCountry: offerId or
   * channel:contentLanguage:feedLabel: offerId.
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
   * False when the item does not have unique product identifiers appropriate to
   * its category, such as GTIN, MPN, and brand. Required according to the
   * Unique Product Identifier Rules for all target countries except for Canada.
   *
   * @param bool $identifierExists
   */
  public function setIdentifierExists($identifierExists)
  {
    $this->identifierExists = $identifierExists;
  }
  /**
   * @return bool
   */
  public function getIdentifierExists()
  {
    return $this->identifierExists;
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
   * The list of [destinations to
   * include](//support.google.com/merchants/answer/7501026) for this target
   * (corresponds to checked check boxes in Merchant Center). Default
   * destinations are always included unless provided in `excludedDestinations`.
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
   * Number and amount of installments to pay for an item.
   *
   * @param Installment $installment
   */
  public function setInstallment(Installment $installment)
  {
    $this->installment = $installment;
  }
  /**
   * @return Installment
   */
  public function getInstallment()
  {
    return $this->installment;
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#product`"
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Additional URLs of lifestyle images of the item. Used to explicitly
   * identify images that showcase your item in a real-world context. See the
   * Help Center article for more information.
   *
   * @param string[] $lifestyleImageLinks
   */
  public function setLifestyleImageLinks($lifestyleImageLinks)
  {
    $this->lifestyleImageLinks = $lifestyleImageLinks;
  }
  /**
   * @return string[]
   */
  public function getLifestyleImageLinks()
  {
    return $this->lifestyleImageLinks;
  }
  /**
   * URL directly linking to your item's page on your website.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * URL template for merchant hosted local storefront.
   *
   * @param string $linkTemplate
   */
  public function setLinkTemplate($linkTemplate)
  {
    $this->linkTemplate = $linkTemplate;
  }
  /**
   * @return string
   */
  public function getLinkTemplate()
  {
    return $this->linkTemplate;
  }
  /**
   * Loyalty program information that is used to surface loyalty benefits ( for
   * example, better pricing, points, etc) to the user of this item. This
   * signular field points to the latest uploaded loyalty program info. This
   * field will be deprecated in the coming weeks and should not be used in
   * favor of the plural 'LoyaltyProgram' field below.
   *
   * @param LoyaltyProgram $loyaltyProgram
   */
  public function setLoyaltyProgram(LoyaltyProgram $loyaltyProgram)
  {
    $this->loyaltyProgram = $loyaltyProgram;
  }
  /**
   * @return LoyaltyProgram
   */
  public function getLoyaltyProgram()
  {
    return $this->loyaltyProgram;
  }
  /**
   * Optional. A list of loyalty program information that is used to surface
   * loyalty benefits (for example, better pricing, points, etc) to the user of
   * this item.
   *
   * @param LoyaltyProgram[] $loyaltyPrograms
   */
  public function setLoyaltyPrograms($loyaltyPrograms)
  {
    $this->loyaltyPrograms = $loyaltyPrograms;
  }
  /**
   * @return LoyaltyProgram[]
   */
  public function getLoyaltyPrograms()
  {
    return $this->loyaltyPrograms;
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
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @param string $maxEnergyEfficiencyClass
   */
  public function setMaxEnergyEfficiencyClass($maxEnergyEfficiencyClass)
  {
    $this->maxEnergyEfficiencyClass = $maxEnergyEfficiencyClass;
  }
  /**
   * @return string
   */
  public function getMaxEnergyEfficiencyClass()
  {
    return $this->maxEnergyEfficiencyClass;
  }
  /**
   * Maximal product handling time (in business days).
   *
   * @param string $maxHandlingTime
   */
  public function setMaxHandlingTime($maxHandlingTime)
  {
    $this->maxHandlingTime = $maxHandlingTime;
  }
  /**
   * @return string
   */
  public function getMaxHandlingTime()
  {
    return $this->maxHandlingTime;
  }
  /**
   * Maximum retail price (MRP) of the item. Applicable to India only.
   *
   * @param Price $maximumRetailPrice
   */
  public function setMaximumRetailPrice(Price $maximumRetailPrice)
  {
    $this->maximumRetailPrice = $maximumRetailPrice;
  }
  /**
   * @return Price
   */
  public function getMaximumRetailPrice()
  {
    return $this->maximumRetailPrice;
  }
  /**
   * The energy efficiency class as defined in EU directive 2010/30/EU.
   *
   * @param string $minEnergyEfficiencyClass
   */
  public function setMinEnergyEfficiencyClass($minEnergyEfficiencyClass)
  {
    $this->minEnergyEfficiencyClass = $minEnergyEfficiencyClass;
  }
  /**
   * @return string
   */
  public function getMinEnergyEfficiencyClass()
  {
    return $this->minEnergyEfficiencyClass;
  }
  /**
   * Minimal product handling time (in business days).
   *
   * @param string $minHandlingTime
   */
  public function setMinHandlingTime($minHandlingTime)
  {
    $this->minHandlingTime = $minHandlingTime;
  }
  /**
   * @return string
   */
  public function getMinHandlingTime()
  {
    return $this->minHandlingTime;
  }
  /**
   * URL for the mobile-optimized version of your item's landing page.
   *
   * @param string $mobileLink
   */
  public function setMobileLink($mobileLink)
  {
    $this->mobileLink = $mobileLink;
  }
  /**
   * @return string
   */
  public function getMobileLink()
  {
    return $this->mobileLink;
  }
  /**
   * URL template for merchant hosted local storefront optimized for mobile
   * devices.
   *
   * @param string $mobileLinkTemplate
   */
  public function setMobileLinkTemplate($mobileLinkTemplate)
  {
    $this->mobileLinkTemplate = $mobileLinkTemplate;
  }
  /**
   * @return string
   */
  public function getMobileLinkTemplate()
  {
    return $this->mobileLinkTemplate;
  }
  /**
   * Manufacturer Part Number (MPN) of the item.
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
   * Required. A unique identifier for the item. Leading and trailing
   * whitespaces are stripped and multiple whitespaces are replaced by a single
   * whitespace upon submission. Only valid unicode characters are accepted. See
   * the products feed specification for details. *Note:* Content API methods
   * that operate on products take the REST ID of the product, *not* this
   * identifier.
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
   * The item's pattern (for example, polka dots).
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
   * Publication of this item should be temporarily paused. Acceptable values
   * are: - "`ads`"
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
   * The pick up option for the item. Acceptable values are: - "`buy`" -
   * "`reserve`" - "`ship to store`" - "`not supported`"
   *
   * @param string $pickupMethod
   */
  public function setPickupMethod($pickupMethod)
  {
    $this->pickupMethod = $pickupMethod;
  }
  /**
   * @return string
   */
  public function getPickupMethod()
  {
    return $this->pickupMethod;
  }
  /**
   * Item store pickup timeline. Acceptable values are: - "`same day`" - "`next
   * day`" - "`2-day`" - "`3-day`" - "`4-day`" - "`5-day`" - "`6-day`" -
   * "`7-day`" - "`multi-week`"
   *
   * @param string $pickupSla
   */
  public function setPickupSla($pickupSla)
  {
    $this->pickupSla = $pickupSla;
  }
  /**
   * @return string
   */
  public function getPickupSla()
  {
    return $this->pickupSla;
  }
  /**
   * Price of the item.
   *
   * @param Price $price
   */
  public function setPrice(Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * Technical specification or additional product details.
   *
   * @param ProductProductDetail[] $productDetails
   */
  public function setProductDetails($productDetails)
  {
    $this->productDetails = $productDetails;
  }
  /**
   * @return ProductProductDetail[]
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
   * Categories of the item (formatted as in product data specification).
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
   * The unique ID of a promotion.
   *
   * @param string[] $promotionIds
   */
  public function setPromotionIds($promotionIds)
  {
    $this->promotionIds = $promotionIds;
  }
  /**
   * @return string[]
   */
  public function getPromotionIds()
  {
    return $this->promotionIds;
  }
  /**
   * Advertised sale price of the item.
   *
   * @param Price $salePrice
   */
  public function setSalePrice(Price $salePrice)
  {
    $this->salePrice = $salePrice;
  }
  /**
   * @return Price
   */
  public function getSalePrice()
  {
    return $this->salePrice;
  }
  /**
   * Date range during which the item is on sale (see product data specification
   * ).
   *
   * @param string $salePriceEffectiveDate
   */
  public function setSalePriceEffectiveDate($salePriceEffectiveDate)
  {
    $this->salePriceEffectiveDate = $salePriceEffectiveDate;
  }
  /**
   * @return string
   */
  public function getSalePriceEffectiveDate()
  {
    return $this->salePriceEffectiveDate;
  }
  /**
   * The quantity of the product that is available for selling on Google.
   * Supported only for online products.
   *
   * @param string $sellOnGoogleQuantity
   */
  public function setSellOnGoogleQuantity($sellOnGoogleQuantity)
  {
    $this->sellOnGoogleQuantity = $sellOnGoogleQuantity;
  }
  /**
   * @return string
   */
  public function getSellOnGoogleQuantity()
  {
    return $this->sellOnGoogleQuantity;
  }
  /**
   * Shipping rules.
   *
   * @param ProductShipping[] $shipping
   */
  public function setShipping($shipping)
  {
    $this->shipping = $shipping;
  }
  /**
   * @return ProductShipping[]
   */
  public function getShipping()
  {
    return $this->shipping;
  }
  /**
   * Height of the item for shipping.
   *
   * @param ProductShippingDimension $shippingHeight
   */
  public function setShippingHeight(ProductShippingDimension $shippingHeight)
  {
    $this->shippingHeight = $shippingHeight;
  }
  /**
   * @return ProductShippingDimension
   */
  public function getShippingHeight()
  {
    return $this->shippingHeight;
  }
  /**
   * The shipping label of the product, used to group product in account-level
   * shipping rules.
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
   * Length of the item for shipping.
   *
   * @param ProductShippingDimension $shippingLength
   */
  public function setShippingLength(ProductShippingDimension $shippingLength)
  {
    $this->shippingLength = $shippingLength;
  }
  /**
   * @return ProductShippingDimension
   */
  public function getShippingLength()
  {
    return $this->shippingLength;
  }
  /**
   * Weight of the item for shipping.
   *
   * @param ProductShippingWeight $shippingWeight
   */
  public function setShippingWeight(ProductShippingWeight $shippingWeight)
  {
    $this->shippingWeight = $shippingWeight;
  }
  /**
   * @return ProductShippingWeight
   */
  public function getShippingWeight()
  {
    return $this->shippingWeight;
  }
  /**
   * Width of the item for shipping.
   *
   * @param ProductShippingDimension $shippingWidth
   */
  public function setShippingWidth(ProductShippingDimension $shippingWidth)
  {
    $this->shippingWidth = $shippingWidth;
  }
  /**
   * @return ProductShippingDimension
   */
  public function getShippingWidth()
  {
    return $this->shippingWidth;
  }
  /**
   * List of country codes (ISO 3166-1 alpha-2) to exclude the offer from
   * Shopping Ads destination. Countries from this list are removed from
   * countries configured in MC feed settings.
   *
   * @param string[] $shoppingAdsExcludedCountries
   */
  public function setShoppingAdsExcludedCountries($shoppingAdsExcludedCountries)
  {
    $this->shoppingAdsExcludedCountries = $shoppingAdsExcludedCountries;
  }
  /**
   * @return string[]
   */
  public function getShoppingAdsExcludedCountries()
  {
    return $this->shoppingAdsExcludedCountries;
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
   * The cut of the item. Recommended for apparel items.
   *
   * @param string $sizeType
   */
  public function setSizeType($sizeType)
  {
    $this->sizeType = $sizeType;
  }
  /**
   * @return string
   */
  public function getSizeType()
  {
    return $this->sizeType;
  }
  /**
   * Size of the item. Only one value is allowed. For variants with different
   * sizes, insert a separate product for each size with the same `itemGroupId`
   * value (see size definition).
   *
   * @param string[] $sizes
   */
  public function setSizes($sizes)
  {
    $this->sizes = $sizes;
  }
  /**
   * @return string[]
   */
  public function getSizes()
  {
    return $this->sizes;
  }
  /**
   * Output only. The source of the offer, that is, how the offer was created.
   * Acceptable values are: - "`api`" - "`crawl`" - "`feed`"
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Structured description, for algorithmically (AI)-generated descriptions.
   *
   * @param ProductStructuredDescription $structuredDescription
   */
  public function setStructuredDescription(ProductStructuredDescription $structuredDescription)
  {
    $this->structuredDescription = $structuredDescription;
  }
  /**
   * @return ProductStructuredDescription
   */
  public function getStructuredDescription()
  {
    return $this->structuredDescription;
  }
  /**
   * Structured title, for algorithmically (AI)-generated titles.
   *
   * @param ProductStructuredTitle $structuredTitle
   */
  public function setStructuredTitle(ProductStructuredTitle $structuredTitle)
  {
    $this->structuredTitle = $structuredTitle;
  }
  /**
   * @return ProductStructuredTitle
   */
  public function getStructuredTitle()
  {
    return $this->structuredTitle;
  }
  /**
   * Number of periods (months or years) and amount of payment per period for an
   * item with an associated subscription contract.
   *
   * @param ProductSubscriptionCost $subscriptionCost
   */
  public function setSubscriptionCost(ProductSubscriptionCost $subscriptionCost)
  {
    $this->subscriptionCost = $subscriptionCost;
  }
  /**
   * @return ProductSubscriptionCost
   */
  public function getSubscriptionCost()
  {
    return $this->subscriptionCost;
  }
  /**
   * Optional. The list of sustainability incentive programs.
   *
   * @param ProductSustainabilityIncentive[] $sustainabilityIncentives
   */
  public function setSustainabilityIncentives($sustainabilityIncentives)
  {
    $this->sustainabilityIncentives = $sustainabilityIncentives;
  }
  /**
   * @return ProductSustainabilityIncentive[]
   */
  public function getSustainabilityIncentives()
  {
    return $this->sustainabilityIncentives;
  }
  /**
   * Required. The CLDR territory code for the item's country of sale.
   *
   * @param string $targetCountry
   */
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  /**
   * @return string
   */
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
  /**
   * The tax category of the product, used to configure detailed tax nexus in
   * account-level tax settings.
   *
   * @param string $taxCategory
   */
  public function setTaxCategory($taxCategory)
  {
    $this->taxCategory = $taxCategory;
  }
  /**
   * @return string
   */
  public function getTaxCategory()
  {
    return $this->taxCategory;
  }
  /**
   * Tax information.
   *
   * @param ProductTax[] $taxes
   */
  public function setTaxes($taxes)
  {
    $this->taxes = $taxes;
  }
  /**
   * @return ProductTax[]
   */
  public function getTaxes()
  {
    return $this->taxes;
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
  /**
   * The transit time label of the product, used to group product in account-
   * level transit time tables.
   *
   * @param string $transitTimeLabel
   */
  public function setTransitTimeLabel($transitTimeLabel)
  {
    $this->transitTimeLabel = $transitTimeLabel;
  }
  /**
   * @return string
   */
  public function getTransitTimeLabel()
  {
    return $this->transitTimeLabel;
  }
  /**
   * The preference of the denominator of the unit price.
   *
   * @param ProductUnitPricingBaseMeasure $unitPricingBaseMeasure
   */
  public function setUnitPricingBaseMeasure(ProductUnitPricingBaseMeasure $unitPricingBaseMeasure)
  {
    $this->unitPricingBaseMeasure = $unitPricingBaseMeasure;
  }
  /**
   * @return ProductUnitPricingBaseMeasure
   */
  public function getUnitPricingBaseMeasure()
  {
    return $this->unitPricingBaseMeasure;
  }
  /**
   * The measure and dimension of an item.
   *
   * @param ProductUnitPricingMeasure $unitPricingMeasure
   */
  public function setUnitPricingMeasure(ProductUnitPricingMeasure $unitPricingMeasure)
  {
    $this->unitPricingMeasure = $unitPricingMeasure;
  }
  /**
   * @return ProductUnitPricingMeasure
   */
  public function getUnitPricingMeasure()
  {
    return $this->unitPricingMeasure;
  }
  /**
   * URL of the 3D model of the item to provide more visuals.
   *
   * @param string $virtualModelLink
   */
  public function setVirtualModelLink($virtualModelLink)
  {
    $this->virtualModelLink = $virtualModelLink;
  }
  /**
   * @return string
   */
  public function getVirtualModelLink()
  {
    return $this->virtualModelLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Product::class, 'Google_Service_ShoppingContent_Product');
