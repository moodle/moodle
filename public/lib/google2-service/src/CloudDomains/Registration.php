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

namespace Google\Service\CloudDomains;

class Registration extends \Google\Collection
{
  /**
   * Register failure unspecified.
   */
  public const REGISTER_FAILURE_REASON_REGISTER_FAILURE_REASON_UNSPECIFIED = 'REGISTER_FAILURE_REASON_UNSPECIFIED';
  /**
   * Registration failed for an unknown reason.
   */
  public const REGISTER_FAILURE_REASON_REGISTER_FAILURE_REASON_UNKNOWN = 'REGISTER_FAILURE_REASON_UNKNOWN';
  /**
   * The domain is not available for registration.
   */
  public const REGISTER_FAILURE_REASON_DOMAIN_NOT_AVAILABLE = 'DOMAIN_NOT_AVAILABLE';
  /**
   * The provided contact information was rejected.
   */
  public const REGISTER_FAILURE_REASON_INVALID_CONTACTS = 'INVALID_CONTACTS';
  /**
   * The state is undefined.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The domain is being registered.
   */
  public const STATE_REGISTRATION_PENDING = 'REGISTRATION_PENDING';
  /**
   * The domain registration failed. You can delete resources in this state to
   * allow registration to be retried.
   */
  public const STATE_REGISTRATION_FAILED = 'REGISTRATION_FAILED';
  /**
   * The domain is being transferred from another registrar to Cloud Domains.
   *
   * @deprecated
   */
  public const STATE_TRANSFER_PENDING = 'TRANSFER_PENDING';
  /**
   * The attempt to transfer the domain from another registrar to Cloud Domains
   * failed. You can delete resources in this state and retry the transfer.
   *
   * @deprecated
   */
  public const STATE_TRANSFER_FAILED = 'TRANSFER_FAILED';
  /**
   * The domain is being imported from Google Domains to Cloud Domains.
   *
   * @deprecated
   */
  public const STATE_IMPORT_PENDING = 'IMPORT_PENDING';
  /**
   * The domain is registered and operational. The domain renews automatically
   * as long as it remains in this state and the `RenewalMethod` is set to
   * `AUTOMATIC_RENEWAL`.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The domain is suspended and inoperative. For more details, see the `issues`
   * field.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The domain is no longer managed with Cloud Domains. It may have been
   * transferred to another registrar or exported for management in [Google
   * Domains](https://domains.google/). You can no longer update it with this
   * API, and information shown about it may be stale. Domains in this state are
   * not automatically renewed by Cloud Domains.
   */
  public const STATE_EXPORTED = 'EXPORTED';
  /**
   * The domain is expired.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * Transfer failure unspecified.
   */
  public const TRANSFER_FAILURE_REASON_TRANSFER_FAILURE_REASON_UNSPECIFIED = 'TRANSFER_FAILURE_REASON_UNSPECIFIED';
  /**
   * Transfer failed for an unknown reason.
   */
  public const TRANSFER_FAILURE_REASON_TRANSFER_FAILURE_REASON_UNKNOWN = 'TRANSFER_FAILURE_REASON_UNKNOWN';
  /**
   * An email confirmation sent to the user was rejected or expired.
   */
  public const TRANSFER_FAILURE_REASON_EMAIL_CONFIRMATION_FAILURE = 'EMAIL_CONFIRMATION_FAILURE';
  /**
   * The domain is available for registration.
   */
  public const TRANSFER_FAILURE_REASON_DOMAIN_NOT_REGISTERED = 'DOMAIN_NOT_REGISTERED';
  /**
   * The domain has a transfer lock with its current registrar which must be
   * removed prior to transfer.
   */
  public const TRANSFER_FAILURE_REASON_DOMAIN_HAS_TRANSFER_LOCK = 'DOMAIN_HAS_TRANSFER_LOCK';
  /**
   * The authorization code entered is not valid.
   */
  public const TRANSFER_FAILURE_REASON_INVALID_AUTHORIZATION_CODE = 'INVALID_AUTHORIZATION_CODE';
  /**
   * The transfer was cancelled by the domain owner, current registrar, or TLD
   * registry.
   */
  public const TRANSFER_FAILURE_REASON_TRANSFER_CANCELLED = 'TRANSFER_CANCELLED';
  /**
   * The transfer was rejected by the current registrar. Contact the current
   * registrar for more information.
   */
  public const TRANSFER_FAILURE_REASON_TRANSFER_REJECTED = 'TRANSFER_REJECTED';
  /**
   * The registrant email address cannot be parsed from the domain's current
   * public contact data.
   */
  public const TRANSFER_FAILURE_REASON_INVALID_REGISTRANT_EMAIL_ADDRESS = 'INVALID_REGISTRANT_EMAIL_ADDRESS';
  /**
   * The domain is not eligible for transfer due requirements imposed by the
   * current registrar or TLD registry.
   */
  public const TRANSFER_FAILURE_REASON_DOMAIN_NOT_ELIGIBLE_FOR_TRANSFER = 'DOMAIN_NOT_ELIGIBLE_FOR_TRANSFER';
  /**
   * Another transfer is already pending for this domain. The existing transfer
   * attempt must expire or be cancelled in order to proceed.
   */
  public const TRANSFER_FAILURE_REASON_TRANSFER_ALREADY_PENDING = 'TRANSFER_ALREADY_PENDING';
  protected $collection_key = 'supportedPrivacy';
  protected $contactSettingsType = ContactSettings::class;
  protected $contactSettingsDataType = '';
  /**
   * Output only. The creation timestamp of the `Registration` resource.
   *
   * @var string
   */
  public $createTime;
  protected $dnsSettingsType = DnsSettings::class;
  protected $dnsSettingsDataType = '';
  /**
   * Required. Immutable. The domain name. Unicode domain names must be
   * expressed in Punycode format.
   *
   * @var string
   */
  public $domainName;
  /**
   * Output only. Special properties of the domain.
   *
   * @var string[]
   */
  public $domainProperties;
  /**
   * Output only. The expiration timestamp of the `Registration`.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The set of issues with the `Registration` that require
   * attention.
   *
   * @var string[]
   */
  public $issues;
  /**
   * Set of labels associated with the `Registration`.
   *
   * @var string[]
   */
  public $labels;
  protected $managementSettingsType = ManagementSettings::class;
  protected $managementSettingsDataType = '';
  /**
   * Output only. Name of the `Registration` resource, in the format
   * `projects/locations/registrations/`.
   *
   * @var string
   */
  public $name;
  protected $pendingContactSettingsType = ContactSettings::class;
  protected $pendingContactSettingsDataType = '';
  /**
   * Output only. The reason the domain registration failed. Only set for
   * domains in REGISTRATION_FAILED state.
   *
   * @var string
   */
  public $registerFailureReason;
  /**
   * Output only. The state of the `Registration`
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Set of options for the `contact_settings.privacy` field that
   * this `Registration` supports.
   *
   * @var string[]
   */
  public $supportedPrivacy;
  /**
   * Output only. Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). The reason the domain transfer failed. Only set for domains
   * in TRANSFER_FAILED state.
   *
   * @deprecated
   * @var string
   */
  public $transferFailureReason;

