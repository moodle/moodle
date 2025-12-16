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

namespace Google\Service\MigrationCenterAPI;

class GuestInstalledApplication extends \Google\Collection
{
  protected $collection_key = 'licenses';
  /**
   * Installed application name.
   *
   * @var string
   */
  public $applicationName;
  /**
   * The time when the application was installed.
   *
   * @var string
   */
  public $installTime;
  /**
   * License strings associated with the installed application.
   *
   * @var string[]
   */
  public $licenses;
  /**
   * Source path.
   *
   * @var string
   */
  public $path;
  /**
   * Installed application vendor.
   *
   * @var string
   */
  public $vendor;
  /**
   * Installed application version.
   *
   * @var string
   */
  public $version;

  /**
   * Installed application name.
   *
   * @param string $applicationName
   */
  public function setApplicationName($applicationName)
  {
    $this->applicationName = $applicationName;
  }
  /**
   * @return string
   */
  public function getApplicationName()
  {
    return $this->applicationName;
  }
  /**
   * The time when the application was installed.
   *
   * @param string $installTime
   */
  public function setInstallTime($installTime)
  {
    $this->installTime = $installTime;
  }
  /**
   * @return string
   */
  public function getInstallTime()
  {
    return $this->installTime;
  }
  /**
   * License strings associated with the installed application.
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
   * Source path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Installed application vendor.
   *
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
  /**
   * Installed application version.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestInstalledApplication::class, 'Google_Service_MigrationCenterAPI_GuestInstalledApplication');
