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

class YoutubePayload extends \Google\Collection
{
  /**
   * Unspecified. Should not use, reserved as an invalid value.
   */
  public const PARTNER_PLAN_TYPE_PARTNER_PLAN_TYPE_UNSPECIFIED = 'PARTNER_PLAN_TYPE_UNSPECIFIED';
  /**
   * This item is offered as a standalone product to the user.
   */
  public const PARTNER_PLAN_TYPE_PARTNER_PLAN_TYPE_STANDALONE = 'PARTNER_PLAN_TYPE_STANDALONE';
  /**
   * This item is bundled with another partner offering, the item is provisioned
   * at purchase time.
   */
  public const PARTNER_PLAN_TYPE_PARTNER_PLAN_TYPE_HARD_BUNDLE = 'PARTNER_PLAN_TYPE_HARD_BUNDLE';
  /**
   * This item is bundled with another partner offering, the item is provisioned
   * after puchase, when the user opts in this Google service.
   */
  public const PARTNER_PLAN_TYPE_PARTNER_PLAN_TYPE_SOFT_BUNDLE = 'PARTNER_PLAN_TYPE_SOFT_BUNDLE';
  protected $collection_key = 'partnerEligibilityIds';
  /**
   * Output only. The access expiration time for this line item.
   *
   * @var string
   */
  public $accessEndTime;
  /**
   * The list of eligibility_ids which are applicable for the line item.
   *
   * @var string[]
   */
  public $partnerEligibilityIds;
  /**
   * Optional. Specifies the plan type offered to the end user by the partner.
   *
   * @var string
   */
  public $partnerPlanType;

  /**
   * Output only. The access expiration time for this line item.
   *
   * @param string $accessEndTime
   */
  public function setAccessEndTime($accessEndTime)
  {
    $this->accessEndTime = $accessEndTime;
  }
  /**
   * @return string
   */
  public function getAccessEndTime()
  {
    return $this->accessEndTime;
  }
  /**
   * The list of eligibility_ids which are applicable for the line item.
   *
   * @param string[] $partnerEligibilityIds
   */
  public function setPartnerEligibilityIds($partnerEligibilityIds)
  {
    $this->partnerEligibilityIds = $partnerEligibilityIds;
  }
  /**
   * @return string[]
   */
  public function getPartnerEligibilityIds()
  {
    return $this->partnerEligibilityIds;
  }
  /**
   * Optional. Specifies the plan type offered to the end user by the partner.
   *
   * Accepted values: PARTNER_PLAN_TYPE_UNSPECIFIED,
   * PARTNER_PLAN_TYPE_STANDALONE, PARTNER_PLAN_TYPE_HARD_BUNDLE,
   * PARTNER_PLAN_TYPE_SOFT_BUNDLE
   *
   * @param self::PARTNER_PLAN_TYPE_* $partnerPlanType
   */
  public function setPartnerPlanType($partnerPlanType)
  {
    $this->partnerPlanType = $partnerPlanType;
  }
  /**
   * @return self::PARTNER_PLAN_TYPE_*
   */
  public function getPartnerPlanType()
  {
    return $this->partnerPlanType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubePayload::class, 'Google_Service_PaymentsResellerSubscription_YoutubePayload');
