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

namespace Google\Service\CloudBuild;

class PipelineRun extends \Google\Collection
{
  /**
   * Default enum type; should not be used.
   */
  public const PIPELINE_RUN_STATUS_PIPELINE_RUN_STATUS_UNSPECIFIED = 'PIPELINE_RUN_STATUS_UNSPECIFIED';
  /**
   * Cancelled status.
   */
  public const PIPELINE_RUN_STATUS_PIPELINE_RUN_CANCELLED = 'PIPELINE_RUN_CANCELLED';
  protected $collection_key = 'workspaces';
  /**
   * User annotations. See https://google.aip.dev/128#annotations
   *
   * @var string[]
   */
  public $annotations;
  protected $childReferencesType = ChildStatusReference::class;
  protected $childReferencesDataType = 'array';
  /**
   * Output only. Time the pipeline completed.
   *
   * @var string
   */
  public $completionTime;
  protected $conditionsType = GoogleDevtoolsCloudbuildV2Condition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. Time at which the request to create the `PipelineRun` was
   * received.
   *
   * @var string
   */
  public $createTime;
  /**
   * Needed for declarative-friendly resources.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. FinallyStartTime is when all non-finally tasks have been
   * completed and only finally tasks are being executed. +optional
   *
   * @var string
   */
  public $finallyStartTime;
  /**
   * Output only. GCB default params.
   *
   * @var string[]
   */
  public $gcbParams;
  /**
   * Output only. The `PipelineRun` name with format
   * `projects/{project}/locations/{location}/pipelineRuns/{pipeline_run}`
   *
   * @var string
   */
  public $name;
  protected $paramsType = Param::class;
  protected $paramsDataType = 'array';
  protected $pipelineRefType = PipelineRef::class;
  protected $pipelineRefDataType = '';
  /**
   * Pipelinerun status the user can provide. Used for cancellation.
   *
   * @var string
   */
  public $pipelineRunStatus;
  protected $pipelineSpecType = PipelineSpec::class;
  protected $pipelineSpecDataType = '';
  /**
   * Output only. Inline pipelineSpec yaml string, used by workflow run
   * requests.
   *
   * @var string
   */
  public $pipelineSpecYaml;
  protected $provenanceType = Provenance::class;
  protected $provenanceDataType = '';
  /**
   * Output only. The `Record` of this `PipelineRun`. Format: `projects/{project
   * }/locations/{location}/results/{result_id}/records/{record_id}`
   *
   * @var string
   */
  public $record;
  protected $resolvedPipelineSpecType = PipelineSpec::class;
  protected $resolvedPipelineSpecDataType = '';
  protected $resultsType = PipelineRunResult::class;
  protected $resultsDataType = 'array';
  protected $securityType = Security::class;
  protected $securityDataType = '';
  /**
   * Service account used in the Pipeline. Deprecated; please use
   * security.service_account instead.
   *
   * @deprecated
   * @var string
   */
  public $serviceAccount;
  protected $skippedTasksType = SkippedTask::class;
  protected $skippedTasksDataType = 'array';
  /**
   * Output only. Time the pipeline is actually started.
   *
   * @var string
   */
  public $startTime;
  protected $timeoutsType = TimeoutFields::class;
  protected $timeoutsDataType = '';
  /**
   * Output only. A unique identifier for the `PipelineRun`.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the request to update the `PipelineRun` was
   * received.
   *
   * @var string
   */
  public $updateTime;
  protected $workerType = Worker::class;
  protected $workerDataType = '';
  /**
   * Output only. The WorkerPool used to run this PipelineRun.
   *
   * @var string
   */
  public $workerPool;
  /**
   * Output only. The Workflow used to create this PipelineRun.
   *
   * @var string
   */
  public $workflow;
  protected $workspacesType = WorkspaceBinding::class;
  protected $workspacesDataType = 'array';

