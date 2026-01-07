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

class GoogleCloudChannelV1ImportCustomerRequest extends \Google\Model
{
  /**
   * Optional. The super admin of the resold customer generates this token to
   * authorize a reseller to access their Cloud Identity and purchase
   * entitlements on their behalf. You can omit this token after authorization.
   * See https://support.google.com/a/answer/7643790 for more details.
   *
   * @var string
   */
  public $authToken;
  /**
   * Optional. Cloud Identity ID of a channel partner who will be the direct
   * reseller for the customer's order. This field is required for 2-tier
   * transfer scenarios and can be provided via the request Parent binding as
   * well.
   *
   * @var string
   */
  public $channelPartnerId;
  /**
   * Required. Customer's Cloud Identity ID
   *
   * @var string
   */
  public $cloudIdentityId;
  /**
   * Optional. Specifies the customer that will receive imported Cloud Identity
   * information. Format: accounts/{account_id}/customers/{customer_id}
   *
   * @var string
   */
  public $customer;
  /**
   * Required. Customer domain.
   *
   * @var string
   */
  public $domain;
  /**
   * Required. Choose to overwrite an existing customer if found. This must be
   * set to true if there is an existing customer with a conflicting region code
   * or domain.
   *
   * @var bool
   */
  public $overwriteIfExists;
  /**
   * Required. Customer's primary admin email.
   *
   * @var string
   */
  public $primaryAdminEmail;

  /**
   * Optional. The super admin of the resold customer generates this token to
   * authorize a reseller to access their Cloud Identity and purchase
   * entitlements on their behalf. You can omit this token after authorization.
   * See https://support.google.com/a/answer/7643790 for more details.
   *
   * @param string $authToken
   */
  public function setAuthToken($authToken)
  {
    $this->authToken = $authToken;
  }
  /**
   * @return string
   */
  public function getAuthToken()
  {
    return $this->authToken;
  }
  /**
   * Optional. Cloud Identity ID of a channel partner who will be the direct
   * reseller for the customer's order. This field is required for 2-tier
   * transfer scenarios and can be provided via the request Parent binding as
   * well.
   *
   * @param string $channelPartnerId
   */
  public function setChannelPartnerId($channelPartnerId)
  {
    $this->channelPartnerId = $channelPartnerId;
  }
  /**
   * @return string
   */
  public function getChannelPartnerId()
  {
    return $this->channelPartnerId;
  }
  /**
   * Required. Customer's Cloud Identity ID
   *
   * @param string $cloudIdentityId
   */
  public function setCloudIdentityId($cloudIdentityId)
  {
    $this->cloudIdentityId = $cloudIdentityId;
  }
  /**
   * @return string
   */
  public function getCloudIdentityId()
  {
    return $this->cloudIdentityId;
  }
  /**
   * Optional. Specifies the customer that will receive imported Cloud Identity
   * information. Format: accounts/{account_id}/customers/{customer_id}
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Required. Customer domain.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Required. Choose to overwrite an existing customer if found. This must be
   * set to true if there is an existing customer with a conflicting region code
   * or domain.
   *
   * @param bool $overwriteIfExists
   */
  public function setOverwriteIfExists($overwriteIfExists)
  {
    $this->overwriteIfExists = $overwriteIfExists;
  }
  /**
   * @return bool
   */
  public function getOverwriteIfExists()
  {
    return $this->overwriteIfExists;
  }
  /**
   * Required. Customer's primary admin email.
   *
   * @param string $primaryAdminEmail
   */
  public function setPrimaryAdminEmail($primaryAdminEmail)
  {
    $this->primaryAdminEmail = $primaryAdminEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryAdminEmail()
  {
    return $this->primaryAdminEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1ImportCustomerRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1ImportCustomerRequest');
