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

namespace Google\Service\PaymentsResellerSubscription;

class Promotion extends \Google\Collection
{
  /**
   * The promotion type is unspecified.
   */
  public const PROMOTION_TYPE_PROMOTION_TYPE_UNSPECIFIED = 'PROMOTION_TYPE_UNSPECIFIED';
  /**
   * The promotion is a free trial.
   */
  public const PROMOTION_TYPE_PROMOTION_TYPE_FREE_TRIAL = 'PROMOTION_TYPE_FREE_TRIAL';
  /**
   * The promotion is a reduced introductory pricing.
   */
  public const PROMOTION_TYPE_PROMOTION_TYPE_INTRODUCTORY_PRICING = 'PROMOTION_TYPE_INTRODUCTORY_PRICING';
  protected $collection_key = 'titles';
  /**
   * Output only. The product ids this promotion can be applied to.
   *
   * @var string[]
   */
  public $applicableProducts;
  /**
   * Optional. Specifies the end time (exclusive) of the period that the
   * promotion is available in. If unset, the promotion is available
   * indefinitely.
   *
   * @var string
   */
  public $endTime;
  protected $freeTrialDurationType = Duration::class;
  protected $freeTrialDurationDataType = '';
  protected $introductoryPricingDetailsType = PromotionIntroductoryPricingDetails::class;
  protected $introductoryPricingDetailsDataType = '';
  /**
   * Identifier. Response only. Resource name of the subscription promotion. It
   * will have the format of "partners/{partner_id}/promotion/{promotion_id}"
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Specifies the type of the promotion.
   *
   * @var string
   */
  public $promotionType;
  /**
   * Output only. 2-letter ISO region code where the promotion is available in.
   * Ex. "US" Please refers to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @var string[]
   */
  public $regionCodes;
  /**
   * Optional. Specifies the start time (inclusive) of the period that the
   * promotion is available in.
   *
   * @var string
   */
  public $startTime;
  protected $titlesType = GoogleTypeLocalizedText::class;
  protected $titlesDataType = 'array';

  /**
   * Output only. The product ids this promotion can be applied to.
   *
   * @param string[] $applicableProducts
   */
  public function setApplicableProducts($applicableProducts)
  {
    $this->applicableProducts = $applicableProducts;
  }
  /**
   * @return string[]
   */
  public function getApplicableProducts()
  {
    return $this->applicableProducts;
  }
  /**
   * Optional. Specifies the end time (exclusive) of the period that the
   * promotion is available in. If unset, the promotion is available
   * indefinitely.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. Specifies the duration of the free trial of the subscription when
   * promotion_type is PROMOTION_TYPE_FREE_TRIAL
   *
   * @param Duration $freeTrialDuration
   */
  public function setFreeTrialDuration(Duration $freeTrialDuration)
  {
    $this->freeTrialDuration = $freeTrialDuration;
  }
  /**
   * @return Duration
   */
  public function getFreeTrialDuration()
  {
    return $this->freeTrialDuration;
  }
  /**
   * Optional. Specifies the introductory pricing details when the
   * promotion_type is PROMOTION_TYPE_INTRODUCTORY_PRICING.
   *
   * @param PromotionIntroductoryPricingDetails $introductoryPricingDetails
   */
  public function setIntroductoryPricingDetails(PromotionIntroductoryPricingDetails $introductoryPricingDetails)
  {
    $this->introductoryPricingDetails = $introductoryPricingDetails;
  }
  /**
   * @return PromotionIntroductoryPricingDetails
   */
  public function getIntroductoryPricingDetails()
  {
    return $this->introductoryPricingDetails;
  }
  /**
   * Identifier. Response only. Resource name of the subscription promotion. It
   * will have the format of "partners/{partner_id}/promotion/{promotion_id}"
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
   * Output only. Specifies the type of the promotion.
   *
   * Accepted values: PROMOTION_TYPE_UNSPECIFIED, PROMOTION_TYPE_FREE_TRIAL,
   * PROMOTION_TYPE_INTRODUCTORY_PRICING
   *
   * @param self::PROMOTION_TYPE_* $promotionType
   */
  public function setPromotionType($promotionType)
  {
    $this->promotionType = $promotionType;
  }
  /**
   * @return self::PROMOTION_TYPE_*
   */
  public function getPromotionType()
  {
    return $this->promotionType;
  }
  /**
   * Output only. 2-letter ISO region code where the promotion is available in.
   * Ex. "US" Please refers to: https://en.wikipedia.org/wiki/ISO_3166-1
   *
   * @param string[] $regionCodes
   */
  public function setRegionCodes($regionCodes)
  {
    $this->regionCodes = $regionCodes;
  }
  /**
   * @return string[]
   */
  public function getRegionCodes()
  {
    return $this->regionCodes;
  }
  /**
   * Optional. Specifies the start time (inclusive) of the period that the
   * promotion is available in.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Localized human readable name of the promotion.
   *
   * @param GoogleTypeLocalizedText[] $titles
   */
  public function setTitles($titles)
  {
    $this->titles = $titles;
  }
  /**
   * @return GoogleTypeLocalizedText[]
   */
  public function getTitles()
  {
    return $this->titles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Promotion::class, 'Google_Service_PaymentsResellerSubscription_Promotion');
