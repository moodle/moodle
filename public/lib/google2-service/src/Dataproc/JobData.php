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

class JobData extends \Google\Collection
{
  public const STATUS_JOB_EXECUTION_STATUS_UNSPECIFIED = 'JOB_EXECUTION_STATUS_UNSPECIFIED';
  public const STATUS_JOB_EXECUTION_STATUS_RUNNING = 'JOB_EXECUTION_STATUS_RUNNING';
  public const STATUS_JOB_EXECUTION_STATUS_SUCCEEDED = 'JOB_EXECUTION_STATUS_SUCCEEDED';
  public const STATUS_JOB_EXECUTION_STATUS_FAILED = 'JOB_EXECUTION_STATUS_FAILED';
  public const STATUS_JOB_EXECUTION_STATUS_UNKNOWN = 'JOB_EXECUTION_STATUS_UNKNOWN';
  protected $collection_key = 'stageIds';
  /**
   * @var string
   */
  public $completionTime;
  /**
   * @var string
   */
  public $description;
  /**
   * @var string
   */
  public $jobGroup;
  /**
   * @var string
   */
  public $jobId;
  /**
   * @var int[]
   */
  public $killTasksSummary;
  /**
   * @var string
   */
  public $name;
  /**
   * @var int
   */
  public $numActiveStages;
  /**
   * @var int
   */
  public $numActiveTasks;
  /**
   * @var int
   */
  public $numCompletedIndices;
  /**
   * @var int
   */
  public $numCompletedStages;
  /**
   * @var int
   */
  public $numCompletedTasks;
  /**
   * @var int
   */
  public $numFailedStages;
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
  public $numSkippedStages;
  /**
   * @var int
   */
  public $numSkippedTasks;
  /**
   * @var int
   */
  public $numTasks;
  /**
   * @var int[]
   */
  public $skippedStages;
  /**
   * @var string
   */
  public $sqlExecutionId;
  /**
   * @var string[]
   */
  public $stageIds;
  /**
   * @var string
   */
  public $status;
  /**
   * @var string
   */
  public $submissionTime;

  /**
   * @param string $completionTime
   */
  public function setCompletionTime($completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return string
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param string $jobGroup
   */
  public function setJobGroup($jobGroup)
  {
    $this->jobGroup = $jobGroup;
  }
  /**
   * @return string
   */
  public function getJobGroup()
  {
    return $this->jobGroup;
  }
  /**
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * @param int[] $killTasksSummary
   */
  public function setKillTasksSummary($killTasksSummary)
  {
    $this->killTasksSummary = $killTasksSummary;
  }
  /**
   * @return int[]
   */
  public function getKillTasksSummary()
  {
    return $this->killTasksSummary;
  }
  /**
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
   * @param int $numCompletedIndices
   */
  public function setNumCompletedIndices($numCompletedIndices)
  {
    $this->numCompletedIndices = $numCompletedIndices;
  }
  /**
   * @return int
   */
  public function getNumCompletedIndices()
  {
    return $this->numCompletedIndices;
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
  /**
   * @param int $numSkippedTasks
   */
  public function setNumSkippedTasks($numSkippedTasks)
  {
    $this->numSkippedTasks = $numSkippedTasks;
  }
  /**
   * @return int
   */
  public function getNumSkippedTasks()
  {
    return $this->numSkippedTasks;
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
   * @param int[] $skippedStages
   */
  public function setSkippedStages($skippedStages)
  {
    $this->skippedStages = $skippedStages;
  }
  /**
   * @return int[]
   */
  public function getSkippedStages()
  {
    return $this->skippedStages;
  }
  /**
   * @param string $sqlExecutionId
   */
  public function setSqlExecutionId($sqlExecutionId)
  {
    $this->sqlExecutionId = $sqlExecutionId;
  }
  /**
   * @return string
   */
  public function getSqlExecutionId()
  {
    return $this->sqlExecutionId;
  }
  /**
   * @param string[] $stageIds
   */
  public function setStageIds($stageIds)
  {
    $this->stageIds = $stageIds;
  }
  /**
   * @return string[]
   */
  public function getStageIds()
  {
    return $this->stageIds;
  }
  /**
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param string $submissionTime
   */
  public function setSubmissionTime($submissionTime)
  {
    $this->submissionTime = $submissionTime;
  }
  /**
   * @return string
   */
  public function getSubmissionTime()
  {
    return $this->submissionTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobData::class, 'Google_Service_Dataproc_JobData');
