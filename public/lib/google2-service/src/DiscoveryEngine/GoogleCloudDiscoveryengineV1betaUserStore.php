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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaUserStore extends \Google\Model
{
  /**
   * Optional. The default subscription LicenseConfig for the UserStore, if
   * UserStore.enable_license_auto_register is true, new users will
   * automatically register under the default subscription. If default
   * LicenseConfig doesn't have remaining license seats left, new users will not
   * be assigned with license and will be blocked for Vertex AI Search features.
   * This is used if `license_assignment_tier_rules` is not configured.
   *
   * @var string
   */
  public $defaultLicenseConfig;
  /**
   * The display name of the User Store.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Whether to enable license auto update for users in this User
   * Store. If true, users with expired licenses will automatically be updated
   * to use the default license config as long as the default license config has
   * seats left.
   *
   * @var bool
   */
  public $enableExpiredLicenseAutoUpdate;
  /**
   * Optional. Whether to enable license auto register for users in this User
   * Store. If true, new users will automatically register under the default
   * license config as long as the default license config has seats left.
   *
   * @var bool
   */
  public $enableLicenseAutoRegister;
  /**
   * Immutable. The full resource name of the User Store, in the format of
   * `projects/{project}/locations/{location}/userStores/{user_store}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The default subscription LicenseConfig for the UserStore, if
   * UserStore.enable_license_auto_register is true, new users will
   * automatically register under the default subscription. If default
   * LicenseConfig doesn't have remaining license seats left, new users will not
   * be assigned with license and will be blocked for Vertex AI Search features.
   * This is used if `license_assignment_tier_rules` is not configured.
   *
   * @param string $defaultLicenseConfig
   */
  public function setDefaultLicenseConfig($defaultLicenseConfig)
  {
    $this->defaultLicenseConfig = $defaultLicenseConfig;
  }
  /**
   * @return string
   */
  public function getDefaultLicenseConfig()
  {
    return $this->defaultLicenseConfig;
  }
  /**
   * The display name of the User Store.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Whether to enable license auto update for users in this User
   * Store. If true, users with expired licenses will automatically be updated
   * to use the default license config as long as the default license config has
   * seats left.
   *
   * @param bool $enableExpiredLicenseAutoUpdate
   */
  public function setEnableExpiredLicenseAutoUpdate($enableExpiredLicenseAutoUpdate)
  {
    $this->enableExpiredLicenseAutoUpdate = $enableExpiredLicenseAutoUpdate;
  }
  /**
   * @return bool
   */
  public function getEnableExpiredLicenseAutoUpdate()
  {
    return $this->enableExpiredLicenseAutoUpdate;
  }
  /**
   * Optional. Whether to enable license auto register for users in this User
   * Store. If true, new users will automatically register under the default
   * license config as long as the default license config has seats left.
   *
   * @param bool $enableLicenseAutoRegister
   */
  public function setEnableLicenseAutoRegister($enableLicenseAutoRegister)
  {
    $this->enableLicenseAutoRegister = $enableLicenseAutoRegister;
  }
  /**
   * @return bool
   */
  public function getEnableLicenseAutoRegister()
  {
    return $this->enableLicenseAutoRegister;
  }
  /**
   * Immutable. The full resource name of the User Store, in the format of
   * `projects/{project}/locations/{location}/userStores/{user_store}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaUserStore::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaUserStore');
