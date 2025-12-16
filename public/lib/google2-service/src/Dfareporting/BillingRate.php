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

namespace Google\Service\Dfareporting;

class BillingRate extends \Google\Collection
{
  public const TYPE_AD_SERVING = 'AD_SERVING';
  public const TYPE_CLICKS = 'CLICKS';
  public const TYPE_MINIMUM_SERVICE = 'MINIMUM_SERVICE';
  public const TYPE_PATH_TO_CONVERSION = 'PATH_TO_CONVERSION';
  public const TYPE_RICH_MEDIA_INPAGE = 'RICH_MEDIA_INPAGE';
  public const TYPE_RICH_MEDIA_EXPANDING = 'RICH_MEDIA_EXPANDING';
  public const TYPE_RICH_MEDIA_FLOATING = 'RICH_MEDIA_FLOATING';
  public const TYPE_RICH_MEDIA_VIDEO = 'RICH_MEDIA_VIDEO';
  public const TYPE_RICH_MEDIA_TEASER = 'RICH_MEDIA_TEASER';
  public const TYPE_RICH_MEDIA_VPAID = 'RICH_MEDIA_VPAID';
  public const TYPE_INSTREAM_VIDEO = 'INSTREAM_VIDEO';
  public const TYPE_PIXEL = 'PIXEL';
  public const TYPE_TRACKING = 'TRACKING';
  public const TYPE_TRAFFICKING_FEATURE = 'TRAFFICKING_FEATURE';
  public const TYPE_CUSTOM_REPORTS = 'CUSTOM_REPORTS';
  public const TYPE_EXPOSURE_TO_CONVERSION = 'EXPOSURE_TO_CONVERSION';
  public const TYPE_DATA_TRANSFER = 'DATA_TRANSFER';
  public const TYPE_DATA_TRANSFER_SETUP = 'DATA_TRANSFER_SETUP';
  public const TYPE_STARTUP = 'STARTUP';
  public const TYPE_STATEMENT_OF_WORK = 'STATEMENT_OF_WORK';
  public const TYPE_PROVIDED_LIST = 'PROVIDED_LIST';
  public const TYPE_PROVIDED_LIST_SETUP = 'PROVIDED_LIST_SETUP';
  public const TYPE_ENHANCED_FORMATS = 'ENHANCED_FORMATS';
  public const TYPE_TRACKING_AD_IMPRESSIONS = 'TRACKING_AD_IMPRESSIONS';
  public const TYPE_TRACKING_AD_CLICKS = 'TRACKING_AD_CLICKS';
  public const TYPE_NIELSEN_DIGITAL_AD_RATINGS_FEE = 'NIELSEN_DIGITAL_AD_RATINGS_FEE';
  public const TYPE_INSTREAM_VIDEO_REDIRECT = 'INSTREAM_VIDEO_REDIRECT';
  public const TYPE_INSTREAM_VIDEO_VPAID = 'INSTREAM_VIDEO_VPAID';
  public const TYPE_DISPLAY_AD_SERVING = 'DISPLAY_AD_SERVING';
  public const TYPE_VIDEO_AD_SERVING = 'VIDEO_AD_SERVING';
  public const TYPE_AUDIO_AD_SERVING = 'AUDIO_AD_SERVING';
  public const TYPE_ADVANCED_DISPLAY_AD_SERVING = 'ADVANCED_DISPLAY_AD_SERVING';
  public const UNIT_OF_MEASURE_CPM = 'CPM';
  public const UNIT_OF_MEASURE_CPC = 'CPC';
  public const UNIT_OF_MEASURE_EA = 'EA';
  public const UNIT_OF_MEASURE_P2C = 'P2C';
  protected $collection_key = 'tieredRates';
  /**
   * Billing currency code in ISO 4217 format.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * End date of this billing rate.
   *
   * @var string
   */
  public $endDate;
  /**
   * ID of this billing rate.
   *
   * @var string
   */
  public $id;
  /**
   * Name of this billing rate. This must be less than 256 characters long.
   *
   * @var string
   */
  public $name;
  /**
   * Flat rate in micros of this billing rate. This cannot co-exist with tiered
   * rate.
   *
   * @var string
   */
  public $rateInMicros;
  /**
   * Start date of this billing rate.
   *
   * @var string
   */
  public $startDate;
  protected $tieredRatesType = BillingRateTieredRate::class;
  protected $tieredRatesDataType = 'array';
  /**
   * Type of this billing rate.
   *
   * @var string
   */
  public $type;
  /**
   * Unit of measure for this billing rate.
   *
   * @var string
   */
  public $unitOfMeasure;

  /**
   * Billing currency code in ISO 4217 format.
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
   * End date of this billing rate.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * ID of this billing rate.
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
   * Name of this billing rate. This must be less than 256 characters long.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Flat rate in micros of this billing rate. This cannot co-exist with tiered
   * rate.
   *
   * @param string $rateInMicros
   */
  public function setRateInMicros($rateInMicros)
  {
    $this->rateInMicros = $rateInMicros;
  }
  /**
   * @return string
   */
  public function getRateInMicros()
  {
    return $this->rateInMicros;
  }
  /**
   * Start date of this billing rate.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Tiered rate of this billing rate. This cannot co-exist with flat rate.
   *
   * @param BillingRateTieredRate[] $tieredRates
   */
  public function setTieredRates($tieredRates)
  {
    $this->tieredRates = $tieredRates;
  }
  /**
   * @return BillingRateTieredRate[]
   */
  public function getTieredRates()
  {
    return $this->tieredRates;
  }
  /**
   * Type of this billing rate.
   *
   * Accepted values: AD_SERVING, CLICKS, MINIMUM_SERVICE, PATH_TO_CONVERSION,
   * RICH_MEDIA_INPAGE, RICH_MEDIA_EXPANDING, RICH_MEDIA_FLOATING,
   * RICH_MEDIA_VIDEO, RICH_MEDIA_TEASER, RICH_MEDIA_VPAID, INSTREAM_VIDEO,
   * PIXEL, TRACKING, TRAFFICKING_FEATURE, CUSTOM_REPORTS,
   * EXPOSURE_TO_CONVERSION, DATA_TRANSFER, DATA_TRANSFER_SETUP, STARTUP,
   * STATEMENT_OF_WORK, PROVIDED_LIST, PROVIDED_LIST_SETUP, ENHANCED_FORMATS,
   * TRACKING_AD_IMPRESSIONS, TRACKING_AD_CLICKS,
   * NIELSEN_DIGITAL_AD_RATINGS_FEE, INSTREAM_VIDEO_REDIRECT,
   * INSTREAM_VIDEO_VPAID, DISPLAY_AD_SERVING, VIDEO_AD_SERVING,
   * AUDIO_AD_SERVING, ADVANCED_DISPLAY_AD_SERVING
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Unit of measure for this billing rate.
   *
   * Accepted values: CPM, CPC, EA, P2C
   *
   * @param self::UNIT_OF_MEASURE_* $unitOfMeasure
   */
  public function setUnitOfMeasure($unitOfMeasure)
  {
    $this->unitOfMeasure = $unitOfMeasure;
  }
  /**
   * @return self::UNIT_OF_MEASURE_*
   */
  public function getUnitOfMeasure()
  {
    return $this->unitOfMeasure;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BillingRate::class, 'Google_Service_Dfareporting_BillingRate');
