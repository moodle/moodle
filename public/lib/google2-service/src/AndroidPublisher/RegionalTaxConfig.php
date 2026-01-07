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

namespace Google\Service\AndroidPublisher;

class RegionalTaxConfig extends \Google\Model
{
  /**
   * No telecommunications tax collected.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_UNSPECIFIED = 'STREAMING_TAX_TYPE_UNSPECIFIED';
  /**
   * US-specific telecommunications tax tier for video streaming, on demand,
   * rentals / subscriptions / pay-per-view.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_VIDEO_RENTAL = 'STREAMING_TAX_TYPE_TELCO_VIDEO_RENTAL';
  /**
   * US-specific telecommunications tax tier for video streaming of pre-recorded
   * content like movies, tv shows.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_VIDEO_SALES = 'STREAMING_TAX_TYPE_TELCO_VIDEO_SALES';
  /**
   * US-specific telecommunications tax tier for video streaming of multi-
   * channel programming.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_VIDEO_MULTI_CHANNEL = 'STREAMING_TAX_TYPE_TELCO_VIDEO_MULTI_CHANNEL';
  /**
   * US-specific telecommunications tax tier for audio streaming, rental /
   * subscription.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_AUDIO_RENTAL = 'STREAMING_TAX_TYPE_TELCO_AUDIO_RENTAL';
  /**
   * US-specific telecommunications tax tier for audio streaming, sale /
   * permanent download.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_AUDIO_SALES = 'STREAMING_TAX_TYPE_TELCO_AUDIO_SALES';
  /**
   * US-specific telecommunications tax tier for multi channel audio streaming
   * like radio.
   */
  public const STREAMING_TAX_TYPE_STREAMING_TAX_TYPE_TELCO_AUDIO_MULTI_CHANNEL = 'STREAMING_TAX_TYPE_TELCO_AUDIO_MULTI_CHANNEL';
  public const TAX_TIER_TAX_TIER_UNSPECIFIED = 'TAX_TIER_UNSPECIFIED';
  public const TAX_TIER_TAX_TIER_BOOKS_1 = 'TAX_TIER_BOOKS_1';
  public const TAX_TIER_TAX_TIER_NEWS_1 = 'TAX_TIER_NEWS_1';
  public const TAX_TIER_TAX_TIER_NEWS_2 = 'TAX_TIER_NEWS_2';
  public const TAX_TIER_TAX_TIER_MUSIC_OR_AUDIO_1 = 'TAX_TIER_MUSIC_OR_AUDIO_1';
  public const TAX_TIER_TAX_TIER_LIVE_OR_BROADCAST_1 = 'TAX_TIER_LIVE_OR_BROADCAST_1';
  /**
   * You must tell us if your app contains streaming products to correctly
   * charge US state and local sales tax. Field only supported in the United
   * States.
   *
   * @var bool
   */
  public $eligibleForStreamingServiceTaxRate;
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g. "US".
   *
   * @var string
   */
  public $regionCode;
  /**
   * To collect communications or amusement taxes in the United States, choose
   * the appropriate tax category. [Learn
   * more](https://support.google.com/googleplay/android-
   * developer/answer/10463498#streaming_tax).
   *
   * @var string
   */
  public $streamingTaxType;
  /**
   * Tax tier to specify reduced tax rate. Developers who sell digital news,
   * magazines, newspapers, books, or audiobooks in various regions may be
   * eligible for reduced tax rates. [Learn
   * more](https://support.google.com/googleplay/android-
   * developer/answer/10463498).
   *
   * @var string
   */
  public $taxTier;

  /**
   * You must tell us if your app contains streaming products to correctly
   * charge US state and local sales tax. Field only supported in the United
   * States.
   *
   * @param bool $eligibleForStreamingServiceTaxRate
   */
  public function setEligibleForStreamingServiceTaxRate($eligibleForStreamingServiceTaxRate)
  {
    $this->eligibleForStreamingServiceTaxRate = $eligibleForStreamingServiceTaxRate;
  }
  /**
   * @return bool
   */
  public function getEligibleForStreamingServiceTaxRate()
  {
    return $this->eligibleForStreamingServiceTaxRate;
  }
  /**
   * Required. Region code this configuration applies to, as defined by ISO
   * 3166-2, e.g. "US".
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * To collect communications or amusement taxes in the United States, choose
   * the appropriate tax category. [Learn
   * more](https://support.google.com/googleplay/android-
   * developer/answer/10463498#streaming_tax).
   *
   * Accepted values: STREAMING_TAX_TYPE_UNSPECIFIED,
   * STREAMING_TAX_TYPE_TELCO_VIDEO_RENTAL,
   * STREAMING_TAX_TYPE_TELCO_VIDEO_SALES,
   * STREAMING_TAX_TYPE_TELCO_VIDEO_MULTI_CHANNEL,
   * STREAMING_TAX_TYPE_TELCO_AUDIO_RENTAL,
   * STREAMING_TAX_TYPE_TELCO_AUDIO_SALES,
   * STREAMING_TAX_TYPE_TELCO_AUDIO_MULTI_CHANNEL
   *
   * @param self::STREAMING_TAX_TYPE_* $streamingTaxType
   */
  public function setStreamingTaxType($streamingTaxType)
  {
    $this->streamingTaxType = $streamingTaxType;
  }
  /**
   * @return self::STREAMING_TAX_TYPE_*
   */
  public function getStreamingTaxType()
  {
    return $this->streamingTaxType;
  }
  /**
   * Tax tier to specify reduced tax rate. Developers who sell digital news,
   * magazines, newspapers, books, or audiobooks in various regions may be
   * eligible for reduced tax rates. [Learn
   * more](https://support.google.com/googleplay/android-
   * developer/answer/10463498).
   *
   * Accepted values: TAX_TIER_UNSPECIFIED, TAX_TIER_BOOKS_1, TAX_TIER_NEWS_1,
   * TAX_TIER_NEWS_2, TAX_TIER_MUSIC_OR_AUDIO_1, TAX_TIER_LIVE_OR_BROADCAST_1
   *
   * @param self::TAX_TIER_* $taxTier
   */
  public function setTaxTier($taxTier)
  {
    $this->taxTier = $taxTier;
  }
  /**
   * @return self::TAX_TIER_*
   */
  public function getTaxTier()
  {
    return $this->taxTier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionalTaxConfig::class, 'Google_Service_AndroidPublisher_RegionalTaxConfig');
