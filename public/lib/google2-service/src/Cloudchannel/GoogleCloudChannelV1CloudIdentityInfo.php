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

class GoogleCloudChannelV1CloudIdentityInfo extends \Google\Model
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
   * Output only. URI of Customer's Admin console dashboard.
   *
   * @var string
   */
  public $adminConsoleUri;
  /**
   * The alternate email.
   *
   * @var string
   */
  public $alternateEmail;
  /**
   * CustomerType indicates verification type needed for using services.
   *
   * @var string
   */
  public $customerType;
  protected $eduDataType = GoogleCloudChannelV1EduData::class;
  protected $eduDataDataType = '';
  /**
   * Output only. Whether the domain is verified. This field is not returned for
   * a Customer's cloud_identity_info resource. Partners can use the
   * domains.get() method of the Workspace SDK's Directory API, or listen to the
   * PRIMARY_DOMAIN_VERIFIED Pub/Sub event in to track domain verification of
   * their resolve Workspace customers.
   *
   * @var bool
   */
  public $isDomainVerified;
  /**
   * Language code.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Phone number associated with the Cloud Identity.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Output only. The primary domain name.
   *
   * @var string
   */
  public $primaryDomain;

  /**
   * Output only. URI of Customer's Admin console dashboard.
   *
   * @param string $adminConsoleUri
   */
  public function setAdminConsoleUri($adminConsoleUri)
  {
    $this->adminConsoleUri = $adminConsoleUri;
  }
  /**
   * @return string
   */
  public function getAdminConsoleUri()
  {
    return $this->adminConsoleUri;
  }
  /**
   * The alternate email.
   *
   * @param string $alternateEmail
   */
  public function setAlternateEmail($alternateEmail)
  {
    $this->alternateEmail = $alternateEmail;
  }
  /**
   * @return string
   */
  public function getAlternateEmail()
  {
    return $this->alternateEmail;
  }
  /**
   * CustomerType indicates verification type needed for using services.
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
   * Edu information about the customer.
   *
   * @param GoogleCloudChannelV1EduData $eduData
   */
  public function setEduData(GoogleCloudChannelV1EduData $eduData)
  {
    $this->eduData = $eduData;
  }
  /**
   * @return GoogleCloudChannelV1EduData
   */
  public function getEduData()
  {
    return $this->eduData;
  }
  /**
   * Output only. Whether the domain is verified. This field is not returned for
   * a Customer's cloud_identity_info resource. Partners can use the
   * domains.get() method of the Workspace SDK's Directory API, or listen to the
   * PRIMARY_DOMAIN_VERIFIED Pub/Sub event in to track domain verification of
   * their resolve Workspace customers.
   *
   * @param bool $isDomainVerified
   */
  public function setIsDomainVerified($isDomainVerified)
  {
    $this->isDomainVerified = $isDomainVerified;
  }
  /**
   * @return bool
   */
  public function getIsDomainVerified()
  {
    return $this->isDomainVerified;
  }
  /**
   * Language code.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Phone number associated with the Cloud Identity.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * Output only. The primary domain name.
   *
   * @param string $primaryDomain
   */
  public function setPrimaryDomain($primaryDomain)
  {
    $this->primaryDomain = $primaryDomain;
  }
  /**
   * @return string
   */
  public function getPrimaryDomain()
  {
    return $this->primaryDomain;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1CloudIdentityInfo::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1CloudIdentityInfo');
