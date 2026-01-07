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

namespace Google\Service\AccessContextManager;

class ScopedAccessSettings extends \Google\Model
{
  protected $activeSettingsType = AccessSettings::class;
  protected $activeSettingsDataType = '';
  protected $dryRunSettingsType = AccessSettings::class;
  protected $dryRunSettingsDataType = '';
  protected $scopeType = AccessScope::class;
  protected $scopeDataType = '';

  /**
   * Optional. Access settings for this scoped access settings. This field may
   * be empty if dry_run_settings is set.
   *
   * @param AccessSettings $activeSettings
   */
  public function setActiveSettings(AccessSettings $activeSettings)
  {
    $this->activeSettings = $activeSettings;
  }
  /**
   * @return AccessSettings
   */
  public function getActiveSettings()
  {
    return $this->activeSettings;
  }
  /**
   * Optional. Dry-run access settings for this scoped access settings. This
   * field may be empty if active_settings is set.
   *
   * @param AccessSettings $dryRunSettings
   */
  public function setDryRunSettings(AccessSettings $dryRunSettings)
  {
    $this->dryRunSettings = $dryRunSettings;
  }
  /**
   * @return AccessSettings
   */
  public function getDryRunSettings()
  {
    return $this->dryRunSettings;
  }
  /**
   * Optional. Application, etc. to which the access settings will be applied
   * to. Implicitly, this is the scoped access settings key; as such, it must be
   * unique and non-empty.
   *
   * @param AccessScope $scope
   */
  public function setScope(AccessScope $scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return AccessScope
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScopedAccessSettings::class, 'Google_Service_AccessContextManager_ScopedAccessSettings');
