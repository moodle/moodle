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

class Policy extends \Google\Collection
{
  /**
   * The auto-update policy is not set. Equivalent to CHOICE_TO_THE_USER.
   */
  public const APP_AUTO_UPDATE_POLICY_APP_AUTO_UPDATE_POLICY_UNSPECIFIED = 'APP_AUTO_UPDATE_POLICY_UNSPECIFIED';
  /**
   * The user can control auto-updates.
   */
  public const APP_AUTO_UPDATE_POLICY_CHOICE_TO_THE_USER = 'CHOICE_TO_THE_USER';
  /**
   * Apps are never auto-updated.
   */
  public const APP_AUTO_UPDATE_POLICY_NEVER = 'NEVER';
  /**
   * Apps are auto-updated over Wi-Fi only.
   */
  public const APP_AUTO_UPDATE_POLICY_WIFI_ONLY = 'WIFI_ONLY';
  /**
   * Apps are auto-updated at any time. Data charges may apply.
   */
  public const APP_AUTO_UPDATE_POLICY_ALWAYS = 'ALWAYS';
  /**
   * Unspecified. Defaults to APP_FUNCTIONS_ALLOWED.
   */
  public const APP_FUNCTIONS_APP_FUNCTIONS_UNSPECIFIED = 'APP_FUNCTIONS_UNSPECIFIED';
  /**
   * Apps on the device for fully managed devices or in the work profile for
   * devices with work profiles are not allowed to expose app functions. If this
   * is set, crossProfileAppFunctions must not be set to
   * CROSS_PROFILE_APP_FUNCTIONS_ALLOWED, otherwise the policy will be rejected.
   */
  public const APP_FUNCTIONS_APP_FUNCTIONS_DISALLOWED = 'APP_FUNCTIONS_DISALLOWED';
  /**
   * Apps on the device for fully managed devices or in the work profile for
   * devices with work profiles are allowed to expose app functions.
   */
  public const APP_FUNCTIONS_APP_FUNCTIONS_ALLOWED = 'APP_FUNCTIONS_ALLOWED';
  /**
   * Unspecified. Defaults to ASSIST_CONTENT_ALLOWED.
   */
  public const ASSIST_CONTENT_POLICY_ASSIST_CONTENT_POLICY_UNSPECIFIED = 'ASSIST_CONTENT_POLICY_UNSPECIFIED';
  /**
   * Assist content is blocked from being sent to a privileged app.Supported on
   * Android 15 and above. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 15.
   */
  public const ASSIST_CONTENT_POLICY_ASSIST_CONTENT_DISALLOWED = 'ASSIST_CONTENT_DISALLOWED';
  /**
   * Assist content is allowed to be sent to a privileged app.Supported on
   * Android 15 and above.
   */
  public const ASSIST_CONTENT_POLICY_ASSIST_CONTENT_ALLOWED = 'ASSIST_CONTENT_ALLOWED';
  /**
   * Unspecified. Defaults to AUTO_DATE_AND_TIME_ZONE_USER_CHOICE.
   */
  public const AUTO_DATE_AND_TIME_ZONE_AUTO_DATE_AND_TIME_ZONE_UNSPECIFIED = 'AUTO_DATE_AND_TIME_ZONE_UNSPECIFIED';
  /**
   * Auto date, time, and time zone are left to user's choice.
   */
  public const AUTO_DATE_AND_TIME_ZONE_AUTO_DATE_AND_TIME_ZONE_USER_CHOICE = 'AUTO_DATE_AND_TIME_ZONE_USER_CHOICE';
  /**
   * Enforce auto date, time, and time zone on the device.
   */
  public const AUTO_DATE_AND_TIME_ZONE_AUTO_DATE_AND_TIME_ZONE_ENFORCED = 'AUTO_DATE_AND_TIME_ZONE_ENFORCED';
  /**
   * If camera_disabled is true, this is equivalent to CAMERA_ACCESS_DISABLED.
   * Otherwise, this is equivalent to CAMERA_ACCESS_USER_CHOICE.
   */
  public const CAMERA_ACCESS_CAMERA_ACCESS_UNSPECIFIED = 'CAMERA_ACCESS_UNSPECIFIED';
  /**
   * The field camera_disabled is ignored. This is the default device behaviour:
   * all cameras on the device are available. On Android 12 and above, the user
   * can use the camera access toggle.
   */
  public const CAMERA_ACCESS_CAMERA_ACCESS_USER_CHOICE = 'CAMERA_ACCESS_USER_CHOICE';
  /**
   * The field camera_disabled is ignored. All cameras on the device are
   * disabled (for fully managed devices, this applies device-wide and for work
   * profiles this applies only to the work profile).There are no explicit
   * restrictions placed on the camera access toggle on Android 12 and above: on
   * fully managed devices, the camera access toggle has no effect as all
   * cameras are disabled. On devices with a work profile, this toggle has no
   * effect on apps in the work profile, but it affects apps outside the work
   * profile.
   */
  public const CAMERA_ACCESS_CAMERA_ACCESS_DISABLED = 'CAMERA_ACCESS_DISABLED';
  /**
   * The field camera_disabled is ignored. All cameras on the device are
   * available. On fully managed devices running Android 12 and above, the user
   * is unable to use the camera access toggle. On devices which are not fully
   * managed or which run Android 11 or below, this is equivalent to
   * CAMERA_ACCESS_USER_CHOICE.
   */
  public const CAMERA_ACCESS_CAMERA_ACCESS_ENFORCED = 'CAMERA_ACCESS_ENFORCED';
  /**
   * Unspecified. Defaults to CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED.
   */
  public const CREDENTIAL_PROVIDER_POLICY_DEFAULT_CREDENTIAL_PROVIDER_POLICY_DEFAULT_UNSPECIFIED = 'CREDENTIAL_PROVIDER_POLICY_DEFAULT_UNSPECIFIED';
  /**
   * Apps with credentialProviderPolicy unspecified are not allowed to act as a
   * credential provider.
   */
  public const CREDENTIAL_PROVIDER_POLICY_DEFAULT_CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED = 'CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED';
  /**
   * Apps with credentialProviderPolicy unspecified are not allowed to act as a
   * credential provider except for the OEM default credential providers. OEM
   * default credential providers are always allowed to act as credential
   * providers.
   */
  public const CREDENTIAL_PROVIDER_POLICY_DEFAULT_CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED_EXCEPT_SYSTEM = 'CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED_EXCEPT_SYSTEM';
  /**
   * Policy not specified. If no policy is specified for a permission at any
   * level, then the PROMPT behavior is used by default.
   */
  public const DEFAULT_PERMISSION_POLICY_PERMISSION_POLICY_UNSPECIFIED = 'PERMISSION_POLICY_UNSPECIFIED';
  /**
   * Prompt the user to grant a permission.
   */
  public const DEFAULT_PERMISSION_POLICY_PROMPT = 'PROMPT';
  /**
   * Automatically grant a permission.On Android 12 and above, READ_SMS (https:/
   * /developer.android.com/reference/android/Manifest.permission#READ_SMS) and
   * following sensor-related permissions can only be granted on fully managed
   * devices: ACCESS_FINE_LOCATION (https://developer.android.com/reference/andr
   * oid/Manifest.permission#ACCESS_FINE_LOCATION) ACCESS_BACKGROUND_LOCATION (h
   * ttps://developer.android.com/reference/android/Manifest.permission#ACCESS_B
   * ACKGROUND_LOCATION) ACCESS_COARSE_LOCATION (https://developer.android.com/r
   * eference/android/Manifest.permission#ACCESS_COARSE_LOCATION) CAMERA (https:
   * //developer.android.com/reference/android/Manifest.permission#CAMERA)
   * RECORD_AUDIO (https://developer.android.com/reference/android/Manifest.perm
   * ission#RECORD_AUDIO) ACTIVITY_RECOGNITION (https://developer.android.com/re
   * ference/android/Manifest.permission#ACTIVITY_RECOGNITION) BODY_SENSORS (htt
   * ps://developer.android.com/reference/android/Manifest.permission#BODY_SENSO
   * RS)
   */
  public const DEFAULT_PERMISSION_POLICY_GRANT = 'GRANT';
  /**
   * Automatically deny a permission.
   */
  public const DEFAULT_PERMISSION_POLICY_DENY = 'DENY';
  /**
   * This value is ignored, i.e. no encryption required
   */
  public const ENCRYPTION_POLICY_ENCRYPTION_POLICY_UNSPECIFIED = 'ENCRYPTION_POLICY_UNSPECIFIED';
  /**
   * Encryption required but no password required to boot
   */
  public const ENCRYPTION_POLICY_ENABLED_WITHOUT_PASSWORD = 'ENABLED_WITHOUT_PASSWORD';
  /**
   * Encryption required with password required to boot
   */
  public const ENCRYPTION_POLICY_ENABLED_WITH_PASSWORD = 'ENABLED_WITH_PASSWORD';
  /**
   * Unspecified. Defaults to displaying the enterprise name that's set at the
   * time of device setup. In future, this will default to
   * ENTERPRISE_DISPLAY_NAME_VISIBLE.
   */
  public const ENTERPRISE_DISPLAY_NAME_VISIBILITY_ENTERPRISE_DISPLAY_NAME_VISIBILITY_UNSPECIFIED = 'ENTERPRISE_DISPLAY_NAME_VISIBILITY_UNSPECIFIED';
  /**
   * The enterprise display name is visible on the device. Supported on work
   * profiles on Android 7 and above. Supported on fully managed devices on
   * Android 8 and above. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 7. A NonComplianceDetail with
   * MANAGEMENT_MODE is reported on fully managed devices on Android 7.
   */
  public const ENTERPRISE_DISPLAY_NAME_VISIBILITY_ENTERPRISE_DISPLAY_NAME_VISIBLE = 'ENTERPRISE_DISPLAY_NAME_VISIBLE';
  /**
   * The enterprise display name is hidden on the device.
   */
  public const ENTERPRISE_DISPLAY_NAME_VISIBILITY_ENTERPRISE_DISPLAY_NAME_HIDDEN = 'ENTERPRISE_DISPLAY_NAME_HIDDEN';
  /**
   * Defaults to LOCATION_USER_CHOICE.
   */
  public const LOCATION_MODE_LOCATION_MODE_UNSPECIFIED = 'LOCATION_MODE_UNSPECIFIED';
  /**
   * On Android 8 and below, all location detection methods are enabled,
   * including GPS, networks, and other sensors. On Android 9 and above, this is
   * equivalent to LOCATION_ENFORCED.
   *
   * @deprecated
   */
  public const LOCATION_MODE_HIGH_ACCURACY = 'HIGH_ACCURACY';
  /**
   * On Android 8 and below, only GPS and other sensors are enabled. On Android
   * 9 and above, this is equivalent to LOCATION_ENFORCED.
   *
   * @deprecated
   */
  public const LOCATION_MODE_SENSORS_ONLY = 'SENSORS_ONLY';
  /**
   * On Android 8 and below, only the network location provider is enabled. On
   * Android 9 and above, this is equivalent to LOCATION_ENFORCED.
   *
   * @deprecated
   */
  public const LOCATION_MODE_BATTERY_SAVING = 'BATTERY_SAVING';
  /**
   * On Android 8 and below, location setting and accuracy are disabled. On
   * Android 9 and above, this is equivalent to LOCATION_DISABLED.
   *
   * @deprecated
   */
  public const LOCATION_MODE_OFF = 'OFF';
  /**
   * Location setting is not restricted on the device. No specific behavior is
   * set or enforced.
   */
  public const LOCATION_MODE_LOCATION_USER_CHOICE = 'LOCATION_USER_CHOICE';
  /**
   * Enable location setting on the device. Important: On Android 11 and above,
   * work profiles on company-owned devices cannot directly enforce enabling of
   * location services. When LOCATION_ENFORCED is set, then a
   * NonComplianceDetail with USER_ACTION is reported. Compliance can only be
   * restored once the user manually turns on location services through the
   * device's Settings application.
   */
  public const LOCATION_MODE_LOCATION_ENFORCED = 'LOCATION_ENFORCED';
  /**
   * Disable location setting on the device. Important: On Android 11 and above,
   * work profiles on company-owned devices cannot directly enforce disabling of
   * location services. When LOCATION_DISABLED is set, then a
   * nonComplianceDetail with USER_ACTION is reported. Compliance can only be
   * restored once the user manually turns off location services through the
   * device's Settings application.
   */
  public const LOCATION_MODE_LOCATION_DISABLED = 'LOCATION_DISABLED';
  /**
   * If unmute_microphone_disabled is true, this is equivalent to
   * MICROPHONE_ACCESS_DISABLED. Otherwise, this is equivalent to
   * MICROPHONE_ACCESS_USER_CHOICE.
   */
  public const MICROPHONE_ACCESS_MICROPHONE_ACCESS_UNSPECIFIED = 'MICROPHONE_ACCESS_UNSPECIFIED';
  /**
   * The field unmute_microphone_disabled is ignored. This is the default device
   * behaviour: the microphone on the device is available. On Android 12 and
   * above, the user can use the microphone access toggle.
   */
  public const MICROPHONE_ACCESS_MICROPHONE_ACCESS_USER_CHOICE = 'MICROPHONE_ACCESS_USER_CHOICE';
  /**
   * The field unmute_microphone_disabled is ignored. The microphone on the
   * device is disabled (for fully managed devices, this applies device-
   * wide).The microphone access toggle has no effect as the microphone is
   * disabled.
   */
  public const MICROPHONE_ACCESS_MICROPHONE_ACCESS_DISABLED = 'MICROPHONE_ACCESS_DISABLED';
  /**
   * The field unmute_microphone_disabled is ignored. The microphone on the
   * device is available. On devices running Android 12 and above, the user is
   * unable to use the microphone access toggle. On devices which run Android 11
   * or below, this is equivalent to MICROPHONE_ACCESS_USER_CHOICE.
   */
  public const MICROPHONE_ACCESS_MICROPHONE_ACCESS_ENFORCED = 'MICROPHONE_ACCESS_ENFORCED';
  /**
   * Unspecified. Defaults to WHITELIST.
   */
  public const PLAY_STORE_MODE_PLAY_STORE_MODE_UNSPECIFIED = 'PLAY_STORE_MODE_UNSPECIFIED';
  /**
   * Only apps that are in the policy are available and any app not in the
   * policy will be automatically uninstalled from the device.
   */
  public const PLAY_STORE_MODE_WHITELIST = 'WHITELIST';
  /**
   * All apps are available and any app that should not be on the device should
   * be explicitly marked as 'BLOCKED' in the applications policy.
   */
  public const PLAY_STORE_MODE_BLACKLIST = 'BLACKLIST';
  /**
   * Unspecified. Defaults to PREFERENTIAL_NETWORK_SERVICES_DISABLED.
   */
  public const PREFERENTIAL_NETWORK_SERVICE_PREFERENTIAL_NETWORK_SERVICE_UNSPECIFIED = 'PREFERENTIAL_NETWORK_SERVICE_UNSPECIFIED';
  /**
   * Preferential network service is disabled on the work profile.
   */
  public const PREFERENTIAL_NETWORK_SERVICE_PREFERENTIAL_NETWORK_SERVICE_DISABLED = 'PREFERENTIAL_NETWORK_SERVICE_DISABLED';
  /**
   * Preferential network service is enabled on the work profile. This setting
   * is only supported on work profiles on devices running Android 12 or above.
   * Starting with Android 13, fully managed devices are also supported.
   */
  public const PREFERENTIAL_NETWORK_SERVICE_PREFERENTIAL_NETWORK_SERVICE_ENABLED = 'PREFERENTIAL_NETWORK_SERVICE_ENABLED';
  /**
   * Unspecified. Defaults to PRINTING_ALLOWED.
   */
  public const PRINTING_POLICY_PRINTING_POLICY_UNSPECIFIED = 'PRINTING_POLICY_UNSPECIFIED';
  /**
   * Printing is disallowed. A NonComplianceDetail with API_LEVEL is reported if
   * the Android version is less than 9.
   */
  public const PRINTING_POLICY_PRINTING_DISALLOWED = 'PRINTING_DISALLOWED';
  /**
   * Printing is allowed.
   */
  public const PRINTING_POLICY_PRINTING_ALLOWED = 'PRINTING_ALLOWED';
  protected $collection_key = 'wipeDataFlags';
  /**
   * Account types that can't be managed by the user.
   *
   * @var string[]
   */
  public $accountTypesWithManagementDisabled;
  /**
   * Whether adding new users and profiles is disabled. For devices where
   * managementMode is DEVICE_OWNER this field is ignored and the user is never
   * allowed to add or remove users.
   *
   * @var bool
   */
  public $addUserDisabled;
  /**
   * Whether adjusting the master volume is disabled. Also mutes the device. The
   * setting has effect only on fully managed devices.
   *
   * @var bool
   */
  public $adjustVolumeDisabled;
  protected $advancedSecurityOverridesType = AdvancedSecurityOverrides::class;
  protected $advancedSecurityOverridesDataType = '';
  protected $alwaysOnVpnPackageType = AlwaysOnVpnPackage::class;
  protected $alwaysOnVpnPackageDataType = '';
  /**
   * This setting is not supported. Any value is ignored.
   *
   * @deprecated
   * @var string[]
   */
  public $androidDevicePolicyTracks;
  /**
   * Recommended alternative: autoUpdateMode which is set per app, provides
   * greater flexibility around update frequency.When autoUpdateMode is set to
   * AUTO_UPDATE_POSTPONED or AUTO_UPDATE_HIGH_PRIORITY, this field has no
   * effect.The app auto update policy, which controls when automatic app
   * updates can be applied.
   *
   * @var string
   */
  public $appAutoUpdatePolicy;
  /**
   * Optional. Controls whether apps on the device for fully managed devices or
   * in the work profile for devices with work profiles are allowed to expose
   * app functions.
   *
   * @var string
   */
  public $appFunctions;
  protected $applicationsType = ApplicationPolicy::class;
  protected $applicationsDataType = 'array';
  /**
   * Optional. Controls whether AssistContent
   * (https://developer.android.com/reference/android/app/assist/AssistContent)
   * is allowed to be sent to a privileged app such as an assistant app.
   * AssistContent includes screenshots and information about an app, such as
   * package name. This is supported on Android 15 and above.
   *
   * @var string
   */
  public $assistContentPolicy;
  /**
   * Whether auto date, time, and time zone are enabled on a company-owned
   * device. If this is set, then autoTimeRequired is ignored.
   *
   * @var string
   */
  public $autoDateAndTimeZone;
  /**
   * Whether auto time is required, which prevents the user from manually
   * setting the date and time. If autoDateAndTimeZone is set, this field is
   * ignored.
   *
   * @deprecated
   * @var bool
   */
  public $autoTimeRequired;
  /**
   * Whether applications other than the ones configured in applications are
   * blocked from being installed. When set, applications that were installed
   * under a previous policy but no longer appear in the policy are
   * automatically uninstalled.
   *
   * @deprecated
   * @var bool
   */
  public $blockApplicationsEnabled;
  /**
   * Whether configuring bluetooth is disabled.
   *
   * @var bool
   */
  public $bluetoothConfigDisabled;
  /**
   * Whether bluetooth contact sharing is disabled.
   *
   * @var bool
   */
  public $bluetoothContactSharingDisabled;
  /**
   * Whether bluetooth is disabled. Prefer this setting over
   * bluetooth_config_disabled because bluetooth_config_disabled can be bypassed
   * by the user.
   *
   * @var bool
   */
  public $bluetoothDisabled;
  /**
   * Controls the use of the camera and whether the user has access to the
   * camera access toggle.
   *
   * @var string
   */
  public $cameraAccess;
  /**
   * If camera_access is set to any value other than CAMERA_ACCESS_UNSPECIFIED,
   * this has no effect. Otherwise this field controls whether cameras are
   * disabled: If true, all cameras are disabled, otherwise they are available.
   * For fully managed devices this field applies for all apps on the device.
   * For work profiles, this field applies only to apps in the work profile, and
   * the camera access of apps outside the work profile is unaffected.
   *
   * @deprecated
   * @var bool
   */
  public $cameraDisabled;
  /**
   * Whether configuring cell broadcast is disabled.
   *
   * @var bool
   */
  public $cellBroadcastsConfigDisabled;
  protected $choosePrivateKeyRulesType = ChoosePrivateKeyRule::class;
  protected $choosePrivateKeyRulesDataType = 'array';
  protected $complianceRulesType = ComplianceRule::class;
  protected $complianceRulesDataType = 'array';
  /**
   * Whether creating windows besides app windows is disabled.
   *
   * @var bool
   */
  public $createWindowsDisabled;
  /**
   * Controls which apps are allowed to act as credential providers on Android
   * 14 and above. These apps store credentials, see this
   * (https://developer.android.com/training/sign-in/passkeys) and this (https:/
   * /developer.android.com/reference/androidx/credentials/CredentialManager)
   * for details. See also credentialProviderPolicy.
   *
   * @var string
   */
  public $credentialProviderPolicyDefault;
  /**
   * Whether configuring user credentials is disabled.
   *
   * @var bool
   */
  public $credentialsConfigDisabled;
  protected $crossProfilePoliciesType = CrossProfilePolicies::class;
  protected $crossProfilePoliciesDataType = '';
  /**
   * Whether roaming data services are disabled.
   *
   * @var bool
   */
  public $dataRoamingDisabled;
  /**
   * Whether the user is allowed to enable debugging features.
   *
   * @deprecated
   * @var bool
   */
  public $debuggingFeaturesAllowed;
  protected $defaultApplicationSettingsType = DefaultApplicationSetting::class;
  protected $defaultApplicationSettingsDataType = 'array';
  /**
   * The default permission policy for runtime permission requests.
   *
   * @var string
   */
  public $defaultPermissionPolicy;
  protected $deviceConnectivityManagementType = DeviceConnectivityManagement::class;
  protected $deviceConnectivityManagementDataType = '';
  protected $deviceOwnerLockScreenInfoType = UserFacingMessage::class;
  protected $deviceOwnerLockScreenInfoDataType = '';
  protected $deviceRadioStateType = DeviceRadioState::class;
  protected $deviceRadioStateDataType = '';
  protected $displaySettingsType = DisplaySettings::class;
  protected $displaySettingsDataType = '';
  /**
   * Whether encryption is enabled
   *
   * @var string
   */
  public $encryptionPolicy;
  /**
   * Whether app verification is force-enabled.
   *
   * @deprecated
   * @var bool
   */
  public $ensureVerifyAppsEnabled;
  /**
   * Optional. Controls whether the enterpriseDisplayName is visible on the
   * device (e.g. lock screen message on company-owned devices).
   *
   * @var string
   */
  public $enterpriseDisplayNameVisibility;
  /**
   * Whether factory resetting from settings is disabled.
   *
   * @var bool
   */
  public $factoryResetDisabled;
  /**
   * Email addresses of device administrators for factory reset protection. When
   * the device is factory reset, it will require one of these admins to log in
   * with the Google account email and password to unlock the device. If no
   * admins are specified, the device won't provide factory reset protection.
   *
   * @var string[]
   */
  public $frpAdminEmails;
  /**
   * Whether the user is allowed to have fun. Controls whether the Easter egg
   * game in Settings is disabled.
   *
   * @var bool
   */
  public $funDisabled;
  /**
   * Whether user installation of apps is disabled.
   *
   * @var bool
   */
  public $installAppsDisabled;
  /**
   * This field has no effect.
   *
   * @deprecated
   * @var bool
   */
  public $installUnknownSourcesAllowed;
  /**
   * If true, this disables the Lock Screen
   * (https://source.android.com/docs/core/display/multi_display/lock-screen)
   * for primary and/or secondary displays. This policy is supported only in
   * dedicated device management mode.
   *
   * @var bool
   */
  public $keyguardDisabled;
  /**
   * Disabled keyguard customizations, such as widgets.
   *
   * @var string[]
   */
  public $keyguardDisabledFeatures;
  /**
   * Whether the kiosk custom launcher is enabled. This replaces the home screen
   * with a launcher that locks down the device to the apps installed via the
   * applications setting. Apps appear on a single page in alphabetical order.
   * Use kioskCustomization to further configure the kiosk device behavior.
   *
   * @var bool
   */
  public $kioskCustomLauncherEnabled;
  protected $kioskCustomizationType = KioskCustomization::class;
  protected $kioskCustomizationDataType = '';
  /**
   * The degree of location detection enabled.
   *
   * @var string
   */
  public $locationMode;
  protected $longSupportMessageType = UserFacingMessage::class;
  protected $longSupportMessageDataType = '';
  /**
   * Maximum time in milliseconds for user activity until the device locks. A
   * value of 0 means there is no restriction.
   *
   * @var string
   */
  public $maximumTimeToLock;
  /**
   * Controls the use of the microphone and whether the user has access to the
   * microphone access toggle. This applies only on fully managed devices.
   *
   * @var string
   */
  public $microphoneAccess;
  /**
   * The minimum allowed Android API level.
   *
   * @var int
   */
  public $minimumApiLevel;
  /**
   * Whether configuring mobile networks is disabled.
   *
   * @var bool
   */
  public $mobileNetworksConfigDisabled;
  /**
   * Whether adding or removing accounts is disabled.
   *
   * @var bool
   */
  public $modifyAccountsDisabled;
  /**
   * Whether the user mounting physical external media is disabled.
   *
   * @var bool
   */
  public $mountPhysicalMediaDisabled;
  /**
   * The name of the policy in the form
   * enterprises/{enterpriseId}/policies/{policyId}.
   *
   * @var string
   */
  public $name;
  /**
   * Whether the network escape hatch is enabled. If a network connection can't
   * be made at boot time, the escape hatch prompts the user to temporarily
   * connect to a network in order to refresh the device policy. After applying
   * policy, the temporary network will be forgotten and the device will
   * continue booting. This prevents being unable to connect to a network if
   * there is no suitable network in the last policy and the device boots into
   * an app in lock task mode, or the user is otherwise unable to reach device
   * settings.Note: Setting wifiConfigDisabled to true will override this
   * setting under specific circumstances. Please see wifiConfigDisabled for
   * further details. Setting configureWifi to DISALLOW_CONFIGURING_WIFI will
   * override this setting under specific circumstances. Please see
   * DISALLOW_CONFIGURING_WIFI for further details.
   *
   * @var bool
   */
  public $networkEscapeHatchEnabled;
  /**
   * Whether resetting network settings is disabled.
   *
   * @var bool
   */
  public $networkResetDisabled;
  protected $oncCertificateProvidersType = OncCertificateProvider::class;
  protected $oncCertificateProvidersDataType = 'array';
  /**
   * Network configuration for the device. See configure networks for more
   * information.
   *
   * @var array[]
   */
  public $openNetworkConfiguration;
  /**
   * Whether using NFC to beam data from apps is disabled.
   *
   * @var bool
   */
  public $outgoingBeamDisabled;
  /**
   * Whether outgoing calls are disabled.
   *
   * @var bool
   */
  public $outgoingCallsDisabled;
  protected $passwordPoliciesType = PasswordRequirements::class;
  protected $passwordPoliciesDataType = 'array';
  protected $passwordRequirementsType = PasswordRequirements::class;
  protected $passwordRequirementsDataType = '';
  protected $permissionGrantsType = PermissionGrant::class;
  protected $permissionGrantsDataType = 'array';
  protected $permittedAccessibilityServicesType = PackageNameList::class;
  protected $permittedAccessibilityServicesDataType = '';
  protected $permittedInputMethodsType = PackageNameList::class;
  protected $permittedInputMethodsDataType = '';
  protected $persistentPreferredActivitiesType = PersistentPreferredActivity::class;
  protected $persistentPreferredActivitiesDataType = 'array';
  protected $personalUsagePoliciesType = PersonalUsagePolicies::class;
  protected $personalUsagePoliciesDataType = '';
  /**
   * This mode controls which apps are available to the user in the Play Store
   * and the behavior on the device when apps are removed from the policy.
   *
   * @var string
   */
  public $playStoreMode;
  protected $policyEnforcementRulesType = PolicyEnforcementRule::class;
  protected $policyEnforcementRulesDataType = 'array';
  /**
   * Controls whether preferential network service is enabled on the work
   * profile or on fully managed devices. For example, an organization may have
   * an agreement with a carrier that all of the work data from its employees'
   * devices will be sent via a network service dedicated for enterprise use. An
   * example of a supported preferential network service is the enterprise slice
   * on 5G networks. This policy has no effect if
   * preferentialNetworkServiceSettings or
   * ApplicationPolicy.preferentialNetworkId is set on devices running Android
   * 13 or above.
   *
   * @var string
   */
  public $preferentialNetworkService;
  /**
   * Optional. Controls whether printing is allowed. This is supported on
   * devices running Android 9 and above. .
   *
   * @var string
   */
  public $printingPolicy;
  /**
   * Allows showing UI on a device for a user to choose a private key alias if
   * there are no matching rules in ChoosePrivateKeyRules. For devices below
   * Android P, setting this may leave enterprise keys vulnerable. This value
   * will have no effect if any application has CERT_SELECTION delegation scope.
   *
   * @var bool
   */
  public $privateKeySelectionEnabled;
  protected $recommendedGlobalProxyType = ProxyInfo::class;
  protected $recommendedGlobalProxyDataType = '';
  /**
   * Whether removing other users is disabled.
   *
   * @var bool
   */
  public $removeUserDisabled;
  /**
   * Whether rebooting the device into safe boot is disabled.
   *
   * @deprecated
   * @var bool
   */
  public $safeBootDisabled;
  /**
   * Whether screen capture is disabled.
   *
   * @var bool
   */
  public $screenCaptureDisabled;
  /**
   * Whether changing the user icon is disabled. This applies only on devices
   * running Android 7 and above.
   *
   * @var bool
   */
  public $setUserIconDisabled;
  /**
   * Whether changing the wallpaper is disabled.
   *
   * @var bool
   */
  public $setWallpaperDisabled;
  protected $setupActionsType = SetupAction::class;
  protected $setupActionsDataType = 'array';
  /**
   * Whether location sharing is disabled.
   *
   * @var bool
   */
  public $shareLocationDisabled;
  protected $shortSupportMessageType = UserFacingMessage::class;
  protected $shortSupportMessageDataType = '';
  /**
   * Flag to skip hints on the first use. Enterprise admin can enable the system
   * recommendation for apps to skip their user tutorial and other introductory
   * hints on first start-up.
   *
   * @var bool
   */
  public $skipFirstUseHintsEnabled;
  /**
   * Whether sending and receiving SMS messages is disabled.
   *
   * @var bool
   */
  public $smsDisabled;
  /**
   * Whether the status bar is disabled. This disables notifications, quick
   * settings, and other screen overlays that allow escape from full-screen
   * mode. DEPRECATED. To disable the status bar on a kiosk device, use
   * InstallType KIOSK or kioskCustomLauncherEnabled.
   *
   * @deprecated
   * @var bool
   */
  public $statusBarDisabled;
  protected $statusReportingSettingsType = StatusReportingSettings::class;
  protected $statusReportingSettingsDataType = '';
  /**
   * The battery plugged in modes for which the device stays on. When using this
   * setting, it is recommended to clear maximum_time_to_lock so that the device
   * doesn't lock itself while it stays on.
   *
   * @var string[]
   */
  public $stayOnPluggedModes;
  protected $systemUpdateType = SystemUpdate::class;
  protected $systemUpdateDataType = '';
  /**
   * Whether configuring tethering and portable hotspots is disabled. If
   * tetheringSettings is set to anything other than
   * TETHERING_SETTINGS_UNSPECIFIED, this setting is ignored.
   *
   * @deprecated
   * @var bool
   */
  public $tetheringConfigDisabled;
  /**
   * Whether user uninstallation of applications is disabled. This prevents apps
   * from being uninstalled, even those removed using applications
   *
   * @var bool
   */
  public $uninstallAppsDisabled;
  /**
   * If microphone_access is set to any value other than
   * MICROPHONE_ACCESS_UNSPECIFIED, this has no effect. Otherwise this field
   * controls whether microphones are disabled: If true, all microphones are
   * disabled, otherwise they are available. This is available only on fully
   * managed devices.
   *
   * @deprecated
   * @var bool
   */
  public $unmuteMicrophoneDisabled;
  protected $usageLogType = UsageLog::class;
  protected $usageLogDataType = '';
  /**
   * Whether transferring files over USB is disabled. This is supported only on
   * company-owned devices.
   *
   * @deprecated
   * @var bool
   */
  public $usbFileTransferDisabled;
  /**
   * Whether USB storage is enabled. Deprecated.
   *
   * @deprecated
   * @var bool
   */
  public $usbMassStorageEnabled;
  /**
   * The version of the policy. This is a read-only field. The version is
   * incremented each time the policy is updated.
   *
   * @var string
   */
  public $version;
  /**
   * Whether configuring VPN is disabled.
   *
   * @var bool
   */
  public $vpnConfigDisabled;
  /**
   * Whether configuring Wi-Fi networks is disabled. Supported on fully managed
   * devices and work profiles on company-owned devices. For fully managed
   * devices, setting this to true removes all configured networks and retains
   * only the networks configured using openNetworkConfiguration. For work
   * profiles on company-owned devices, existing configured networks are not
   * affected and the user is not allowed to add, remove, or modify Wi-Fi
   * networks. If configureWifi is set to anything other than
   * CONFIGURE_WIFI_UNSPECIFIED, this setting is ignored. Note: If a network
   * connection can't be made at boot time and configuring Wi-Fi is disabled
   * then network escape hatch will be shown in order to refresh the device
   * policy (see networkEscapeHatchEnabled).
   *
   * @deprecated
   * @var bool
   */
  public $wifiConfigDisabled;
  /**
   * This is deprecated.
   *
   * @deprecated
   * @var bool
   */
  public $wifiConfigsLockdownEnabled;
  /**
   * Optional. Wipe flags to indicate what data is wiped when a device or
   * profile wipe is triggered due to any reason (for example, non-compliance).
   * This does not apply to the enterprises.devices.delete method. . This list
   * must not have duplicates.
   *
   * @var string[]
   */
  public $wipeDataFlags;
  protected $workAccountSetupConfigType = WorkAccountSetupConfig::class;
  protected $workAccountSetupConfigDataType = '';

