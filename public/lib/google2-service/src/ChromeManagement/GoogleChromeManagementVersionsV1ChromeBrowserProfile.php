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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1ChromeBrowserProfile extends \Google\Model
{
  /**
   * Unspecified affiliation state.
   */
  public const AFFILIATION_STATE_AFFILIATION_STATE_UNSPECIFIED = 'AFFILIATION_STATE_UNSPECIFIED';
  /**
   * Unaffiliated - but we do not have the details for the type of unaffiliated
   * profile.
   */
  public const AFFILIATION_STATE_UNAFFILIATED_GENERIC = 'UNAFFILIATED_GENERIC';
  /**
   * Unaffiliated - A managed profile that appears on a totally unamanaged
   * browser.
   */
  public const AFFILIATION_STATE_PROFILE_ONLY = 'PROFILE_ONLY';
  /**
   * Unaffiliated - A managed profile that appears on a machine that is locally
   * managed by a different organization (through platform management mechanisms
   * like GPO).
   */
  public const AFFILIATION_STATE_UNAFFILIATED_LOCAL_MACHINE = 'UNAFFILIATED_LOCAL_MACHINE';
  /**
   * Unaffiliated - A managed profile that appears on a managed browser that is
   * cloud managed by a different organization (using Chrome Browser Cloud
   * Management).
   */
  public const AFFILIATION_STATE_UNAFFILIATED_CLOUD_MACHINE = 'UNAFFILIATED_CLOUD_MACHINE';
  /**
   * Affiliated - Both the profile and the managed browser are managed by the
   * same organization.
   */
  public const AFFILIATION_STATE_AFFILIATED_CLOUD_MANAGED = 'AFFILIATED_CLOUD_MANAGED';
  /**
   * Represents an unspecified identity provider.
   */
  public const IDENTITY_PROVIDER_IDENTITY_PROVIDER_UNSPECIFIED = 'IDENTITY_PROVIDER_UNSPECIFIED';
  /**
   * Represents a Google identity provider.
   */
  public const IDENTITY_PROVIDER_GOOGLE_IDENTITY_PROVIDER = 'GOOGLE_IDENTITY_PROVIDER';
  /**
   * Represents an external identity provider.
   */
  public const IDENTITY_PROVIDER_EXTERNAL_IDENTITY_PROVIDER = 'EXTERNAL_IDENTITY_PROVIDER';
  /**
   * Output only. The specific affiliation state of the profile.
   *
   * @var string
   */
  public $affiliationState;
  /**
   * Optional. Location of the profile annotated by the admin.
   *
   * @var string
   */
  public $annotatedLocation;
  /**
   * Optional. User of the profile annotated by the admin.
   *
   * @var string
   */
  public $annotatedUser;
  protected $attestationCredentialType = GoogleChromeManagementVersionsV1AttestationCredential::class;
  protected $attestationCredentialDataType = '';
  /**
   * Output only. Channel of the browser on which the profile exists.
   *
   * @var string
   */
  public $browserChannel;
  /**
   * Output only. Version of the browser on which the profile exists.
   *
   * @var string
   */
  public $browserVersion;
  protected $deviceInfoType = GoogleChromeManagementVersionsV1DeviceInfo::class;
  protected $deviceInfoDataType = '';
  /**
   * Output only. Profile display name set by client.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Etag of this ChromeBrowserProfile resource. This etag can be
   * used with UPDATE operation to ensure consistency.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Number of extensions installed on the profile.
   *
   * @var string
   */
  public $extensionCount;
  /**
   * Output only. Timestamp of the first enrollment of the profile.
   *
   * @var string
   */
  public $firstEnrollmentTime;
  /**
   * Output only. Identify provider of the profile.
   *
   * @var string
   */
  public $identityProvider;
  /**
   * Output only. Timestamp of the latest activity by the profile.
   *
   * @var string
   */
  public $lastActivityTime;
  /**
   * Output only. Timestamp of the latest policy fetch by the profile.
   *
   * @var string
   */
  public $lastPolicyFetchTime;
  /**
   * Output only. Timestamp of the latest policy sync by the profile.
   *
   * @var string
   */
  public $lastPolicySyncTime;
  /**
   * Output only. Timestamp of the latest status report by the profile.
   *
   * @var string
   */
  public $lastStatusReportTime;
  /**
   * Identifier. Format: customers/{customer_id}/profiles/{profile_permanent_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. OS platform of the device on which the profile exists.
   *
   * @var string
   */
  public $osPlatformType;
  /**
   * Output only. Major OS platform version of the device on which the profile
   * exists, from profile reporting.
   *
   * @var string
   */
  public $osPlatformVersion;
  /**
   * Output only. OS version of the device on which the profile exists.
   *
   * @var string
   */
  public $osVersion;
  /**
   * Output only. Number of policies applied on the profile.
   *
   * @var string
   */
  public $policyCount;
  /**
   * Output only. Chrome client side profile ID.
   *
   * @var string
   */
  public $profileId;
  /**
   * Output only. Profile permanent ID is the unique identifier of a profile
   * within one customer.
   *
   * @var string
   */
  public $profilePermanentId;
  protected $reportingDataType = GoogleChromeManagementVersionsV1ReportingData::class;
  protected $reportingDataDataType = '';
  /**
   * Output only. Whether the profile supports FCM notifications.
   *
   * @var bool
   */
  public $supportsFcmNotifications;
  /**
   * Output only. Email address of the user to which the profile belongs.
   *
   * @var string
   */
  public $userEmail;
  /**
   * Output only. Unique Directory API ID of the user that can be used in Admin
   * SDK Users API.
   *
   * @var string
   */
  public $userId;

  /**
   * Output only. The specific affiliation state of the profile.
   *
   * Accepted values: AFFILIATION_STATE_UNSPECIFIED, UNAFFILIATED_GENERIC,
   * PROFILE_ONLY, UNAFFILIATED_LOCAL_MACHINE, UNAFFILIATED_CLOUD_MACHINE,
   * AFFILIATED_CLOUD_MANAGED
   *
   * @param self::AFFILIATION_STATE_* $affiliationState
   */
  public function setAffiliationState($affiliationState)
  {
    $this->affiliationState = $affiliationState;
  }
  /**
   * @return self::AFFILIATION_STATE_*
   */
  public function getAffiliationState()
  {
    return $this->affiliationState;
  }
  /**
   * Optional. Location of the profile annotated by the admin.
   *
   * @param string $annotatedLocation
   */
  public function setAnnotatedLocation($annotatedLocation)
  {
    $this->annotatedLocation = $annotatedLocation;
  }
  /**
   * @return string
   */
  public function getAnnotatedLocation()
  {
    return $this->annotatedLocation;
  }
  /**
   * Optional. User of the profile annotated by the admin.
   *
   * @param string $annotatedUser
   */
  public function setAnnotatedUser($annotatedUser)
  {
    $this->annotatedUser = $annotatedUser;
  }
  /**
   * @return string
   */
  public function getAnnotatedUser()
  {
    return $this->annotatedUser;
  }
  /**
   * Output only. Attestation credential information of the profile.
   *
   * @param GoogleChromeManagementVersionsV1AttestationCredential $attestationCredential
   */
  public function setAttestationCredential(GoogleChromeManagementVersionsV1AttestationCredential $attestationCredential)
  {
    $this->attestationCredential = $attestationCredential;
  }
  /**
   * @return GoogleChromeManagementVersionsV1AttestationCredential
   */
  public function getAttestationCredential()
  {
    return $this->attestationCredential;
  }
  /**
   * Output only. Channel of the browser on which the profile exists.
   *
   * @param string $browserChannel
   */
  public function setBrowserChannel($browserChannel)
  {
    $this->browserChannel = $browserChannel;
  }
  /**
   * @return string
   */
  public function getBrowserChannel()
  {
    return $this->browserChannel;
  }
  /**
   * Output only. Version of the browser on which the profile exists.
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
   * Output only. Basic information of the device on which the profile exists.
   * This information is only available for the affiliated profiles.
   *
   * @param GoogleChromeManagementVersionsV1DeviceInfo $deviceInfo
   */
  public function setDeviceInfo(GoogleChromeManagementVersionsV1DeviceInfo $deviceInfo)
  {
    $this->deviceInfo = $deviceInfo;
  }
  /**
   * @return GoogleChromeManagementVersionsV1DeviceInfo
   */
  public function getDeviceInfo()
  {
    return $this->deviceInfo;
  }
  /**
   * Output only. Profile display name set by client.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Etag of this ChromeBrowserProfile resource. This etag can be
   * used with UPDATE operation to ensure consistency.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Number of extensions installed on the profile.
   *
   * @param string $extensionCount
   */
  public function setExtensionCount($extensionCount)
  {
    $this->extensionCount = $extensionCount;
  }
  /**
   * @return string
   */
  public function getExtensionCount()
  {
    return $this->extensionCount;
  }
  /**
   * Output only. Timestamp of the first enrollment of the profile.
   *
   * @param string $firstEnrollmentTime
   */
  public function setFirstEnrollmentTime($firstEnrollmentTime)
  {
    $this->firstEnrollmentTime = $firstEnrollmentTime;
  }
  /**
   * @return string
   */
  public function getFirstEnrollmentTime()
  {
    return $this->firstEnrollmentTime;
  }
  /**
   * Output only. Identify provider of the profile.
   *
   * Accepted values: IDENTITY_PROVIDER_UNSPECIFIED, GOOGLE_IDENTITY_PROVIDER,
   * EXTERNAL_IDENTITY_PROVIDER
   *
   * @param self::IDENTITY_PROVIDER_* $identityProvider
   */
  public function setIdentityProvider($identityProvider)
  {
    $this->identityProvider = $identityProvider;
  }
  /**
   * @return self::IDENTITY_PROVIDER_*
   */
  public function getIdentityProvider()
  {
    return $this->identityProvider;
  }
  /**
   * Output only. Timestamp of the latest activity by the profile.
   *
   * @param string $lastActivityTime
   */
  public function setLastActivityTime($lastActivityTime)
  {
    $this->lastActivityTime = $lastActivityTime;
  }
  /**
   * @return string
   */
  public function getLastActivityTime()
  {
    return $this->lastActivityTime;
  }
  /**
   * Output only. Timestamp of the latest policy fetch by the profile.
   *
   * @param string $lastPolicyFetchTime
   */
  public function setLastPolicyFetchTime($lastPolicyFetchTime)
  {
    $this->lastPolicyFetchTime = $lastPolicyFetchTime;
  }
  /**
   * @return string
   */
  public function getLastPolicyFetchTime()
  {
    return $this->lastPolicyFetchTime;
  }
  /**
   * Output only. Timestamp of the latest policy sync by the profile.
   *
   * @param string $lastPolicySyncTime
   */
  public function setLastPolicySyncTime($lastPolicySyncTime)
  {
    $this->lastPolicySyncTime = $lastPolicySyncTime;
  }
  /**
   * @return string
   */
  public function getLastPolicySyncTime()
  {
    return $this->lastPolicySyncTime;
  }
  /**
   * Output only. Timestamp of the latest status report by the profile.
   *
   * @param string $lastStatusReportTime
   */
  public function setLastStatusReportTime($lastStatusReportTime)
  {
    $this->lastStatusReportTime = $lastStatusReportTime;
  }
  /**
   * @return string
   */
  public function getLastStatusReportTime()
  {
    return $this->lastStatusReportTime;
  }
  /**
   * Identifier. Format: customers/{customer_id}/profiles/{profile_permanent_id}
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
   * Output only. OS platform of the device on which the profile exists.
   *
   * @param string $osPlatformType
   */
  public function setOsPlatformType($osPlatformType)
  {
    $this->osPlatformType = $osPlatformType;
  }
  /**
   * @return string
   */
  public function getOsPlatformType()
  {
    return $this->osPlatformType;
  }
  /**
   * Output only. Major OS platform version of the device on which the profile
   * exists, from profile reporting.
   *
   * @param string $osPlatformVersion
   */
  public function setOsPlatformVersion($osPlatformVersion)
  {
    $this->osPlatformVersion = $osPlatformVersion;
  }
  /**
   * @return string
   */
  public function getOsPlatformVersion()
  {
    return $this->osPlatformVersion;
  }
  /**
   * Output only. OS version of the device on which the profile exists.
   *
   * @param string $osVersion
   */
  public function setOsVersion($osVersion)
  {
    $this->osVersion = $osVersion;
  }
  /**
   * @return string
   */
  public function getOsVersion()
  {
    return $this->osVersion;
  }
  /**
   * Output only. Number of policies applied on the profile.
   *
   * @param string $policyCount
   */
  public function setPolicyCount($policyCount)
  {
    $this->policyCount = $policyCount;
  }
  /**
   * @return string
   */
  public function getPolicyCount()
  {
    return $this->policyCount;
  }
  /**
   * Output only. Chrome client side profile ID.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * Output only. Profile permanent ID is the unique identifier of a profile
   * within one customer.
   *
   * @param string $profilePermanentId
   */
  public function setProfilePermanentId($profilePermanentId)
  {
    $this->profilePermanentId = $profilePermanentId;
  }
  /**
   * @return string
   */
  public function getProfilePermanentId()
  {
    return $this->profilePermanentId;
  }
  /**
   * Output only. Detailed reporting data of the profile. This information is
   * only available when the profile reporting policy is enabled.
   *
   * @param GoogleChromeManagementVersionsV1ReportingData $reportingData
   */
  public function setReportingData(GoogleChromeManagementVersionsV1ReportingData $reportingData)
  {
    $this->reportingData = $reportingData;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ReportingData
   */
  public function getReportingData()
  {
    return $this->reportingData;
  }
  /**
   * Output only. Whether the profile supports FCM notifications.
   *
   * @param bool $supportsFcmNotifications
   */
  public function setSupportsFcmNotifications($supportsFcmNotifications)
  {
    $this->supportsFcmNotifications = $supportsFcmNotifications;
  }
  /**
   * @return bool
   */
  public function getSupportsFcmNotifications()
  {
    return $this->supportsFcmNotifications;
  }
  /**
   * Output only. Email address of the user to which the profile belongs.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
  /**
   * Output only. Unique Directory API ID of the user that can be used in Admin
   * SDK Users API.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ChromeBrowserProfile::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ChromeBrowserProfile');
