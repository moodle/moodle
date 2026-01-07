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

namespace Google\Service\AdExchangeBuyerII;

class DealTerms extends \Google\Model
{
  /**
   * A placeholder for an undefined branding type.
   */
  public const BRANDING_TYPE_BRANDING_TYPE_UNSPECIFIED = 'BRANDING_TYPE_UNSPECIFIED';
  /**
   * Full URL is included in bid requests.
   */
  public const BRANDING_TYPE_BRANDED = 'BRANDED';
  /**
   * A TopLevelDomain or masked URL is sent in bid requests rather than the full
   * one.
   */
  public const BRANDING_TYPE_SEMI_TRANSPARENT = 'SEMI_TRANSPARENT';
  /**
   * Visibility of the URL in bid requests. (default: BRANDED)
   *
   * @var string
   */
  public $brandingType;
  /**
   * Publisher provided description for the terms.
   *
   * @var string
   */
  public $description;
  protected $estimatedGrossSpendType = Price::class;
  protected $estimatedGrossSpendDataType = '';
  /**
   * Non-binding estimate of the impressions served per day. Can be set by buyer
   * or seller.
   *
   * @var string
   */
  public $estimatedImpressionsPerDay;
  protected $guaranteedFixedPriceTermsType = GuaranteedFixedPriceTerms::class;
  protected $guaranteedFixedPriceTermsDataType = '';
  protected $nonGuaranteedAuctionTermsType = NonGuaranteedAuctionTerms::class;
  protected $nonGuaranteedAuctionTermsDataType = '';
  protected $nonGuaranteedFixedPriceTermsType = NonGuaranteedFixedPriceTerms::class;
  protected $nonGuaranteedFixedPriceTermsDataType = '';
  /**
   * The time zone name. For deals with Cost Per Day billing, defines the time
   * zone used to mark the boundaries of a day. It should be an IANA TZ name,
   * such as "America/Los_Angeles". For more information, see
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones.
   *
   * @var string
   */
  public $sellerTimeZone;

  /**
   * Visibility of the URL in bid requests. (default: BRANDED)
   *
   * Accepted values: BRANDING_TYPE_UNSPECIFIED, BRANDED, SEMI_TRANSPARENT
   *
   * @param self::BRANDING_TYPE_* $brandingType
   */
  public function setBrandingType($brandingType)
  {
    $this->brandingType = $brandingType;
  }
  /**
   * @return self::BRANDING_TYPE_*
   */
  public function getBrandingType()
  {
    return $this->brandingType;
  }
  /**
   * Publisher provided description for the terms.
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
   * Non-binding estimate of the estimated gross spend for this deal. Can be set
   * by buyer or seller.
   *
   * @param Price $estimatedGrossSpend
   */
  public function setEstimatedGrossSpend(Price $estimatedGrossSpend)
  {
    $this->estimatedGrossSpend = $estimatedGrossSpend;
  }
  /**
   * @return Price
   */
  public function getEstimatedGrossSpend()
  {
    return $this->estimatedGrossSpend;
  }
  /**
   * Non-binding estimate of the impressions served per day. Can be set by buyer
   * or seller.
   *
   * @param string $estimatedImpressionsPerDay
   */
  public function setEstimatedImpressionsPerDay($estimatedImpressionsPerDay)
  {
    $this->estimatedImpressionsPerDay = $estimatedImpressionsPerDay;
  }
  /**
   * @return string
   */
  public function getEstimatedImpressionsPerDay()
  {
    return $this->estimatedImpressionsPerDay;
  }
  /**
   * The terms for guaranteed fixed price deals.
   *
   * @param GuaranteedFixedPriceTerms $guaranteedFixedPriceTerms
   */
  public function setGuaranteedFixedPriceTerms(GuaranteedFixedPriceTerms $guaranteedFixedPriceTerms)
  {
    $this->guaranteedFixedPriceTerms = $guaranteedFixedPriceTerms;
  }
  /**
   * @return GuaranteedFixedPriceTerms
   */
  public function getGuaranteedFixedPriceTerms()
  {
    return $this->guaranteedFixedPriceTerms;
  }
  /**
   * The terms for non-guaranteed auction deals.
   *
   * @param NonGuaranteedAuctionTerms $nonGuaranteedAuctionTerms
   */
  public function setNonGuaranteedAuctionTerms(NonGuaranteedAuctionTerms $nonGuaranteedAuctionTerms)
  {
    $this->nonGuaranteedAuctionTerms = $nonGuaranteedAuctionTerms;
  }
  /**
   * @return NonGuaranteedAuctionTerms
   */
  public function getNonGuaranteedAuctionTerms()
  {
    return $this->nonGuaranteedAuctionTerms;
  }
  /**
   * The terms for non-guaranteed fixed price deals.
   *
   * @param NonGuaranteedFixedPriceTerms $nonGuaranteedFixedPriceTerms
   */
  public function setNonGuaranteedFixedPriceTerms(NonGuaranteedFixedPriceTerms $nonGuaranteedFixedPriceTerms)
  {
    $this->nonGuaranteedFixedPriceTerms = $nonGuaranteedFixedPriceTerms;
  }
  /**
   * @return NonGuaranteedFixedPriceTerms
   */
  public function getNonGuaranteedFixedPriceTerms()
  {
    return $this->nonGuaranteedFixedPriceTerms;
  }
  /**
   * The time zone name. For deals with Cost Per Day billing, defines the time
   * zone used to mark the boundaries of a day. It should be an IANA TZ name,
   * such as "America/Los_Angeles". For more information, see
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones.
   *
   * @param string $sellerTimeZone
   */
  public function setSellerTimeZone($sellerTimeZone)
  {
    $this->sellerTimeZone = $sellerTimeZone;
  }
  /**
   * @return string
   */
  public function getSellerTimeZone()
  {
    return $this->sellerTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DealTerms::class, 'Google_Service_AdExchangeBuyerII_DealTerms');
