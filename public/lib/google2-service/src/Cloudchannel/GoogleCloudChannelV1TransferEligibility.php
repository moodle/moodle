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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1TransferEligibility extends \Google\Model
{
  /**
   * Not used.
   */
  public const INELIGIBILITY_REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * Reseller needs to accept TOS before transferring the SKU.
   */
  public const INELIGIBILITY_REASON_PENDING_TOS_ACCEPTANCE = 'PENDING_TOS_ACCEPTANCE';
  /**
   * Reseller not eligible to sell the SKU.
   */
  public const INELIGIBILITY_REASON_SKU_NOT_ELIGIBLE = 'SKU_NOT_ELIGIBLE';
  /**
   * SKU subscription is suspended
   */
  public const INELIGIBILITY_REASON_SKU_SUSPENDED = 'SKU_SUSPENDED';
  /**
   * The reseller is not authorized to transact on this Product. See
   * https://support.google.com/channelservices/answer/9759265
   */
  public const INELIGIBILITY_REASON_CHANNEL_PARTNER_NOT_AUTHORIZED_FOR_SKU = 'CHANNEL_PARTNER_NOT_AUTHORIZED_FOR_SKU';
  /**
   * Localized description if reseller is not eligible to transfer the SKU.
   *
   * @var string
   */
  public $description;
  /**
   * Specified the reason for ineligibility.
   *
   * @var string
   */
  public $ineligibilityReason;
  /**
   * Whether reseller is eligible to transfer the SKU.
   *
   * @var bool
   */
  public $isEligible;

  /**
   * Localized description if reseller is not eligible to transfer the SKU.
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
   * Specified the reason for ineligibility.
   *
   * Accepted values: REASON_UNSPECIFIED, PENDING_TOS_ACCEPTANCE,
   * SKU_NOT_ELIGIBLE, SKU_SUSPENDED, CHANNEL_PARTNER_NOT_AUTHORIZED_FOR_SKU
   *
   * @param self::INELIGIBILITY_REASON_* $ineligibilityReason
   */
  public function setIneligibilityReason($ineligibilityReason)
  {
    $this->ineligibilityReason = $ineligibilityReason;
  }
  /**
   * @return self::INELIGIBILITY_REASON_*
   */
  public function getIneligibilityReason()
  {
    return $this->ineligibilityReason;
  }
  /**
   * Whether reseller is eligible to transfer the SKU.
   *
   * @param bool $isEligible
   */
  public function setIsEligible($isEligible)
  {
    $this->isEligible = $isEligible;
  }
  /**
   * @return bool
   */
  public function getIsEligible()
  {
    return $this->isEligible;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1TransferEligibility::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1TransferEligibility');
