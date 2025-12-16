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

class GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload extends \Google\Collection
{
  protected $collection_key = 'partnerEligibilityIds';
  /**
   * @var string
   */
  public $accessEndTime;
  /**
   * @var string[]
   */
  public $partnerEligibilityIds;
  /**
   * @var string
   */
  public $partnerPlanType;

  /**
   * @param string
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
   * @param string[]
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
   * @param string
   */
  public function setPartnerPlanType($partnerPlanType)
  {
    $this->partnerPlanType = $partnerPlanType;
  }
  /**
   * @return string
   */
  public function getPartnerPlanType()
  {
    return $this->partnerPlanType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleCloudPaymentsResellerSubscriptionV1YoutubePayload');
