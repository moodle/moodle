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

class StageAttemptTasksSummary extends \Google\Model
{
  /**
   * @var string
   */
  public $applicationId;
  /**
   * @var int
   */
  public $numFailedTasks;
  /**
   * @var int
   */
  public $numKilledTasks;
  /**
   * @var int
   */
  public $numPendingTasks;
  /**
   * @var int
   */
  public $numRunningTasks;
  /**
   * @var int
   */
  public $numSuccessTasks;
  /**
   * @var int
   */
  public $numTasks;
  /**
   * @var int
   */
  public $stageAttemptId;
  /**
   * @var string
   */
  public $stageId;

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
   * @param int $numFailedTasks
   */
  public function setNumFailedTasks($numFailedTasks)
  {
    $this->numFailedTasks = $numFailedTasks;
  }
  /**
   * @return int
   */
  public function getNumFailedTasks()
  {
    return $this->numFailedTasks;
  }
  /**
   * @param int $numKilledTasks
   */
  public function setNumKilledTasks($numKilledTasks)
  {
    $this->numKilledTasks = $numKilledTasks;
  }
  /**
   * @return int
   */
  public function getNumKilledTasks()
  {
    return $this->numKilledTasks;
  }
  /**
   * @param int $numPendingTasks
   */
  public function setNumPendingTasks($numPendingTasks)
  {
    $this->numPendingTasks = $numPendingTasks;
  }
  /**
   * @return int
   */
  public function getNumPendingTasks()
  {
    return $this->numPendingTasks;
  }
  /**
   * @param int $numRunningTasks
   */
  public function setNumRunningTasks($numRunningTasks)
  {
    $this->numRunningTasks = $numRunningTasks;
  }
  /**
   * @return int
   */
  public function getNumRunningTasks()
  {
    return $this->numRunningTasks;
  }
  /**
   * @param int $numSuccessTasks
   */
  public function setNumSuccessTasks($numSuccessTasks)
  {
    $this->numSuccessTasks = $numSuccessTasks;
  }
  /**
   * @return int
   */
  public function getNumSuccessTasks()
  {
    return $this->numSuccessTasks;
  }
  /**
   * @param int $numTasks
   */
  public function setNumTasks($numTasks)
  {
    $this->numTasks = $numTasks;
  }
  /**
   * @return int
   */
  public function getNumTasks()
  {
    return $this->numTasks;
  }
  /**
   * @param int $stageAttemptId
   */
  public function setStageAttemptId($stageAttemptId)
  {
    $this->stageAttemptId = $stageAttemptId;
  }
  /**
   * @return int
   */
  public function getStageAttemptId()
  {
    return $this->stageAttemptId;
  }
  /**
   * @param string $stageId
   */
  public function setStageId($stageId)
  {
    $this->stageId = $stageId;
  }
  /**
   * @return string
   */
  public function getStageId()
  {
    return $this->stageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageAttemptTasksSummary::class, 'Google_Service_Dataproc_StageAttemptTasksSummary');
