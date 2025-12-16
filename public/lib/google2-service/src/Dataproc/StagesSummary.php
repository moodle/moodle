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

namespace Google\Service\Dataproc;

class StagesSummary extends \Google\Model
{
  /**
   * @var string
   */
  public $applicationId;
  /**
   * @var int
   */
  public $numActiveStages;
  /**
   * @var int
   */
  public $numCompletedStages;
  /**
   * @var int
   */
  public $numFailedStages;
  /**
   * @var int
   */
  public $numPendingStages;
  /**
   * @var int
   */
  public $numSkippedStages;

  /**
   * @param string $applicationId
   */
  public function setApplicationId($applicationId)
  {
    $this->applicationId = $applicationId;
  }
  /**
   * @return string
   */
  public function getApplicationId()
  {
    return $this->applicationId;
  }
  /**
   * @param int $numActiveStages
   */
  public function setNumActiveStages($numActiveStages)
  {
    $this->numActiveStages = $numActiveStages;
  }
  /**
   * @return int
   */
  public function getNumActiveStages()
  {
    return $this->numActiveStages;
  }
  /**
   * @param int $numCompletedStages
   */
  public function setNumCompletedStages($numCompletedStages)
  {
    $this->numCompletedStages = $numCompletedStages;
  }
  /**
   * @return int
   */
  public function getNumCompletedStages()
  {
    return $this->numCompletedStages;
  }
  /**
   * @param int $numFailedStages
   */
  public function setNumFailedStages($numFailedStages)
  {
    $this->numFailedStages = $numFailedStages;
  }
  /**
   * @return int
   */
  public function getNumFailedStages()
  {
    return $this->numFailedStages;
  }
  /**
   * @param int $numPendingStages
   */
  public function setNumPendingStages($numPendingStages)
  {
    $this->numPendingStages = $numPendingStages;
  }
  /**
   * @return int
   */
  public function getNumPendingStages()
  {
    return $this->numPendingStages;
  }
  /**
   * @param int $numSkippedStages
   */
  public function setNumSkippedStages($numSkippedStages)
  {
    $this->numSkippedStages = $numSkippedStages;
  }
  /**
   * @return int
   */
  public function getNumSkippedStages()
  {
    return $this->numSkippedStages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StagesSummary::class, 'Google_Service_Dataproc_StagesSummary');
