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

namespace Google\Service\Container;

class AutoscaledRolloutPolicy extends \Google\Model
{
  /**
   * Optional. Time to wait after cordoning the blue pool before draining the
   * nodes. Defaults to 3 days. The value can be set between 0 and 7 days,
   * inclusive.
   *
   * @var string
   */
  public $waitForDrainDuration;

  /**
   * Optional. Time to wait after cordoning the blue pool before draining the
   * nodes. Defaults to 3 days. The value can be set between 0 and 7 days,
   * inclusive.
   *
   * @param string $waitForDrainDuration
   */
  public function setWaitForDrainDuration($waitForDrainDuration)
  {
    $this->waitForDrainDuration = $waitForDrainDuration;
  }
  /**
   * @return string
   */
  public function getWaitForDrainDuration()
  {
    return $this->waitForDrainDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscaledRolloutPolicy::class, 'Google_Service_Container_AutoscaledRolloutPolicy');
