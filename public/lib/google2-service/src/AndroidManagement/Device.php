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

namespace Google\Service\AndroidManagement;

class Device extends \Google\Collection
{
  /**
   * This value is disallowed.
   */
  public const APPLIED_STATE_DEVICE_STATE_UNSPECIFIED = 'DEVICE_STATE_UNSPECIFIED';
  /**
   * The device is active.
   */
  public const APPLIED_STATE_ACTIVE = 'ACTIVE';
  /**
   * The device is disabled.
   */
  public const APPLIED_STATE_DISABLED = 'DISABLED';
  /**
   * The device was deleted. This state is never returned by an API call, but is
   * used in the final status report when the device acknowledges the deletion.
   * If the device is deleted via the API call, this state is published to
   * Pub/Sub. If the user deletes the work profile or resets the device, the
   * device state will remain unknown to the server.
   */
  public const APPLIED_STATE_DELETED = 'DELETED';
  /**
   * The device is being provisioned. Newly enrolled devices are in this state
   * until they have a policy applied.
   */
  public const APPLIED_STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The device is lost. This state is only possible on organization-owned
   * devices.
   */
  public const APPLIED_STATE_LOST = 'LOST';
  /**
   * The device is preparing for migrating to Android Management API. No further
   * action is needed for the migration to continue.
   */
  public const APPLIED_STATE_PREPARING_FOR_MIGRATION = 'PREPARING_FOR_MIGRATION';
  /**
   * This is a financed device that has been "locked" by the financing agent.
   * This means certain policy settings have been applied which limit device
   * functionality until the device has been "unlocked" by the financing agent.
   * The device will continue to apply policy settings excluding those
   * overridden by the financing agent. When the device is "locked", the state
   * is reported in appliedState as DEACTIVATED_BY_DEVICE_FINANCE.
   */
  public const APPLIED_STATE_DEACTIVATED_BY_DEVICE_FINANCE = 'DEACTIVATED_BY_DEVICE_FINANCE';
  /**
   * This value is disallowed.
   */
  public const MANAGEMENT_MODE_MANAGEMENT_MODE_UNSPECIFIED = 'MANAGEMENT_MODE_UNSPECIFIED';
  /**
   * Device owner. Android Device Policy has full control over the device.
   */
  public const MANAGEMENT_MODE_DEVICE_OWNER = 'DEVICE_OWNER';
  /**
   * Profile owner. Android Device Policy has control over a managed profile on
   * the device.
   */
  public const MANAGEMENT_MODE_PROFILE_OWNER = 'PROFILE_OWNER';
  /**
   * Ownership is unspecified.
   */
  public const OWNERSHIP_OWNERSHIP_UNSPECIFIED = 'OWNERSHIP_UNSPECIFIED';
  /**
   * Device is company-owned.
   */
  public const OWNERSHIP_COMPANY_OWNED = 'COMPANY_OWNED';
  /**
   * Device is personally-owned.
   */
  public const OWNERSHIP_PERSONALLY_OWNED = 'PERSONALLY_OWNED';
  /**
   * This value is disallowed.
   */
  public const STATE_DEVICE_STATE_UNSPECIFIED = 'DEVICE_STATE_UNSPECIFIED';
  /**
   * The device is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The device is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The device was deleted. This state is never returned by an API call, but is
   * used in the final status report when the device acknowledges the deletion.
   * If the device is deleted via the API call, this state is published to
   * Pub/Sub. If the user deletes the work profile or resets the device, the
   * device state will remain unknown to the server.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The device is being provisioned. Newly enrolled devices are in this state
   * until they have a policy applied.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The device is lost. This state is only possible on organization-owned
   * devices.
   */
  public const STATE_LOST = 'LOST';
  /**
   * The device is preparing for migrating to Android Management API. No further
   * action is needed for the migration to continue.
   */
  public const STATE_PREPARING_FOR_MIGRATION = 'PREPARING_FOR_MIGRATION';
  /**
   * This is a financed device that has been "locked" by the financing agent.
   * This means certain policy settings have been applied which limit device
   * functionality until the device has been "unlocked" by the financing agent.
   * The device will continue to apply policy settings excluding those
   * overridden by the financing agent. When the device is "locked", the state
   * is reported in appliedState as DEACTIVATED_BY_DEVICE_FINANCE.
   */
  public const STATE_DEACTIVATED_BY_DEVICE_FINANCE = 'DEACTIVATED_BY_DEVICE_FINANCE';
  protected $collection_key = 'previousDeviceNames';
  /**
   * The API level of the Android platform version running on the device.
   *
   * @var int
   */
  public $apiLevel;
  protected $applicationReportsType = ApplicationReport::class;
  protected $applicationReportsDataType = 'array';
  protected $appliedPasswordPoliciesType = PasswordRequirements::class;
  protected $appliedPasswordPoliciesDataType = 'array';
  /**
   * The name of the policy currently applied to the device.
   *
   * @var string
   */
  public $appliedPolicyName;
  /**
   * The version of the policy currently applied to the device.
   *
   * @var string
   */
  public $appliedPolicyVersion;
  /**
   * The state currently applied to the device.
   *
   * @var string
   */
  public $appliedState;
  protected $commonCriteriaModeInfoType = CommonCriteriaModeInfo::class;
  protected $commonCriteriaModeInfoDataType = '';
  protected $defaultApplicationInfoType = DefaultApplicationInfo::class;
  protected $defaultApplicationInfoDataType = 'array';
  protected $deviceSettingsType = DeviceSettings::class;
  protected $deviceSettingsDataType = '';
  protected $disabledReasonType = UserFacingMessage::class;
  protected $disabledReasonDataType = '';
  protected $displaysType = Display::class;
  protected $displaysDataType = 'array';
  protected $dpcMigrationInfoType = DpcMigrationInfo::class;
  protected $dpcMigrationInfoDataType = '';
  /**
   * The time of device enrollment.
   *
   * @var string
   */
  public $enrollmentTime;
  /**
   * If the device was enrolled with an enrollment token with additional data
   * provided, this field contains that data.
   *
   * @var string
   */
  public $enrollmentTokenData;
  /**
   * If the device was enrolled with an enrollment token, this field contains
   * the name of the token.
   *
   * @var string
   */
  public $enrollmentTokenName;
  protected $hardwareInfoType = HardwareInfo::class;
  protected $hardwareInfoDataType = '';
  protected $hardwareStatusSamplesType = HardwareStatus::class;
  protected $hardwareStatusSamplesDataType = 'array';
  /**
   * Deprecated.
   *
   * @deprecated
   * @var string
   */
  public $lastPolicyComplianceReportTime;
  /**
   * The last time the device fetched its policy.
   *
   * @var string
   */
  public $lastPolicySyncTime;
  /**
   * The last time the device sent a status report.
   *
   * @var string
   */
  public $lastStatusReportTime;
  /**
   * The type of management mode Android Device Policy takes on the device. This
   * influences which policy settings are supported.
   *
   * @var string
   */
  public $managementMode;
  protected $memoryEventsType = MemoryEvent::class;
  protected $memoryEventsDataType = 'array';
  protected $memoryInfoType = MemoryInfo::class;
  protected $memoryInfoDataType = '';
  /**
   * The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
   *
   * @var string
   */
  public $name;
  protected $networkInfoType = NetworkInfo::class;
  protected $networkInfoDataType = '';
  protected $nonComplianceDetailsType = NonComplianceDetail::class;
  protected $nonComplianceDetailsDataType = 'array';
  /**
   * Ownership of the managed device.
   *
   * @var string
   */
  public $ownership;
  /**
   * Whether the device is compliant with its policy.
   *
   * @var bool
   */
  public $policyCompliant;
  /**
   * The name of the policy applied to the device, in the form
   * enterprises/{enterpriseId}/policies/{policyId}. If not specified, the
   * policy_name for the device's user is applied. This field can be modified by
   * a patch request. You can specify only the policyId when calling
   * enterprises.devices.patch, as long as the policyId doesn’t contain any
   * slashes. The rest of the policy name is inferred.
   *
   * @var string
   */
  public $policyName;
  protected $powerManagementEventsType = PowerManagementEvent::class;
  protected $powerManagementEventsDataType = 'array';
  /**
   * If the same physical device has been enrolled multiple times, this field
   * contains its previous device names. The serial number is used as the unique
   * identifier to determine if the same physical device has enrolled
   * previously. The names are in chronological order.
   *
   * @var string[]
   */
  public $previousDeviceNames;
  protected $securityPostureType = SecurityPosture::class;
  protected $securityPostureDataType = '';
  protected $softwareInfoType = SoftwareInfo::class;
  protected $softwareInfoDataType = '';
  /**
   * The state to be applied to the device. This field can be modified by a
   * patch request. Note that when calling enterprises.devices.patch, ACTIVE and
   * DISABLED are the only allowable values. To enter the device into a DELETED
   * state, call enterprises.devices.delete.
   *
   * @var string
   */
  public $state;
  /**
   * Map of selected system properties name and value related to the device.
   * This information is only available if systemPropertiesEnabled is true in
   * the device's policy.
   *
   * @var string[]
   */
  public $systemProperties;
  protected $userType = User::class;
  protected $userDataType = '';
  /**
   * The resource name of the user that owns this device in the form
   * enterprises/{enterpriseId}/users/{userId}.
   *
   * @var string
   */
  public $userName;

