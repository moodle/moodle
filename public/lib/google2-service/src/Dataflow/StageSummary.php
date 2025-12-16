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

namespace Google\Service\Dataflow;

class StageSummary extends \Google\Collection
{
  /**
   * The component state is unknown or unspecified.
   */
  public const STATE_EXECUTION_STATE_UNKNOWN = 'EXECUTION_STATE_UNKNOWN';
  /**
   * The component is not yet running.
   */
  public const STATE_EXECUTION_STATE_NOT_STARTED = 'EXECUTION_STATE_NOT_STARTED';
  /**
   * The component is currently running.
   */
  public const STATE_EXECUTION_STATE_RUNNING = 'EXECUTION_STATE_RUNNING';
  /**
   * The component succeeded.
   */
  public const STATE_EXECUTION_STATE_SUCCEEDED = 'EXECUTION_STATE_SUCCEEDED';
  /**
   * The component failed.
   */
  public const STATE_EXECUTION_STATE_FAILED = 'EXECUTION_STATE_FAILED';
  /**
   * Execution of the component was cancelled.
   */
  public const STATE_EXECUTION_STATE_CANCELLED = 'EXECUTION_STATE_CANCELLED';
  protected $collection_key = 'metrics';
  /**
   * End time of this stage. If the work item is completed, this is the actual
   * end time of the stage. Otherwise, it is the predicted end time.
   *
   * @var string
   */
  public $endTime;
  protected $metricsType = MetricUpdate::class;
  protected $metricsDataType = 'array';
  protected $progressType = ProgressTimeseries::class;
  protected $progressDataType = '';
  /**
   * ID of this stage
   *
   * @var string
   */
  public $stageId;
  /**
   * Start time of this stage.
   *
   * @var string
   */
  public $startTime;
  /**
   * State of this stage.
   *
   * @var string
   */
  public $state;
  protected $stragglerSummaryType = StragglerSummary::class;
  protected $stragglerSummaryDataType = '';

  /**
   * End time of this stage. If the work item is completed, this is the actual
   * end time of the stage. Otherwise, it is the predicted end time.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Metrics for this stage.
   *
   * @param MetricUpdate[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return MetricUpdate[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Progress for this stage. Only applicable to Batch jobs.
   *
   * @param ProgressTimeseries $progress
   */
  public function setProgress(ProgressTimeseries $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return ProgressTimeseries
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * ID of this stage
   *
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
  /**
   * Start time of this stage.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * State of this stage.
   *
   * Accepted values: EXECUTION_STATE_UNKNOWN, EXECUTION_STATE_NOT_STARTED,
   * EXECUTION_STATE_RUNNING, EXECUTION_STATE_SUCCEEDED, EXECUTION_STATE_FAILED,
   * EXECUTION_STATE_CANCELLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Straggler summary for this stage.
   *
   * @param StragglerSummary $stragglerSummary
   */
  public function setStragglerSummary(StragglerSummary $stragglerSummary)
  {
    $this->stragglerSummary = $stragglerSummary;
  }
  /**
   * @return StragglerSummary
   */
  public function getStragglerSummary()
  {
    return $this->stragglerSummary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageSummary::class, 'Google_Service_Dataflow_StageSummary');
