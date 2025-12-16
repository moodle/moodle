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

class ChannelToStoreLinkDetailsBillingDetails extends \Google\Model
{
  public const BILLING_STATUS_billingStatusUnspecified = 'billingStatusUnspecified';
  public const BILLING_STATUS_billingStatusPending = 'billingStatusPending';
  public const BILLING_STATUS_billingStatusActive = 'billingStatusActive';
  public const BILLING_STATUS_billingStatusInactive = 'billingStatusInactive';
  /**
   * The current billing profile status.
   *
   * @var string
   */
  public $billingStatus;

  /**
   * The current billing profile status.
   *
   * Accepted values: billingStatusUnspecified, billingStatusPending,
   * billingStatusActive, billingStatusInactive
   *
   * @param self::BILLING_STATUS_* $billingStatus
   */
  public function setBillingStatus($billingStatus)
  {
    $this->billingStatus = $billingStatus;
  }
  /**
   * @return self::BILLING_STATUS_*
   */
  public function getBillingStatus()
  {
    return $this->billingStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelToStoreLinkDetailsBillingDetails::class, 'Google_Service_YouTube_ChannelToStoreLinkDetailsBillingDetails');