  /**
   * The API level of the Android platform version running on the device.
   *
   * @param int $apiLevel
   */
  public function setApiLevel($apiLevel)
  {
    $this->apiLevel = $apiLevel;
  }
  /**
   * @return int
   */
  public function getApiLevel()
  {
    return $this->apiLevel;
  }
  /**
   * Reports for apps installed on the device. This information is only
   * available when application_reports_enabled is true in the device's policy.
   *
   * @param ApplicationReport[] $applicationReports
   */
  public function setApplicationReports($applicationReports)
  {
    $this->applicationReports = $applicationReports;
  }
  /**
   * @return ApplicationReport[]
   */
  public function getApplicationReports()
  {
    return $this->applicationReports;
  }
  /**
   * The password requirements currently applied to the device. This field
   * exists because the applied requirements may be slightly different from
   * those specified in passwordPolicies in some cases. Note that this field
   * does not provide information about password compliance. For non-compliance
   * information, see nonComplianceDetails. NonComplianceDetail.fieldPath, is
   * set based on passwordPolicies, not based on this field.
   *
   * @param PasswordRequirements[] $appliedPasswordPolicies
   */
  public function setAppliedPasswordPolicies($appliedPasswordPolicies)
  {
    $this->appliedPasswordPolicies = $appliedPasswordPolicies;
  }
  /**
   * @return PasswordRequirements[]
   */
  public function getAppliedPasswordPolicies()
  {
    return $this->appliedPasswordPolicies;
  }
  /**
   * The name of the policy currently applied to the device.
   *
   * @param string $appliedPolicyName
   */
  public function setAppliedPolicyName($appliedPolicyName)
  {
    $this->appliedPolicyName = $appliedPolicyName;
  }
  /**
   * @return string
   */
  public function getAppliedPolicyName()
  {
    return $this->appliedPolicyName;
  }
  /**
   * The version of the policy currently applied to the device.
   *
   * @param string $appliedPolicyVersion
   */
  public function setAppliedPolicyVersion($appliedPolicyVersion)
  {
    $this->appliedPolicyVersion = $appliedPolicyVersion;
  }
  /**
   * @return string
   */
  public function getAppliedPolicyVersion()
  {
    return $this->appliedPolicyVersion;
  }
  /**
   * The state currently applied to the device.
   *
   * Accepted values: DEVICE_STATE_UNSPECIFIED, ACTIVE, DISABLED, DELETED,
   * PROVISIONING, LOST, PREPARING_FOR_MIGRATION, DEACTIVATED_BY_DEVICE_FINANCE
   *
   * @param self::APPLIED_STATE_* $appliedState
   */
  public function setAppliedState($appliedState)
  {
    $this->appliedState = $appliedState;
  }
  /**
   * @return self::APPLIED_STATE_*
   */
  public function getAppliedState()
  {
    return $this->appliedState;
  }
  /**
   * Information about Common Criteria Mode—security standards defined in the
   * Common Criteria for Information Technology Security Evaluation
   * (https://www.commoncriteriaportal.org/) (CC).This information is only
   * available if statusReportingSettings.commonCriteriaModeEnabled is true in
   * the device's policy the device is company-owned.
   *
   * @param CommonCriteriaModeInfo $commonCriteriaModeInfo
   */
  public function setCommonCriteriaModeInfo(CommonCriteriaModeInfo $commonCriteriaModeInfo)
  {
    $this->commonCriteriaModeInfo = $commonCriteriaModeInfo;
  }
  /**
   * @return CommonCriteriaModeInfo
   */
  public function getCommonCriteriaModeInfo()
  {
    return $this->commonCriteriaModeInfo;
  }
  /**
   * Output only. The default application information for the
   * DefaultApplicationType. This information is only available if
   * defaultApplicationInfoReportingEnabled is true in the device's policy.
   * Available on Android 16 and above.All app types are reported on fully
   * managed devices. DEFAULT_BROWSER, DEFAULT_CALL_REDIRECTION,
   * DEFAULT_CALL_SCREENING and DEFAULT_DIALER types are reported for the work
   * profiles on company-owned devices with a work profile and personally-owned
   * devices. DEFAULT_WALLET is also reported for company-owned devices with a
   * work profile, but will only include work profile information.
   *
   * @param DefaultApplicationInfo[] $defaultApplicationInfo
   */
  public function setDefaultApplicationInfo($defaultApplicationInfo)
  {
    $this->defaultApplicationInfo = $defaultApplicationInfo;
  }
  /**
   * @return DefaultApplicationInfo[]
   */
  public function getDefaultApplicationInfo()
  {
    return $this->defaultApplicationInfo;
  }
  /**
   * Device settings information. This information is only available if
   * deviceSettingsEnabled is true in the device's policy.
   *
   * @param DeviceSettings $deviceSettings
   */
  public function setDeviceSettings(DeviceSettings $deviceSettings)
  {
    $this->deviceSettings = $deviceSettings;
  }
  /**
   * @return DeviceSettings
   */
  public function getDeviceSettings()
  {
    return $this->deviceSettings;
  }
  /**
   * If the device state is DISABLED, an optional message that is displayed on
   * the device indicating the reason the device is disabled. This field can be
   * modified by a patch request.
   *
   * @param UserFacingMessage $disabledReason
   */
  public function setDisabledReason(UserFacingMessage $disabledReason)
  {
    $this->disabledReason = $disabledReason;
  }
  /**
   * @return UserFacingMessage
   */
  public function getDisabledReason()
  {
    return $this->disabledReason;
  }
  /**
   * Detailed information about displays on the device. This information is only
   * available if displayInfoEnabled is true in the device's policy.
   *
   * @param Display[] $displays
   */
  public function setDisplays($displays)
  {
    $this->displays = $displays;
  }
  /**
   * @return Display[]
   */
  public function getDisplays()
  {
    return $this->displays;
  }
  /**
   * Output only. Information related to whether this device was migrated from
   * being managed by another Device Policy Controller (DPC).
   *
   * @param DpcMigrationInfo $dpcMigrationInfo
   */
  public function setDpcMigrationInfo(DpcMigrationInfo $dpcMigrationInfo)
  {
    $this->dpcMigrationInfo = $dpcMigrationInfo;
  }
  /**
   * @return DpcMigrationInfo
   */
  public function getDpcMigrationInfo()
  {
    return $this->dpcMigrationInfo;
  }
  /**
   * The time of device enrollment.
   *
   * @param string $enrollmentTime
   */
  public function setEnrollmentTime($enrollmentTime)
  {
    $this->enrollmentTime = $enrollmentTime;
  }
  /**
   * @return string
   */
  public function getEnrollmentTime()
  {
    return $this->enrollmentTime;
  }
  /**
   * If the device was enrolled with an enrollment token with additional data
   * provided, this field contains that data.
   *
   * @param string $enrollmentTokenData
   */
  public function setEnrollmentTokenData($enrollmentTokenData)
  {
    $this->enrollmentTokenData = $enrollmentTokenData;
  }
  /**
   * @return string
   */
  public function getEnrollmentTokenData()
  {
    return $this->enrollmentTokenData;
  }
  /**
   * If the device was enrolled with an enrollment token, this field contains
   * the name of the token.
   *
   * @param string $enrollmentTokenName
   */
  public function setEnrollmentTokenName($enrollmentTokenName)
  {
    $this->enrollmentTokenName = $enrollmentTokenName;
  }
  /**
   * @return string
   */
  public function getEnrollmentTokenName()
  {
    return $this->enrollmentTokenName;
  }
  /**
   * Detailed information about the device hardware.
   *
   * @param HardwareInfo $hardwareInfo
   */
  public function setHardwareInfo(HardwareInfo $hardwareInfo)
  {
    $this->hardwareInfo = $hardwareInfo;
  }
  /**
   * @return HardwareInfo
   */
  public function getHardwareInfo()
  {
    return $this->hardwareInfo;
  }
  /**
   * Hardware status samples in chronological order. This information is only
   * available if hardwareStatusEnabled is true in the device's policy.
   *
   * @param HardwareStatus[] $hardwareStatusSamples
   */
  public function setHardwareStatusSamples($hardwareStatusSamples)
  {
    $this->hardwareStatusSamples = $hardwareStatusSamples;
  }
  /**
   * @return HardwareStatus[]
   */
  public function getHardwareStatusSamples()
  {
    return $this->hardwareStatusSamples;
  }
  /**
   * Deprecated.
   *
   * @deprecated
   * @param string $lastPolicyComplianceReportTime
   */
  public function setLastPolicyComplianceReportTime($lastPolicyComplianceReportTime)
  {
    $this->lastPolicyComplianceReportTime = $lastPolicyComplianceReportTime;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLastPolicyComplianceReportTime()
  {
    return $this->lastPolicyComplianceReportTime;
  }
  /**
   * The last time the device fetched its policy.
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
   * The last time the device sent a status report.
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
   * The type of management mode Android Device Policy takes on the device. This
   * influences which policy settings are supported.
   *
   * Accepted values: MANAGEMENT_MODE_UNSPECIFIED, DEVICE_OWNER, PROFILE_OWNER
   *
   * @param self::MANAGEMENT_MODE_* $managementMode
   */
  public function setManagementMode($managementMode)
  {
    $this->managementMode = $managementMode;
  }
  /**
   * @return self::MANAGEMENT_MODE_*
   */
  public function getManagementMode()
  {
    return $this->managementMode;
  }
  /**
   * Events related to memory and storage measurements in chronological order.
   * This information is only available if memoryInfoEnabled is true in the
   * device's policy.Events are retained for a certain period of time and old
   * events are deleted.
   *
   * @param MemoryEvent[] $memoryEvents
   */
  public function setMemoryEvents($memoryEvents)
  {
    $this->memoryEvents = $memoryEvents;
  }
  /**
   * @return MemoryEvent[]
   */
  public function getMemoryEvents()
  {
    return $this->memoryEvents;
  }
  /**
   * Memory information: contains information about device memory and storage.
   *
   * @param MemoryInfo $memoryInfo
   */
  public function setMemoryInfo(MemoryInfo $memoryInfo)
  {
    $this->memoryInfo = $memoryInfo;
  }
  /**
   * @return MemoryInfo
   */
  public function getMemoryInfo()
  {
    return $this->memoryInfo;
  }
  /**
   * The name of the device in the form
   * enterprises/{enterpriseId}/devices/{deviceId}.
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
   * Device network information. This information is only available if
   * networkInfoEnabled is true in the device's policy.
   *
   * @param NetworkInfo $networkInfo
   */
  public function setNetworkInfo(NetworkInfo $networkInfo)
  {
    $this->networkInfo = $networkInfo;
  }
  /**
   * @return NetworkInfo
   */
  public function getNetworkInfo()
  {
    return $this->networkInfo;
  }
  /**
   * Details about policy settings that the device is not compliant with.
   *
   * @param NonComplianceDetail[] $nonComplianceDetails
   */
  public function setNonComplianceDetails($nonComplianceDetails)
  {
    $this->nonComplianceDetails = $nonComplianceDetails;
  }
  /**
   * @return NonComplianceDetail[]
   */
  public function getNonComplianceDetails()
  {
    return $this->nonComplianceDetails;
  }
  /**
   * Ownership of the managed device.
   *
   * Accepted values: OWNERSHIP_UNSPECIFIED, COMPANY_OWNED, PERSONALLY_OWNED
   *
   * @param self::OWNERSHIP_* $ownership
   */
  public function setOwnership($ownership)
  {
    $this->ownership = $ownership;
  }
  /**
   * @return self::OWNERSHIP_*
   */
  public function getOwnership()
  {
    return $this->ownership;
  }
  /**
   * Whether the device is compliant with its policy.
   *
   * @param bool $policyCompliant
   */
  public function setPolicyCompliant($policyCompliant)
  {
    $this->policyCompliant = $policyCompliant;
  }
  /**
   * @return bool
   */
  public function getPolicyCompliant()
  {
    return $this->policyCompliant;
  }
  /**
   * The name of the policy applied to the device, in the form
   * enterprises/{enterpriseId}/policies/{policyId}. If not specified, the
   * policy_name for the device's user is applied. This field can be modified by
   * a patch request. You can specify only the policyId when calling
   * enterprises.devices.patch, as long as the policyId doesn’t contain any
   * slashes. The rest of the policy name is inferred.
   *
   * @param string $policyName
   */
  public function setPolicyName($policyName)
  {
    $this->policyName = $policyName;
  }
  /**
   * @return string
   */
  public function getPolicyName()
  {
    return $this->policyName;
  }
  /**
   * Power management events on the device in chronological order. This
   * information is only available if powerManagementEventsEnabled is true in
   * the device's policy.
   *
   * @param PowerManagementEvent[] $powerManagementEvents
   */
  public function setPowerManagementEvents($powerManagementEvents)
  {
    $this->powerManagementEvents = $powerManagementEvents;
  }
  /**
   * @return PowerManagementEvent[]
   */
  public function getPowerManagementEvents()
  {
    return $this->powerManagementEvents;
  }
  /**
   * If the same physical device has been enrolled multiple times, this field
   * contains its previous device names. The serial number is used as the unique
   * identifier to determine if the same physical device has enrolled
   * previously. The names are in chronological order.
   *
   * @param string[] $previousDeviceNames
   */
  public function setPreviousDeviceNames($previousDeviceNames)
  {
    $this->previousDeviceNames = $previousDeviceNames;
  }
  /**
   * @return string[]
   */
  public function getPreviousDeviceNames()
  {
    return $this->previousDeviceNames;
  }
  /**
   * Device's security posture value that reflects how secure the device is.
   *
   * @param SecurityPosture $securityPosture
   */
  public function setSecurityPosture(SecurityPosture $securityPosture)
  {
    $this->securityPosture = $securityPosture;
  }
  /**
   * @return SecurityPosture
   */
  public function getSecurityPosture()
  {
    return $this->securityPosture;
  }
  /**
   * Detailed information about the device software. This information is only
   * available if softwareInfoEnabled is true in the device's policy.
   *
   * @param SoftwareInfo $softwareInfo
   */
  public function setSoftwareInfo(SoftwareInfo $softwareInfo)
  {
    $this->softwareInfo = $softwareInfo;
  }
  /**
   * @return SoftwareInfo
   */
  public function getSoftwareInfo()
  {
    return $this->softwareInfo;
  }
  /**
   * The state to be applied to the device. This field can be modified by a
   * patch request. Note that when calling enterprises.devices.patch, ACTIVE and
   * DISABLED are the only allowable values. To enter the device into a DELETED
   * state, call enterprises.devices.delete.
   *
   * Accepted values: DEVICE_STATE_UNSPECIFIED, ACTIVE, DISABLED, DELETED,
   * PROVISIONING, LOST, PREPARING_FOR_MIGRATION, DEACTIVATED_BY_DEVICE_FINANCE
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
   * Map of selected system properties name and value related to the device.
   * This information is only available if systemPropertiesEnabled is true in
   * the device's policy.
   *
   * @param string[] $systemProperties
   */
  public function setSystemProperties($systemProperties)
  {
    $this->systemProperties = $systemProperties;
  }
  /**
   * @return string[]
   */
  public function getSystemProperties()
  {
    return $this->systemProperties;
  }
  /**
   * The user who owns the device.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
  /**
   * The resource name of the user that owns this device in the form
   * enterprises/{enterpriseId}/users/{userId}.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Device::class, 'Google_Service_AndroidManagement_Device');
