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

class ErrorBudget extends \Google\Model
{
  /**
   * Optional. The maximum number of failed units allowed in a location without
   * pausing the rollout.
   *
   * @var int
   */
  public $allowedCount;
  /**
   * Optional. The maximum percentage of units allowed to fail (0, 100] within a
   * location without pausing the rollout.
   *
   * @var int
   */
  public $allowedPercentage;

  /**
   * Optional. The maximum number of failed units allowed in a location without
   * pausing the rollout.
   *
   * @param int $allowedCount
   */
  public function setAllowedCount($allowedCount)
  {
    $this->allowedCount = $allowedCount;
  }
  /**
   * @return int
   */
  public function getAllowedCount()
  {
    return $this->allowedCount;
  }
  /**
   * Optional. The maximum percentage of units allowed to fail (0, 100] within a
   * location without pausing the rollout.
   *
   * @param int $allowedPercentage
   */
  public function setAllowedPercentage($allowedPercentage)
  {
    $this->allowedPercentage = $allowedPercentage;
  }
  /**
   * @return int
   */
  public function getAllowedPercentage()
  {
    return $this->allowedPercentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorBudget::class, 'Google_Service_SaaSServiceManagement_ErrorBudget');
