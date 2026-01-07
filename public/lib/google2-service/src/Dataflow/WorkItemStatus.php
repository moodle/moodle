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

class WorkItemStatus extends \Google\Collection
{
  protected $collection_key = 'metricUpdates';
  /**
   * True if the WorkItem was completed (successfully or unsuccessfully).
   *
   * @var bool
   */
  public $completed;
  protected $counterUpdatesType = CounterUpdate::class;
  protected $counterUpdatesDataType = 'array';
  protected $dynamicSourceSplitType = DynamicSourceSplit::class;
  protected $dynamicSourceSplitDataType = '';
  protected $errorsType = Status::class;
  protected $errorsDataType = 'array';
  protected $metricUpdatesType = MetricUpdate::class;
  protected $metricUpdatesDataType = 'array';
  protected $progressType = ApproximateProgress::class;
  protected $progressDataType = '';
  /**
   * The report index. When a WorkItem is leased, the lease will contain an
   * initial report index. When a WorkItem's status is reported to the system,
   * the report should be sent with that report index, and the response will
   * contain the index the worker should use for the next report. Reports
   * received with unexpected index values will be rejected by the service. In
   * order to preserve idempotency, the worker should not alter the contents of
   * a report, even if the worker must submit the same report multiple times
   * before getting back a response. The worker should not submit a subsequent
   * report until the response for the previous report had been received from
   * the service.
   *
   * @var string
   */
  public $reportIndex;
  protected $reportedProgressType = ApproximateReportedProgress::class;
  protected $reportedProgressDataType = '';
  /**
   * Amount of time the worker requests for its lease.
   *
   * @var string
   */
  public $requestedLeaseDuration;
  protected $sourceForkType = SourceFork::class;
  protected $sourceForkDataType = '';
  protected $sourceOperationResponseType = SourceOperationResponse::class;
  protected $sourceOperationResponseDataType = '';
  protected $stopPositionType = Position::class;
  protected $stopPositionDataType = '';
  /**
   * Total time the worker spent being throttled by external systems.
   *
   * @var 
   */
  public $totalThrottlerWaitTimeSeconds;
  /**
   * Identifies the WorkItem.
   *
   * @var string
   */
  public $workItemId;

