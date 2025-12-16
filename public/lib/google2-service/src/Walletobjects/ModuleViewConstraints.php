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

namespace Google\Service\Walletobjects;

class ModuleViewConstraints extends \Google\Model
{
  protected $displayIntervalType = TimeInterval::class;
  protected $displayIntervalDataType = '';

  /**
   * The period of time that the module will be displayed to users. Can define
   * both a `startTime` and `endTime`. The module is displayed immediately after
   * insertion unless a `startTime` is set. The module is displayed indefinitely
   * if `endTime` is not set.
   *
   * @param TimeInterval $displayInterval
   */
  public function setDisplayInterval(TimeInterval $displayInterval)
  {
    $this->displayInterval = $displayInterval;
  }
  /**
   * @return TimeInterval
   */
  public function getDisplayInterval()
  {
    return $this->displayInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModuleViewConstraints::class, 'Google_Service_Walletobjects_ModuleViewConstraints');
