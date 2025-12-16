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

namespace Google\Service\Verifiedaccess;

class VerifyChallengeResponseResult extends \Google\Model
{
  /**
   * UNSPECIFIED.
   */
  public const KEY_TRUST_LEVEL_KEY_TRUST_LEVEL_UNSPECIFIED = 'KEY_TRUST_LEVEL_UNSPECIFIED';
  /**
   * ChromeOS device in verified mode.
   */
  public const KEY_TRUST_LEVEL_CHROME_OS_VERIFIED_MODE = 'CHROME_OS_VERIFIED_MODE';
  /**
   * ChromeOS device in developer mode.
   */
  public const KEY_TRUST_LEVEL_CHROME_OS_DEVELOPER_MODE = 'CHROME_OS_DEVELOPER_MODE';
  /**
   * Chrome Browser with the key stored in the device hardware.
   */
  public const KEY_TRUST_LEVEL_CHROME_BROWSER_HW_KEY = 'CHROME_BROWSER_HW_KEY';
  /**
   * Chrome Browser with the key stored at OS level.
   */
  public const KEY_TRUST_LEVEL_CHROME_BROWSER_OS_KEY = 'CHROME_BROWSER_OS_KEY';
  /**
   * Chrome Browser without an attestation key.
   */
  public const KEY_TRUST_LEVEL_CHROME_BROWSER_NO_KEY = 'CHROME_BROWSER_NO_KEY';
  /**
   * UNSPECIFIED.
   */
  public const PROFILE_KEY_TRUST_LEVEL_KEY_TRUST_LEVEL_UNSPECIFIED = 'KEY_TRUST_LEVEL_UNSPECIFIED';
  /**
   * ChromeOS device in verified mode.
   */
  public const PROFILE_KEY_TRUST_LEVEL_CHROME_OS_VERIFIED_MODE = 'CHROME_OS_VERIFIED_MODE';
  /**
   * ChromeOS device in developer mode.
   */
  public const PROFILE_KEY_TRUST_LEVEL_CHROME_OS_DEVELOPER_MODE = 'CHROME_OS_DEVELOPER_MODE';
  /**
   * Chrome Browser with the key stored in the device hardware.
   */
  public const PROFILE_KEY_TRUST_LEVEL_CHROME_BROWSER_HW_KEY = 'CHROME_BROWSER_HW_KEY';
  /**
   * Chrome Browser with the key stored at OS level.
   */
  public const PROFILE_KEY_TRUST_LEVEL_CHROME_BROWSER_OS_KEY = 'CHROME_BROWSER_OS_KEY';
  /**
   * Chrome Browser without an attestation key.
   */
  public const PROFILE_KEY_TRUST_LEVEL_CHROME_BROWSER_NO_KEY = 'CHROME_BROWSER_NO_KEY';
  /**
   * Output only. Attested device ID (ADID).
   *
   * @var string
   */
  public $attestedDeviceId;
  /**
   * Output only. Unique customer id that this device belongs to, as defined by
   * the Google Admin SDK at https://developers.google.com/admin-
   * sdk/directory/v1/guides/manage-customers
   *
   * @var string
   */
  public $customerId;
  /**
   * Output only. Device enrollment id for ChromeOS devices.
   *
   * @var string
   */
  public $deviceEnrollmentId;
  /**
   * Output only. Device permanent id is returned in this field (for the machine
   * response only).
   *
   * @var string
   */
  public $devicePermanentId;
  /**
   * Output only. Deprecated. Device signal in json string representation.
   * Prefer using `device_signals` instead.
   *
   * @var string
   */
  public $deviceSignal;
  protected $deviceSignalsType = DeviceSignals::class;
  protected $deviceSignalsDataType = '';
  /**
   * Output only. Device attested key trust level.
   *
   * @var string
   */
  public $keyTrustLevel;
  /**
   * Output only. Unique customer id that this profile belongs to, as defined by
   * the Google Admin SDK at https://developers.google.com/admin-
   * sdk/directory/v1/guides/manage-customers
   *
   * @var string
   */
  public $profileCustomerId;
  /**
   * Output only. Profile attested key trust level.
   *
   * @var string
   */
  public $profileKeyTrustLevel;
  /**
   * Output only. The unique server-side ID of a profile on the device.
   *
   * @var string
   */
  public $profilePermanentId;
  /**
   * Output only. Certificate Signing Request (in the SPKAC format, base64
   * encoded) is returned in this field. This field will be set only if device
   * has included CSR in its challenge response. (the option to include CSR is
   * now available for both user and machine responses)
   *
   * @var string
   */
  public $signedPublicKeyAndChallenge;
  /**
   * Output only. Virtual device id of the device. The definition of virtual
   * device id is platform-specific.
   *
   * @var string
   */
  public $virtualDeviceId;
  /**
   * Output only. The client-provided ID of a profile on the device.
   *
   * @var string
   */
  public $virtualProfileId;

