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

namespace Google\Service\Doubleclicksearch;

class Conversion extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const AD_USER_DATA_CONSENT_UNKNOWN = 'UNKNOWN';
  /**
   * Granted.
   */
  public const AD_USER_DATA_CONSENT_GRANTED = 'GRANTED';
  /**
   * Denied.
   */
  public const AD_USER_DATA_CONSENT_DENIED = 'DENIED';
  protected $collection_key = 'customMetric';
  /**
   * DS ad group ID.
   *
   * @var string
   */
  public $adGroupId;
  /**
   * DS ad ID.
   *
   * @var string
   */
  public $adId;
  /**
   * Represents consent for core platform services (CPS) preferences in
   * settings. No default value. Acceptable values are: GRANTED: The desired
   * consent status is to grant. Read the CPS preferences from GTE settings.
   * DENIED: The desired consent status is to deny; CPS list is empty.
   *
   * @var string
   */
  public $adUserDataConsent;
  /**
   * DS advertiser ID.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * DS agency ID.
   *
   * @var string
   */
  public $agencyId;
  /**
   * Available to advertisers only after contacting DoubleClick Search customer
   * support.
   *
   * @var string
   */
  public $attributionModel;
  /**
   * DS campaign ID.
   *
   * @var string
   */
  public $campaignId;
  /**
   * Sales channel for the product. Acceptable values are: - "`local`": a
   * physical store - "`online`": an online store
   *
   * @var string
   */
  public $channel;
  /**
   * DS click ID for the conversion.
   *
   * @var string
   */
  public $clickId;
  /**
   * For offline conversions, advertisers provide this ID. Advertisers can
   * specify any ID that is meaningful to them. Each conversion in a request
   * must specify a unique ID, and the combination of ID and timestamp must be
   * unique amongst all conversions within the advertiser. For online
   * conversions, DS copies the `dsConversionId` or `floodlightOrderId` into
   * this property depending on the advertiser's Floodlight instructions.
   *
   * @var string
   */
  public $conversionId;
  /**
   * The time at which the conversion was last modified, in epoch millis UTC.
   *
   * @var string
   */
  public $conversionModifiedTimestamp;
  /**
   * The time at which the conversion took place, in epoch millis UTC.
   *
   * @var string
   */
  public $conversionTimestamp;
  /**
   * Available to advertisers only after contacting DoubleClick Search customer
   * support.
   *
   * @var string
   */
  public $countMillis;
  /**
   * DS criterion (keyword) ID.
   *
   * @var string
   */
  public $criterionId;
  /**
   * The currency code for the conversion's revenue. Should be in ISO 4217
   * alphabetic (3-char) format.
   *
   * @var string
   */
  public $currencyCode;
  protected $customDimensionType = CustomDimension::class;
  protected $customDimensionDataType = 'array';
  protected $customMetricType = CustomMetric::class;
  protected $customMetricDataType = 'array';
  /**
   * Customer ID of a client account in the new Search Ads 360 experience.
   *
   * @var string
   */
  public $customerId;
  /**
   * The type of device on which the conversion occurred.
   *
   * @var string
   */
  public $deviceType;
  /**
   * ID that DoubleClick Search generates for each conversion.
   *
   * @var string
   */
  public $dsConversionId;
  /**
   * DS engine account ID.
   *
   * @var string
   */
  public $engineAccountId;
  /**
   * The Floodlight order ID provided by the advertiser for the conversion.
   *
   * @var string
   */
  public $floodlightOrderId;
  /**
   * ID that DS generates and uses to uniquely identify the inventory account
   * that contains the product.
   *
   * @var string
   */
  public $inventoryAccountId;
  /**
   * The country registered for the Merchant Center feed that contains the
   * product. Use an ISO 3166 code to specify a country.
   *
   * @var string
   */
  public $productCountry;
  /**
   * DS product group ID.
   *
   * @var string
   */
  public $productGroupId;
  /**
   * The product ID (SKU).
   *
   * @var string
   */
  public $productId;
  /**
   * The language registered for the Merchant Center feed that contains the
   * product. Use an ISO 639 code to specify a language.
   *
   * @var string
   */
  public $productLanguage;
  /**
   * The quantity of this conversion, in millis.
   *
   * @var string
   */
  public $quantityMillis;
  /**
   * The revenue amount of this `TRANSACTION` conversion, in micros (value
   * multiplied by 1000000, no decimal). For example, to specify a revenue value
   * of "10" enter "10000000" (10 million) in your request.
   *
   * @var string
   */
  public $revenueMicros;
  /**
   * The numeric segmentation identifier (for example, DoubleClick Search
   * Floodlight activity ID).
   *
   * @var string
   */
  public $segmentationId;
  /**
   * The friendly segmentation identifier (for example, DoubleClick Search
   * Floodlight activity name).
   *
   * @var string
   */
  public $segmentationName;
  /**
   * The segmentation type of this conversion (for example, `FLOODLIGHT`).
   *
   * @var string
   */
  public $segmentationType;
  /**
   * The state of the conversion, that is, either `ACTIVE` or `REMOVED`. Note:
   * state DELETED is deprecated.
   *
   * @var string
   */
  public $state;
  /**
   * The ID of the local store for which the product was advertised. Applicable
   * only when the channel is "`local`".
   *
   * @var string
   */
  public $storeId;
  /**
   * The type of the conversion, that is, either `ACTION` or `TRANSACTION`. An
   * `ACTION` conversion is an action by the user that has no monetarily
   * quantifiable value, while a `TRANSACTION` conversion is an action that does
   * have a monetarily quantifiable value. Examples are email list signups
   * (`ACTION`) versus ecommerce purchases (`TRANSACTION`).
   *
   * @var string
   */
  public $type;

  /**
   * DS ad group ID.
   *
   * @param string $adGroupId
   */
  public function setAdGroupId($adGroupId)
  {
    $this->adGroupId = $adGroupId;
  }
  /**
   * @return string
   */
  public function getAdGroupId()
  {
    return $this->adGroupId;
  }
  /**
   * DS ad ID.
   *
   * @param string $adId
   */
  public function setAdId($adId)
  {
    $this->adId = $adId;
  }
  /**
   * @return string
   */
  public function getAdId()
  {
    return $this->adId;
  }
  /**
   * Represents consent for core platform services (CPS) preferences in
   * settings. No default value. Acceptable values are: GRANTED: The desired
   * consent status is to grant. Read the CPS preferences from GTE settings.
   * DENIED: The desired consent status is to deny; CPS list is empty.
   *
   * Accepted values: UNKNOWN, GRANTED, DENIED
   *
   * @param self::AD_USER_DATA_CONSENT_* $adUserDataConsent
   */
  public function setAdUserDataConsent($adUserDataConsent)
  {
    $this->adUserDataConsent = $adUserDataConsent;
  }
  /**
   * @return self::AD_USER_DATA_CONSENT_*
   */
  public function getAdUserDataConsent()
  {
    return $this->adUserDataConsent;
  }
  /**
   * DS advertiser ID.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * DS agency ID.
   *
   * @param string $agencyId
   */
  public function setAgencyId($agencyId)
  {
    $this->agencyId = $agencyId;
  }
  /**
   * @return string
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }
  /**
   * Available to advertisers only after contacting DoubleClick Search customer
   * support.
   *
   * @param string $attributionModel
   */
  public function setAttributionModel($attributionModel)
  {
    $this->attributionModel = $attributionModel;
  }
  /**
   * @return string
   */
  public function getAttributionModel()
  {
    return $this->attributionModel;
  }
  /**
   * DS campaign ID.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Sales channel for the product. Acceptable values are: - "`local`": a
   * physical store - "`online`": an online store
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
   * DS click ID for the conversion.
   *
   * @param string $clickId
   */
  public function setClickId($clickId)
  {
    $this->clickId = $clickId;
  }
  /**
   * @return string
   */
  public function getClickId()
  {
    return $this->clickId;
  }
  /**
   * For offline conversions, advertisers provide this ID. Advertisers can
   * specify any ID that is meaningful to them. Each conversion in a request
   * must specify a unique ID, and the combination of ID and timestamp must be
   * unique amongst all conversions within the advertiser. For online
   * conversions, DS copies the `dsConversionId` or `floodlightOrderId` into
   * this property depending on the advertiser's Floodlight instructions.
   *
   * @param string $conversionId
   */
  public function setConversionId($conversionId)
  {
    $this->conversionId = $conversionId;
  }
  /**
   * @return string
   */
  public function getConversionId()
  {
    return $this->conversionId;
  }
  /**
   * The time at which the conversion was last modified, in epoch millis UTC.
   *
   * @param string $conversionModifiedTimestamp
   */
  public function setConversionModifiedTimestamp($conversionModifiedTimestamp)
  {
    $this->conversionModifiedTimestamp = $conversionModifiedTimestamp;
  }
  /**
   * @return string
   */
  public function getConversionModifiedTimestamp()
  {
    return $this->conversionModifiedTimestamp;
  }
  /**
   * The time at which the conversion took place, in epoch millis UTC.
   *
   * @param string $conversionTimestamp
   */
  public function setConversionTimestamp($conversionTimestamp)
  {
    $this->conversionTimestamp = $conversionTimestamp;
  }
  /**
   * @return string
   */
  public function getConversionTimestamp()
  {
    return $this->conversionTimestamp;
  }
  /**
   * Available to advertisers only after contacting DoubleClick Search customer
   * support.
   *
   * @param string $countMillis
   */
  public function setCountMillis($countMillis)
  {
    $this->countMillis = $countMillis;
  }
  /**
   * @return string
   */
  public function getCountMillis()
  {
    return $this->countMillis;
  }
  /**
   * DS criterion (keyword) ID.
   *
   * @param string $criterionId
   */
  public function setCriterionId($criterionId)
  {
    $this->criterionId = $criterionId;
  }
  /**
   * @return string
   */
  public function getCriterionId()
  {
    return $this->criterionId;
  }
  /**
   * The currency code for the conversion's revenue. Should be in ISO 4217
   * alphabetic (3-char) format.
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
   * Custom dimensions for the conversion, which can be used to filter data in a
   * report.
   *
   * @param CustomDimension[] $customDimension
   */
  public function setCustomDimension($customDimension)
  {
    $this->customDimension = $customDimension;
  }
  /**
   * @return CustomDimension[]
   */
  public function getCustomDimension()
  {
    return $this->customDimension;
  }
  /**
   * Custom metrics for the conversion.
   *
   * @param CustomMetric[] $customMetric
   */
  public function setCustomMetric($customMetric)
  {
    $this->customMetric = $customMetric;
  }
  /**
   * @return CustomMetric[]
   */
  public function getCustomMetric()
  {
    return $this->customMetric;
  }
  /**
   * Customer ID of a client account in the new Search Ads 360 experience.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * The type of device on which the conversion occurred.
   *
   * @param string $deviceType
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return string
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * ID that DoubleClick Search generates for each conversion.
   *
   * @param string $dsConversionId
   */
  public function setDsConversionId($dsConversionId)
  {
    $this->dsConversionId = $dsConversionId;
  }
  /**
   * @return string
   */
  public function getDsConversionId()
  {
    return $this->dsConversionId;
  }
  /**
   * DS engine account ID.
   *
   * @param string $engineAccountId
   */
  public function setEngineAccountId($engineAccountId)
  {
    $this->engineAccountId = $engineAccountId;
  }
  /**
   * @return string
   */
  public function getEngineAccountId()
  {
    return $this->engineAccountId;
  }
  /**
   * The Floodlight order ID provided by the advertiser for the conversion.
   *
   * @param string $floodlightOrderId
   */
  public function setFloodlightOrderId($floodlightOrderId)
  {
    $this->floodlightOrderId = $floodlightOrderId;
  }
  /**
   * @return string
   */
  public function getFloodlightOrderId()
  {
    return $this->floodlightOrderId;
  }
  /**
   * ID that DS generates and uses to uniquely identify the inventory account
   * that contains the product.
   *
   * @param string $inventoryAccountId
   */
  public function setInventoryAccountId($inventoryAccountId)
  {
    $this->inventoryAccountId = $inventoryAccountId;
  }
  /**
   * @return string
   */
  public function getInventoryAccountId()
  {
    return $this->inventoryAccountId;
  }
  /**
   * The country registered for the Merchant Center feed that contains the
   * product. Use an ISO 3166 code to specify a country.
   *
   * @param string $productCountry
   */
  public function setProductCountry($productCountry)
  {
    $this->productCountry = $productCountry;
  }
  /**
   * @return string
   */
  public function getProductCountry()
  {
    return $this->productCountry;
  }
  /**
   * DS product group ID.
   *
   * @param string $productGroupId
   */
  public function setProductGroupId($productGroupId)
  {
    $this->productGroupId = $productGroupId;
  }
  /**
   * @return string
   */
  public function getProductGroupId()
  {
    return $this->productGroupId;
  }
  /**
   * The product ID (SKU).
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The language registered for the Merchant Center feed that contains the
   * product. Use an ISO 639 code to specify a language.
   *
   * @param string $productLanguage
   */
  public function setProductLanguage($productLanguage)
  {
    $this->productLanguage = $productLanguage;
  }
  /**
   * @return string
   */
  public function getProductLanguage()
  {
    return $this->productLanguage;
  }
  /**
   * The quantity of this conversion, in millis.
   *
   * @param string $quantityMillis
   */
  public function setQuantityMillis($quantityMillis)
  {
    $this->quantityMillis = $quantityMillis;
  }
  /**
   * @return string
   */
  public function getQuantityMillis()
  {
    return $this->quantityMillis;
  }
  /**
   * The revenue amount of this `TRANSACTION` conversion, in micros (value
   * multiplied by 1000000, no decimal). For example, to specify a revenue value
   * of "10" enter "10000000" (10 million) in your request.
   *
   * @param string $revenueMicros
   */
  public function setRevenueMicros($revenueMicros)
  {
    $this->revenueMicros = $revenueMicros;
  }
  /**
   * @return string
   */
  public function getRevenueMicros()
  {
    return $this->revenueMicros;
  }
  /**
   * The numeric segmentation identifier (for example, DoubleClick Search
   * Floodlight activity ID).
   *
   * @param string $segmentationId
   */
  public function setSegmentationId($segmentationId)
  {
    $this->segmentationId = $segmentationId;
  }
  /**
   * @return string
   */
  public function getSegmentationId()
  {
    return $this->segmentationId;
  }
  /**
   * The friendly segmentation identifier (for example, DoubleClick Search
   * Floodlight activity name).
   *
   * @param string $segmentationName
   */
  public function setSegmentationName($segmentationName)
  {
    $this->segmentationName = $segmentationName;
  }
  /**
   * @return string
   */
  public function getSegmentationName()
  {
    return $this->segmentationName;
  }
  /**
   * The segmentation type of this conversion (for example, `FLOODLIGHT`).
   *
   * @param string $segmentationType
   */
  public function setSegmentationType($segmentationType)
  {
    $this->segmentationType = $segmentationType;
  }
  /**
   * @return string
   */
  public function getSegmentationType()
  {
    return $this->segmentationType;
  }
  /**
   * The state of the conversion, that is, either `ACTIVE` or `REMOVED`. Note:
   * state DELETED is deprecated.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The ID of the local store for which the product was advertised. Applicable
   * only when the channel is "`local`".
   *
   * @param string $storeId
   */
  public function setStoreId($storeId)
  {
    $this->storeId = $storeId;
  }
  /**
   * @return string
   */
  public function getStoreId()
  {
    return $this->storeId;
  }
  /**
   * The type of the conversion, that is, either `ACTION` or `TRANSACTION`. An
   * `ACTION` conversion is an action by the user that has no monetarily
   * quantifiable value, while a `TRANSACTION` conversion is an action that does
   * have a monetarily quantifiable value. Examples are email list signups
   * (`ACTION`) versus ecommerce purchases (`TRANSACTION`).
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Conversion::class, 'Google_Service_Doubleclicksearch_Conversion');
