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

class SparkWrapperObject extends \Google\Model
{
  protected $appSummaryType = AppSummary::class;
  protected $appSummaryDataType = '';
  protected $applicationEnvironmentInfoType = ApplicationEnvironmentInfo::class;
  protected $applicationEnvironmentInfoDataType = '';
  /**
   * Application Id created by Spark.
   *
   * @var string
   */
  public $applicationId;
  protected $applicationInfoType = ApplicationInfo::class;
  protected $applicationInfoDataType = '';
  /**
   * VM Timestamp associated with the data object.
   *
   * @var string
   */
  public $eventTimestamp;
  protected $executorStageSummaryType = ExecutorStageSummary::class;
  protected $executorStageSummaryDataType = '';
  protected $executorSummaryType = ExecutorSummary::class;
  protected $executorSummaryDataType = '';
  protected $jobDataType = JobData::class;
  protected $jobDataDataType = '';
  protected $nativeBuildInfoUiDataType = NativeBuildInfoUiData::class;
  protected $nativeBuildInfoUiDataDataType = '';
  protected $nativeSqlExecutionUiDataType = NativeSqlExecutionUiData::class;
  protected $nativeSqlExecutionUiDataDataType = '';
  protected $poolDataType = PoolData::class;
  protected $poolDataDataType = '';
  protected $processSummaryType = ProcessSummary::class;
  protected $processSummaryDataType = '';
  protected $rddOperationGraphType = RddOperationGraph::class;
  protected $rddOperationGraphDataType = '';
  protected $rddStorageInfoType = RddStorageInfo::class;
  protected $rddStorageInfoDataType = '';
  protected $resourceProfileInfoType = ResourceProfileInfo::class;
  protected $resourceProfileInfoDataType = '';
  protected $sparkPlanGraphType = SparkPlanGraph::class;
  protected $sparkPlanGraphDataType = '';
  protected $speculationStageSummaryType = SpeculationStageSummary::class;
  protected $speculationStageSummaryDataType = '';
  protected $sqlExecutionUiDataType = SqlExecutionUiData::class;
  protected $sqlExecutionUiDataDataType = '';
  protected $stageDataType = StageData::class;
  protected $stageDataDataType = '';
  protected $streamBlockDataType = StreamBlockData::class;
  protected $streamBlockDataDataType = '';
  protected $streamingQueryDataType = StreamingQueryData::class;
  protected $streamingQueryDataDataType = '';
  protected $streamingQueryProgressType = StreamingQueryProgress::class;
  protected $streamingQueryProgressDataType = '';
  protected $taskDataType = TaskData::class;
  protected $taskDataDataType = '';

