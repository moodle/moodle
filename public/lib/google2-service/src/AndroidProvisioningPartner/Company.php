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

namespace Google\Service\AndroidProvisioningPartner;

class Company extends \Google\Collection
{
  /**
   * Default value. This value should never be set if the enum is present.
   */
  public const TERMS_STATUS_TERMS_STATUS_UNSPECIFIED = 'TERMS_STATUS_UNSPECIFIED';
  /**
   * None of the company's users have accepted the ToS.
   */
  public const TERMS_STATUS_TERMS_STATUS_NOT_ACCEPTED = 'TERMS_STATUS_NOT_ACCEPTED';
  /**
   * One (or more) of the company's users has accepted the ToS.
   */
  public const TERMS_STATUS_TERMS_STATUS_ACCEPTED = 'TERMS_STATUS_ACCEPTED';
  /**
   * None of the company's users has accepted the current ToS but at least one
   * user accepted a previous ToS.
   */
  public const TERMS_STATUS_TERMS_STATUS_STALE = 'TERMS_STATUS_STALE';
  protected $collection_key = 'ownerEmails';
  /**
   * Optional. Email address of customer's users in the admin role. Each email
   * address must be associated with a Google Account.
   *
   * @var string[]
   */
  public $adminEmails;
  /**
   * Output only. The ID of the company. Assigned by the server.
   *
   * @var string
   */
  public $companyId;
  /**
   * Required. The name of the company. For example _XYZ Corp_. Displayed to the
   * company's employees in the zero-touch enrollment portal.
   *
   * @var string
   */
  public $companyName;
  protected $googleWorkspaceAccountType = GoogleWorkspaceAccount::class;
  protected $googleWorkspaceAccountDataType = '';
  /**
   * Input only. The preferred locale of the customer represented as a BCP47
   * language code. This field is validated on input and requests containing
   * unsupported language codes will be rejected. Supported language codes:
   * Arabic (ar) Chinese (Hong Kong) (zh-HK) Chinese (Simplified) (zh-CN)
   * Chinese (Traditional) (zh-TW) Czech (cs) Danish (da) Dutch (nl) English
   * (UK) (en-GB) English (US) (en-US) Filipino (fil) Finnish (fi) French (fr)
   * German (de) Hebrew (iw) Hindi (hi) Hungarian (hu) Indonesian (id) Italian
   * (it) Japanese (ja) Korean (ko) Norwegian (Bokmal) (no) Polish (pl)
   * Portuguese (Brazil) (pt-BR) Portuguese (Portugal) (pt-PT) Russian (ru)
   * Spanish (es) Spanish (Latin America) (es-419) Swedish (sv) Thai (th)
   * Turkish (tr) Ukrainian (uk) Vietnamese (vi)
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. The API resource name of the company. The resource name is one
   * of the following formats: * `partners/[PARTNER_ID]/customers/[CUSTOMER_ID]`
   * * `partners/[PARTNER_ID]/vendors/[VENDOR_ID]` *
   * `partners/[PARTNER_ID]/vendors/[VENDOR_ID]/customers/[CUSTOMER_ID]`
   * Assigned by the server.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. Email address of customer's users in the owner role.
   * At least one `owner_email` is required. Owners share the same access as
   * admins but can also add, delete, and edit your organization's portal users.
   *
   * @var string[]
   */
  public $ownerEmails;
  /**
   * Input only. If set to true, welcome email will not be sent to the customer.
   * It is recommended to skip the welcome email if devices will be claimed with
   * additional DEVICE_PROTECTION service, as the customer will receive separate
   * emails at device claim time. This field is ignored if this is not a Zero-
   * touch customer.
   *
   * @var bool
   */
  public $skipWelcomeEmail;
  /**
   * Output only. Whether any user from the company has accepted the latest
   * Terms of Service (ToS). See TermsStatus.
   *
   * @var string
   */
  public $termsStatus;

