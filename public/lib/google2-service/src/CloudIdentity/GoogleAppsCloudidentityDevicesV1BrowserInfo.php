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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1BrowserInfo extends \Google\Model
{
  /**
   * Management state is not specified.
   */
  public const BROWSER_MANAGEMENT_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Browser/Profile is not managed by any customer.
   */
  public const BROWSER_MANAGEMENT_STATE_UNMANAGED = 'UNMANAGED';
  /**
   * Browser/Profile is managed, but by some other customer.
   */
  public const BROWSER_MANAGEMENT_STATE_MANAGED_BY_OTHER_DOMAIN = 'MANAGED_BY_OTHER_DOMAIN';
  /**
   * Profile is managed by customer.
   */
  public const BROWSER_MANAGEMENT_STATE_PROFILE_MANAGED = 'PROFILE_MANAGED';
  /**
   * Browser is managed by customer.
   */
  public const BROWSER_MANAGEMENT_STATE_BROWSER_MANAGED = 'BROWSER_MANAGED';
  /**
   * Password protection is not specified.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PASSWORD_PROTECTION_TRIGGER_UNSPECIFIED = 'PASSWORD_PROTECTION_TRIGGER_UNSPECIFIED';
  /**
   * Password reuse is never detected.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PROTECTION_OFF = 'PROTECTION_OFF';
  /**
   * Warning is shown when the user reuses their protected password on a non-
   * allowed site.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PASSWORD_REUSE = 'PASSWORD_REUSE';
  /**
   * Warning is shown when the user reuses their protected password on a
   * phishing site.
   */
  public const PASSWORD_PROTECTION_WARNING_TRIGGER_PHISHING_REUSE = 'PHISHING_REUSE';
  /**
   * Browser protection level is not specified.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_SAFE_BROWSING_LEVEL_UNSPECIFIED = 'SAFE_BROWSING_LEVEL_UNSPECIFIED';
  /**
   * No protection against dangerous websites, downloads, and extensions.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_DISABLED = 'DISABLED';
  /**
   * Standard protection against websites, downloads, and extensions that are
   * known to be dangerous.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_STANDARD = 'STANDARD';
  /**
   * Faster, proactive protection against dangerous websites, downloads, and
   * extensions.
   */
  public const SAFE_BROWSING_PROTECTION_LEVEL_ENHANCED = 'ENHANCED';
  /**
   * Output only. Browser's management state.
   *
   * @var string
   */
  public $browserManagementState;
  /**
   * Version of the request initiating browser. E.g. `91.0.4442.4`.
   *
   * @var string
   */
  public $browserVersion;
  /**
   * Current state of [built-in DNS
   * client](https://chromeenterprise.google/policies/#BuiltInDnsClientEnabled).
   *
   * @var bool
   */
  public $isBuiltInDnsClientEnabled;
  /**
   * Current state of [bulk data analysis](https://chromeenterprise.google/polic
   * ies/#OnBulkDataEntryEnterpriseConnector). Set to true if provider list from
   * Chrome is non-empty.
   *
   * @var bool
   */
  public $isBulkDataEntryAnalysisEnabled;
  /**
   * Deprecated: This field is not used for Chrome version 118 and later.
   * Current state of [Chrome
   * Cleanup](https://chromeenterprise.google/policies/#ChromeCleanupEnabled).
   *
   * @deprecated
   * @var bool
   */
  public $isChromeCleanupEnabled;
  /**
   * Current state of [Chrome Remote Desktop
   * app](https://chromeenterprise.google/policies/#URLBlocklist).
   *
   * @var bool
   */
  public $isChromeRemoteDesktopAppBlocked;
  /**
   * Current state of [file download analysis](https://chromeenterprise.google/p
   * olicies/#OnFileDownloadedEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @var bool
   */
  public $isFileDownloadAnalysisEnabled;
  /**
   * Current state of [file upload analysis](https://chromeenterprise.google/pol
   * icies/#OnFileAttachedEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @var bool
   */
  public $isFileUploadAnalysisEnabled;
  /**
   * Current state of [real-time URL check](https://chromeenterprise.google/poli
   * cies/#EnterpriseRealTimeUrlCheckMode). Set to true if provider list from
   * Chrome is non-empty.
   *
   * @var bool
   */
  public $isRealtimeUrlCheckEnabled;
  /**
   * Current state of [security event analysis](https://chromeenterprise.google/
   * policies/#OnSecurityEventEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @var bool
   */
  public $isSecurityEventAnalysisEnabled;
  /**
   * Current state of [site isolation](https://chromeenterprise.google/policies/
   * ?policy=IsolateOrigins).
   *
   * @var bool
   */
  public $isSiteIsolationEnabled;
  /**
   * Current state of [third-party blocking](https://chromeenterprise.google/pol
   * icies/#ThirdPartyBlockingEnabled).
   *
   * @var bool
   */
  public $isThirdPartyBlockingEnabled;
  /**
   * Current state of [password protection trigger](https://chromeenterprise.goo
   * gle/policies/#PasswordProtectionWarningTrigger).
   *
   * @var string
   */
  public $passwordProtectionWarningTrigger;
  /**
   * Current state of [Safe Browsing protection level](https://chromeenterprise.
   * google/policies/#SafeBrowsingProtectionLevel).
   *
   * @var string
   */
  public $safeBrowsingProtectionLevel;

  /**
   * Output only. Browser's management state.
   *
   * Accepted values: UNSPECIFIED, UNMANAGED, MANAGED_BY_OTHER_DOMAIN,
   * PROFILE_MANAGED, BROWSER_MANAGED
   *
   * @param self::BROWSER_MANAGEMENT_STATE_* $browserManagementState
   */
  public function setBrowserManagementState($browserManagementState)
  {
    $this->browserManagementState = $browserManagementState;
  }
  /**
   * @return self::BROWSER_MANAGEMENT_STATE_*
   */
  public function getBrowserManagementState()
  {
    return $this->browserManagementState;
  }
  /**
   * Version of the request initiating browser. E.g. `91.0.4442.4`.
   *
   * @param string $browserVersion
   */
  public function setBrowserVersion($browserVersion)
  {
    $this->browserVersion = $browserVersion;
  }
  /**
   * @return string
   */
  public function getBrowserVersion()
  {
    return $this->browserVersion;
  }
  /**
   * Current state of [built-in DNS
   * client](https://chromeenterprise.google/policies/#BuiltInDnsClientEnabled).
   *
   * @param bool $isBuiltInDnsClientEnabled
   */
  public function setIsBuiltInDnsClientEnabled($isBuiltInDnsClientEnabled)
  {
    $this->isBuiltInDnsClientEnabled = $isBuiltInDnsClientEnabled;
  }
  /**
   * @return bool
   */
  public function getIsBuiltInDnsClientEnabled()
  {
    return $this->isBuiltInDnsClientEnabled;
  }
  /**
   * Current state of [bulk data analysis](https://chromeenterprise.google/polic
   * ies/#OnBulkDataEntryEnterpriseConnector). Set to true if provider list from
   * Chrome is non-empty.
   *
   * @param bool $isBulkDataEntryAnalysisEnabled
   */
  public function setIsBulkDataEntryAnalysisEnabled($isBulkDataEntryAnalysisEnabled)
  {
    $this->isBulkDataEntryAnalysisEnabled = $isBulkDataEntryAnalysisEnabled;
  }
  /**
   * @return bool
   */
  public function getIsBulkDataEntryAnalysisEnabled()
  {
    return $this->isBulkDataEntryAnalysisEnabled;
  }
  /**
   * Deprecated: This field is not used for Chrome version 118 and later.
   * Current state of [Chrome
   * Cleanup](https://chromeenterprise.google/policies/#ChromeCleanupEnabled).
   *
   * @deprecated
   * @param bool $isChromeCleanupEnabled
   */
  public function setIsChromeCleanupEnabled($isChromeCleanupEnabled)
  {
    $this->isChromeCleanupEnabled = $isChromeCleanupEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIsChromeCleanupEnabled()
  {
    return $this->isChromeCleanupEnabled;
  }
  /**
   * Current state of [Chrome Remote Desktop
   * app](https://chromeenterprise.google/policies/#URLBlocklist).
   *
   * @param bool $isChromeRemoteDesktopAppBlocked
   */
  public function setIsChromeRemoteDesktopAppBlocked($isChromeRemoteDesktopAppBlocked)
  {
    $this->isChromeRemoteDesktopAppBlocked = $isChromeRemoteDesktopAppBlocked;
  }
  /**
   * @return bool
   */
  public function getIsChromeRemoteDesktopAppBlocked()
  {
    return $this->isChromeRemoteDesktopAppBlocked;
  }
  /**
   * Current state of [file download analysis](https://chromeenterprise.google/p
   * olicies/#OnFileDownloadedEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @param bool $isFileDownloadAnalysisEnabled
   */
  public function setIsFileDownloadAnalysisEnabled($isFileDownloadAnalysisEnabled)
  {
    $this->isFileDownloadAnalysisEnabled = $isFileDownloadAnalysisEnabled;
  }
  /**
   * @return bool
   */
  public function getIsFileDownloadAnalysisEnabled()
  {
    return $this->isFileDownloadAnalysisEnabled;
  }
  /**
   * Current state of [file upload analysis](https://chromeenterprise.google/pol
   * icies/#OnFileAttachedEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @param bool $isFileUploadAnalysisEnabled
   */
  public function setIsFileUploadAnalysisEnabled($isFileUploadAnalysisEnabled)
  {
    $this->isFileUploadAnalysisEnabled = $isFileUploadAnalysisEnabled;
  }
  /**
   * @return bool
   */
  public function getIsFileUploadAnalysisEnabled()
  {
    return $this->isFileUploadAnalysisEnabled;
  }
  /**
   * Current state of [real-time URL check](https://chromeenterprise.google/poli
   * cies/#EnterpriseRealTimeUrlCheckMode). Set to true if provider list from
   * Chrome is non-empty.
   *
   * @param bool $isRealtimeUrlCheckEnabled
   */
  public function setIsRealtimeUrlCheckEnabled($isRealtimeUrlCheckEnabled)
  {
    $this->isRealtimeUrlCheckEnabled = $isRealtimeUrlCheckEnabled;
  }
  /**
   * @return bool
   */
  public function getIsRealtimeUrlCheckEnabled()
  {
    return $this->isRealtimeUrlCheckEnabled;
  }
  /**
   * Current state of [security event analysis](https://chromeenterprise.google/
   * policies/#OnSecurityEventEnterpriseConnector). Set to true if provider list
   * from Chrome is non-empty.
   *
   * @param bool $isSecurityEventAnalysisEnabled
   */
  public function setIsSecurityEventAnalysisEnabled($isSecurityEventAnalysisEnabled)
  {
    $this->isSecurityEventAnalysisEnabled = $isSecurityEventAnalysisEnabled;
  }
  /**
   * @return bool
   */
  public function getIsSecurityEventAnalysisEnabled()
  {
    return $this->isSecurityEventAnalysisEnabled;
  }
  /**
   * Current state of [site isolation](https://chromeenterprise.google/policies/
   * ?policy=IsolateOrigins).
   *
   * @param bool $isSiteIsolationEnabled
   */
  public function setIsSiteIsolationEnabled($isSiteIsolationEnabled)
  {
    $this->isSiteIsolationEnabled = $isSiteIsolationEnabled;
  }
  /**
   * @return bool
   */
  public function getIsSiteIsolationEnabled()
  {
    return $this->isSiteIsolationEnabled;
  }
  /**
   * Current state of [third-party blocking](https://chromeenterprise.google/pol
   * icies/#ThirdPartyBlockingEnabled).
   *
   * @param bool $isThirdPartyBlockingEnabled
   */
  public function setIsThirdPartyBlockingEnabled($isThirdPartyBlockingEnabled)
  {
    $this->isThirdPartyBlockingEnabled = $isThirdPartyBlockingEnabled;
  }
  /**
   * @return bool
   */
  public function getIsThirdPartyBlockingEnabled()
  {
    return $this->isThirdPartyBlockingEnabled;
  }
  /**
   * Current state of [password protection trigger](https://chromeenterprise.goo
   * gle/policies/#PasswordProtectionWarningTrigger).
   *
   * Accepted values: PASSWORD_PROTECTION_TRIGGER_UNSPECIFIED, PROTECTION_OFF,
   * PASSWORD_REUSE, PHISHING_REUSE
   *
   * @param self::PASSWORD_PROTECTION_WARNING_TRIGGER_* $passwordProtectionWarningTrigger
   */
  public function setPasswordProtectionWarningTrigger($passwordProtectionWarningTrigger)
  {
    $this->passwordProtectionWarningTrigger = $passwordProtectionWarningTrigger;
  }
  /**
   * @return self::PASSWORD_PROTECTION_WARNING_TRIGGER_*
   */
  public function getPasswordProtectionWarningTrigger()
  {
    return $this->passwordProtectionWarningTrigger;
  }
  /**
   * Current state of [Safe Browsing protection level](https://chromeenterprise.
   * google/policies/#SafeBrowsingProtectionLevel).
   *
   * Accepted values: SAFE_BROWSING_LEVEL_UNSPECIFIED, DISABLED, STANDARD,
   * ENHANCED
   *
   * @param self::SAFE_BROWSING_PROTECTION_LEVEL_* $safeBrowsingProtectionLevel
   */
  public function setSafeBrowsingProtectionLevel($safeBrowsingProtectionLevel)
  {
    $this->safeBrowsingProtectionLevel = $safeBrowsingProtectionLevel;
  }
  /**
   * @return self::SAFE_BROWSING_PROTECTION_LEVEL_*
   */
  public function getSafeBrowsingProtectionLevel()
  {
    return $this->safeBrowsingProtectionLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1BrowserInfo::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1BrowserInfo');
