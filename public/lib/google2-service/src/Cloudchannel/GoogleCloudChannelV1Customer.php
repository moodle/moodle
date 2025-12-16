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

class GoogleCloudChannelV1Customer extends \Google\Model
{
  /**
   * Default value if not set yet
   */
  public const CUSTOMER_ATTESTATION_STATE_CUSTOMER_ATTESTATION_STATE_UNSPECIFIED = 'CUSTOMER_ATTESTATION_STATE_UNSPECIFIED';
  /**
   * Customer is exempt from attesting based on exemption list at
   * https://cloud.google.com/terms/direct-tos-exemptions. Contact information
   * of customer will be mandatory.
   */
  public const CUSTOMER_ATTESTATION_STATE_EXEMPT = 'EXEMPT';
  /**
   * Customer is not exempt and has verified the information provided is
   * correct. Contact information of customer will be mandatory.
   */
  public const CUSTOMER_ATTESTATION_STATE_NON_EXEMPT_AND_INFO_VERIFIED = 'NON_EXEMPT_AND_INFO_VERIFIED';
  /**
   * Secondary contact email. You need to provide an alternate email to create
   * different domains if a primary contact email already exists. Users will
   * receive a notification with credentials when you create an admin.google.com
   * account. Secondary emails are also recovery email addresses. Alternate
   * emails are optional when you create Team customers.
   *
   * @var string
   */
  public $alternateEmail;
  /**
   * Cloud Identity ID of the customer's channel partner. Populated only if a
   * channel partner exists for this customer.
   *
   * @var string
   */
  public $channelPartnerId;
  /**
   * Output only. The customer's Cloud Identity ID if the customer has a Cloud
   * Identity resource.
   *
   * @var string
   */
  public $cloudIdentityId;
  protected $cloudIdentityInfoType = GoogleCloudChannelV1CloudIdentityInfo::class;
  protected $cloudIdentityInfoDataType = '';
  /**
   * Optional. External CRM ID for the customer. Populated only if a CRM ID
   * exists for this customer.
   *
   * @var string
   */
  public $correlationId;
  /**
   * Output only. Time when the customer was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Indicate if a customer is attesting about the correctness of
   * provided information. Only required if creating a GCP Entitlement.
   *
   * @var string
   */
  public $customerAttestationState;
  /**
   * Required. The customer's primary domain. Must match the primary contact
   * email's domain.
   *
   * @var string
   */
  public $domain;
  /**
   * Optional. The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. Resource name of the customer. Format:
   * accounts/{account_id}/customers/{customer_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Name of the organization that the customer entity represents.
   *
   * @var string
   */
  public $orgDisplayName;
  protected $orgPostalAddressType = GoogleTypePostalAddress::class;
  protected $orgPostalAddressDataType = '';
  protected $primaryContactInfoType = GoogleCloudChannelV1ContactInfo::class;
  protected $primaryContactInfoDataType = '';
  /**
   * Output only. Time when the customer was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Secondary contact email. You need to provide an alternate email to create
   * different domains if a primary contact email already exists. Users will
   * receive a notification with credentials when you create an admin.google.com
   * account. Secondary emails are also recovery email addresses. Alternate
   * emails are optional when you create Team customers.
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
   * Cloud Identity ID of the customer's channel partner. Populated only if a
   * channel partner exists for this customer.
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
   * Output only. The customer's Cloud Identity ID if the customer has a Cloud
   * Identity resource.
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
   * Output only. Cloud Identity information for the customer. Populated only if
   * a Cloud Identity account exists for this customer.
   *
   * @param GoogleCloudChannelV1CloudIdentityInfo $cloudIdentityInfo
   */
  public function setCloudIdentityInfo(GoogleCloudChannelV1CloudIdentityInfo $cloudIdentityInfo)
  {
    $this->cloudIdentityInfo = $cloudIdentityInfo;
  }
  /**
   * @return GoogleCloudChannelV1CloudIdentityInfo
   */
  public function getCloudIdentityInfo()
  {
    return $this->cloudIdentityInfo;
  }
  /**
   * Optional. External CRM ID for the customer. Populated only if a CRM ID
   * exists for this customer.
   *
   * @param string $correlationId
   */
  public function setCorrelationId($correlationId)
  {
    $this->correlationId = $correlationId;
  }
  /**
   * @return string
   */
  public function getCorrelationId()
  {
    return $this->correlationId;
  }
  /**
   * Output only. Time when the customer was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Indicate if a customer is attesting about the correctness of
   * provided information. Only required if creating a GCP Entitlement.
   *
   * Accepted values: CUSTOMER_ATTESTATION_STATE_UNSPECIFIED, EXEMPT,
   * NON_EXEMPT_AND_INFO_VERIFIED
   *
   * @param self::CUSTOMER_ATTESTATION_STATE_* $customerAttestationState
   */
  public function setCustomerAttestationState($customerAttestationState)
  {
    $this->customerAttestationState = $customerAttestationState;
  }
  /**
   * @return self::CUSTOMER_ATTESTATION_STATE_*
   */
  public function getCustomerAttestationState()
  {
    return $this->customerAttestationState;
  }
  /**
   * Required. The customer's primary domain. Must match the primary contact
   * email's domain.
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
   * Optional. The BCP-47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
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
   * Output only. Resource name of the customer. Format:
   * accounts/{account_id}/customers/{customer_id}
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
   * Required. Name of the organization that the customer entity represents.
   *
   * @param string $orgDisplayName
   */
  public function setOrgDisplayName($orgDisplayName)
  {
    $this->orgDisplayName = $orgDisplayName;
  }
  /**
   * @return string
   */
  public function getOrgDisplayName()
  {
    return $this->orgDisplayName;
  }
  /**
   * Required. The organization address for the customer. To enforce US laws and
   * embargoes, we require a region, postal code, and address lines. You must
   * provide valid addresses for every customer. To set the customer's language,
   * use the Customer-level language code.
   *
   * @param GoogleTypePostalAddress $orgPostalAddress
   */
  public function setOrgPostalAddress(GoogleTypePostalAddress $orgPostalAddress)
  {
    $this->orgPostalAddress = $orgPostalAddress;
  }
  /**
   * @return GoogleTypePostalAddress
   */
  public function getOrgPostalAddress()
  {
    return $this->orgPostalAddress;
  }
  /**
   * Primary contact info.
   *
   * @param GoogleCloudChannelV1ContactInfo $primaryContactInfo
   */
  public function setPrimaryContactInfo(GoogleCloudChannelV1ContactInfo $primaryContactInfo)
  {
    $this->primaryContactInfo = $primaryContactInfo;
  }
  /**
   * @return GoogleCloudChannelV1ContactInfo
   */
  public function getPrimaryContactInfo()
  {
    return $this->primaryContactInfo;
  }
  /**
   * Output only. Time when the customer was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1Customer::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1Customer');