  /**
   * True if the WorkItem was completed (successfully or unsuccessfully).
   *
   * @param bool $completed
   */
  public function setCompleted($completed)
  {
    $this->completed = $completed;
  }
  /**
   * @return bool
   */
  public function getCompleted()
  {
    return $this->completed;
  }
  /**
   * Worker output counters for this WorkItem.
   *
   * @param CounterUpdate[] $counterUpdates
   */
  public function setCounterUpdates($counterUpdates)
  {
    $this->counterUpdates = $counterUpdates;
  }
  /**
   * @return CounterUpdate[]
   */
  public function getCounterUpdates()
  {
    return $this->counterUpdates;
  }
  /**
   * See documentation of stop_position.
   *
   * @param DynamicSourceSplit $dynamicSourceSplit
   */
  public function setDynamicSourceSplit(DynamicSourceSplit $dynamicSourceSplit)
  {
    $this->dynamicSourceSplit = $dynamicSourceSplit;
  }
  /**
   * @return DynamicSourceSplit
   */
  public function getDynamicSourceSplit()
  {
    return $this->dynamicSourceSplit;
  }
  /**
   * Specifies errors which occurred during processing. If errors are provided,
   * and completed = true, then the WorkItem is considered to have failed.
   *
   * @param Status[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Status[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * DEPRECATED in favor of counter_updates.
   *
   * @deprecated
   * @param MetricUpdate[] $metricUpdates
   */
  public function setMetricUpdates($metricUpdates)
  {
    $this->metricUpdates = $metricUpdates;
  }
  /**
   * @deprecated
   * @return MetricUpdate[]
   */
  public function getMetricUpdates()
  {
    return $this->metricUpdates;
  }
  /**
   * DEPRECATED in favor of reported_progress.
   *
   * @deprecated
   * @param ApproximateProgress $progress
   */
  public function setProgress(ApproximateProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @deprecated
   * @return ApproximateProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * The report index. When a WorkItem is leased, the lease will contain an
   * initial report index. When a WorkItem's status is reported to the system,
   * the report should be sent with that report index, and the response will
   * contain the index the worker should use for the next report. Reports
   * received with unexpected index values will be rejected by the service. In
   * order to preserve idempotency, the worker should not alter the contents of
   * a report, even if the worker must submit the same report multiple times
   * before getting back a response. The worker should not submit a subsequent
   * report until the response for the previous report had been received from
   * the service.
   *
   * @param string $reportIndex
   */
  public function setReportIndex($reportIndex)
  {
    $this->reportIndex = $reportIndex;
  }
  /**
   * @return string
   */
  public function getReportIndex()
  {
    return $this->reportIndex;
  }
  /**
   * The worker's progress through this WorkItem.
   *
   * @param ApproximateReportedProgress $reportedProgress
   */
  public function setReportedProgress(ApproximateReportedProgress $reportedProgress)
  {
    $this->reportedProgress = $reportedProgress;
  }
  /**
   * @return ApproximateReportedProgress
   */
  public function getReportedProgress()
  {
    return $this->reportedProgress;
  }
  /**
   * Amount of time the worker requests for its lease.
   *
   * @param string $requestedLeaseDuration
   */
  public function setRequestedLeaseDuration($requestedLeaseDuration)
  {
    $this->requestedLeaseDuration = $requestedLeaseDuration;
  }
  /**
   * @return string
   */
  public function getRequestedLeaseDuration()
  {
    return $this->requestedLeaseDuration;
  }
  /**
   * DEPRECATED in favor of dynamic_source_split.
   *
   * @deprecated
   * @param SourceFork $sourceFork
   */
  public function setSourceFork(SourceFork $sourceFork)
  {
    $this->sourceFork = $sourceFork;
  }
  /**
   * @deprecated
   * @return SourceFork
   */
  public function getSourceFork()
  {
    return $this->sourceFork;
  }
  /**
   * If the work item represented a SourceOperationRequest, and the work is
   * completed, contains the result of the operation.
   *
   * @param SourceOperationResponse $sourceOperationResponse
   */
  public function setSourceOperationResponse(SourceOperationResponse $sourceOperationResponse)
  {
    $this->sourceOperationResponse = $sourceOperationResponse;
  }
  /**
   * @return SourceOperationResponse
   */
  public function getSourceOperationResponse()
  {
    return $this->sourceOperationResponse;
  }
  /**
   * A worker may split an active map task in two parts, "primary" and
   * "residual", continuing to process the primary part and returning the
   * residual part into the pool of available work. This event is called a
   * "dynamic split" and is critical to the dynamic work rebalancing feature.
   * The two obtained sub-tasks are called "parts" of the split. The parts, if
   * concatenated, must represent the same input as would be read by the current
   * task if the split did not happen. The exact way in which the original task
   * is decomposed into the two parts is specified either as a position
   * demarcating them (stop_position), or explicitly as two DerivedSources, if
   * this task consumes a user-defined source type (dynamic_source_split). The
   * "current" task is adjusted as a result of the split: after a task with
   * range [A, B) sends a stop_position update at C, its range is considered to
   * be [A, C), e.g.: * Progress should be interpreted relative to the new
   * range, e.g. "75% completed" means "75% of [A, C) completed" * The worker
   * should interpret proposed_stop_position relative to the new range, e.g.
   * "split at 68%" should be interpreted as "split at 68% of [A, C)". * If the
   * worker chooses to split again using stop_position, only stop_positions in
   * [A, C) will be accepted. * Etc. dynamic_source_split has similar semantics:
   * e.g., if a task with source S splits using dynamic_source_split into {P, R}
   * (where P and R must be together equivalent to S), then subsequent progress
   * and proposed_stop_position should be interpreted relative to P, and in a
   * potential subsequent dynamic_source_split into {P', R'}, P' and R' must be
   * together equivalent to P, etc.
   *
   * @param Position $stopPosition
   */
  public function setStopPosition(Position $stopPosition)
  {
    $this->stopPosition = $stopPosition;
  }
  /**
   * @return Position
   */
  public function getStopPosition()
  {
    return $this->stopPosition;
  }
  public function setTotalThrottlerWaitTimeSeconds($totalThrottlerWaitTimeSeconds)
  {
    $this->totalThrottlerWaitTimeSeconds = $totalThrottlerWaitTimeSeconds;
  }
  public function getTotalThrottlerWaitTimeSeconds()
  {
    return $this->totalThrottlerWaitTimeSeconds;
  }
  /**
   * Identifies the WorkItem.
   *
   * @param string $workItemId
   */
  public function setWorkItemId($workItemId)
  {
    $this->workItemId = $workItemId;
  }
  /**
   * @return string
   */
  public function getWorkItemId()
  {
    return $this->workItemId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkItemStatus::class, 'Google_Service_Dataflow_WorkItemStatus');
