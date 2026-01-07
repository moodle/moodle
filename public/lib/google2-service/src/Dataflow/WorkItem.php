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

class WorkItem extends \Google\Collection
{
  protected $collection_key = 'packages';
  /**
   * Work item-specific configuration as an opaque blob.
   *
   * @var string
   */
  public $configuration;
  /**
   * Identifies this WorkItem.
   *
   * @var string
   */
  public $id;
  /**
   * The initial index to use when reporting the status of the WorkItem.
   *
   * @var string
   */
  public $initialReportIndex;
  /**
   * Identifies the workflow job this WorkItem belongs to.
   *
   * @var string
   */
  public $jobId;
  /**
   * Time when the lease on this Work will expire.
   *
   * @var string
   */
  public $leaseExpireTime;
  protected $mapTaskType = MapTask::class;
  protected $mapTaskDataType = '';
  protected $packagesType = Package::class;
  protected $packagesDataType = 'array';
  /**
   * Identifies the cloud project this WorkItem belongs to.
   *
   * @var string
   */
  public $projectId;
  /**
   * Recommended reporting interval.
   *
   * @var string
   */
  public $reportStatusInterval;
  protected $seqMapTaskType = SeqMapTask::class;
  protected $seqMapTaskDataType = '';
  protected $shellTaskType = ShellTask::class;
  protected $shellTaskDataType = '';
  protected $sourceOperationTaskType = SourceOperationRequest::class;
  protected $sourceOperationTaskDataType = '';
  protected $streamingComputationTaskType = StreamingComputationTask::class;
  protected $streamingComputationTaskDataType = '';
  protected $streamingConfigTaskType = StreamingConfigTask::class;
  protected $streamingConfigTaskDataType = '';
  protected $streamingSetupTaskType = StreamingSetupTask::class;
  protected $streamingSetupTaskDataType = '';

  /**
   * Work item-specific configuration as an opaque blob.
   *
   * @param string $configuration
   */
  public function setConfiguration($configuration)
  {
    $this->configuration = $configuration;
  }
  /**
   * @return string
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
  /**
   * Identifies this WorkItem.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The initial index to use when reporting the status of the WorkItem.
   *
   * @param string $initialReportIndex
   */
  public function setInitialReportIndex($initialReportIndex)
  {
    $this->initialReportIndex = $initialReportIndex;
  }
  /**
   * @return string
   */
  public function getInitialReportIndex()
  {
    return $this->initialReportIndex;
  }
  /**
   * Identifies the workflow job this WorkItem belongs to.
   *
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
   * Time when the lease on this Work will expire.
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
   * Additional information for MapTask WorkItems.
   *
   * @param MapTask $mapTask
   */
  public function setMapTask(MapTask $mapTask)
  {
    $this->mapTask = $mapTask;
  }
  /**
   * @return MapTask
   */
  public function getMapTask()
  {
    return $this->mapTask;
  }
  /**
   * Any required packages that need to be fetched in order to execute this
   * WorkItem.
   *
   * @param Package[] $packages
   */
  public function setPackages($packages)
  {
    $this->packages = $packages;
  }
  /**
   * @return Package[]
   */
  public function getPackages()
  {
    return $this->packages;
  }
  /**
   * Identifies the cloud project this WorkItem belongs to.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Recommended reporting interval.
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
   * Additional information for SeqMapTask WorkItems.
   *
   * @param SeqMapTask $seqMapTask
   */
  public function setSeqMapTask(SeqMapTask $seqMapTask)
  {
    $this->seqMapTask = $seqMapTask;
  }
  /**
   * @return SeqMapTask
   */
  public function getSeqMapTask()
  {
    return $this->seqMapTask;
  }
  /**
   * Additional information for ShellTask WorkItems.
   *
   * @param ShellTask $shellTask
   */
  public function setShellTask(ShellTask $shellTask)
  {
    $this->shellTask = $shellTask;
  }
  /**
   * @return ShellTask
   */
  public function getShellTask()
  {
    return $this->shellTask;
  }
  /**
   * Additional information for source operation WorkItems.
   *
   * @param SourceOperationRequest $sourceOperationTask
   */
  public function setSourceOperationTask(SourceOperationRequest $sourceOperationTask)
  {
    $this->sourceOperationTask = $sourceOperationTask;
  }
  /**
   * @return SourceOperationRequest
   */
  public function getSourceOperationTask()
  {
    return $this->sourceOperationTask;
  }
  /**
   * Additional information for StreamingComputationTask WorkItems.
   *
   * @param StreamingComputationTask $streamingComputationTask
   */
  public function setStreamingComputationTask(StreamingComputationTask $streamingComputationTask)
  {
    $this->streamingComputationTask = $streamingComputationTask;
  }
  /**
   * @return StreamingComputationTask
   */
  public function getStreamingComputationTask()
  {
    return $this->streamingComputationTask;
  }
  /**
   * Additional information for StreamingConfigTask WorkItems.
   *
   * @param StreamingConfigTask $streamingConfigTask
   */
  public function setStreamingConfigTask(StreamingConfigTask $streamingConfigTask)
  {
    $this->streamingConfigTask = $streamingConfigTask;
  }
  /**
   * @return StreamingConfigTask
   */
  public function getStreamingConfigTask()
  {
    return $this->streamingConfigTask;
  }
  /**
   * Additional information for StreamingSetupTask WorkItems.
   *
   * @param StreamingSetupTask $streamingSetupTask
   */
  public function setStreamingSetupTask(StreamingSetupTask $streamingSetupTask)
  {
    $this->streamingSetupTask = $streamingSetupTask;
  }
  /**
   * @return StreamingSetupTask
   */
  public function getStreamingSetupTask()
  {
    return $this->streamingSetupTask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkItem::class, 'Google_Service_Dataflow_WorkItem');
