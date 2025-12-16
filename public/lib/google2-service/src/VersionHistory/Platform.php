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

namespace Google\Service\VersionHistory;

class Platform extends \Google\Model
{
  public const PLATFORM_TYPE_PLATFORM_TYPE_UNSPECIFIED = 'PLATFORM_TYPE_UNSPECIFIED';
  /**
   * Chrome Desktop for Windows (32-bit).
   */
  public const PLATFORM_TYPE_WIN = 'WIN';
  /**
   * Chrome Desktop for Windows (x86_64).
   */
  public const PLATFORM_TYPE_WIN64 = 'WIN64';
  /**
   * Chrome Desktop for macOS (x86_64).
   */
  public const PLATFORM_TYPE_MAC = 'MAC';
  /**
   * Chrome Desktop for Linux.
   */
  public const PLATFORM_TYPE_LINUX = 'LINUX';
  /**
   * Chrome for Android.
   */
  public const PLATFORM_TYPE_ANDROID = 'ANDROID';
  /**
   * WebView for Android.
   */
  public const PLATFORM_TYPE_WEBVIEW = 'WEBVIEW';
  /**
   * Chrome for iOS.
   */
  public const PLATFORM_TYPE_IOS = 'IOS';
  public const PLATFORM_TYPE_ALL = 'ALL';
  /**
   * Chrome for macOS (ARM64).
   */
  public const PLATFORM_TYPE_MAC_ARM64 = 'MAC_ARM64';
  /**
   * ChromeOS Lacros (x86_64).
   */
  public const PLATFORM_TYPE_LACROS = 'LACROS';
  /**
   * ChromeOS Lacros (ARM).
   */
  public const PLATFORM_TYPE_LACROS_ARM32 = 'LACROS_ARM32';
  /**
   * ChromeOS.
   */
  public const PLATFORM_TYPE_CHROMEOS = 'CHROMEOS';
  /**
   * ChromeOS Lacros (ARM64).
   */
  public const PLATFORM_TYPE_LACROS_ARM64 = 'LACROS_ARM64';
  /**
   * Chrome for Fuchsia.
   */
  public const PLATFORM_TYPE_FUCHSIA = 'FUCHSIA';
  /**
   * Chrome Desktop for Windows (ARM64).
   */
  public const PLATFORM_TYPE_WIN_ARM64 = 'WIN_ARM64';
  /**
   * Platform name. Format is "{product}/platforms/{platform}"
   *
   * @var string
   */
  public $name;
  /**
   * Type of platform.
   *
   * @var string
   */
  public $platformType;

  /**
   * Platform name. Format is "{product}/platforms/{platform}"
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
   * Type of platform.
   *
   * Accepted values: PLATFORM_TYPE_UNSPECIFIED, WIN, WIN64, MAC, LINUX,
   * ANDROID, WEBVIEW, IOS, ALL, MAC_ARM64, LACROS, LACROS_ARM32, CHROMEOS,
   * LACROS_ARM64, FUCHSIA, WIN_ARM64
   *
   * @param self::PLATFORM_TYPE_* $platformType
   */
  public function setPlatformType($platformType)
  {
    $this->platformType = $platformType;
  }
  /**
   * @return self::PLATFORM_TYPE_*
   */
  public function getPlatformType()
  {
    return $this->platformType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Platform::class, 'Google_Service_VersionHistory_Platform');
