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

class SubscriptionPromotionSpec extends \Google\Model
{
  /**
   * The promotion type is unspecified.
   */
  public const TYPE_PROMOTION_TYPE_UNSPECIFIED = 'PROMOTION_TYPE_UNSPECIFIED';
  /**
   * The promotion is a free trial.
   */
  public const TYPE_PROMOTION_TYPE_FREE_TRIAL = 'PROMOTION_TYPE_FREE_TRIAL';
  /**
   * The promotion is a reduced introductory pricing.
   */
  public const TYPE_PROMOTION_TYPE_INTRODUCTORY_PRICING = 'PROMOTION_TYPE_INTRODUCTORY_PRICING';
  protected $freeTrialDurationType = Duration::class;
  protected $freeTrialDurationDataType = '';
  protected $introductoryPricingDetailsType = PromotionIntroductoryPricingDetails::class;
  protected $introductoryPricingDetailsDataType = '';
  /**
   * Required. Promotion resource name that identifies a promotion. The format
   * is 'partners/{partner_id}/promotions/{promotion_id}'.
   *
   * @var string
   */
  public $promotion;
  /**
   * Output only. The type of the promotion for the spec.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The duration of the free trial if the promotion is of type
   * FREE_TRIAL.
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
   * Output only. The details of the introductory pricing spec if the promotion
   * is of type INTRODUCTORY_PRICING.
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
   * Required. Promotion resource name that identifies a promotion. The format
   * is 'partners/{partner_id}/promotions/{promotion_id}'.
   *
   * @param string $promotion
   */
  public function setPromotion($promotion)
  {
    $this->promotion = $promotion;
  }
  /**
   * @return string
   */
  public function getPromotion()
  {
    return $this->promotion;
  }
  /**
   * Output only. The type of the promotion for the spec.
   *
   * Accepted values: PROMOTION_TYPE_UNSPECIFIED, PROMOTION_TYPE_FREE_TRIAL,
   * PROMOTION_TYPE_INTRODUCTORY_PRICING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionPromotionSpec::class, 'Google_Service_PaymentsResellerSubscription_SubscriptionPromotionSpec');