  /**
   * Required. Settings for contact information linked to the `Registration`.
   * You cannot update these with the `UpdateRegistration` method. To update
   * these settings, use the `ConfigureContactSettings` method.
   *
   * @param ContactSettings $contactSettings
   */
  public function setContactSettings(ContactSettings $contactSettings)
  {
    $this->contactSettings = $contactSettings;
  }
  /**
   * @return ContactSettings
   */
  public function getContactSettings()
  {
    return $this->contactSettings;
  }
  /**
   * Output only. The creation timestamp of the `Registration` resource.
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
   * Settings controlling the DNS configuration of the `Registration`. You
   * cannot update these with the `UpdateRegistration` method. To update these
   * settings, use the `ConfigureDnsSettings` method.
   *
   * @param DnsSettings $dnsSettings
   */
  public function setDnsSettings(DnsSettings $dnsSettings)
  {
    $this->dnsSettings = $dnsSettings;
  }
  /**
   * @return DnsSettings
   */
  public function getDnsSettings()
  {
    return $this->dnsSettings;
  }
  /**
   * Required. Immutable. The domain name. Unicode domain names must be
   * expressed in Punycode format.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
  /**
   * Output only. Special properties of the domain.
   *
   * @param string[] $domainProperties
   */
  public function setDomainProperties($domainProperties)
  {
    $this->domainProperties = $domainProperties;
  }
  /**
   * @return string[]
   */
  public function getDomainProperties()
  {
    return $this->domainProperties;
  }
  /**
   * Output only. The expiration timestamp of the `Registration`.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The set of issues with the `Registration` that require
   * attention.
   *
   * @param string[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return string[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Set of labels associated with the `Registration`.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Settings for management of the `Registration`, including renewal, billing,
   * and transfer. You cannot update these with the `UpdateRegistration` method.
   * To update these settings, use the `ConfigureManagementSettings` method.
   *
   * @param ManagementSettings $managementSettings
   */
  public function setManagementSettings(ManagementSettings $managementSettings)
  {
    $this->managementSettings = $managementSettings;
  }
  /**
   * @return ManagementSettings
   */
  public function getManagementSettings()
  {
    return $this->managementSettings;
  }
  /**
   * Output only. Name of the `Registration` resource, in the format
   * `projects/locations/registrations/`.
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
   * Output only. Pending contact settings for the `Registration`. Updates to
   * the `contact_settings` field that change its `registrant_contact` or
   * `privacy` fields require email confirmation by the `registrant_contact`
   * before taking effect. This field is set only if there are pending updates
   * to the `contact_settings` that have not been confirmed. To confirm the
   * changes, the `registrant_contact` must follow the instructions in the email
   * they receive.
   *
   * @param ContactSettings $pendingContactSettings
   */
  public function setPendingContactSettings(ContactSettings $pendingContactSettings)
  {
    $this->pendingContactSettings = $pendingContactSettings;
  }
  /**
   * @return ContactSettings
   */
  public function getPendingContactSettings()
  {
    return $this->pendingContactSettings;
  }
  /**
   * Output only. The reason the domain registration failed. Only set for
   * domains in REGISTRATION_FAILED state.
   *
   * Accepted values: REGISTER_FAILURE_REASON_UNSPECIFIED,
   * REGISTER_FAILURE_REASON_UNKNOWN, DOMAIN_NOT_AVAILABLE, INVALID_CONTACTS
   *
   * @param self::REGISTER_FAILURE_REASON_* $registerFailureReason
   */
  public function setRegisterFailureReason($registerFailureReason)
  {
    $this->registerFailureReason = $registerFailureReason;
  }
  /**
   * @return self::REGISTER_FAILURE_REASON_*
   */
  public function getRegisterFailureReason()
  {
    return $this->registerFailureReason;
  }
  /**
   * Output only. The state of the `Registration`
   *
   * Accepted values: STATE_UNSPECIFIED, REGISTRATION_PENDING,
   * REGISTRATION_FAILED, TRANSFER_PENDING, TRANSFER_FAILED, IMPORT_PENDING,
   * ACTIVE, SUSPENDED, EXPORTED, EXPIRED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Set of options for the `contact_settings.privacy` field that
   * this `Registration` supports.
   *
   * @param string[] $supportedPrivacy
   */
  public function setSupportedPrivacy($supportedPrivacy)
  {
    $this->supportedPrivacy = $supportedPrivacy;
  }
  /**
   * @return string[]
   */
  public function getSupportedPrivacy()
  {
    return $this->supportedPrivacy;
  }
  /**
   * Output only. Deprecated: For more information, see [Cloud Domains feature
   * deprecation](https://cloud.google.com/domains/docs/deprecations/feature-
   * deprecations). The reason the domain transfer failed. Only set for domains
   * in TRANSFER_FAILED state.
   *
   * Accepted values: TRANSFER_FAILURE_REASON_UNSPECIFIED,
   * TRANSFER_FAILURE_REASON_UNKNOWN, EMAIL_CONFIRMATION_FAILURE,
   * DOMAIN_NOT_REGISTERED, DOMAIN_HAS_TRANSFER_LOCK,
   * INVALID_AUTHORIZATION_CODE, TRANSFER_CANCELLED, TRANSFER_REJECTED,
   * INVALID_REGISTRANT_EMAIL_ADDRESS, DOMAIN_NOT_ELIGIBLE_FOR_TRANSFER,
   * TRANSFER_ALREADY_PENDING
   *
   * @deprecated
   * @param self::TRANSFER_FAILURE_REASON_* $transferFailureReason
   */
  public function setTransferFailureReason($transferFailureReason)
  {
    $this->transferFailureReason = $transferFailureReason;
  }
  /**
   * @deprecated
   * @return self::TRANSFER_FAILURE_REASON_*
   */
  public function getTransferFailureReason()
  {
    return $this->transferFailureReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Registration::class, 'Google_Service_CloudDomains_Registration');
