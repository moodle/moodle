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

class SpeculationStageSummary extends \Google\Model
{
  /**
   * @var int
   */
  public $numActiveTasks;
  /**
   * @var int
   */
  public $numCompletedTasks;
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
   * @param int $numActiveTasks
   */
  public function setNumActiveTasks($numActiveTasks)
  {
    $this->numActiveTasks = $numActiveTasks;
  }
  /**
   * @return int
   */
  public function getNumActiveTasks()
  {
    return $this->numActiveTasks;
  }
  /**
   * @param int $numCompletedTasks
   */
  public function setNumCompletedTasks($numCompletedTasks)
  {
    $this->numCompletedTasks = $numCompletedTasks;
  }
  /**
   * @return int
   */
  public function getNumCompletedTasks()
  {
    return $this->numCompletedTasks;
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
class_alias(SpeculationStageSummary::class, 'Google_Service_Dataproc_SpeculationStageSummary');