  /**
   * @param AppSummary $appSummary
   */
  public function setAppSummary(AppSummary $appSummary)
  {
    $this->appSummary = $appSummary;
  }
  /**
   * @return AppSummary
   */
  public function getAppSummary()
  {
    return $this->appSummary;
  }
  /**
   * @param ApplicationEnvironmentInfo $applicationEnvironmentInfo
   */
  public function setApplicationEnvironmentInfo(ApplicationEnvironmentInfo $applicationEnvironmentInfo)
  {
    $this->applicationEnvironmentInfo = $applicationEnvironmentInfo;
  }
  /**
   * @return ApplicationEnvironmentInfo
   */
  public function getApplicationEnvironmentInfo()
  {
    return $this->applicationEnvironmentInfo;
  }
  /**
   * Application Id created by Spark.
   *
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
   * @param ApplicationInfo $applicationInfo
   */
  public function setApplicationInfo(ApplicationInfo $applicationInfo)
  {
    $this->applicationInfo = $applicationInfo;
  }
  /**
   * @return ApplicationInfo
   */
  public function getApplicationInfo()
  {
    return $this->applicationInfo;
  }
  /**
   * VM Timestamp associated with the data object.
   *
   * @param string $eventTimestamp
   */
  public function setEventTimestamp($eventTimestamp)
  {
    $this->eventTimestamp = $eventTimestamp;
  }
  /**
   * @return string
   */
  public function getEventTimestamp()
  {
    return $this->eventTimestamp;
  }
  /**
   * @param ExecutorStageSummary $executorStageSummary
   */
  public function setExecutorStageSummary(ExecutorStageSummary $executorStageSummary)
  {
    $this->executorStageSummary = $executorStageSummary;
  }
  /**
   * @return ExecutorStageSummary
   */
  public function getExecutorStageSummary()
  {
    return $this->executorStageSummary;
  }
  /**
   * @param ExecutorSummary $executorSummary
   */
  public function setExecutorSummary(ExecutorSummary $executorSummary)
  {
    $this->executorSummary = $executorSummary;
  }
  /**
   * @return ExecutorSummary
   */
  public function getExecutorSummary()
  {
    return $this->executorSummary;
  }
  /**
   * @param JobData $jobData
   */
  public function setJobData(JobData $jobData)
  {
    $this->jobData = $jobData;
  }
  /**
   * @return JobData
   */
  public function getJobData()
  {
    return $this->jobData;
  }
  /**
   * Native Build Info
   *
   * @param NativeBuildInfoUiData $nativeBuildInfoUiData
   */
  public function setNativeBuildInfoUiData(NativeBuildInfoUiData $nativeBuildInfoUiData)
  {
    $this->nativeBuildInfoUiData = $nativeBuildInfoUiData;
  }
  /**
   * @return NativeBuildInfoUiData
   */
  public function getNativeBuildInfoUiData()
  {
    return $this->nativeBuildInfoUiData;
  }
  /**
   * Native SQL Execution Info
   *
   * @param NativeSqlExecutionUiData $nativeSqlExecutionUiData
   */
  public function setNativeSqlExecutionUiData(NativeSqlExecutionUiData $nativeSqlExecutionUiData)
  {
    $this->nativeSqlExecutionUiData = $nativeSqlExecutionUiData;
  }
  /**
   * @return NativeSqlExecutionUiData
   */
  public function getNativeSqlExecutionUiData()
  {
    return $this->nativeSqlExecutionUiData;
  }
  /**
   * @param PoolData $poolData
   */
  public function setPoolData(PoolData $poolData)
  {
    $this->poolData = $poolData;
  }
  /**
   * @return PoolData
   */
  public function getPoolData()
  {
    return $this->poolData;
  }
  /**
   * @param ProcessSummary $processSummary
   */
  public function setProcessSummary(ProcessSummary $processSummary)
  {
    $this->processSummary = $processSummary;
  }
  /**
   * @return ProcessSummary
   */
  public function getProcessSummary()
  {
    return $this->processSummary;
  }
  /**
   * @param RddOperationGraph $rddOperationGraph
   */
  public function setRddOperationGraph(RddOperationGraph $rddOperationGraph)
  {
    $this->rddOperationGraph = $rddOperationGraph;
  }
  /**
   * @return RddOperationGraph
   */
  public function getRddOperationGraph()
  {
    return $this->rddOperationGraph;
  }
  /**
   * @param RddStorageInfo $rddStorageInfo
   */
  public function setRddStorageInfo(RddStorageInfo $rddStorageInfo)
  {
    $this->rddStorageInfo = $rddStorageInfo;
  }
  /**
   * @return RddStorageInfo
   */
  public function getRddStorageInfo()
  {
    return $this->rddStorageInfo;
  }
  /**
   * @param ResourceProfileInfo $resourceProfileInfo
   */
  public function setResourceProfileInfo(ResourceProfileInfo $resourceProfileInfo)
  {
    $this->resourceProfileInfo = $resourceProfileInfo;
  }
  /**
   * @return ResourceProfileInfo
   */
  public function getResourceProfileInfo()
  {
    return $this->resourceProfileInfo;
  }
  /**
   * @param SparkPlanGraph $sparkPlanGraph
   */
  public function setSparkPlanGraph(SparkPlanGraph $sparkPlanGraph)
  {
    $this->sparkPlanGraph = $sparkPlanGraph;
  }
  /**
   * @return SparkPlanGraph
   */
  public function getSparkPlanGraph()
  {
    return $this->sparkPlanGraph;
  }
  /**
   * @param SpeculationStageSummary $speculationStageSummary
   */
  public function setSpeculationStageSummary(SpeculationStageSummary $speculationStageSummary)
  {
    $this->speculationStageSummary = $speculationStageSummary;
  }
  /**
   * @return SpeculationStageSummary
   */
  public function getSpeculationStageSummary()
  {
    return $this->speculationStageSummary;
  }
  /**
   * @param SqlExecutionUiData $sqlExecutionUiData
   */
  public function setSqlExecutionUiData(SqlExecutionUiData $sqlExecutionUiData)
  {
    $this->sqlExecutionUiData = $sqlExecutionUiData;
  }
  /**
   * @return SqlExecutionUiData
   */
  public function getSqlExecutionUiData()
  {
    return $this->sqlExecutionUiData;
  }
  /**
   * @param StageData $stageData
   */
  public function setStageData(StageData $stageData)
  {
    $this->stageData = $stageData;
  }
  /**
   * @return StageData
   */
  public function getStageData()
  {
    return $this->stageData;
  }
  /**
   * @param StreamBlockData $streamBlockData
   */
  public function setStreamBlockData(StreamBlockData $streamBlockData)
  {
    $this->streamBlockData = $streamBlockData;
  }
  /**
   * @return StreamBlockData
   */
  public function getStreamBlockData()
  {
    return $this->streamBlockData;
  }
  /**
   * @param StreamingQueryData $streamingQueryData
   */
  public function setStreamingQueryData(StreamingQueryData $streamingQueryData)
  {
    $this->streamingQueryData = $streamingQueryData;
  }
  /**
   * @return StreamingQueryData
   */
  public function getStreamingQueryData()
  {
    return $this->streamingQueryData;
  }
  /**
   * @param StreamingQueryProgress $streamingQueryProgress
   */
  public function setStreamingQueryProgress(StreamingQueryProgress $streamingQueryProgress)
  {
    $this->streamingQueryProgress = $streamingQueryProgress;
  }
  /**
   * @return StreamingQueryProgress
   */
  public function getStreamingQueryProgress()
  {
    return $this->streamingQueryProgress;
  }
  /**
   * @param TaskData $taskData
   */
  public function setTaskData(TaskData $taskData)
  {
    $this->taskData = $taskData;
  }
  /**
   * @return TaskData
   */
  public function getTaskData()
  {
    return $this->taskData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SparkWrapperObject::class, 'Google_Service_Dataproc_SparkWrapperObject');
