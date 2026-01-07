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

class MemoryUsageSample extends \Google\Model
{
  /**
   * Percentage of system memory utilized. Must be in the interval [0, 100].
   *
   * @var float
   */
  public $utilizedPercentage;

  /**
   * Percentage of system memory utilized. Must be in the interval [0, 100].
   *
   * @param float $utilizedPercentage
   */
  public function setUtilizedPercentage($utilizedPercentage)
  {
    $this->utilizedPercentage = $utilizedPercentage;
  }
  /**
   * @return float
   */
  public function getUtilizedPercentage()
  {
    return $this->utilizedPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MemoryUsageSample::class, 'Google_Service_MigrationCenterAPI_MemoryUsageSample');