  /**
   * Account types that can't be managed by the user.
   *
   * @param string[] $accountTypesWithManagementDisabled
   */
  public function setAccountTypesWithManagementDisabled($accountTypesWithManagementDisabled)
  {
    $this->accountTypesWithManagementDisabled = $accountTypesWithManagementDisabled;
  }
  /**
   * @return string[]
   */
  public function getAccountTypesWithManagementDisabled()
  {
    return $this->accountTypesWithManagementDisabled;
  }
  /**
   * Whether adding new users and profiles is disabled. For devices where
   * managementMode is DEVICE_OWNER this field is ignored and the user is never
   * allowed to add or remove users.
   *
   * @param bool $addUserDisabled
   */
  public function setAddUserDisabled($addUserDisabled)
  {
    $this->addUserDisabled = $addUserDisabled;
  }
  /**
   * @return bool
   */
  public function getAddUserDisabled()
  {
    return $this->addUserDisabled;
  }
  /**
   * Whether adjusting the master volume is disabled. Also mutes the device. The
   * setting has effect only on fully managed devices.
   *
   * @param bool $adjustVolumeDisabled
   */
  public function setAdjustVolumeDisabled($adjustVolumeDisabled)
  {
    $this->adjustVolumeDisabled = $adjustVolumeDisabled;
  }
  /**
   * @return bool
   */
  public function getAdjustVolumeDisabled()
  {
    return $this->adjustVolumeDisabled;
  }
  /**
   * Advanced security settings. In most cases, setting these is not needed.
   *
   * @param AdvancedSecurityOverrides $advancedSecurityOverrides
   */
  public function setAdvancedSecurityOverrides(AdvancedSecurityOverrides $advancedSecurityOverrides)
  {
    $this->advancedSecurityOverrides = $advancedSecurityOverrides;
  }
  /**
   * @return AdvancedSecurityOverrides
   */
  public function getAdvancedSecurityOverrides()
  {
    return $this->advancedSecurityOverrides;
  }
  /**
   * Configuration for an always-on VPN connection. Use with vpn_config_disabled
   * to prevent modification of this setting.
   *
   * @param AlwaysOnVpnPackage $alwaysOnVpnPackage
   */
  public function setAlwaysOnVpnPackage(AlwaysOnVpnPackage $alwaysOnVpnPackage)
  {
    $this->alwaysOnVpnPackage = $alwaysOnVpnPackage;
  }
  /**
   * @return AlwaysOnVpnPackage
   */
  public function getAlwaysOnVpnPackage()
  {
    return $this->alwaysOnVpnPackage;
  }
  /**
   * This setting is not supported. Any value is ignored.
   *
   * @deprecated
   * @param string[] $androidDevicePolicyTracks
   */
  public function setAndroidDevicePolicyTracks($androidDevicePolicyTracks)
  {
    $this->androidDevicePolicyTracks = $androidDevicePolicyTracks;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getAndroidDevicePolicyTracks()
  {
    return $this->androidDevicePolicyTracks;
  }
  /**
   * Recommended alternative: autoUpdateMode which is set per app, provides
   * greater flexibility around update frequency.When autoUpdateMode is set to
   * AUTO_UPDATE_POSTPONED or AUTO_UPDATE_HIGH_PRIORITY, this field has no
   * effect.The app auto update policy, which controls when automatic app
   * updates can be applied.
   *
   * Accepted values: APP_AUTO_UPDATE_POLICY_UNSPECIFIED, CHOICE_TO_THE_USER,
   * NEVER, WIFI_ONLY, ALWAYS
   *
   * @param self::APP_AUTO_UPDATE_POLICY_* $appAutoUpdatePolicy
   */
  public function setAppAutoUpdatePolicy($appAutoUpdatePolicy)
  {
    $this->appAutoUpdatePolicy = $appAutoUpdatePolicy;
  }
  /**
   * @return self::APP_AUTO_UPDATE_POLICY_*
   */
  public function getAppAutoUpdatePolicy()
  {
    return $this->appAutoUpdatePolicy;
  }
  /**
   * Optional. Controls whether apps on the device for fully managed devices or
   * in the work profile for devices with work profiles are allowed to expose
   * app functions.
   *
   * Accepted values: APP_FUNCTIONS_UNSPECIFIED, APP_FUNCTIONS_DISALLOWED,
   * APP_FUNCTIONS_ALLOWED
   *
   * @param self::APP_FUNCTIONS_* $appFunctions
   */
  public function setAppFunctions($appFunctions)
  {
    $this->appFunctions = $appFunctions;
  }
  /**
   * @return self::APP_FUNCTIONS_*
   */
  public function getAppFunctions()
  {
    return $this->appFunctions;
  }
  /**
   * Policy applied to apps. This can have at most 3,000 elements.
   *
   * @param ApplicationPolicy[] $applications
   */
  public function setApplications($applications)
  {
    $this->applications = $applications;
  }
  /**
   * @return ApplicationPolicy[]
   */
  public function getApplications()
  {
    return $this->applications;
  }
  /**
   * Optional. Controls whether AssistContent
   * (https://developer.android.com/reference/android/app/assist/AssistContent)
   * is allowed to be sent to a privileged app such as an assistant app.
   * AssistContent includes screenshots and information about an app, such as
   * package name. This is supported on Android 15 and above.
   *
   * Accepted values: ASSIST_CONTENT_POLICY_UNSPECIFIED,
   * ASSIST_CONTENT_DISALLOWED, ASSIST_CONTENT_ALLOWED
   *
   * @param self::ASSIST_CONTENT_POLICY_* $assistContentPolicy
   */
  public function setAssistContentPolicy($assistContentPolicy)
  {
    $this->assistContentPolicy = $assistContentPolicy;
  }
  /**
   * @return self::ASSIST_CONTENT_POLICY_*
   */
  public function getAssistContentPolicy()
  {
    return $this->assistContentPolicy;
  }
  /**
   * Whether auto date, time, and time zone are enabled on a company-owned
   * device. If this is set, then autoTimeRequired is ignored.
   *
   * Accepted values: AUTO_DATE_AND_TIME_ZONE_UNSPECIFIED,
   * AUTO_DATE_AND_TIME_ZONE_USER_CHOICE, AUTO_DATE_AND_TIME_ZONE_ENFORCED
   *
   * @param self::AUTO_DATE_AND_TIME_ZONE_* $autoDateAndTimeZone
   */
  public function setAutoDateAndTimeZone($autoDateAndTimeZone)
  {
    $this->autoDateAndTimeZone = $autoDateAndTimeZone;
  }
  /**
   * @return self::AUTO_DATE_AND_TIME_ZONE_*
   */
  public function getAutoDateAndTimeZone()
  {
    return $this->autoDateAndTimeZone;
  }
  /**
   * Whether auto time is required, which prevents the user from manually
   * setting the date and time. If autoDateAndTimeZone is set, this field is
   * ignored.
   *
   * @deprecated
   * @param bool $autoTimeRequired
   */
  public function setAutoTimeRequired($autoTimeRequired)
  {
    $this->autoTimeRequired = $autoTimeRequired;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAutoTimeRequired()
  {
    return $this->autoTimeRequired;
  }
  /**
   * Whether applications other than the ones configured in applications are
   * blocked from being installed. When set, applications that were installed
   * under a previous policy but no longer appear in the policy are
   * automatically uninstalled.
   *
   * @deprecated
   * @param bool $blockApplicationsEnabled
   */
  public function setBlockApplicationsEnabled($blockApplicationsEnabled)
  {
    $this->blockApplicationsEnabled = $blockApplicationsEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getBlockApplicationsEnabled()
  {
    return $this->blockApplicationsEnabled;
  }
  /**
   * Whether configuring bluetooth is disabled.
   *
   * @param bool $bluetoothConfigDisabled
   */
  public function setBluetoothConfigDisabled($bluetoothConfigDisabled)
  {
    $this->bluetoothConfigDisabled = $bluetoothConfigDisabled;
  }
  /**
   * @return bool
   */
  public function getBluetoothConfigDisabled()
  {
    return $this->bluetoothConfigDisabled;
  }
  /**
   * Whether bluetooth contact sharing is disabled.
   *
   * @param bool $bluetoothContactSharingDisabled
   */
  public function setBluetoothContactSharingDisabled($bluetoothContactSharingDisabled)
  {
    $this->bluetoothContactSharingDisabled = $bluetoothContactSharingDisabled;
  }
  /**
   * @return bool
   */
  public function getBluetoothContactSharingDisabled()
  {
    return $this->bluetoothContactSharingDisabled;
  }
  /**
   * Whether bluetooth is disabled. Prefer this setting over
   * bluetooth_config_disabled because bluetooth_config_disabled can be bypassed
   * by the user.
   *
   * @param bool $bluetoothDisabled
   */
  public function setBluetoothDisabled($bluetoothDisabled)
  {
    $this->bluetoothDisabled = $bluetoothDisabled;
  }
  /**
   * @return bool
   */
  public function getBluetoothDisabled()
  {
    return $this->bluetoothDisabled;
  }
  /**
   * Controls the use of the camera and whether the user has access to the
   * camera access toggle.
   *
   * Accepted values: CAMERA_ACCESS_UNSPECIFIED, CAMERA_ACCESS_USER_CHOICE,
   * CAMERA_ACCESS_DISABLED, CAMERA_ACCESS_ENFORCED
   *
   * @param self::CAMERA_ACCESS_* $cameraAccess
   */
  public function setCameraAccess($cameraAccess)
  {
    $this->cameraAccess = $cameraAccess;
  }
  /**
   * @return self::CAMERA_ACCESS_*
   */
  public function getCameraAccess()
  {
    return $this->cameraAccess;
  }
  /**
   * If camera_access is set to any value other than CAMERA_ACCESS_UNSPECIFIED,
   * this has no effect. Otherwise this field controls whether cameras are
   * disabled: If true, all cameras are disabled, otherwise they are available.
   * For fully managed devices this field applies for all apps on the device.
   * For work profiles, this field applies only to apps in the work profile, and
   * the camera access of apps outside the work profile is unaffected.
   *
   * @deprecated
   * @param bool $cameraDisabled
   */
  public function setCameraDisabled($cameraDisabled)
  {
    $this->cameraDisabled = $cameraDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCameraDisabled()
  {
    return $this->cameraDisabled;
  }
  /**
   * Whether configuring cell broadcast is disabled.
   *
   * @param bool $cellBroadcastsConfigDisabled
   */
  public function setCellBroadcastsConfigDisabled($cellBroadcastsConfigDisabled)
  {
    $this->cellBroadcastsConfigDisabled = $cellBroadcastsConfigDisabled;
  }
  /**
   * @return bool
   */
  public function getCellBroadcastsConfigDisabled()
  {
    return $this->cellBroadcastsConfigDisabled;
  }
  /**
   * Rules for determining apps' access to private keys. See
   * ChoosePrivateKeyRule for details. This must be empty if any application has
   * CERT_SELECTION delegation scope.
   *
   * @param ChoosePrivateKeyRule[] $choosePrivateKeyRules
   */
  public function setChoosePrivateKeyRules($choosePrivateKeyRules)
  {
    $this->choosePrivateKeyRules = $choosePrivateKeyRules;
  }
  /**
   * @return ChoosePrivateKeyRule[]
   */
  public function getChoosePrivateKeyRules()
  {
    return $this->choosePrivateKeyRules;
  }
  /**
   * Rules declaring which mitigating actions to take when a device is not
   * compliant with its policy. When the conditions for multiple rules are
   * satisfied, all of the mitigating actions for the rules are taken. There is
   * a maximum limit of 100 rules. Use policy enforcement rules instead.
   *
   * @deprecated
   * @param ComplianceRule[] $complianceRules
   */
  public function setComplianceRules($complianceRules)
  {
    $this->complianceRules = $complianceRules;
  }
  /**
   * @deprecated
   * @return ComplianceRule[]
   */
  public function getComplianceRules()
  {
    return $this->complianceRules;
  }
  /**
   * Whether creating windows besides app windows is disabled.
   *
   * @param bool $createWindowsDisabled
   */
  public function setCreateWindowsDisabled($createWindowsDisabled)
  {
    $this->createWindowsDisabled = $createWindowsDisabled;
  }
  /**
   * @return bool
   */
  public function getCreateWindowsDisabled()
  {
    return $this->createWindowsDisabled;
  }
  /**
   * Controls which apps are allowed to act as credential providers on Android
   * 14 and above. These apps store credentials, see this
   * (https://developer.android.com/training/sign-in/passkeys) and this (https:/
   * /developer.android.com/reference/androidx/credentials/CredentialManager)
   * for details. See also credentialProviderPolicy.
   *
   * Accepted values: CREDENTIAL_PROVIDER_POLICY_DEFAULT_UNSPECIFIED,
   * CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED,
   * CREDENTIAL_PROVIDER_DEFAULT_DISALLOWED_EXCEPT_SYSTEM
   *
   * @param self::CREDENTIAL_PROVIDER_POLICY_DEFAULT_* $credentialProviderPolicyDefault
   */
  public function setCredentialProviderPolicyDefault($credentialProviderPolicyDefault)
  {
    $this->credentialProviderPolicyDefault = $credentialProviderPolicyDefault;
  }
  /**
   * @return self::CREDENTIAL_PROVIDER_POLICY_DEFAULT_*
   */
  public function getCredentialProviderPolicyDefault()
  {
    return $this->credentialProviderPolicyDefault;
  }
  /**
   * Whether configuring user credentials is disabled.
   *
   * @param bool $credentialsConfigDisabled
   */
  public function setCredentialsConfigDisabled($credentialsConfigDisabled)
  {
    $this->credentialsConfigDisabled = $credentialsConfigDisabled;
  }
  /**
   * @return bool
   */
  public function getCredentialsConfigDisabled()
  {
    return $this->credentialsConfigDisabled;
  }
  /**
   * Cross-profile policies applied on the device.
   *
   * @param CrossProfilePolicies $crossProfilePolicies
   */
  public function setCrossProfilePolicies(CrossProfilePolicies $crossProfilePolicies)
  {
    $this->crossProfilePolicies = $crossProfilePolicies;
  }
  /**
   * @return CrossProfilePolicies
   */
  public function getCrossProfilePolicies()
  {
    return $this->crossProfilePolicies;
  }
  /**
   * Whether roaming data services are disabled.
   *
   * @param bool $dataRoamingDisabled
   */
  public function setDataRoamingDisabled($dataRoamingDisabled)
  {
    $this->dataRoamingDisabled = $dataRoamingDisabled;
  }
  /**
   * @return bool
   */
  public function getDataRoamingDisabled()
  {
    return $this->dataRoamingDisabled;
  }
  /**
   * Whether the user is allowed to enable debugging features.
   *
   * @deprecated
   * @param bool $debuggingFeaturesAllowed
   */
  public function setDebuggingFeaturesAllowed($debuggingFeaturesAllowed)
  {
    $this->debuggingFeaturesAllowed = $debuggingFeaturesAllowed;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDebuggingFeaturesAllowed()
  {
    return $this->debuggingFeaturesAllowed;
  }
  /**
   * Optional. The default application setting for supported types. If the
   * default application is successfully set for at least one app type on a
   * profile, users are prevented from changing any default applications on that
   * profile.Only one DefaultApplicationSetting is allowed for each
   * DefaultApplicationType.See Default application settings
   * (https://developers.google.com/android/management/default-application-
   * settings) guide for more details.
   *
   * @param DefaultApplicationSetting[] $defaultApplicationSettings
   */
  public function setDefaultApplicationSettings($defaultApplicationSettings)
  {
    $this->defaultApplicationSettings = $defaultApplicationSettings;
  }
  /**
   * @return DefaultApplicationSetting[]
   */
  public function getDefaultApplicationSettings()
  {
    return $this->defaultApplicationSettings;
  }
  /**
   * The default permission policy for runtime permission requests.
   *
   * Accepted values: PERMISSION_POLICY_UNSPECIFIED, PROMPT, GRANT, DENY
   *
   * @param self::DEFAULT_PERMISSION_POLICY_* $defaultPermissionPolicy
   */
  public function setDefaultPermissionPolicy($defaultPermissionPolicy)
  {
    $this->defaultPermissionPolicy = $defaultPermissionPolicy;
  }
  /**
   * @return self::DEFAULT_PERMISSION_POLICY_*
   */
  public function getDefaultPermissionPolicy()
  {
    return $this->defaultPermissionPolicy;
  }
  /**
   * Covers controls for device connectivity such as Wi-Fi, USB data access,
   * keyboard/mouse connections, and more.
   *
   * @param DeviceConnectivityManagement $deviceConnectivityManagement
   */
  public function setDeviceConnectivityManagement(DeviceConnectivityManagement $deviceConnectivityManagement)
  {
    $this->deviceConnectivityManagement = $deviceConnectivityManagement;
  }
  /**
   * @return DeviceConnectivityManagement
   */
  public function getDeviceConnectivityManagement()
  {
    return $this->deviceConnectivityManagement;
  }
  /**
   * The device owner information to be shown on the lock screen.
   *
   * @param UserFacingMessage $deviceOwnerLockScreenInfo
   */
  public function setDeviceOwnerLockScreenInfo(UserFacingMessage $deviceOwnerLockScreenInfo)
  {
    $this->deviceOwnerLockScreenInfo = $deviceOwnerLockScreenInfo;
  }
  /**
   * @return UserFacingMessage
   */
  public function getDeviceOwnerLockScreenInfo()
  {
    return $this->deviceOwnerLockScreenInfo;
  }
  /**
   * Covers controls for radio state such as Wi-Fi, bluetooth, and more.
   *
   * @param DeviceRadioState $deviceRadioState
   */
  public function setDeviceRadioState(DeviceRadioState $deviceRadioState)
  {
    $this->deviceRadioState = $deviceRadioState;
  }
  /**
   * @return DeviceRadioState
   */
  public function getDeviceRadioState()
  {
    return $this->deviceRadioState;
  }
  /**
   * Optional. Controls for the display settings.
   *
   * @param DisplaySettings $displaySettings
   */
  public function setDisplaySettings(DisplaySettings $displaySettings)
  {
    $this->displaySettings = $displaySettings;
  }
  /**
   * @return DisplaySettings
   */
  public function getDisplaySettings()
  {
    return $this->displaySettings;
  }
  /**
   * Whether encryption is enabled
   *
   * Accepted values: ENCRYPTION_POLICY_UNSPECIFIED, ENABLED_WITHOUT_PASSWORD,
   * ENABLED_WITH_PASSWORD
   *
   * @param self::ENCRYPTION_POLICY_* $encryptionPolicy
   */
  public function setEncryptionPolicy($encryptionPolicy)
  {
    $this->encryptionPolicy = $encryptionPolicy;
  }
  /**
   * @return self::ENCRYPTION_POLICY_*
   */
  public function getEncryptionPolicy()
  {
    return $this->encryptionPolicy;
  }
  /**
   * Whether app verification is force-enabled.
   *
   * @deprecated
   * @param bool $ensureVerifyAppsEnabled
   */
  public function setEnsureVerifyAppsEnabled($ensureVerifyAppsEnabled)
  {
    $this->ensureVerifyAppsEnabled = $ensureVerifyAppsEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnsureVerifyAppsEnabled()
  {
    return $this->ensureVerifyAppsEnabled;
  }
  /**
   * Optional. Controls whether the enterpriseDisplayName is visible on the
   * device (e.g. lock screen message on company-owned devices).
   *
   * Accepted values: ENTERPRISE_DISPLAY_NAME_VISIBILITY_UNSPECIFIED,
   * ENTERPRISE_DISPLAY_NAME_VISIBLE, ENTERPRISE_DISPLAY_NAME_HIDDEN
   *
   * @param self::ENTERPRISE_DISPLAY_NAME_VISIBILITY_* $enterpriseDisplayNameVisibility
   */
  public function setEnterpriseDisplayNameVisibility($enterpriseDisplayNameVisibility)
  {
    $this->enterpriseDisplayNameVisibility = $enterpriseDisplayNameVisibility;
  }
  /**
   * @return self::ENTERPRISE_DISPLAY_NAME_VISIBILITY_*
   */
  public function getEnterpriseDisplayNameVisibility()
  {
    return $this->enterpriseDisplayNameVisibility;
  }
  /**
   * Whether factory resetting from settings is disabled.
   *
   * @param bool $factoryResetDisabled
   */
  public function setFactoryResetDisabled($factoryResetDisabled)
  {
    $this->factoryResetDisabled = $factoryResetDisabled;
  }
  /**
   * @return bool
   */
  public function getFactoryResetDisabled()
  {
    return $this->factoryResetDisabled;
  }
  /**
   * Email addresses of device administrators for factory reset protection. When
   * the device is factory reset, it will require one of these admins to log in
   * with the Google account email and password to unlock the device. If no
   * admins are specified, the device won't provide factory reset protection.
   *
   * @param string[] $frpAdminEmails
   */
  public function setFrpAdminEmails($frpAdminEmails)
  {
    $this->frpAdminEmails = $frpAdminEmails;
  }
  /**
   * @return string[]
   */
  public function getFrpAdminEmails()
  {
    return $this->frpAdminEmails;
  }
  /**
   * Whether the user is allowed to have fun. Controls whether the Easter egg
   * game in Settings is disabled.
   *
   * @param bool $funDisabled
   */
  public function setFunDisabled($funDisabled)
  {
    $this->funDisabled = $funDisabled;
  }
  /**
   * @return bool
   */
  public function getFunDisabled()
  {
    return $this->funDisabled;
  }
  /**
   * Whether user installation of apps is disabled.
   *
   * @param bool $installAppsDisabled
   */
  public function setInstallAppsDisabled($installAppsDisabled)
  {
    $this->installAppsDisabled = $installAppsDisabled;
  }
  /**
   * @return bool
   */
  public function getInstallAppsDisabled()
  {
    return $this->installAppsDisabled;
  }
  /**
   * This field has no effect.
   *
   * @deprecated
   * @param bool $installUnknownSourcesAllowed
   */
  public function setInstallUnknownSourcesAllowed($installUnknownSourcesAllowed)
  {
    $this->installUnknownSourcesAllowed = $installUnknownSourcesAllowed;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getInstallUnknownSourcesAllowed()
  {
    return $this->installUnknownSourcesAllowed;
  }
  /**
   * If true, this disables the Lock Screen
   * (https://source.android.com/docs/core/display/multi_display/lock-screen)
   * for primary and/or secondary displays. This policy is supported only in
   * dedicated device management mode.
   *
   * @param bool $keyguardDisabled
   */
  public function setKeyguardDisabled($keyguardDisabled)
  {
    $this->keyguardDisabled = $keyguardDisabled;
  }
  /**
   * @return bool
   */
  public function getKeyguardDisabled()
  {
    return $this->keyguardDisabled;
  }
  /**
   * Disabled keyguard customizations, such as widgets.
   *
   * @param string[] $keyguardDisabledFeatures
   */
  public function setKeyguardDisabledFeatures($keyguardDisabledFeatures)
  {
    $this->keyguardDisabledFeatures = $keyguardDisabledFeatures;
  }
  /**
   * @return string[]
   */
  public function getKeyguardDisabledFeatures()
  {
    return $this->keyguardDisabledFeatures;
  }
  /**
   * Whether the kiosk custom launcher is enabled. This replaces the home screen
   * with a launcher that locks down the device to the apps installed via the
   * applications setting. Apps appear on a single page in alphabetical order.
   * Use kioskCustomization to further configure the kiosk device behavior.
   *
   * @param bool $kioskCustomLauncherEnabled
   */
  public function setKioskCustomLauncherEnabled($kioskCustomLauncherEnabled)
  {
    $this->kioskCustomLauncherEnabled = $kioskCustomLauncherEnabled;
  }
  /**
   * @return bool
   */
  public function getKioskCustomLauncherEnabled()
  {
    return $this->kioskCustomLauncherEnabled;
  }
  /**
   * Settings controlling the behavior of a device in kiosk mode. To enable
   * kiosk mode, set kioskCustomLauncherEnabled to true or specify an app in the
   * policy with installType KIOSK.
   *
   * @param KioskCustomization $kioskCustomization
   */
  public function setKioskCustomization(KioskCustomization $kioskCustomization)
  {
    $this->kioskCustomization = $kioskCustomization;
  }
  /**
   * @return KioskCustomization
   */
  public function getKioskCustomization()
  {
    return $this->kioskCustomization;
  }
  /**
   * The degree of location detection enabled.
   *
   * Accepted values: LOCATION_MODE_UNSPECIFIED, HIGH_ACCURACY, SENSORS_ONLY,
   * BATTERY_SAVING, OFF, LOCATION_USER_CHOICE, LOCATION_ENFORCED,
   * LOCATION_DISABLED
   *
   * @param self::LOCATION_MODE_* $locationMode
   */
  public function setLocationMode($locationMode)
  {
    $this->locationMode = $locationMode;
  }
  /**
   * @return self::LOCATION_MODE_*
   */
  public function getLocationMode()
  {
    return $this->locationMode;
  }
  /**
   * A message displayed to the user in the device administators settings
   * screen.
   *
   * @param UserFacingMessage $longSupportMessage
   */
  public function setLongSupportMessage(UserFacingMessage $longSupportMessage)
  {
    $this->longSupportMessage = $longSupportMessage;
  }
  /**
   * @return UserFacingMessage
   */
  public function getLongSupportMessage()
  {
    return $this->longSupportMessage;
  }
  /**
   * Maximum time in milliseconds for user activity until the device locks. A
   * value of 0 means there is no restriction.
   *
   * @param string $maximumTimeToLock
   */
  public function setMaximumTimeToLock($maximumTimeToLock)
  {
    $this->maximumTimeToLock = $maximumTimeToLock;
  }
  /**
   * @return string
   */
  public function getMaximumTimeToLock()
  {
    return $this->maximumTimeToLock;
  }
  /**
   * Controls the use of the microphone and whether the user has access to the
   * microphone access toggle. This applies only on fully managed devices.
   *
   * Accepted values: MICROPHONE_ACCESS_UNSPECIFIED,
   * MICROPHONE_ACCESS_USER_CHOICE, MICROPHONE_ACCESS_DISABLED,
   * MICROPHONE_ACCESS_ENFORCED
   *
   * @param self::MICROPHONE_ACCESS_* $microphoneAccess
   */
  public function setMicrophoneAccess($microphoneAccess)
  {
    $this->microphoneAccess = $microphoneAccess;
  }
  /**
   * @return self::MICROPHONE_ACCESS_*
   */
  public function getMicrophoneAccess()
  {
    return $this->microphoneAccess;
  }
  /**
   * The minimum allowed Android API level.
   *
   * @param int $minimumApiLevel
   */
  public function setMinimumApiLevel($minimumApiLevel)
  {
    $this->minimumApiLevel = $minimumApiLevel;
  }
  /**
   * @return int
   */
  public function getMinimumApiLevel()
  {
    return $this->minimumApiLevel;
  }
  /**
   * Whether configuring mobile networks is disabled.
   *
   * @param bool $mobileNetworksConfigDisabled
   */
  public function setMobileNetworksConfigDisabled($mobileNetworksConfigDisabled)
  {
    $this->mobileNetworksConfigDisabled = $mobileNetworksConfigDisabled;
  }
  /**
   * @return bool
   */
  public function getMobileNetworksConfigDisabled()
  {
    return $this->mobileNetworksConfigDisabled;
  }
  /**
   * Whether adding or removing accounts is disabled.
   *
   * @param bool $modifyAccountsDisabled
   */
  public function setModifyAccountsDisabled($modifyAccountsDisabled)
  {
    $this->modifyAccountsDisabled = $modifyAccountsDisabled;
  }
  /**
   * @return bool
   */
  public function getModifyAccountsDisabled()
  {
    return $this->modifyAccountsDisabled;
  }
  /**
   * Whether the user mounting physical external media is disabled.
   *
   * @param bool $mountPhysicalMediaDisabled
   */
  public function setMountPhysicalMediaDisabled($mountPhysicalMediaDisabled)
  {
    $this->mountPhysicalMediaDisabled = $mountPhysicalMediaDisabled;
  }
  /**
   * @return bool
   */
  public function getMountPhysicalMediaDisabled()
  {
    return $this->mountPhysicalMediaDisabled;
  }
  /**
   * The name of the policy in the form
   * enterprises/{enterpriseId}/policies/{policyId}.
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
   * Whether the network escape hatch is enabled. If a network connection can't
   * be made at boot time, the escape hatch prompts the user to temporarily
   * connect to a network in order to refresh the device policy. After applying
   * policy, the temporary network will be forgotten and the device will
   * continue booting. This prevents being unable to connect to a network if
   * there is no suitable network in the last policy and the device boots into
   * an app in lock task mode, or the user is otherwise unable to reach device
   * settings.Note: Setting wifiConfigDisabled to true will override this
   * setting under specific circumstances. Please see wifiConfigDisabled for
   * further details. Setting configureWifi to DISALLOW_CONFIGURING_WIFI will
   * override this setting under specific circumstances. Please see
   * DISALLOW_CONFIGURING_WIFI for further details.
   *
   * @param bool $networkEscapeHatchEnabled
   */
  public function setNetworkEscapeHatchEnabled($networkEscapeHatchEnabled)
  {
    $this->networkEscapeHatchEnabled = $networkEscapeHatchEnabled;
  }
  /**
   * @return bool
   */
  public function getNetworkEscapeHatchEnabled()
  {
    return $this->networkEscapeHatchEnabled;
  }
  /**
   * Whether resetting network settings is disabled.
   *
   * @param bool $networkResetDisabled
   */
  public function setNetworkResetDisabled($networkResetDisabled)
  {
    $this->networkResetDisabled = $networkResetDisabled;
  }
  /**
   * @return bool
   */
  public function getNetworkResetDisabled()
  {
    return $this->networkResetDisabled;
  }
  /**
   * This feature is not generally available.
   *
   * @param OncCertificateProvider[] $oncCertificateProviders
   */
  public function setOncCertificateProviders($oncCertificateProviders)
  {
    $this->oncCertificateProviders = $oncCertificateProviders;
  }
  /**
   * @return OncCertificateProvider[]
   */
  public function getOncCertificateProviders()
  {
    return $this->oncCertificateProviders;
  }
  /**
   * Network configuration for the device. See configure networks for more
   * information.
   *
   * @param array[] $openNetworkConfiguration
   */
  public function setOpenNetworkConfiguration($openNetworkConfiguration)
  {
    $this->openNetworkConfiguration = $openNetworkConfiguration;
  }
  /**
   * @return array[]
   */
  public function getOpenNetworkConfiguration()
  {
    return $this->openNetworkConfiguration;
  }
  /**
   * Whether using NFC to beam data from apps is disabled.
   *
   * @param bool $outgoingBeamDisabled
   */
  public function setOutgoingBeamDisabled($outgoingBeamDisabled)
  {
    $this->outgoingBeamDisabled = $outgoingBeamDisabled;
  }
  /**
   * @return bool
   */
  public function getOutgoingBeamDisabled()
  {
    return $this->outgoingBeamDisabled;
  }
  /**
   * Whether outgoing calls are disabled.
   *
   * @param bool $outgoingCallsDisabled
   */
  public function setOutgoingCallsDisabled($outgoingCallsDisabled)
  {
    $this->outgoingCallsDisabled = $outgoingCallsDisabled;
  }
  /**
   * @return bool
   */
  public function getOutgoingCallsDisabled()
  {
    return $this->outgoingCallsDisabled;
  }
  /**
   * Password requirement policies. Different policies can be set for work
   * profile or fully managed devices by setting the password_scope field in the
   * policy.
   *
   * @param PasswordRequirements[] $passwordPolicies
   */
  public function setPasswordPolicies($passwordPolicies)
  {
    $this->passwordPolicies = $passwordPolicies;
  }
  /**
   * @return PasswordRequirements[]
   */
  public function getPasswordPolicies()
  {
    return $this->passwordPolicies;
  }
  /**
   * Password requirements. The field
   * password_requirements.require_password_unlock must not be set. DEPRECATED -
   * Use passwordPolicies.Note:Complexity-based values of PasswordQuality, that
   * is, COMPLEXITY_LOW, COMPLEXITY_MEDIUM, and COMPLEXITY_HIGH, cannot be used
   * here. unified_lock_settings cannot be used here.
   *
   * @deprecated
   * @param PasswordRequirements $passwordRequirements
   */
  public function setPasswordRequirements(PasswordRequirements $passwordRequirements)
  {
    $this->passwordRequirements = $passwordRequirements;
  }
  /**
   * @deprecated
   * @return PasswordRequirements
   */
  public function getPasswordRequirements()
  {
    return $this->passwordRequirements;
  }
  /**
   * Explicit permission or group grants or denials for all apps. These values
   * override the default_permission_policy.
   *
   * @param PermissionGrant[] $permissionGrants
   */
  public function setPermissionGrants($permissionGrants)
  {
    $this->permissionGrants = $permissionGrants;
  }
  /**
   * @return PermissionGrant[]
   */
  public function getPermissionGrants()
  {
    return $this->permissionGrants;
  }
  /**
   * Specifies permitted accessibility services. If the field is not set, any
   * accessibility service can be used. If the field is set, only the
   * accessibility services in this list and the system's built-in accessibility
   * service can be used. In particular, if the field is set to empty, only the
   * system's built-in accessibility servicess can be used. This can be set on
   * fully managed devices and on work profiles. When applied to a work profile,
   * this affects both the personal profile and the work profile.
   *
   * @param PackageNameList $permittedAccessibilityServices
   */
  public function setPermittedAccessibilityServices(PackageNameList $permittedAccessibilityServices)
  {
    $this->permittedAccessibilityServices = $permittedAccessibilityServices;
  }
  /**
   * @return PackageNameList
   */
  public function getPermittedAccessibilityServices()
  {
    return $this->permittedAccessibilityServices;
  }
  /**
   * If present, only the input methods provided by packages in this list are
   * permitted. If this field is present, but the list is empty, then only
   * system input methods are permitted.
   *
   * @param PackageNameList $permittedInputMethods
   */
  public function setPermittedInputMethods(PackageNameList $permittedInputMethods)
  {
    $this->permittedInputMethods = $permittedInputMethods;
  }
  /**
   * @return PackageNameList
   */
  public function getPermittedInputMethods()
  {
    return $this->permittedInputMethods;
  }
  /**
   * Default intent handler activities.
   *
   * @param PersistentPreferredActivity[] $persistentPreferredActivities
   */
  public function setPersistentPreferredActivities($persistentPreferredActivities)
  {
    $this->persistentPreferredActivities = $persistentPreferredActivities;
  }
  /**
   * @return PersistentPreferredActivity[]
   */
  public function getPersistentPreferredActivities()
  {
    return $this->persistentPreferredActivities;
  }
  /**
   * Policies managing personal usage on a company-owned device.
   *
   * @param PersonalUsagePolicies $personalUsagePolicies
   */
  public function setPersonalUsagePolicies(PersonalUsagePolicies $personalUsagePolicies)
  {
    $this->personalUsagePolicies = $personalUsagePolicies;
  }
  /**
   * @return PersonalUsagePolicies
   */
  public function getPersonalUsagePolicies()
  {
    return $this->personalUsagePolicies;
  }
  /**
   * This mode controls which apps are available to the user in the Play Store
   * and the behavior on the device when apps are removed from the policy.
   *
   * Accepted values: PLAY_STORE_MODE_UNSPECIFIED, WHITELIST, BLACKLIST
   *
   * @param self::PLAY_STORE_MODE_* $playStoreMode
   */
  public function setPlayStoreMode($playStoreMode)
  {
    $this->playStoreMode = $playStoreMode;
  }
  /**
   * @return self::PLAY_STORE_MODE_*
   */
  public function getPlayStoreMode()
  {
    return $this->playStoreMode;
  }
  /**
   * Rules that define the behavior when a particular policy can not be applied
   * on device
   *
   * @param PolicyEnforcementRule[] $policyEnforcementRules
   */
  public function setPolicyEnforcementRules($policyEnforcementRules)
  {
    $this->policyEnforcementRules = $policyEnforcementRules;
  }
  /**
   * @return PolicyEnforcementRule[]
   */
  public function getPolicyEnforcementRules()
  {
    return $this->policyEnforcementRules;
  }
  /**
   * Controls whether preferential network service is enabled on the work
   * profile or on fully managed devices. For example, an organization may have
   * an agreement with a carrier that all of the work data from its employees'
   * devices will be sent via a network service dedicated for enterprise use. An
   * example of a supported preferential network service is the enterprise slice
   * on 5G networks. This policy has no effect if
   * preferentialNetworkServiceSettings or
   * ApplicationPolicy.preferentialNetworkId is set on devices running Android
   * 13 or above.
   *
   * Accepted values: PREFERENTIAL_NETWORK_SERVICE_UNSPECIFIED,
   * PREFERENTIAL_NETWORK_SERVICE_DISABLED, PREFERENTIAL_NETWORK_SERVICE_ENABLED
   *
   * @param self::PREFERENTIAL_NETWORK_SERVICE_* $preferentialNetworkService
   */
  public function setPreferentialNetworkService($preferentialNetworkService)
  {
    $this->preferentialNetworkService = $preferentialNetworkService;
  }
  /**
   * @return self::PREFERENTIAL_NETWORK_SERVICE_*
   */
  public function getPreferentialNetworkService()
  {
    return $this->preferentialNetworkService;
  }
  /**
   * Optional. Controls whether printing is allowed. This is supported on
   * devices running Android 9 and above. .
   *
   * Accepted values: PRINTING_POLICY_UNSPECIFIED, PRINTING_DISALLOWED,
   * PRINTING_ALLOWED
   *
   * @param self::PRINTING_POLICY_* $printingPolicy
   */
  public function setPrintingPolicy($printingPolicy)
  {
    $this->printingPolicy = $printingPolicy;
  }
  /**
   * @return self::PRINTING_POLICY_*
   */
  public function getPrintingPolicy()
  {
    return $this->printingPolicy;
  }
  /**
   * Allows showing UI on a device for a user to choose a private key alias if
   * there are no matching rules in ChoosePrivateKeyRules. For devices below
   * Android P, setting this may leave enterprise keys vulnerable. This value
   * will have no effect if any application has CERT_SELECTION delegation scope.
   *
   * @param bool $privateKeySelectionEnabled
   */
  public function setPrivateKeySelectionEnabled($privateKeySelectionEnabled)
  {
    $this->privateKeySelectionEnabled = $privateKeySelectionEnabled;
  }
  /**
   * @return bool
   */
  public function getPrivateKeySelectionEnabled()
  {
    return $this->privateKeySelectionEnabled;
  }
  /**
   * The network-independent global HTTP proxy. Typically proxies should be
   * configured per-network in open_network_configuration. However for unusual
   * configurations like general internal filtering a global HTTP proxy may be
   * useful. If the proxy is not accessible, network access may break. The
   * global proxy is only a recommendation and some apps may ignore it.
   *
   * @param ProxyInfo $recommendedGlobalProxy
   */
  public function setRecommendedGlobalProxy(ProxyInfo $recommendedGlobalProxy)
  {
    $this->recommendedGlobalProxy = $recommendedGlobalProxy;
  }
  /**
   * @return ProxyInfo
   */
  public function getRecommendedGlobalProxy()
  {
    return $this->recommendedGlobalProxy;
  }
  /**
   * Whether removing other users is disabled.
   *
   * @param bool $removeUserDisabled
   */
  public function setRemoveUserDisabled($removeUserDisabled)
  {
    $this->removeUserDisabled = $removeUserDisabled;
  }
  /**
   * @return bool
   */
  public function getRemoveUserDisabled()
  {
    return $this->removeUserDisabled;
  }
  /**
   * Whether rebooting the device into safe boot is disabled.
   *
   * @deprecated
   * @param bool $safeBootDisabled
   */
  public function setSafeBootDisabled($safeBootDisabled)
  {
    $this->safeBootDisabled = $safeBootDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getSafeBootDisabled()
  {
    return $this->safeBootDisabled;
  }
  /**
   * Whether screen capture is disabled.
   *
   * @param bool $screenCaptureDisabled
   */
  public function setScreenCaptureDisabled($screenCaptureDisabled)
  {
    $this->screenCaptureDisabled = $screenCaptureDisabled;
  }
  /**
   * @return bool
   */
  public function getScreenCaptureDisabled()
  {
    return $this->screenCaptureDisabled;
  }
  /**
   * Whether changing the user icon is disabled. This applies only on devices
   * running Android 7 and above.
   *
   * @param bool $setUserIconDisabled
   */
  public function setSetUserIconDisabled($setUserIconDisabled)
  {
    $this->setUserIconDisabled = $setUserIconDisabled;
  }
  /**
   * @return bool
   */
  public function getSetUserIconDisabled()
  {
    return $this->setUserIconDisabled;
  }
  /**
   * Whether changing the wallpaper is disabled.
   *
   * @param bool $setWallpaperDisabled
   */
  public function setSetWallpaperDisabled($setWallpaperDisabled)
  {
    $this->setWallpaperDisabled = $setWallpaperDisabled;
  }
  /**
   * @return bool
   */
  public function getSetWallpaperDisabled()
  {
    return $this->setWallpaperDisabled;
  }
  /**
   * Action to take during the setup process. At most one action may be
   * specified.
   *
   * @param SetupAction[] $setupActions
   */
  public function setSetupActions($setupActions)
  {
    $this->setupActions = $setupActions;
  }
  /**
   * @return SetupAction[]
   */
  public function getSetupActions()
  {
    return $this->setupActions;
  }
  /**
   * Whether location sharing is disabled.
   *
   * @param bool $shareLocationDisabled
   */
  public function setShareLocationDisabled($shareLocationDisabled)
  {
    $this->shareLocationDisabled = $shareLocationDisabled;
  }
  /**
   * @return bool
   */
  public function getShareLocationDisabled()
  {
    return $this->shareLocationDisabled;
  }
  /**
   * A message displayed to the user in the settings screen wherever
   * functionality has been disabled by the admin. If the message is longer than
   * 200 characters it may be truncated.
   *
   * @param UserFacingMessage $shortSupportMessage
   */
  public function setShortSupportMessage(UserFacingMessage $shortSupportMessage)
  {
    $this->shortSupportMessage = $shortSupportMessage;
  }
  /**
   * @return UserFacingMessage
   */
  public function getShortSupportMessage()
  {
    return $this->shortSupportMessage;
  }
  /**
   * Flag to skip hints on the first use. Enterprise admin can enable the system
   * recommendation for apps to skip their user tutorial and other introductory
   * hints on first start-up.
   *
   * @param bool $skipFirstUseHintsEnabled
   */
  public function setSkipFirstUseHintsEnabled($skipFirstUseHintsEnabled)
  {
    $this->skipFirstUseHintsEnabled = $skipFirstUseHintsEnabled;
  }
  /**
   * @return bool
   */
  public function getSkipFirstUseHintsEnabled()
  {
    return $this->skipFirstUseHintsEnabled;
  }
  /**
   * Whether sending and receiving SMS messages is disabled.
   *
   * @param bool $smsDisabled
   */
  public function setSmsDisabled($smsDisabled)
  {
    $this->smsDisabled = $smsDisabled;
  }
  /**
   * @return bool
   */
  public function getSmsDisabled()
  {
    return $this->smsDisabled;
  }
  /**
   * Whether the status bar is disabled. This disables notifications, quick
   * settings, and other screen overlays that allow escape from full-screen
   * mode. DEPRECATED. To disable the status bar on a kiosk device, use
   * InstallType KIOSK or kioskCustomLauncherEnabled.
   *
   * @deprecated
   * @param bool $statusBarDisabled
   */
  public function setStatusBarDisabled($statusBarDisabled)
  {
    $this->statusBarDisabled = $statusBarDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getStatusBarDisabled()
  {
    return $this->statusBarDisabled;
  }
  /**
   * Status reporting settings
   *
   * @param StatusReportingSettings $statusReportingSettings
   */
  public function setStatusReportingSettings(StatusReportingSettings $statusReportingSettings)
  {
    $this->statusReportingSettings = $statusReportingSettings;
  }
  /**
   * @return StatusReportingSettings
   */
  public function getStatusReportingSettings()
  {
    return $this->statusReportingSettings;
  }
  /**
   * The battery plugged in modes for which the device stays on. When using this
   * setting, it is recommended to clear maximum_time_to_lock so that the device
   * doesn't lock itself while it stays on.
   *
   * @param string[] $stayOnPluggedModes
   */
  public function setStayOnPluggedModes($stayOnPluggedModes)
  {
    $this->stayOnPluggedModes = $stayOnPluggedModes;
  }
  /**
   * @return string[]
   */
  public function getStayOnPluggedModes()
  {
    return $this->stayOnPluggedModes;
  }
  /**
   * The system update policy, which controls how OS updates are applied. If the
   * update type is WINDOWED, the update window will automatically apply to Play
   * app updates as well.Note: Google Play system updates
   * (https://source.android.com/docs/core/ota/modular-system) (also called
   * Mainline updates) are automatically downloaded and require a device reboot
   * to be installed. Refer to the mainline section in Manage system updates
   * (https://developer.android.com/work/dpc/system-updates#mainline) for
   * further details.
   *
   * @param SystemUpdate $systemUpdate
   */
  public function setSystemUpdate(SystemUpdate $systemUpdate)
  {
    $this->systemUpdate = $systemUpdate;
  }
  /**
   * @return SystemUpdate
   */
  public function getSystemUpdate()
  {
    return $this->systemUpdate;
  }
  /**
   * Whether configuring tethering and portable hotspots is disabled. If
   * tetheringSettings is set to anything other than
   * TETHERING_SETTINGS_UNSPECIFIED, this setting is ignored.
   *
   * @deprecated
   * @param bool $tetheringConfigDisabled
   */
  public function setTetheringConfigDisabled($tetheringConfigDisabled)
  {
    $this->tetheringConfigDisabled = $tetheringConfigDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getTetheringConfigDisabled()
  {
    return $this->tetheringConfigDisabled;
  }
  /**
   * Whether user uninstallation of applications is disabled. This prevents apps
   * from being uninstalled, even those removed using applications
   *
   * @param bool $uninstallAppsDisabled
   */
  public function setUninstallAppsDisabled($uninstallAppsDisabled)
  {
    $this->uninstallAppsDisabled = $uninstallAppsDisabled;
  }
  /**
   * @return bool
   */
  public function getUninstallAppsDisabled()
  {
    return $this->uninstallAppsDisabled;
  }
  /**
   * If microphone_access is set to any value other than
   * MICROPHONE_ACCESS_UNSPECIFIED, this has no effect. Otherwise this field
   * controls whether microphones are disabled: If true, all microphones are
   * disabled, otherwise they are available. This is available only on fully
   * managed devices.
   *
   * @deprecated
   * @param bool $unmuteMicrophoneDisabled
   */
  public function setUnmuteMicrophoneDisabled($unmuteMicrophoneDisabled)
  {
    $this->unmuteMicrophoneDisabled = $unmuteMicrophoneDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getUnmuteMicrophoneDisabled()
  {
    return $this->unmuteMicrophoneDisabled;
  }
  /**
   * Configuration of device activity logging.
   *
   * @param UsageLog $usageLog
   */
  public function setUsageLog(UsageLog $usageLog)
  {
    $this->usageLog = $usageLog;
  }
  /**
   * @return UsageLog
   */
  public function getUsageLog()
  {
    return $this->usageLog;
  }
  /**
   * Whether transferring files over USB is disabled. This is supported only on
   * company-owned devices.
   *
   * @deprecated
   * @param bool $usbFileTransferDisabled
   */
  public function setUsbFileTransferDisabled($usbFileTransferDisabled)
  {
    $this->usbFileTransferDisabled = $usbFileTransferDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getUsbFileTransferDisabled()
  {
    return $this->usbFileTransferDisabled;
  }
  /**
   * Whether USB storage is enabled. Deprecated.
   *
   * @deprecated
   * @param bool $usbMassStorageEnabled
   */
  public function setUsbMassStorageEnabled($usbMassStorageEnabled)
  {
    $this->usbMassStorageEnabled = $usbMassStorageEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getUsbMassStorageEnabled()
  {
    return $this->usbMassStorageEnabled;
  }
  /**
   * The version of the policy. This is a read-only field. The version is
   * incremented each time the policy is updated.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * Whether configuring VPN is disabled.
   *
   * @param bool $vpnConfigDisabled
   */
  public function setVpnConfigDisabled($vpnConfigDisabled)
  {
    $this->vpnConfigDisabled = $vpnConfigDisabled;
  }
  /**
   * @return bool
   */
  public function getVpnConfigDisabled()
  {
    return $this->vpnConfigDisabled;
  }
  /**
   * Whether configuring Wi-Fi networks is disabled. Supported on fully managed
   * devices and work profiles on company-owned devices. For fully managed
   * devices, setting this to true removes all configured networks and retains
   * only the networks configured using openNetworkConfiguration. For work
   * profiles on company-owned devices, existing configured networks are not
   * affected and the user is not allowed to add, remove, or modify Wi-Fi
   * networks. If configureWifi is set to anything other than
   * CONFIGURE_WIFI_UNSPECIFIED, this setting is ignored. Note: If a network
   * connection can't be made at boot time and configuring Wi-Fi is disabled
   * then network escape hatch will be shown in order to refresh the device
   * policy (see networkEscapeHatchEnabled).
   *
   * @deprecated
   * @param bool $wifiConfigDisabled
   */
  public function setWifiConfigDisabled($wifiConfigDisabled)
  {
    $this->wifiConfigDisabled = $wifiConfigDisabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getWifiConfigDisabled()
  {
    return $this->wifiConfigDisabled;
  }
  /**
   * This is deprecated.
   *
   * @deprecated
   * @param bool $wifiConfigsLockdownEnabled
   */
  public function setWifiConfigsLockdownEnabled($wifiConfigsLockdownEnabled)
  {
    $this->wifiConfigsLockdownEnabled = $wifiConfigsLockdownEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getWifiConfigsLockdownEnabled()
  {
    return $this->wifiConfigsLockdownEnabled;
  }
  /**
   * Optional. Wipe flags to indicate what data is wiped when a device or
   * profile wipe is triggered due to any reason (for example, non-compliance).
   * This does not apply to the enterprises.devices.delete method. . This list
   * must not have duplicates.
   *
   * @param string[] $wipeDataFlags
   */
  public function setWipeDataFlags($wipeDataFlags)
  {
    $this->wipeDataFlags = $wipeDataFlags;
  }
  /**
   * @return string[]
   */
  public function getWipeDataFlags()
  {
    return $this->wipeDataFlags;
  }
  /**
   * Optional. Controls the work account setup configuration, such as details of
   * whether a Google authenticated account is required.
   *
   * @param WorkAccountSetupConfig $workAccountSetupConfig
   */
  public function setWorkAccountSetupConfig(WorkAccountSetupConfig $workAccountSetupConfig)
  {
    $this->workAccountSetupConfig = $workAccountSetupConfig;
  }
  /**
   * @return WorkAccountSetupConfig
   */
  public function getWorkAccountSetupConfig()
  {
    return $this->workAccountSetupConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_AndroidManagement_Policy');
