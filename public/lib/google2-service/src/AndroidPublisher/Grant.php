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

namespace Google\Service\AndroidPublisher;

class Grant extends \Google\Collection
{
  protected $collection_key = 'appLevelPermissions';
  /**
   * The permissions granted to the user for this app.
   *
   * @var string[]
   */
  public $appLevelPermissions;
  /**
   * Required. Resource name for this grant, following the pattern
   * "developers/{developer}/users/{email}/grants/{package_name}". If this grant
   * is for a draft app, the app ID will be used in this resource name instead
   * of the package name.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The package name of the app. This will be empty for draft apps.
   *
   * @var string
   */
  public $packageName;

  /**
   * The permissions granted to the user for this app.
   *
   * @param string[] $appLevelPermissions
   */
  public function setAppLevelPermissions($appLevelPermissions)
  {
    $this->appLevelPermissions = $appLevelPermissions;
  }
  /**
   * @return string[]
   */
  public function getAppLevelPermissions()
  {
    return $this->appLevelPermissions;
  }
  /**
   * Required. Resource name for this grant, following the pattern
   * "developers/{developer}/users/{email}/grants/{package_name}". If this grant
   * is for a draft app, the app ID will be used in this resource name instead
   * of the package name.
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
   * Immutable. The package name of the app. This will be empty for draft apps.
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
class_alias(Grant::class, 'Google_Service_AndroidPublisher_Grant');
