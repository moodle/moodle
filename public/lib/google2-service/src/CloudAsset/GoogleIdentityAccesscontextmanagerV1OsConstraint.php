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

namespace Google\Service\CloudAsset;

class GoogleIdentityAccesscontextmanagerV1OsConstraint extends \Google\Model
{
  /**
   * The operating system of the device is not specified or not known.
   */
  public const OS_TYPE_OS_UNSPECIFIED = 'OS_UNSPECIFIED';
  /**
   * A desktop Mac operating system.
   */
  public const OS_TYPE_DESKTOP_MAC = 'DESKTOP_MAC';
  /**
   * A desktop Windows operating system.
   */
  public const OS_TYPE_DESKTOP_WINDOWS = 'DESKTOP_WINDOWS';
  /**
   * A desktop Linux operating system.
   */
  public const OS_TYPE_DESKTOP_LINUX = 'DESKTOP_LINUX';
  /**
   * A desktop ChromeOS operating system.
   */
  public const OS_TYPE_DESKTOP_CHROME_OS = 'DESKTOP_CHROME_OS';
  /**
   * An Android operating system.
   */
  public const OS_TYPE_ANDROID = 'ANDROID';
  /**
   * An iOS operating system.
   */
  public const OS_TYPE_IOS = 'IOS';
  /**
   * The minimum allowed OS version. If not set, any version of this OS
   * satisfies the constraint. Format: `"major.minor.patch"`. Examples:
   * `"10.5.301"`, `"9.2.1"`.
   *
   * @var string
   */
  public $minimumVersion;
  /**
   * Required. The allowed OS type.
   *
   * @var string
   */
  public $osType;
  /**
   * Only allows requests from devices with a verified Chrome OS. Verifications
   * includes requirements that the device is enterprise-managed, conformant to
   * domain policies, and the caller has permission to call the API targeted by
   * the request.
   *
   * @var bool
   */
  public $requireVerifiedChromeOs;

  /**
   * The minimum allowed OS version. If not set, any version of this OS
   * satisfies the constraint. Format: `"major.minor.patch"`. Examples:
   * `"10.5.301"`, `"9.2.1"`.
   *
   * @param string $minimumVersion
   */
  public function setMinimumVersion($minimumVersion)
  {
    $this->minimumVersion = $minimumVersion;
  }
  /**
   * @return string
   */
  public function getMinimumVersion()
  {
    return $this->minimumVersion;
  }
  /**
   * Required. The allowed OS type.
   *
   * Accepted values: OS_UNSPECIFIED, DESKTOP_MAC, DESKTOP_WINDOWS,
   * DESKTOP_LINUX, DESKTOP_CHROME_OS, ANDROID, IOS
   *
   * @param self::OS_TYPE_* $osType
   */
  public function setOsType($osType)
  {
    $this->osType = $osType;
  }
  /**
   * @return self::OS_TYPE_*
   */
  public function getOsType()
  {
    return $this->osType;
  }
  /**
   * Only allows requests from devices with a verified Chrome OS. Verifications
   * includes requirements that the device is enterprise-managed, conformant to
   * domain policies, and the caller has permission to call the API targeted by
   * the request.
   *
   * @param bool $requireVerifiedChromeOs
   */
  public function setRequireVerifiedChromeOs($requireVerifiedChromeOs)
  {
    $this->requireVerifiedChromeOs = $requireVerifiedChromeOs;
  }
  /**
   * @return bool
   */
  public function getRequireVerifiedChromeOs()
  {
    return $this->requireVerifiedChromeOs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityAccesscontextmanagerV1OsConstraint::class, 'Google_Service_CloudAsset_GoogleIdentityAccesscontextmanagerV1OsConstraint');