  /**
   * User annotations. See https://google.aip.dev/128#annotations
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. List of TaskRun and Run names and PipelineTask names for
   * children of this PipelineRun.
   *
   * @param ChildStatusReference[] $childReferences
   */
  public function setChildReferences($childReferences)
  {
    $this->childReferences = $childReferences;
  }
  /**
   * @return ChildStatusReference[]
   */
  public function getChildReferences()
  {
    return $this->childReferences;
  }
  /**
   * Output only. Time the pipeline completed.
   *
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
   * Output only. Kubernetes Conditions convention for PipelineRun status and
   * error.
   *
   * @param GoogleDevtoolsCloudbuildV2Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV2Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Output only. Time at which the request to create the `PipelineRun` was
   * received.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Needed for declarative-friendly resources.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. FinallyStartTime is when all non-finally tasks have been
   * completed and only finally tasks are being executed. +optional
   *
   * @param string $finallyStartTime
   */
  public function setFinallyStartTime($finallyStartTime)
  {
    $this->finallyStartTime = $finallyStartTime;
  }
  /**
   * @return string
   */
  public function getFinallyStartTime()
  {
    return $this->finallyStartTime;
  }
  /**
   * Output only. GCB default params.
   *
   * @param string[] $gcbParams
   */
  public function setGcbParams($gcbParams)
  {
    $this->gcbParams = $gcbParams;
  }
  /**
   * @return string[]
   */
  public function getGcbParams()
  {
    return $this->gcbParams;
  }
  /**
   * Output only. The `PipelineRun` name with format
   * `projects/{project}/locations/{location}/pipelineRuns/{pipeline_run}`
   *
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
   * Params is a list of parameter names and values.
   *
   * @param Param[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return Param[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * PipelineRef refer to a specific instance of a Pipeline.
   *
   * @param PipelineRef $pipelineRef
   */
  public function setPipelineRef(PipelineRef $pipelineRef)
  {
    $this->pipelineRef = $pipelineRef;
  }
  /**
   * @return PipelineRef
   */
  public function getPipelineRef()
  {
    return $this->pipelineRef;
  }
  /**
   * Pipelinerun status the user can provide. Used for cancellation.
   *
   * Accepted values: PIPELINE_RUN_STATUS_UNSPECIFIED, PIPELINE_RUN_CANCELLED
   *
   * @param self::PIPELINE_RUN_STATUS_* $pipelineRunStatus
   */
  public function setPipelineRunStatus($pipelineRunStatus)
  {
    $this->pipelineRunStatus = $pipelineRunStatus;
  }
  /**
   * @return self::PIPELINE_RUN_STATUS_*
   */
  public function getPipelineRunStatus()
  {
    return $this->pipelineRunStatus;
  }
  /**
   * PipelineSpec defines the desired state of Pipeline.
   *
   * @param PipelineSpec $pipelineSpec
   */
  public function setPipelineSpec(PipelineSpec $pipelineSpec)
  {
    $this->pipelineSpec = $pipelineSpec;
  }
  /**
   * @return PipelineSpec
   */
  public function getPipelineSpec()
  {
    return $this->pipelineSpec;
  }
  /**
   * Output only. Inline pipelineSpec yaml string, used by workflow run
   * requests.
   *
   * @param string $pipelineSpecYaml
   */
  public function setPipelineSpecYaml($pipelineSpecYaml)
  {
    $this->pipelineSpecYaml = $pipelineSpecYaml;
  }
  /**
   * @return string
   */
  public function getPipelineSpecYaml()
  {
    return $this->pipelineSpecYaml;
  }
  /**
   * Optional. Provenance configuration.
   *
   * @param Provenance $provenance
   */
  public function setProvenance(Provenance $provenance)
  {
    $this->provenance = $provenance;
  }
  /**
   * @return Provenance
   */
  public function getProvenance()
  {
    return $this->provenance;
  }
  /**
   * Output only. The `Record` of this `PipelineRun`. Format: `projects/{project
   * }/locations/{location}/results/{result_id}/records/{record_id}`
   *
   * @param string $record
   */
  public function setRecord($record)
  {
    $this->record = $record;
  }
  /**
   * @return string
   */
  public function getRecord()
  {
    return $this->record;
  }
  /**
   * Output only. The exact PipelineSpec used to instantiate the run.
   *
   * @param PipelineSpec $resolvedPipelineSpec
   */
  public function setResolvedPipelineSpec(PipelineSpec $resolvedPipelineSpec)
  {
    $this->resolvedPipelineSpec = $resolvedPipelineSpec;
  }
  /**
   * @return PipelineSpec
   */
  public function getResolvedPipelineSpec()
  {
    return $this->resolvedPipelineSpec;
  }
  /**
   * Optional. Output only. List of results written out by the pipeline's
   * containers
   *
   * @param PipelineRunResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return PipelineRunResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Optional. Security configuration.
   *
   * @param Security $security
   */
  public function setSecurity(Security $security)
  {
    $this->security = $security;
  }
  /**
   * @return Security
   */
  public function getSecurity()
  {
    return $this->security;
  }
  /**
   * Service account used in the Pipeline. Deprecated; please use
   * security.service_account instead.
   *
   * @deprecated
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. List of tasks that were skipped due to when expressions
   * evaluating to false.
   *
   * @param SkippedTask[] $skippedTasks
   */
  public function setSkippedTasks($skippedTasks)
  {
    $this->skippedTasks = $skippedTasks;
  }
  /**
   * @return SkippedTask[]
   */
  public function getSkippedTasks()
  {
    return $this->skippedTasks;
  }
  /**
   * Output only. Time the pipeline is actually started.
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
   * Time after which the Pipeline times out. Currently three keys are accepted
   * in the map pipeline, tasks and finally with Timeouts.pipeline >=
   * Timeouts.tasks + Timeouts.finally
   *
   * @param TimeoutFields $timeouts
   */
  public function setTimeouts(TimeoutFields $timeouts)
  {
    $this->timeouts = $timeouts;
  }
  /**
   * @return TimeoutFields
   */
  public function getTimeouts()
  {
    return $this->timeouts;
  }
  /**
   * Output only. A unique identifier for the `PipelineRun`.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Time at which the request to update the `PipelineRun` was
   * received.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. Worker configuration.
   *
   * @param Worker $worker
   */
  public function setWorker(Worker $worker)
  {
    $this->worker = $worker;
  }
  /**
   * @return Worker
   */
  public function getWorker()
  {
    return $this->worker;
  }
  /**
   * Output only. The WorkerPool used to run this PipelineRun.
   *
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
  /**
   * Output only. The Workflow used to create this PipelineRun.
   *
   * @param string $workflow
   */
  public function setWorkflow($workflow)
  {
    $this->workflow = $workflow;
  }
  /**
   * @return string
   */
  public function getWorkflow()
  {
    return $this->workflow;
  }
  /**
   * Workspaces is a list of WorkspaceBindings from volumes to workspaces.
   *
   * @param WorkspaceBinding[] $workspaces
   */
  public function setWorkspaces($workspaces)
  {
    $this->workspaces = $workspaces;
  }
  /**
   * @return WorkspaceBinding[]
   */
  public function getWorkspaces()
  {
    return $this->workspaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineRun::class, 'Google_Service_CloudBuild_PipelineRun');
