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

class PasswordRequirements extends \Google\Model
{
  /**
   * There are no password requirements.
   */
  public const PASSWORD_QUALITY_PASSWORD_QUALITY_UNSPECIFIED = 'PASSWORD_QUALITY_UNSPECIFIED';
  /**
   * The device must be secured with a low-security biometric recognition
   * technology, at minimum. This includes technologies that can recognize the
   * identity of an individual that are roughly equivalent to a 3-digit PIN
   * (false detection is less than 1 in 1,000).This, when applied on personally
   * owned work profile devices on Android 12 device-scoped, will be treated as
   * COMPLEXITY_LOW for application. See PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_BIOMETRIC_WEAK = 'BIOMETRIC_WEAK';
  /**
   * A password is required, but there are no restrictions on what the password
   * must contain.This, when applied on personally owned work profile devices on
   * Android 12 device-scoped, will be treated as COMPLEXITY_LOW for
   * application. See PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_SOMETHING = 'SOMETHING';
  /**
   * The password must contain numeric characters.This, when applied on
   * personally owned work profile devices on Android 12 device-scoped, will be
   * treated as COMPLEXITY_MEDIUM for application. See PasswordQuality for
   * details.
   */
  public const PASSWORD_QUALITY_NUMERIC = 'NUMERIC';
  /**
   * The password must contain numeric characters with no repeating (4444) or
   * ordered (1234, 4321, 2468) sequences.This, when applied on personally owned
   * work profile devices on Android 12 device-scoped, will be treated as
   * COMPLEXITY_MEDIUM for application. See PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_NUMERIC_COMPLEX = 'NUMERIC_COMPLEX';
  /**
   * The password must contain alphabetic (or symbol) characters.This, when
   * applied on personally owned work profile devices on Android 12 device-
   * scoped, will be treated as COMPLEXITY_HIGH for application. See
   * PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_ALPHABETIC = 'ALPHABETIC';
  /**
   * The password must contain both numeric and alphabetic (or symbol)
   * characters.This, when applied on personally owned work profile devices on
   * Android 12 device-scoped, will be treated as COMPLEXITY_HIGH for
   * application. See PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_ALPHANUMERIC = 'ALPHANUMERIC';
  /**
   * The password must meet the minimum requirements specified in
   * passwordMinimumLength, passwordMinimumLetters, passwordMinimumSymbols, etc.
   * For example, if passwordMinimumSymbols is 2, the password must contain at
   * least two symbols.This, when applied on personally owned work profile
   * devices on Android 12 device-scoped, will be treated as COMPLEXITY_HIGH for
   * application. In this case, the requirements in passwordMinimumLength,
   * passwordMinimumLetters, passwordMinimumSymbols, etc are not applied. See
   * PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_COMPLEX = 'COMPLEX';
  /**
   * Define the low password complexity band as: pattern PIN with repeating
   * (4444) or ordered (1234, 4321, 2468) sequencesThis sets the minimum
   * complexity band which the password must meet.Enforcement varies among
   * different Android versions, management modes and password scopes. See
   * PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_COMPLEXITY_LOW = 'COMPLEXITY_LOW';
  /**
   * Define the medium password complexity band as: PIN with no repeating (4444)
   * or ordered (1234, 4321, 2468) sequences, length at least 4 alphabetic,
   * length at least 4 alphanumeric, length at least 4This sets the minimum
   * complexity band which the password must meet.Enforcement varies among
   * different Android versions, management modes and password scopes. See
   * PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_COMPLEXITY_MEDIUM = 'COMPLEXITY_MEDIUM';
  /**
   * Define the high password complexity band as:On Android 12 and above: PIN
   * with no repeating (4444) or ordered (1234, 4321, 2468) sequences, length at
   * least 8 alphabetic, length at least 6 alphanumeric, length at least 6This
   * sets the minimum complexity band which the password must meet.Enforcement
   * varies among different Android versions, management modes and password
   * scopes. See PasswordQuality for details.
   */
  public const PASSWORD_QUALITY_COMPLEXITY_HIGH = 'COMPLEXITY_HIGH';
  /**
   * The scope is unspecified. The password requirements are applied to the work
   * profile for work profile devices and the whole device for fully managed or
   * dedicated devices.
   */
  public const PASSWORD_SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * The password requirements are only applied to the device.
   */
  public const PASSWORD_SCOPE_SCOPE_DEVICE = 'SCOPE_DEVICE';
  /**
   * The password requirements are only applied to the work profile.
   */
  public const PASSWORD_SCOPE_SCOPE_PROFILE = 'SCOPE_PROFILE';
  /**
   * Unspecified. Defaults to USE_DEFAULT_DEVICE_TIMEOUT.
   */
  public const REQUIRE_PASSWORD_UNLOCK_REQUIRE_PASSWORD_UNLOCK_UNSPECIFIED = 'REQUIRE_PASSWORD_UNLOCK_UNSPECIFIED';
  /**
   * The timeout period is set to the device’s default.
   */
  public const REQUIRE_PASSWORD_UNLOCK_USE_DEFAULT_DEVICE_TIMEOUT = 'USE_DEFAULT_DEVICE_TIMEOUT';
  /**
   * The timeout period is set to 24 hours.
   */
  public const REQUIRE_PASSWORD_UNLOCK_REQUIRE_EVERY_DAY = 'REQUIRE_EVERY_DAY';
  /**
   * Unspecified. Defaults to ALLOW_UNIFIED_WORK_AND_PERSONAL_LOCK.
   */
  public const UNIFIED_LOCK_SETTINGS_UNIFIED_LOCK_SETTINGS_UNSPECIFIED = 'UNIFIED_LOCK_SETTINGS_UNSPECIFIED';
  /**
   * A common lock for the device and the work profile is allowed.
   */
  public const UNIFIED_LOCK_SETTINGS_ALLOW_UNIFIED_WORK_AND_PERSONAL_LOCK = 'ALLOW_UNIFIED_WORK_AND_PERSONAL_LOCK';
  /**
   * A separate lock for the work profile is required.
   */
  public const UNIFIED_LOCK_SETTINGS_REQUIRE_SEPARATE_WORK_LOCK = 'REQUIRE_SEPARATE_WORK_LOCK';
  /**
   * Number of incorrect device-unlock passwords that can be entered before a
   * device is wiped. A value of 0 means there is no restriction.
   *
   * @var int
   */
  public $maximumFailedPasswordsForWipe;
  /**
   * Password expiration timeout.
   *
   * @var string
   */
  public $passwordExpirationTimeout;
  /**
   * The length of the password history. After setting this field, the user
   * won't be able to enter a new password that is the same as any password in
   * the history. A value of 0 means there is no restriction.
   *
   * @var int
   */
  public $passwordHistoryLength;
  /**
   * The minimum allowed password length. A value of 0 means there is no
   * restriction. Only enforced when password_quality is NUMERIC,
   * NUMERIC_COMPLEX, ALPHABETIC, ALPHANUMERIC, or COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumLength;
  /**
   * Minimum number of letters required in the password. Only enforced when
   * password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumLetters;
  /**
   * Minimum number of lower case letters required in the password. Only
   * enforced when password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumLowerCase;
  /**
   * Minimum number of non-letter characters (numerical digits or symbols)
   * required in the password. Only enforced when password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumNonLetter;
  /**
   * Minimum number of numerical digits required in the password. Only enforced
   * when password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumNumeric;
  /**
   * Minimum number of symbols required in the password. Only enforced when
   * password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumSymbols;
  /**
   * Minimum number of upper case letters required in the password. Only
   * enforced when password_quality is COMPLEX.
   *
   * @var int
   */
  public $passwordMinimumUpperCase;
  /**
   * The required password quality.
   *
   * @var string
   */
  public $passwordQuality;
  /**
   * The scope that the password requirement applies to.
   *
   * @var string
   */
  public $passwordScope;
  /**
   * The length of time after a device or work profile is unlocked using a
   * strong form of authentication (password, PIN, pattern) that it can be
   * unlocked using any other authentication method (e.g. fingerprint, trust
   * agents, face). After the specified time period elapses, only strong forms
   * of authentication can be used to unlock the device or work profile.
   *
   * @var string
   */
  public $requirePasswordUnlock;
  /**
   * Controls whether a unified lock is allowed for the device and the work
   * profile, on devices running Android 9 and above with a work profile. This
   * can be set only if password_scope is set to SCOPE_PROFILE, the policy will
   * be rejected otherwise. If user has not set a separate work lock and this
   * field is set to REQUIRE_SEPARATE_WORK_LOCK, a NonComplianceDetail is
   * reported with nonComplianceReason set to USER_ACTION.
   *
   * @var string
   */
  public $unifiedLockSettings;

