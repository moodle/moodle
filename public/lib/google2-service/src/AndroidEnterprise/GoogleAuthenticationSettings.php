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

namespace Google\Service\AndroidEnterprise;

class GoogleAuthenticationSettings extends \Google\Model
{
  /**
   * This value is unused.
   */
  public const DEDICATED_DEVICES_ALLOWED_dedicatedDevicesAllowedUnspecified = 'dedicatedDevicesAllowedUnspecified';
  /**
   * Dedicated devices are not allowed.
   */
  public const DEDICATED_DEVICES_ALLOWED_disallowed = 'disallowed';
  /**
   * Dedicated devices are allowed.
   */
  public const DEDICATED_DEVICES_ALLOWED_allowed = 'allowed';
  /**
   * This value is unused.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_googleAuthenticationRequiredUnspecified = 'googleAuthenticationRequiredUnspecified';
  /**
   * Google authentication is not required.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_notRequired = 'notRequired';
  /**
   * User is required to be successfully authenticated by Google.
   */
  public const GOOGLE_AUTHENTICATION_REQUIRED_required = 'required';
  /**
   * Whether dedicated devices are allowed.
   *
   * @var string
   */
  public $dedicatedDevicesAllowed;
  /**
   * Whether Google authentication is required.
   *
   * @var string
   */
  public $googleAuthenticationRequired;

  /**
   * Whether dedicated devices are allowed.
   *
   * Accepted values: dedicatedDevicesAllowedUnspecified, disallowed, allowed
   *
   * @param self::DEDICATED_DEVICES_ALLOWED_* $dedicatedDevicesAllowed
   */
  public function setDedicatedDevicesAllowed($dedicatedDevicesAllowed)
  {
    $this->dedicatedDevicesAllowed = $dedicatedDevicesAllowed;
  }
  /**
   * @return self::DEDICATED_DEVICES_ALLOWED_*
   */
  public function getDedicatedDevicesAllowed()
  {
    return $this->dedicatedDevicesAllowed;
  }
  /**
   * Whether Google authentication is required.
   *
   * Accepted values: googleAuthenticationRequiredUnspecified, notRequired,
   * required
   *
   * @param self::GOOGLE_AUTHENTICATION_REQUIRED_* $googleAuthenticationRequired
   */
  public function setGoogleAuthenticationRequired($googleAuthenticationRequired)
  {
    $this->googleAuthenticationRequired = $googleAuthenticationRequired;
  }
  /**
   * @return self::GOOGLE_AUTHENTICATION_REQUIRED_*
   */
  public function getGoogleAuthenticationRequired()
  {
    return $this->googleAuthenticationRequired;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAuthenticationSettings::class, 'Google_Service_AndroidEnterprise_GoogleAuthenticationSettings');