  /**
   * Output only. Attested device ID (ADID).
   *
   * @param string $attestedDeviceId
   */
  public function setAttestedDeviceId($attestedDeviceId)
  {
    $this->attestedDeviceId = $attestedDeviceId;
  }
  /**
   * @return string
   */
  public function getAttestedDeviceId()
  {
    return $this->attestedDeviceId;
  }
  /**
   * Output only. Unique customer id that this device belongs to, as defined by
   * the Google Admin SDK at https://developers.google.com/admin-
   * sdk/directory/v1/guides/manage-customers
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Output only. Device enrollment id for ChromeOS devices.
   *
   * @param string $deviceEnrollmentId
   */
  public function setDeviceEnrollmentId($deviceEnrollmentId)
  {
    $this->deviceEnrollmentId = $deviceEnrollmentId;
  }
  /**
   * @return string
   */
  public function getDeviceEnrollmentId()
  {
    return $this->deviceEnrollmentId;
  }
  /**
   * Output only. Device permanent id is returned in this field (for the machine
   * response only).
   *
   * @param string $devicePermanentId
   */
  public function setDevicePermanentId($devicePermanentId)
  {
    $this->devicePermanentId = $devicePermanentId;
  }
  /**
   * @return string
   */
  public function getDevicePermanentId()
  {
    return $this->devicePermanentId;
  }
  /**
   * Output only. Deprecated. Device signal in json string representation.
   * Prefer using `device_signals` instead.
   *
   * @param string $deviceSignal
   */
  public function setDeviceSignal($deviceSignal)
  {
    $this->deviceSignal = $deviceSignal;
  }
  /**
   * @return string
   */
  public function getDeviceSignal()
  {
    return $this->deviceSignal;
  }
  /**
   * Output only. Device signals.
   *
   * @param DeviceSignals $deviceSignals
   */
  public function setDeviceSignals(DeviceSignals $deviceSignals)
  {
    $this->deviceSignals = $deviceSignals;
  }
  /**
   * @return DeviceSignals
   */
  public function getDeviceSignals()
  {
    return $this->deviceSignals;
  }
  /**
   * Output only. Device attested key trust level.
   *
   * Accepted values: KEY_TRUST_LEVEL_UNSPECIFIED, CHROME_OS_VERIFIED_MODE,
   * CHROME_OS_DEVELOPER_MODE, CHROME_BROWSER_HW_KEY, CHROME_BROWSER_OS_KEY,
   * CHROME_BROWSER_NO_KEY
   *
   * @param self::KEY_TRUST_LEVEL_* $keyTrustLevel
   */
  public function setKeyTrustLevel($keyTrustLevel)
  {
    $this->keyTrustLevel = $keyTrustLevel;
  }
  /**
   * @return self::KEY_TRUST_LEVEL_*
   */
  public function getKeyTrustLevel()
  {
    return $this->keyTrustLevel;
  }
  /**
   * Output only. Unique customer id that this profile belongs to, as defined by
   * the Google Admin SDK at https://developers.google.com/admin-
   * sdk/directory/v1/guides/manage-customers
   *
   * @param string $profileCustomerId
   */
  public function setProfileCustomerId($profileCustomerId)
  {
    $this->profileCustomerId = $profileCustomerId;
  }
  /**
   * @return string
   */
  public function getProfileCustomerId()
  {
    return $this->profileCustomerId;
  }
  /**
   * Output only. Profile attested key trust level.
   *
   * Accepted values: KEY_TRUST_LEVEL_UNSPECIFIED, CHROME_OS_VERIFIED_MODE,
   * CHROME_OS_DEVELOPER_MODE, CHROME_BROWSER_HW_KEY, CHROME_BROWSER_OS_KEY,
   * CHROME_BROWSER_NO_KEY
   *
   * @param self::PROFILE_KEY_TRUST_LEVEL_* $profileKeyTrustLevel
   */
  public function setProfileKeyTrustLevel($profileKeyTrustLevel)
  {
    $this->profileKeyTrustLevel = $profileKeyTrustLevel;
  }
  /**
   * @return self::PROFILE_KEY_TRUST_LEVEL_*
   */
  public function getProfileKeyTrustLevel()
  {
    return $this->profileKeyTrustLevel;
  }
  /**
   * Output only. The unique server-side ID of a profile on the device.
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
   * Output only. Certificate Signing Request (in the SPKAC format, base64
   * encoded) is returned in this field. This field will be set only if device
   * has included CSR in its challenge response. (the option to include CSR is
   * now available for both user and machine responses)
   *
   * @param string $signedPublicKeyAndChallenge
   */
  public function setSignedPublicKeyAndChallenge($signedPublicKeyAndChallenge)
  {
    $this->signedPublicKeyAndChallenge = $signedPublicKeyAndChallenge;
  }
  /**
   * @return string
   */
  public function getSignedPublicKeyAndChallenge()
  {
    return $this->signedPublicKeyAndChallenge;
  }
  /**
   * Output only. Virtual device id of the device. The definition of virtual
   * device id is platform-specific.
   *
   * @param string $virtualDeviceId
   */
  public function setVirtualDeviceId($virtualDeviceId)
  {
    $this->virtualDeviceId = $virtualDeviceId;
  }
  /**
   * @return string
   */
  public function getVirtualDeviceId()
  {
    return $this->virtualDeviceId;
  }
  /**
   * Output only. The client-provided ID of a profile on the device.
   *
   * @param string $virtualProfileId
   */
  public function setVirtualProfileId($virtualProfileId)
  {
    $this->virtualProfileId = $virtualProfileId;
  }
  /**
   * @return string
   */
  public function getVirtualProfileId()
  {
    return $this->virtualProfileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyChallengeResponseResult::class, 'Google_Service_Verifiedaccess_VerifyChallengeResponseResult');
