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

namespace Google\Service\YouTube;

class ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails extends \Google\Model
{
  /**
   * Unspecified status.
   */
  public const STATUS_merchantAffiliateProgramStatusUnspecified = 'merchantAffiliateProgramStatusUnspecified';
  /**
   * Merchant is eligible for the merchant affiliate program.
   */
  public const STATUS_merchantAffiliateProgramStatusEligible = 'merchantAffiliateProgramStatusEligible';
  /**
   * Merchant affiliate program is active.
   */
  public const STATUS_merchantAffiliateProgramStatusActive = 'merchantAffiliateProgramStatusActive';
  /**
   * Merchant affiliate program is paused.
   */
  public const STATUS_merchantAffiliateProgramStatusPaused = 'merchantAffiliateProgramStatusPaused';
  /**
   * The current merchant affiliate program status.
   *
   * @var string
   */
  public $status;

  /**
   * The current merchant affiliate program status.
   *
   * Accepted values: merchantAffiliateProgramStatusUnspecified,
   * merchantAffiliateProgramStatusEligible,
   * merchantAffiliateProgramStatusActive, merchantAffiliateProgramStatusPaused
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails::class, 'Google_Service_YouTube_ChannelToStoreLinkDetailsMerchantAffiliateProgramDetails');
