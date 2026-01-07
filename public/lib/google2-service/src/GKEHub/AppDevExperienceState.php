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

namespace Google\Service\GKEHub;

class AppDevExperienceState extends \Google\Model
{
  protected $networkingInstallSucceededType = AppDevExperienceStatus::class;
  protected $networkingInstallSucceededDataType = '';

  /**
   * Status of subcomponent that detects configured Service Mesh resources.
   *
   * @param AppDevExperienceStatus $networkingInstallSucceeded
   */
  public function setNetworkingInstallSucceeded(AppDevExperienceStatus $networkingInstallSucceeded)
  {
    $this->networkingInstallSucceeded = $networkingInstallSucceeded;
  }
  /**
   * @return AppDevExperienceStatus
   */
  public function getNetworkingInstallSucceeded()
  {
    return $this->networkingInstallSucceeded;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppDevExperienceState::class, 'Google_Service_GKEHub_AppDevExperienceState');