  /**
   * Optional. Email address of customer's users in the admin role. Each email
   * address must be associated with a Google Account.
   *
   * @param string[] $adminEmails
   */
  public function setAdminEmails($adminEmails)
  {
    $this->adminEmails = $adminEmails;
  }
  /**
   * @return string[]
   */
  public function getAdminEmails()
  {
    return $this->adminEmails;
  }
  /**
   * Output only. The ID of the company. Assigned by the server.
   *
   * @param string $companyId
   */
  public function setCompanyId($companyId)
  {
    $this->companyId = $companyId;
  }
  /**
   * @return string
   */
  public function getCompanyId()
  {
    return $this->companyId;
  }
  /**
   * Required. The name of the company. For example _XYZ Corp_. Displayed to the
   * company's employees in the zero-touch enrollment portal.
   *
   * @param string $companyName
   */
  public function setCompanyName($companyName)
  {
    $this->companyName = $companyName;
  }
  /**
   * @return string
   */
  public function getCompanyName()
  {
    return $this->companyName;
  }
  /**
   * Output only. The Google Workspace account associated with this customer.
   * Only used for customer Companies.
   *
   * @param GoogleWorkspaceAccount $googleWorkspaceAccount
   */
  public function setGoogleWorkspaceAccount(GoogleWorkspaceAccount $googleWorkspaceAccount)
  {
    $this->googleWorkspaceAccount = $googleWorkspaceAccount;
  }
  /**
   * @return GoogleWorkspaceAccount
   */
  public function getGoogleWorkspaceAccount()
  {
    return $this->googleWorkspaceAccount;
  }
  /**
   * Input only. The preferred locale of the customer represented as a BCP47
   * language code. This field is validated on input and requests containing
   * unsupported language codes will be rejected. Supported language codes:
   * Arabic (ar) Chinese (Hong Kong) (zh-HK) Chinese (Simplified) (zh-CN)
   * Chinese (Traditional) (zh-TW) Czech (cs) Danish (da) Dutch (nl) English
   * (UK) (en-GB) English (US) (en-US) Filipino (fil) Finnish (fi) French (fr)
   * German (de) Hebrew (iw) Hindi (hi) Hungarian (hu) Indonesian (id) Italian
   * (it) Japanese (ja) Korean (ko) Norwegian (Bokmal) (no) Polish (pl)
   * Portuguese (Brazil) (pt-BR) Portuguese (Portugal) (pt-PT) Russian (ru)
   * Spanish (es) Spanish (Latin America) (es-419) Swedish (sv) Thai (th)
   * Turkish (tr) Ukrainian (uk) Vietnamese (vi)
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
   * Output only. The API resource name of the company. The resource name is one
   * of the following formats: * `partners/[PARTNER_ID]/customers/[CUSTOMER_ID]`
   * * `partners/[PARTNER_ID]/vendors/[VENDOR_ID]` *
   * `partners/[PARTNER_ID]/vendors/[VENDOR_ID]/customers/[CUSTOMER_ID]`
   * Assigned by the server.
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
   * Required. Input only. Email address of customer's users in the owner role.
   * At least one `owner_email` is required. Owners share the same access as
   * admins but can also add, delete, and edit your organization's portal users.
   *
   * @param string[] $ownerEmails
   */
  public function setOwnerEmails($ownerEmails)
  {
    $this->ownerEmails = $ownerEmails;
  }
  /**
   * @return string[]
   */
  public function getOwnerEmails()
  {
    return $this->ownerEmails;
  }
  /**
   * Input only. If set to true, welcome email will not be sent to the customer.
   * It is recommended to skip the welcome email if devices will be claimed with
   * additional DEVICE_PROTECTION service, as the customer will receive separate
   * emails at device claim time. This field is ignored if this is not a Zero-
   * touch customer.
   *
   * @param bool $skipWelcomeEmail
   */
  public function setSkipWelcomeEmail($skipWelcomeEmail)
  {
    $this->skipWelcomeEmail = $skipWelcomeEmail;
  }
  /**
   * @return bool
   */
  public function getSkipWelcomeEmail()
  {
    return $this->skipWelcomeEmail;
  }
  /**
   * Output only. Whether any user from the company has accepted the latest
   * Terms of Service (ToS). See TermsStatus.
   *
   * Accepted values: TERMS_STATUS_UNSPECIFIED, TERMS_STATUS_NOT_ACCEPTED,
   * TERMS_STATUS_ACCEPTED, TERMS_STATUS_STALE
   *
   * @param self::TERMS_STATUS_* $termsStatus
   */
  public function setTermsStatus($termsStatus)
  {
    $this->termsStatus = $termsStatus;
  }
  /**
   * @return self::TERMS_STATUS_*
   */
  public function getTermsStatus()
  {
    return $this->termsStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Company::class, 'Google_Service_AndroidProvisioningPartner_Company');
