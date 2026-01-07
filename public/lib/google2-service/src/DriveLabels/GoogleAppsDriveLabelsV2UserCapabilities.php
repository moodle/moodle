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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2UserCapabilities extends \Google\Model
{
  /**
   * Output only. Whether the user is allowed access to the label manager.
   *
   * @var bool
   */
  public $canAccessLabelManager;
  /**
   * Output only. Whether the user is an administrator for the shared labels
   * feature.
   *
   * @var bool
   */
  public $canAdministrateLabels;
  /**
   * Output only. Whether the user is allowed to create admin labels.
   *
   * @var bool
   */
  public $canCreateAdminLabels;
  /**
   * Output only. Whether the user is allowed to create shared labels.
   *
   * @var bool
   */
  public $canCreateSharedLabels;
  /**
   * Output only. Resource name for the user capabilities.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Whether the user is allowed access to the label manager.
   *
   * @param bool $canAccessLabelManager
   */
  public function setCanAccessLabelManager($canAccessLabelManager)
  {
    $this->canAccessLabelManager = $canAccessLabelManager;
  }
  /**
   * @return bool
   */
  public function getCanAccessLabelManager()
  {
    return $this->canAccessLabelManager;
  }
  /**
   * Output only. Whether the user is an administrator for the shared labels
   * feature.
   *
   * @param bool $canAdministrateLabels
   */
  public function setCanAdministrateLabels($canAdministrateLabels)
  {
    $this->canAdministrateLabels = $canAdministrateLabels;
  }
  /**
   * @return bool
   */
  public function getCanAdministrateLabels()
  {
    return $this->canAdministrateLabels;
  }
  /**
   * Output only. Whether the user is allowed to create admin labels.
   *
   * @param bool $canCreateAdminLabels
   */
  public function setCanCreateAdminLabels($canCreateAdminLabels)
  {
    $this->canCreateAdminLabels = $canCreateAdminLabels;
  }
  /**
   * @return bool
   */
  public function getCanCreateAdminLabels()
  {
    return $this->canCreateAdminLabels;
  }
  /**
   * Output only. Whether the user is allowed to create shared labels.
   *
   * @param bool $canCreateSharedLabels
   */
  public function setCanCreateSharedLabels($canCreateSharedLabels)
  {
    $this->canCreateSharedLabels = $canCreateSharedLabels;
  }
  /**
   * @return bool
   */
  public function getCanCreateSharedLabels()
  {
    return $this->canCreateSharedLabels;
  }
  /**
   * Output only. Resource name for the user capabilities.
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
class_alias(GoogleAppsDriveLabelsV2UserCapabilities::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2UserCapabilities');
