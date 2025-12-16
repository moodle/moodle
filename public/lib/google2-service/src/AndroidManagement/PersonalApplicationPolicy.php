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

class PersonalApplicationPolicy extends \Google\Model
{
  /**
   * Unspecified. Defaults to AVAILABLE.
   */
  public const INSTALL_TYPE_INSTALL_TYPE_UNSPECIFIED = 'INSTALL_TYPE_UNSPECIFIED';
  /**
   * The app is blocked and can't be installed in the personal profile. If the
   * app was previously installed in the device, it will be uninstalled.
   */
  public const INSTALL_TYPE_BLOCKED = 'BLOCKED';
  /**
   * The app is available to install in the personal profile.
   */
  public const INSTALL_TYPE_AVAILABLE = 'AVAILABLE';
  /**
   * The type of installation to perform.
   *
   * @var string
   */
  public $installType;
  /**
   * The package name of the application.
   *
   * @var string
   */
  public $packageName;

  /**
   * The type of installation to perform.
   *
   * Accepted values: INSTALL_TYPE_UNSPECIFIED, BLOCKED, AVAILABLE
   *
   * @param self::INSTALL_TYPE_* $installType
   */
  public function setInstallType($installType)
  {
    $this->installType = $installType;
  }
  /**
   * @return self::INSTALL_TYPE_*
   */
  public function getInstallType()
  {
    return $this->installType;
  }
  /**
   * The package name of the application.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonalApplicationPolicy::class, 'Google_Service_AndroidManagement_PersonalApplicationPolicy');
