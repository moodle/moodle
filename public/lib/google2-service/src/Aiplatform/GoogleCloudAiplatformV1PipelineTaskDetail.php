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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PipelineTaskDetail extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Specifies pending state for the task.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Specifies task is being executed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Specifies task completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Specifies Task cancel is in pending state.
   */
  public const STATE_CANCEL_PENDING = 'CANCEL_PENDING';
  /**
   * Specifies task is being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * Specifies task was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Specifies task failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Specifies task was skipped due to cache hit.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Specifies that the task was not triggered because the task's trigger policy
   * is not satisfied. The trigger policy is specified in the `condition` field
   * of PipelineJob.pipeline_spec.
   */
  public const STATE_NOT_TRIGGERED = 'NOT_TRIGGERED';
  protected $collection_key = 'pipelineTaskStatus';
  /**
   * Output only. Task create time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Task end time.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $executionType = GoogleCloudAiplatformV1Execution::class;
  protected $executionDataType = '';
  protected $executorDetailType = GoogleCloudAiplatformV1PipelineTaskExecutorDetail::class;
  protected $executorDetailDataType = '';
  protected $inputsType = GoogleCloudAiplatformV1PipelineTaskDetailArtifactList::class;
  protected $inputsDataType = 'map';
  protected $outputsType = GoogleCloudAiplatformV1PipelineTaskDetailArtifactList::class;
  protected $outputsDataType = 'map';
  /**
   * Output only. The id of the parent task if the task is within a component
   * scope. Empty if the task is at the root level.
   *
   * @var string
   */
  public $parentTaskId;
  protected $pipelineTaskStatusType = GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus::class;
  protected $pipelineTaskStatusDataType = 'array';
  /**
   * Output only. Task start time.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. State of the task.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The system generated ID of the task.
   *
   * @var string
   */
  public $taskId;
  /**
   * Output only. The user specified name of the task that is defined in
   * pipeline_spec.
   *
   * @var string
   */
  public $taskName;
  /**
   * Output only. The unique name of a task. This field is used by rerun
   * pipeline job. Console UI and Vertex AI SDK will support triggering pipeline
   * job reruns. The name is constructed by concatenating all the parent tasks
   * name with the task name. For example, if a task named "child_task" has a
   * parent task named "parent_task_1" and parent task 1 has a parent task named
   * "parent_task_2", the task unique name will be
   * "parent_task_2.parent_task_1.child_task".
   *
   * @var string
   */
  public $taskUniqueName;

  /**
   * Output only. Task create time.
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
   * Output only. Task end time.
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
   * Output only. The error that occurred during task execution. Only populated
   * when the task's state is FAILED or CANCELLED.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The execution metadata of the task.
   *
   * @param GoogleCloudAiplatformV1Execution $execution
   */
  public function setExecution(GoogleCloudAiplatformV1Execution $execution)
  {
    $this->execution = $execution;
  }
  /**
   * @return GoogleCloudAiplatformV1Execution
   */
  public function getExecution()
  {
    return $this->execution;
  }
  /**
   * Output only. The detailed execution info.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskExecutorDetail $executorDetail
   */
  public function setExecutorDetail(GoogleCloudAiplatformV1PipelineTaskExecutorDetail $executorDetail)
  {
    $this->executorDetail = $executorDetail;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskExecutorDetail
   */
  public function getExecutorDetail()
  {
    return $this->executorDetail;
  }
  /**
   * Output only. The runtime input artifacts of the task.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskDetailArtifactList[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskDetailArtifactList[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * Output only. The runtime output artifacts of the task.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskDetailArtifactList[] $outputs
   */
  public function setOutputs($outputs)
  {
    $this->outputs = $outputs;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskDetailArtifactList[]
   */
  public function getOutputs()
  {
    return $this->outputs;
  }
  /**
   * Output only. The id of the parent task if the task is within a component
   * scope. Empty if the task is at the root level.
   *
   * @param string $parentTaskId
   */
  public function setParentTaskId($parentTaskId)
  {
    $this->parentTaskId = $parentTaskId;
  }
  /**
   * @return string
   */
  public function getParentTaskId()
  {
    return $this->parentTaskId;
  }
  /**
   * Output only. A list of task status. This field keeps a record of task
   * status evolving over time.
   *
   * @param GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus[] $pipelineTaskStatus
   */
  public function setPipelineTaskStatus($pipelineTaskStatus)
  {
    $this->pipelineTaskStatus = $pipelineTaskStatus;
  }
  /**
   * @return GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus[]
   */
  public function getPipelineTaskStatus()
  {
    return $this->pipelineTaskStatus;
  }
  /**
   * Output only. Task start time.
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
   * Output only. State of the task.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED,
   * CANCEL_PENDING, CANCELLING, CANCELLED, FAILED, SKIPPED, NOT_TRIGGERED
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
   * Output only. The system generated ID of the task.
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
  /**
   * Output only. The user specified name of the task that is defined in
   * pipeline_spec.
   *
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * Output only. The unique name of a task. This field is used by rerun
   * pipeline job. Console UI and Vertex AI SDK will support triggering pipeline
   * job reruns. The name is constructed by concatenating all the parent tasks
   * name with the task name. For example, if a task named "child_task" has a
   * parent task named "parent_task_1" and parent task 1 has a parent task named
   * "parent_task_2", the task unique name will be
   * "parent_task_2.parent_task_1.child_task".
   *
   * @param string $taskUniqueName
   */
  public function setTaskUniqueName($taskUniqueName)
  {
    $this->taskUniqueName = $taskUniqueName;
  }
  /**
   * @return string
   */
  public function getTaskUniqueName()
  {
    return $this->taskUniqueName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PipelineTaskDetail::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineTaskDetail');
