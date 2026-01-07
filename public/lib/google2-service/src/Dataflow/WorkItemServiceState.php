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

class WorkItemServiceState extends \Google\Collection
{
  protected $collection_key = 'metricShortId';
  protected $completeWorkStatusType = Status::class;
  protected $completeWorkStatusDataType = '';
  /**
   * Other data returned by the service, specific to the particular worker
   * harness.
   *
   * @var array[]
   */
  public $harnessData;
  protected $hotKeyDetectionType = HotKeyDetection::class;
  protected $hotKeyDetectionDataType = '';
  /**
   * Time at which the current lease will expire.
   *
   * @var string
   */
  public $leaseExpireTime;
  protected $metricShortIdType = MetricShortId::class;
  protected $metricShortIdDataType = 'array';
  /**
   * The index value to use for the next report sent by the worker. Note: If the
   * report call fails for whatever reason, the worker should reuse this index
   * for subsequent report attempts.
   *
   * @var string
   */
  public $nextReportIndex;
  /**
   * New recommended reporting interval.
   *
   * @var string
   */
  public $reportStatusInterval;
  protected $splitRequestType = ApproximateSplitRequest::class;
  protected $splitRequestDataType = '';
  protected $suggestedStopPointType = ApproximateProgress::class;
  protected $suggestedStopPointDataType = '';
  protected $suggestedStopPositionType = Position::class;
  protected $suggestedStopPositionDataType = '';

  /**
   * If set, a request to complete the work item with the given status. This
   * will not be set to OK, unless supported by the specific kind of WorkItem.
   * It can be used for the backend to indicate a WorkItem must terminate, e.g.,
   * for aborting work.
   *
   * @param Status $completeWorkStatus
   */
  public function setCompleteWorkStatus(Status $completeWorkStatus)
  {
    $this->completeWorkStatus = $completeWorkStatus;
  }
  /**
   * @return Status
   */
  public function getCompleteWorkStatus()
  {
    return $this->completeWorkStatus;
  }
  /**
   * Other data returned by the service, specific to the particular worker
   * harness.
   *
   * @param array[] $harnessData
   */
  public function setHarnessData($harnessData)
  {
    $this->harnessData = $harnessData;
  }
  /**
   * @return array[]
   */
  public function getHarnessData()
  {
    return $this->harnessData;
  }
  /**
   * A hot key is a symptom of poor data distribution in which there are enough
   * elements mapped to a single key to impact pipeline performance. When
   * present, this field includes metadata associated with any hot key.
   *
   * @param HotKeyDetection $hotKeyDetection
   */
  public function setHotKeyDetection(HotKeyDetection $hotKeyDetection)
  {
    $this->hotKeyDetection = $hotKeyDetection;
  }
  /**
   * @return HotKeyDetection
   */
  public function getHotKeyDetection()
  {
    return $this->hotKeyDetection;
  }
  /**
   * Time at which the current lease will expire.
   *
   * @param string $leaseExpireTime
   */
  public function setLeaseExpireTime($leaseExpireTime)
  {
    $this->leaseExpireTime = $leaseExpireTime;
  }
  /**
   * @return string
   */
  public function getLeaseExpireTime()
  {
    return $this->leaseExpireTime;
  }
  /**
   * The short ids that workers should use in subsequent metric updates. Workers
   * should strive to use short ids whenever possible, but it is ok to request
   * the short_id again if a worker lost track of it (e.g. if the worker is
   * recovering from a crash). NOTE: it is possible that the response may have
   * short ids for a subset of the metrics.
   *
   * @param MetricShortId[] $metricShortId
   */
  public function setMetricShortId($metricShortId)
  {
    $this->metricShortId = $metricShortId;
  }
  /**
   * @return MetricShortId[]
   */
  public function getMetricShortId()
  {
    return $this->metricShortId;
  }
  /**
   * The index value to use for the next report sent by the worker. Note: If the
   * report call fails for whatever reason, the worker should reuse this index
   * for subsequent report attempts.
   *
   * @param string $nextReportIndex
   */
  public function setNextReportIndex($nextReportIndex)
  {
    $this->nextReportIndex = $nextReportIndex;
  }
  /**
   * @return string
   */
  public function getNextReportIndex()
  {
    return $this->nextReportIndex;
  }
  /**
   * New recommended reporting interval.
   *
   * @param string $reportStatusInterval
   */
  public function setReportStatusInterval($reportStatusInterval)
  {
    $this->reportStatusInterval = $reportStatusInterval;
  }
  /**
   * @return string
   */
  public function getReportStatusInterval()
  {
    return $this->reportStatusInterval;
  }
  /**
   * The progress point in the WorkItem where the Dataflow service suggests that
   * the worker truncate the task.
   *
   * @param ApproximateSplitRequest $splitRequest
   */
  public function setSplitRequest(ApproximateSplitRequest $splitRequest)
  {
    $this->splitRequest = $splitRequest;
  }
  /**
   * @return ApproximateSplitRequest
   */
  public function getSplitRequest()
  {
    return $this->splitRequest;
  }
  /**
   * DEPRECATED in favor of split_request.
   *
   * @deprecated
   * @param ApproximateProgress $suggestedStopPoint
   */
  public function setSuggestedStopPoint(ApproximateProgress $suggestedStopPoint)
  {
    $this->suggestedStopPoint = $suggestedStopPoint;
  }
  /**
   * @deprecated
   * @return ApproximateProgress
   */
  public function getSuggestedStopPoint()
  {
    return $this->suggestedStopPoint;
  }
  /**
   * Obsolete, always empty.
   *
   * @deprecated
   * @param Position $suggestedStopPosition
   */
  public function setSuggestedStopPosition(Position $suggestedStopPosition)
  {
    $this->suggestedStopPosition = $suggestedStopPosition;
  }
  /**
   * @deprecated
   * @return Position
   */
  public function getSuggestedStopPosition()
  {
    return $this->suggestedStopPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkItemServiceState::class, 'Google_Service_Dataflow_WorkItemServiceState');
