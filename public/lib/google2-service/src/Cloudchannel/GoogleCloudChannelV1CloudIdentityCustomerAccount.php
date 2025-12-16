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

class GoogleCloudChannelV1CloudIdentityCustomerAccount extends \Google\Model
{
  /**
   * Not used.
   */
  public const CUSTOMER_TYPE_CUSTOMER_TYPE_UNSPECIFIED = 'CUSTOMER_TYPE_UNSPECIFIED';
  /**
   * Domain-owning customer which needs domain verification to use services.
   */
  public const CUSTOMER_TYPE_DOMAIN = 'DOMAIN';
  /**
   * Team customer which needs email verification to use services.
   */
  public const CUSTOMER_TYPE_TEAM = 'TEAM';
  /**
   * If existing = true, and is 2-tier customer, the channel partner of the
   * customer.
   *
   * @var string
   */
  public $channelPartnerCloudIdentityId;
  /**
   * If existing = true, the Cloud Identity ID of the customer.
   *
   * @var string
   */
  public $customerCloudIdentityId;
  /**
   * If owned = true, the name of the customer that owns the Cloud Identity
   * account. Customer_name uses the format:
   * accounts/{account_id}/customers/{customer_id}
   *
   * @var string
   */
  public $customerName;
  /**
   * If existing = true, the type of the customer.
   *
   * @var string
   */
  public $customerType;
  /**
   * Returns true if a Cloud Identity account exists for a specific domain.
   *
   * @var bool
   */
  public $existing;
  /**
   * Returns true if the Cloud Identity account is associated with a customer of
   * the Channel Services partner (with active subscriptions or purchase
   * consents).
   *
   * @var bool
   */
  public $owned;

  /**
   * If existing = true, and is 2-tier customer, the channel partner of the
   * customer.
   *
   * @param string $channelPartnerCloudIdentityId
   */
  public function setChannelPartnerCloudIdentityId($channelPartnerCloudIdentityId)
  {
    $this->channelPartnerCloudIdentityId = $channelPartnerCloudIdentityId;
  }
  /**
   * @return string
   */
  public function getChannelPartnerCloudIdentityId()
  {
    return $this->channelPartnerCloudIdentityId;
  }
  /**
   * If existing = true, the Cloud Identity ID of the customer.
   *
   * @param string $customerCloudIdentityId
   */
  public function setCustomerCloudIdentityId($customerCloudIdentityId)
  {
    $this->customerCloudIdentityId = $customerCloudIdentityId;
  }
  /**
   * @return string
   */
  public function getCustomerCloudIdentityId()
  {
    return $this->customerCloudIdentityId;
  }
  /**
   * If owned = true, the name of the customer that owns the Cloud Identity
   * account. Customer_name uses the format:
   * accounts/{account_id}/customers/{customer_id}
   *
   * @param string $customerName
   */
  public function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }
  /**
   * @return string
   */
  public function getCustomerName()
  {
    return $this->customerName;
  }
  /**
   * If existing = true, the type of the customer.
   *
   * Accepted values: CUSTOMER_TYPE_UNSPECIFIED, DOMAIN, TEAM
   *
   * @param self::CUSTOMER_TYPE_* $customerType
   */
  public function setCustomerType($customerType)
  {
    $this->customerType = $customerType;
  }
  /**
   * @return self::CUSTOMER_TYPE_*
   */
  public function getCustomerType()
  {
    return $this->customerType;
  }
  /**
   * Returns true if a Cloud Identity account exists for a specific domain.
   *
   * @param bool $existing
   */
  public function setExisting($existing)
  {
    $this->existing = $existing;
  }
  /**
   * @return bool
   */
  public function getExisting()
  {
    return $this->existing;
  }
  /**
   * Returns true if the Cloud Identity account is associated with a customer of
   * the Channel Services partner (with active subscriptions or purchase
   * consents).
   *
   * @param bool $owned
   */
  public function setOwned($owned)
  {
    $this->owned = $owned;
  }
  /**
   * @return bool
   */
  public function getOwned()
  {
    return $this->owned;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1CloudIdentityCustomerAccount::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1CloudIdentityCustomerAccount');
