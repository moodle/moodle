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

class GoogleCloudRunV2Execution extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'conditions';
  /**
   * Output only. Unstructured key value map that may be set by external tools
   * to store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The number of tasks which reached phase Cancelled.
   *
   * @var int
   */
  public $cancelledCount;
  /**
   * Output only. Represents time when the execution was completed. It is not
   * guaranteed to be set in happens-before order across separate operations.
   *
   * @var string
   */
  public $completionTime;
  protected $conditionsType = GoogleCloudRunV2Condition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. Represents time when the execution was acknowledged by the
   * execution controller. It is not guaranteed to be set in happens-before
   * order across separate operations.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Email address of the authenticated creator.
   *
   * @var string
   */
  public $creator;
  /**
   * Output only. For a deleted resource, the deletion time. It is only
   * populated as a response to a Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Output only. A system-generated fingerprint for this version of the
   * resource. May be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permamently deleted. It is only populated as a response to a Delete
   * request.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The number of tasks which reached phase Failed.
   *
   * @var int
   */
  public $failedCount;
  /**
   * Output only. A number that monotonically increases every time the user
   * modifies the desired state.
   *
   * @var string
   */
  public $generation;
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
  /**
   * The least stable launch stage needed to create this resource, as defined by
   * [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. Note that this value might not be what was used
   * as input. For example, if ALPHA was provided as input in the parent
   * resource, but only BETA and GA-level features are were, this field will be
   * BETA.
   *
   * @var string
   */
  public $launchStage;
  /**
   * Output only. URI where logs for this execution can be found in Cloud
   * Console.
   *
   * @var string
   */
  public $logUri;
  /**
   * Output only. The unique name of this Execution.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The generation of this Execution. See comments in
   * `reconciling` for additional information on reconciliation process in Cloud
   * Run.
   *
   * @var string
   */
  public $observedGeneration;
  /**
   * Output only. Specifies the maximum desired number of tasks the execution
   * should run at any given time. Must be <= task_count. The actual number of
   * tasks running in steady state will be less than this number when
   * ((.spec.task_count - .status.successful) < .spec.parallelism), i.e. when
   * the work left to do is less than max parallelism.
   *
   * @var int
   */
  public $parallelism;
  /**
   * Output only. Indicates whether the resource's reconciliation is still in
   * progress. See comments in `Job.reconciling` for additional information on
   * reconciliation process in Cloud Run.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. The number of tasks which have retried at least once.
   *
   * @var int
   */
  public $retriedCount;
  /**
   * Output only. The number of actively running tasks.
   *
   * @var int
   */
  public $runningCount;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Represents time when the execution started to run. It is not
   * guaranteed to be set in happens-before order across separate operations.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The number of tasks which reached phase Succeeded.
   *
   * @var int
   */
  public $succeededCount;
  /**
   * Output only. Specifies the desired number of tasks the execution should
   * run. Setting to 1 means that parallelism is limited to 1 and the success of
   * that task signals the success of the execution.
   *
   * @var int
   */
  public $taskCount;
  protected $templateType = GoogleCloudRunV2TaskTemplate::class;
  protected $templateDataType = '';
  /**
   * Output only. Server assigned unique identifier for the Execution. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
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
   * Output only. The number of tasks which reached phase Cancelled.
   *
   * @param int $cancelledCount
   */
  public function setCancelledCount($cancelledCount)
  {
    $this->cancelledCount = $cancelledCount;
  }
  /**
   * @return int
   */
  public function getCancelledCount()
  {
    return $this->cancelledCount;
  }
  /**
   * Output only. Represents time when the execution was completed. It is not
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
   * Output only. The Condition of this Execution, containing its readiness
   * status, and detailed error information in case it did not reach the desired
   * state.
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
   * Output only. Represents time when the execution was acknowledged by the
   * execution controller. It is not guaranteed to be set in happens-before
   * order across separate operations.
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
   * Output only. Email address of the authenticated creator.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
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
   * Output only. The number of tasks which reached phase Failed.
   *
   * @param int $failedCount
   */
  public function setFailedCount($failedCount)
  {
    $this->failedCount = $failedCount;
  }
  /**
   * @return int
   */
  public function getFailedCount()
  {
    return $this->failedCount;
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
   * The least stable launch stage needed to create this resource, as defined by
   * [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. Note that this value might not be what was used
   * as input. For example, if ALPHA was provided as input in the parent
   * resource, but only BETA and GA-level features are were, this field will be
   * BETA.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
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
   * Output only. The unique name of this Execution.
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
   * Output only. The generation of this Execution. See comments in
   * `reconciling` for additional information on reconciliation process in Cloud
   * Run.
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
   * Output only. Specifies the maximum desired number of tasks the execution
   * should run at any given time. Must be <= task_count. The actual number of
   * tasks running in steady state will be less than this number when
   * ((.spec.task_count - .status.successful) < .spec.parallelism), i.e. when
   * the work left to do is less than max parallelism.
   *
   * @param int $parallelism
   */
  public function setParallelism($parallelism)
  {
    $this->parallelism = $parallelism;
  }
  /**
   * @return int
   */
  public function getParallelism()
  {
    return $this->parallelism;
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
   * Output only. The number of tasks which have retried at least once.
   *
   * @param int $retriedCount
   */
  public function setRetriedCount($retriedCount)
  {
    $this->retriedCount = $retriedCount;
  }
  /**
   * @return int
   */
  public function getRetriedCount()
  {
    return $this->retriedCount;
  }
  /**
   * Output only. The number of actively running tasks.
   *
   * @param int $runningCount
   */
  public function setRunningCount($runningCount)
  {
    $this->runningCount = $runningCount;
  }
  /**
   * @return int
   */
  public function getRunningCount()
  {
    return $this->runningCount;
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
   * Output only. Represents time when the execution started to run. It is not
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
   * Output only. The number of tasks which reached phase Succeeded.
   *
   * @param int $succeededCount
   */
  public function setSucceededCount($succeededCount)
  {
    $this->succeededCount = $succeededCount;
  }
  /**
   * @return int
   */
  public function getSucceededCount()
  {
    return $this->succeededCount;
  }
  /**
   * Output only. Specifies the desired number of tasks the execution should
   * run. Setting to 1 means that parallelism is limited to 1 and the success of
   * that task signals the success of the execution.
   *
   * @param int $taskCount
   */
  public function setTaskCount($taskCount)
  {
    $this->taskCount = $taskCount;
  }
  /**
   * @return int
   */
  public function getTaskCount()
  {
    return $this->taskCount;
  }
  /**
   * Output only. The template used to create tasks for this execution.
   *
   * @param GoogleCloudRunV2TaskTemplate $template
   */
  public function setTemplate(GoogleCloudRunV2TaskTemplate $template)
  {
    $this->template = $template;
  }
  /**
   * @return GoogleCloudRunV2TaskTemplate
   */
  public function getTemplate()
  {
    return $this->template;
  }
  /**
   * Output only. Server assigned unique identifier for the Execution. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Execution::class, 'Google_Service_CloudRun_GoogleCloudRunV2Execution');
