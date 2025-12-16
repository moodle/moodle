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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2Task extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_UNSPECIFIED = 'EXECUTION_ENVIRONMENT_UNSPECIFIED';
  /**
   * Uses the First Generation environment.
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_GEN1 = 'EXECUTION_ENVIRONMENT_GEN1';
  /**
   * Uses Second Generation environment.
   */
  public const EXECUTION_ENVIRONMENT_EXECUTION_ENVIRONMENT_GEN2 = 'EXECUTION_ENVIRONMENT_GEN2';
  protected $collection_key = 'volumes';
  /**
   * Output only. Unstructured key value map that may be set by external tools
   * to store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Represents time when the Task was completed. It is not
   * guaranteed to be set in happens-before order across separate operations.
   *
   * @var string
   */
  public $completionTime;
  protected $conditionsType = GoogleCloudRunV2Condition::class;
  protected $conditionsDataType = 'array';
  protected $containersType = GoogleCloudRunV2Container::class;
  protected $containersDataType = 'array';
  /**
   * Output only. Represents time when the task was created by the system. It is
   * not guaranteed to be set in happens-before order across separate
   * operations.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. For a deleted resource, the deletion time. It is only
   * populated as a response to a Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Output only. A reference to a customer managed encryption key (CMEK) to use
   * to encrypt this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @var string
   */
  public $encryptionKey;
  /**
   * Output only. A system-generated fingerprint for this version of the
   * resource. May be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The name of the parent Execution.
   *
   * @var string
   */
  public $execution;
  /**
   * The execution environment being used to host this Task.
   *
   * @var string
   */
  public $executionEnvironment;
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permamently deleted. It is only populated as a response to a Delete
   * request.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. A number that monotonically increases every time the user
   * modifies the desired state.
   *
   * @var string
   */
  public $generation;
  /**
   * Optional. Output only. True if GPU zonal redundancy is disabled on this
   * task.
   *
   * @var bool
   */
  public $gpuZonalRedundancyDisabled;
  /**
   * Output only. Index of the Task, unique per execution, and beginning at 0.
   *
   * @var int
   */
  public $index;
  /**
   * Output only. The name of the parent Job.
   *
   * @var string
   */
  public $job;
  /**
   * Output only. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels
   *
   * @var string[]
   */
  public $labels;
  protected $lastAttemptResultType = GoogleCloudRunV2TaskAttemptResult::class;
  protected $lastAttemptResultDataType = '';
  /**
   * Output only. URI where logs for this execution can be found in Cloud
   * Console.
   *
   * @var string
   */
  public $logUri;
  /**
   * Number of retries allowed per Task, before marking this Task failed.
   *
   * @var int
   */
  public $maxRetries;
  /**
   * Output only. The unique name of this Task.
   *
   * @var string
   */
  public $name;
  protected $nodeSelectorType = GoogleCloudRunV2NodeSelector::class;
  protected $nodeSelectorDataType = '';
  /**
   * Output only. The generation of this Task. See comments in `Job.reconciling`
   * for additional information on reconciliation process in Cloud Run.
   *
   * @var string
   */
  public $observedGeneration;
  /**
   * Output only. Indicates whether the resource's reconciliation is still in
   * progress. See comments in `Job.reconciling` for additional information on
   * reconciliation process in Cloud Run.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The number of times this Task was retried. Tasks are retried
   * when they fail up to the maxRetries limit.
   *
   * @var int
   */
  public $retried;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Represents time when the task was scheduled to run by the
   * system. It is not guaranteed to be set in happens-before order across
   * separate operations.
   *
   * @var string
   */
  public $scheduledTime;
  /**
   * Email address of the IAM service account associated with the Task of a Job.
   * The service account represents the identity of the running task, and
   * determines what permissions the task has. If not provided, the task will
   * use the project's default service account.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Represents time when the task started to run. It is not
   * guaranteed to be set in happens-before order across separate operations.
   *
   * @var string
   */
  public $startTime;
  /**
   * Max allowed time duration the Task may be active before the system will
   * actively try to mark it failed and kill associated containers. This applies
   * per attempt of a task, meaning each retry can run for the full timeout.
   *
   * @var string
   */
  public $timeout;
  /**
   * Output only. Server assigned unique identifier for the Task. The value is a
   * UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;
  protected $volumesType = GoogleCloudRunV2Volume::class;
  protected $volumesDataType = 'array';
  protected $vpcAccessType = GoogleCloudRunV2VpcAccess::class;
  protected $vpcAccessDataType = '';

  /**
   * Output only. Unstructured key value map that may be set by external tools
   * to store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
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
   * Output only. Represents time when the Task was completed. It is not
   * guaranteed to be set in happens-before order across separate operations.
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
   * Output only. The Condition of this Task, containing its readiness status,
   * and detailed error information in case it did not reach the desired state.
   *
   * @param GoogleCloudRunV2Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleCloudRunV2Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Holds the single container that defines the unit of execution for this
   * task.
   *
   * @param GoogleCloudRunV2Container[] $containers
   */
  public function setContainers($containers)
  {
    $this->containers = $containers;
  }
  /**
   * @return GoogleCloudRunV2Container[]
   */
  public function getContainers()
  {
    return $this->containers;
  }
  /**
   * Output only. Represents time when the task was created by the system. It is
   * not guaranteed to be set in happens-before order across separate
   * operations.
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
   * Output only. For a deleted resource, the deletion time. It is only
   * populated as a response to a Delete request.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Output only. A reference to a customer managed encryption key (CMEK) to use
   * to encrypt this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @param string $encryptionKey
   */
  public function setEncryptionKey($encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return string
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Output only. A system-generated fingerprint for this version of the
   * resource. May be used to detect modification conflict during updates.
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
   * Output only. The name of the parent Execution.
   *
   * @param string $execution
   */
  public function setExecution($execution)
  {
    $this->execution = $execution;
  }
  /**
   * @return string
   */
  public function getExecution()
  {
    return $this->execution;
  }
  /**
   * The execution environment being used to host this Task.
   *
   * Accepted values: EXECUTION_ENVIRONMENT_UNSPECIFIED,
   * EXECUTION_ENVIRONMENT_GEN1, EXECUTION_ENVIRONMENT_GEN2
   *
   * @param self::EXECUTION_ENVIRONMENT_* $executionEnvironment
   */
  public function setExecutionEnvironment($executionEnvironment)
  {
    $this->executionEnvironment = $executionEnvironment;
  }
  /**
   * @return self::EXECUTION_ENVIRONMENT_*
   */
  public function getExecutionEnvironment()
  {
    return $this->executionEnvironment;
  }
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permamently deleted. It is only populated as a response to a Delete
   * request.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. A number that monotonically increases every time the user
   * modifies the desired state.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * Optional. Output only. True if GPU zonal redundancy is disabled on this
   * task.
   *
   * @param bool $gpuZonalRedundancyDisabled
   */
  public function setGpuZonalRedundancyDisabled($gpuZonalRedundancyDisabled)
  {
    $this->gpuZonalRedundancyDisabled = $gpuZonalRedundancyDisabled;
  }
  /**
   * @return bool
   */
  public function getGpuZonalRedundancyDisabled()
  {
    return $this->gpuZonalRedundancyDisabled;
  }
  /**
   * Output only. Index of the Task, unique per execution, and beginning at 0.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Output only. The name of the parent Job.
   *
   * @param string $job
   */
  public function setJob($job)
  {
    $this->job = $job;
  }
  /**
   * @return string
   */
  public function getJob()
  {
    return $this->job;
  }
  /**
   * Output only. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Result of the last attempt of this Task.
   *
   * @param GoogleCloudRunV2TaskAttemptResult $lastAttemptResult
   */
  public function setLastAttemptResult(GoogleCloudRunV2TaskAttemptResult $lastAttemptResult)
  {
    $this->lastAttemptResult = $lastAttemptResult;
  }
  /**
   * @return GoogleCloudRunV2TaskAttemptResult
   */
  public function getLastAttemptResult()
  {
    return $this->lastAttemptResult;
  }
  /**
   * Output only. URI where logs for this execution can be found in Cloud
   * Console.
   *
   * @param string $logUri
   */
  public function setLogUri($logUri)
  {
    $this->logUri = $logUri;
  }
  /**
   * @return string
   */
  public function getLogUri()
  {
    return $this->logUri;
  }
  /**
   * Number of retries allowed per Task, before marking this Task failed.
   *
   * @param int $maxRetries
   */
  public function setMaxRetries($maxRetries)
  {
    $this->maxRetries = $maxRetries;
  }
  /**
   * @return int
   */
  public function getMaxRetries()
  {
    return $this->maxRetries;
  }
  /**
   * Output only. The unique name of this Task.
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
   * Output only. The node selector for the task.
   *
   * @param GoogleCloudRunV2NodeSelector $nodeSelector
   */
  public function setNodeSelector(GoogleCloudRunV2NodeSelector $nodeSelector)
  {
    $this->nodeSelector = $nodeSelector;
  }
  /**
   * @return GoogleCloudRunV2NodeSelector
   */
  public function getNodeSelector()
  {
    return $this->nodeSelector;
  }
  /**
   * Output only. The generation of this Task. See comments in `Job.reconciling`
   * for additional information on reconciliation process in Cloud Run.
   *
   * @param string $observedGeneration
   */
  public function setObservedGeneration($observedGeneration)
  {
    $this->observedGeneration = $observedGeneration;
  }
  /**
   * @return string
   */
  public function getObservedGeneration()
  {
    return $this->observedGeneration;
  }
  /**
   * Output only. Indicates whether the resource's reconciliation is still in
   * progress. See comments in `Job.reconciling` for additional information on
   * reconciliation process in Cloud Run.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. The number of times this Task was retried. Tasks are retried
   * when they fail up to the maxRetries limit.
   *
   * @param int $retried
   */
  public function setRetried($retried)
  {
    $this->retried = $retried;
  }
  /**
   * @return int
   */
  public function getRetried()
  {
    return $this->retried;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Represents time when the task was scheduled to run by the
   * system. It is not guaranteed to be set in happens-before order across
   * separate operations.
   *
   * @param string $scheduledTime
   */
  public function setScheduledTime($scheduledTime)
  {
    $this->scheduledTime = $scheduledTime;
  }
  /**
   * @return string
   */
  public function getScheduledTime()
  {
    return $this->scheduledTime;
  }
  /**
   * Email address of the IAM service account associated with the Task of a Job.
   * The service account represents the identity of the running task, and
   * determines what permissions the task has. If not provided, the task will
   * use the project's default service account.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. Represents time when the task started to run. It is not
   * guaranteed to be set in happens-before order across separate operations.
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
   * Max allowed time duration the Task may be active before the system will
   * actively try to mark it failed and kill associated containers. This applies
   * per attempt of a task, meaning each retry can run for the full timeout.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Output only. Server assigned unique identifier for the Task. The value is a
   * UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
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
   * Output only. The last-modified time.
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
   * A list of Volumes to make available to containers.
   *
   * @param GoogleCloudRunV2Volume[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return GoogleCloudRunV2Volume[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * Output only. VPC Access configuration to use for this Task. For more
   * information, visit
   * https://cloud.google.com/run/docs/configuring/connecting-vpc.
   *
   * @param GoogleCloudRunV2VpcAccess $vpcAccess
   */
  public function setVpcAccess(GoogleCloudRunV2VpcAccess $vpcAccess)
  {
    $this->vpcAccess = $vpcAccess;
  }
  /**
   * @return GoogleCloudRunV2VpcAccess
   */
  public function getVpcAccess()
  {
    return $this->vpcAccess;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Task::class, 'Google_Service_CloudRun_GoogleCloudRunV2Task');