  /**
   * Number of incorrect device-unlock passwords that can be entered before a
   * device is wiped. A value of 0 means there is no restriction.
   *
   * @param int $maximumFailedPasswordsForWipe
   */
  public function setMaximumFailedPasswordsForWipe($maximumFailedPasswordsForWipe)
  {
    $this->maximumFailedPasswordsForWipe = $maximumFailedPasswordsForWipe;
  }
  /**
   * @return int
   */
  public function getMaximumFailedPasswordsForWipe()
  {
    return $this->maximumFailedPasswordsForWipe;
  }
  /**
   * Password expiration timeout.
   *
   * @param string $passwordExpirationTimeout
   */
  public function setPasswordExpirationTimeout($passwordExpirationTimeout)
  {
    $this->passwordExpirationTimeout = $passwordExpirationTimeout;
  }
  /**
   * @return string
   */
  public function getPasswordExpirationTimeout()
  {
    return $this->passwordExpirationTimeout;
  }
  /**
   * The length of the password history. After setting this field, the user
   * won't be able to enter a new password that is the same as any password in
   * the history. A value of 0 means there is no restriction.
   *
   * @param int $passwordHistoryLength
   */
  public function setPasswordHistoryLength($passwordHistoryLength)
  {
    $this->passwordHistoryLength = $passwordHistoryLength;
  }
  /**
   * @return int
   */
  public function getPasswordHistoryLength()
  {
    return $this->passwordHistoryLength;
  }
  /**
   * The minimum allowed password length. A value of 0 means there is no
   * restriction. Only enforced when password_quality is NUMERIC,
   * NUMERIC_COMPLEX, ALPHABETIC, ALPHANUMERIC, or COMPLEX.
   *
   * @param int $passwordMinimumLength
   */
  public function setPasswordMinimumLength($passwordMinimumLength)
  {
    $this->passwordMinimumLength = $passwordMinimumLength;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumLength()
  {
    return $this->passwordMinimumLength;
  }
  /**
   * Minimum number of letters required in the password. Only enforced when
   * password_quality is COMPLEX.
   *
   * @param int $passwordMinimumLetters
   */
  public function setPasswordMinimumLetters($passwordMinimumLetters)
  {
    $this->passwordMinimumLetters = $passwordMinimumLetters;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumLetters()
  {
    return $this->passwordMinimumLetters;
  }
  /**
   * Minimum number of lower case letters required in the password. Only
   * enforced when password_quality is COMPLEX.
   *
   * @param int $passwordMinimumLowerCase
   */
  public function setPasswordMinimumLowerCase($passwordMinimumLowerCase)
  {
    $this->passwordMinimumLowerCase = $passwordMinimumLowerCase;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumLowerCase()
  {
    return $this->passwordMinimumLowerCase;
  }
  /**
   * Minimum number of non-letter characters (numerical digits or symbols)
   * required in the password. Only enforced when password_quality is COMPLEX.
   *
   * @param int $passwordMinimumNonLetter
   */
  public function setPasswordMinimumNonLetter($passwordMinimumNonLetter)
  {
    $this->passwordMinimumNonLetter = $passwordMinimumNonLetter;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumNonLetter()
  {
    return $this->passwordMinimumNonLetter;
  }
  /**
   * Minimum number of numerical digits required in the password. Only enforced
   * when password_quality is COMPLEX.
   *
   * @param int $passwordMinimumNumeric
   */
  public function setPasswordMinimumNumeric($passwordMinimumNumeric)
  {
    $this->passwordMinimumNumeric = $passwordMinimumNumeric;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumNumeric()
  {
    return $this->passwordMinimumNumeric;
  }
  /**
   * Minimum number of symbols required in the password. Only enforced when
   * password_quality is COMPLEX.
   *
   * @param int $passwordMinimumSymbols
   */
  public function setPasswordMinimumSymbols($passwordMinimumSymbols)
  {
    $this->passwordMinimumSymbols = $passwordMinimumSymbols;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumSymbols()
  {
    return $this->passwordMinimumSymbols;
  }
  /**
   * Minimum number of upper case letters required in the password. Only
   * enforced when password_quality is COMPLEX.
   *
   * @param int $passwordMinimumUpperCase
   */
  public function setPasswordMinimumUpperCase($passwordMinimumUpperCase)
  {
    $this->passwordMinimumUpperCase = $passwordMinimumUpperCase;
  }
  /**
   * @return int
   */
  public function getPasswordMinimumUpperCase()
  {
    return $this->passwordMinimumUpperCase;
  }
  /**
   * The required password quality.
   *
   * Accepted values: PASSWORD_QUALITY_UNSPECIFIED, BIOMETRIC_WEAK, SOMETHING,
   * NUMERIC, NUMERIC_COMPLEX, ALPHABETIC, ALPHANUMERIC, COMPLEX,
   * COMPLEXITY_LOW, COMPLEXITY_MEDIUM, COMPLEXITY_HIGH
   *
   * @param self::PASSWORD_QUALITY_* $passwordQuality
   */
  public function setPasswordQuality($passwordQuality)
  {
    $this->passwordQuality = $passwordQuality;
  }
  /**
   * @return self::PASSWORD_QUALITY_*
   */
  public function getPasswordQuality()
  {
    return $this->passwordQuality;
  }
  /**
   * The scope that the password requirement applies to.
   *
   * Accepted values: SCOPE_UNSPECIFIED, SCOPE_DEVICE, SCOPE_PROFILE
   *
   * @param self::PASSWORD_SCOPE_* $passwordScope
   */
  public function setPasswordScope($passwordScope)
  {
    $this->passwordScope = $passwordScope;
  }
  /**
   * @return self::PASSWORD_SCOPE_*
   */
  public function getPasswordScope()
  {
    return $this->passwordScope;
  }
  /**
   * The length of time after a device or work profile is unlocked using a
   * strong form of authentication (password, PIN, pattern) that it can be
   * unlocked using any other authentication method (e.g. fingerprint, trust
   * agents, face). After the specified time period elapses, only strong forms
   * of authentication can be used to unlock the device or work profile.
   *
   * Accepted values: REQUIRE_PASSWORD_UNLOCK_UNSPECIFIED,
   * USE_DEFAULT_DEVICE_TIMEOUT, REQUIRE_EVERY_DAY
   *
   * @param self::REQUIRE_PASSWORD_UNLOCK_* $requirePasswordUnlock
   */
  public function setRequirePasswordUnlock($requirePasswordUnlock)
  {
    $this->requirePasswordUnlock = $requirePasswordUnlock;
  }
  /**
   * @return self::REQUIRE_PASSWORD_UNLOCK_*
   */
  public function getRequirePasswordUnlock()
  {
    return $this->requirePasswordUnlock;
  }
  /**
   * Controls whether a unified lock is allowed for the device and the work
   * profile, on devices running Android 9 and above with a work profile. This
   * can be set only if password_scope is set to SCOPE_PROFILE, the policy will
   * be rejected otherwise. If user has not set a separate work lock and this
   * field is set to REQUIRE_SEPARATE_WORK_LOCK, a NonComplianceDetail is
   * reported with nonComplianceReason set to USER_ACTION.
   *
   * Accepted values: UNIFIED_LOCK_SETTINGS_UNSPECIFIED,
   * ALLOW_UNIFIED_WORK_AND_PERSONAL_LOCK, REQUIRE_SEPARATE_WORK_LOCK
   *
   * @param self::UNIFIED_LOCK_SETTINGS_* $unifiedLockSettings
   */
  public function setUnifiedLockSettings($unifiedLockSettings)
  {
    $this->unifiedLockSettings = $unifiedLockSettings;
  }
  /**
   * @return self::UNIFIED_LOCK_SETTINGS_*
   */
  public function getUnifiedLockSettings()
  {
    return $this->unifiedLockSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PasswordRequirements::class, 'Google_Service_AndroidManagement_PasswordRequirements');
