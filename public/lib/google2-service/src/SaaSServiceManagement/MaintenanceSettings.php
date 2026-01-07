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

namespace Google\Service\SaaSServiceManagement;

class MaintenanceSettings extends \Google\Model
{
  /**
   * Optional. If present, it fixes the release on the unit until the given
   * time; i.e. changes to the release field will be rejected. Rollouts should
   * and will also respect this by not requesting an upgrade in the first place.
   *
   * @var string
   */
  public $pinnedUntilTime;

  /**
   * Optional. If present, it fixes the release on the unit until the given
   * time; i.e. changes to the release field will be rejected. Rollouts should
   * and will also respect this by not requesting an upgrade in the first place.
   *
   * @param string $pinnedUntilTime
   */
  public function setPinnedUntilTime($pinnedUntilTime)
  {
    $this->pinnedUntilTime = $pinnedUntilTime;
  }
  /**
   * @return string
   */
  public function getPinnedUntilTime()
  {
    return $this->pinnedUntilTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceSettings::class, 'Google_Service_SaaSServiceManagement_MaintenanceSettings');
