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

class GoogleCloudRunV2Job extends \Google\Collection
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
   * Unstructured key value map that may be set by external tools to store and
   * arbitrary metadata. They are not queryable and should be preserved when
   * modifying objects. Cloud Run API v2 does not support annotations with
   * `run.googleapis.com`, `cloud.googleapis.com`, `serving.knative.dev`, or
   * `autoscaling.knative.dev` namespaces, and they will be rejected on new
   * resources. All system annotations in v1 now have a corresponding field in
   * v2 Job. This field follows Kubernetes annotations' namespacing, limits, and
   * rules.
   *
   * @var string[]
   */
  public $annotations;
  protected $binaryAuthorizationType = GoogleCloudRunV2BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  /**
   * Arbitrary identifier for the API client.
   *
   * @var string
   */
  public $client;
  /**
   * Arbitrary version identifier for the API client.
   *
   * @var string
   */
  public $clientVersion;
  protected $conditionsType = GoogleCloudRunV2Condition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. The creation time.
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
   * Output only. The deletion time. It is only populated as a response to a
   * Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. A system-generated fingerprint for this version of the resource.
   * May be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Number of executions created for this job.
   *
   * @var int
   */
  public $executionCount;
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permamently deleted.
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
   * Unstructured key value map that can be used to organize and categorize
   * objects. User-provided labels are shared with Google's billing system, so
   * they can be used to filter, or break down billing charges by team,
   * component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 Job.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Email address of the last authenticated modifier.
   *
   * @var string
   */
  public $lastModifier;
  protected $latestCreatedExecutionType = GoogleCloudRunV2ExecutionReference::class;
  protected $latestCreatedExecutionDataType = '';
  /**
   * The launch stage as defined by [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. If no value is specified, GA is assumed. Set the
   * launch stage to a preview stage on input to allow use of preview features
   * in that stage. On read (or output), describes whether the resource uses
   * preview features. For example, if ALPHA is provided as input, but only BETA
   * and GA-level features are used, this field will be BETA on output.
   *
   * @var string
   */
  public $launchStage;
  /**
   * The fully qualified name of this Job. Format:
   * projects/{project}/locations/{location}/jobs/{job}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The generation of this Job. See comments in `reconciling` for
   * additional information on reconciliation process in Cloud Run.
   *
   * @var string
   */
  public $observedGeneration;
  /**
   * Output only. Returns true if the Job is currently being acted upon by the
   * system to bring it into the desired state. When a new Job is created, or an
   * existing one is updated, Cloud Run will asynchronously perform all
   * necessary steps to bring the Job to the desired state. This process is
   * called reconciliation. While reconciliation is in process,
   * `observed_generation` and `latest_succeeded_execution`, will have transient
   * values that might mismatch the intended state: Once reconciliation is over
   * (and this field is false), there are two possible outcomes: reconciliation
   * succeeded and the state matches the Job, or there was an error, and
   * reconciliation failed. This state can be found in
   * `terminal_condition.state`. If reconciliation succeeded, the following
   * fields will match: `observed_generation` and `generation`,
   * `latest_succeeded_execution` and `latest_created_execution`. If
   * reconciliation failed, `observed_generation` and
   * `latest_succeeded_execution` will have the state of the last succeeded
   * execution or empty for newly created Job. Additional information on the
   * failure can be found in `terminal_condition` and `conditions`.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * A unique string used as a suffix for creating a new execution. The Job will
   * become ready when the execution is successfully completed. The sum of job
   * name and token length must be fewer than 63 characters.
   *
   * @var string
   */
  public $runExecutionToken;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * A unique string used as a suffix creating a new execution. The Job will
   * become ready when the execution is successfully started. The sum of job
   * name and token length must be fewer than 63 characters.
   *
   * @var string
   */
  public $startExecutionToken;
  protected $templateType = GoogleCloudRunV2ExecutionTemplate::class;
  protected $templateDataType = '';
  protected $terminalConditionType = GoogleCloudRunV2Condition::class;
  protected $terminalConditionDataType = '';
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
   * Unstructured key value map that may be set by external tools to store and
   * arbitrary metadata. They are not queryable and should be preserved when
   * modifying objects. Cloud Run API v2 does not support annotations with
   * `run.googleapis.com`, `cloud.googleapis.com`, `serving.knative.dev`, or
   * `autoscaling.knative.dev` namespaces, and they will be rejected on new
   * resources. All system annotations in v1 now have a corresponding field in
   * v2 Job. This field follows Kubernetes annotations' namespacing, limits, and
   * rules.
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
   * Settings for the Binary Authorization feature.
   *
   * @param GoogleCloudRunV2BinaryAuthorization $binaryAuthorization
   */
  public function setBinaryAuthorization(GoogleCloudRunV2BinaryAuthorization $binaryAuthorization)
  {
    $this->binaryAuthorization = $binaryAuthorization;
  }
  /**
   * @return GoogleCloudRunV2BinaryAuthorization
   */
  public function getBinaryAuthorization()
  {
    return $this->binaryAuthorization;
  }
  /**
   * Arbitrary identifier for the API client.
   *
   * @param string $client
   */
  public function setClient($client)
  {
    $this->client = $client;
  }
  /**
   * @return string
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * Arbitrary version identifier for the API client.
   *
   * @param string $clientVersion
   */
  public function setClientVersion($clientVersion)
  {
    $this->clientVersion = $clientVersion;
  }
  /**
   * @return string
   */
  public function getClientVersion()
  {
    return $this->clientVersion;
  }
  /**
   * Output only. The Conditions of all other associated sub-resources. They
   * contain additional diagnostics information in case the Job does not reach
   * its desired state. See comments in `reconciling` for additional information
   * on reconciliation process in Cloud Run.
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
   * Output only. The creation time.
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
   * Output only. The deletion time. It is only populated as a response to a
   * Delete request.
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
   * Optional. A system-generated fingerprint for this version of the resource.
   * May be used to detect modification conflict during updates.
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
   * Output only. Number of executions created for this job.
   *
   * @param int $executionCount
   */
  public function setExecutionCount($executionCount)
  {
    $this->executionCount = $executionCount;
  }
  /**
   * @return int
   */
  public function getExecutionCount()
  {
    return $this->executionCount;
  }
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permamently deleted.
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
   * Unstructured key value map that can be used to organize and categorize
   * objects. User-provided labels are shared with Google's billing system, so
   * they can be used to filter, or break down billing charges by team,
   * component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 Job.
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
   * Output only. Email address of the last authenticated modifier.
   *
   * @param string $lastModifier
   */
  public function setLastModifier($lastModifier)
  {
    $this->lastModifier = $lastModifier;
  }
  /**
   * @return string
   */
  public function getLastModifier()
  {
    return $this->lastModifier;
  }
  /**
   * Output only. Name of the last created execution.
   *
   * @param GoogleCloudRunV2ExecutionReference $latestCreatedExecution
   */
  public function setLatestCreatedExecution(GoogleCloudRunV2ExecutionReference $latestCreatedExecution)
  {
    $this->latestCreatedExecution = $latestCreatedExecution;
  }
  /**
   * @return GoogleCloudRunV2ExecutionReference
   */
  public function getLatestCreatedExecution()
  {
    return $this->latestCreatedExecution;
  }
  /**
   * The launch stage as defined by [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. If no value is specified, GA is assumed. Set the
   * launch stage to a preview stage on input to allow use of preview features
   * in that stage. On read (or output), describes whether the resource uses
   * preview features. For example, if ALPHA is provided as input, but only BETA
   * and GA-level features are used, this field will be BETA on output.
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
   * The fully qualified name of this Job. Format:
   * projects/{project}/locations/{location}/jobs/{job}
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
   * Output only. The generation of this Job. See comments in `reconciling` for
   * additional information on reconciliation process in Cloud Run.
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
   * Output only. Returns true if the Job is currently being acted upon by the
   * system to bring it into the desired state. When a new Job is created, or an
   * existing one is updated, Cloud Run will asynchronously perform all
   * necessary steps to bring the Job to the desired state. This process is
   * called reconciliation. While reconciliation is in process,
   * `observed_generation` and `latest_succeeded_execution`, will have transient
   * values that might mismatch the intended state: Once reconciliation is over
   * (and this field is false), there are two possible outcomes: reconciliation
   * succeeded and the state matches the Job, or there was an error, and
   * reconciliation failed. This state can be found in
   * `terminal_condition.state`. If reconciliation succeeded, the following
   * fields will match: `observed_generation` and `generation`,
   * `latest_succeeded_execution` and `latest_created_execution`. If
   * reconciliation failed, `observed_generation` and
   * `latest_succeeded_execution` will have the state of the last succeeded
   * execution or empty for newly created Job. Additional information on the
   * failure can be found in `terminal_condition` and `conditions`.
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
   * A unique string used as a suffix for creating a new execution. The Job will
   * become ready when the execution is successfully completed. The sum of job
   * name and token length must be fewer than 63 characters.
   *
   * @param string $runExecutionToken
   */
  public function setRunExecutionToken($runExecutionToken)
  {
    $this->runExecutionToken = $runExecutionToken;
  }
  /**
   * @return string
   */
  public function getRunExecutionToken()
  {
    return $this->runExecutionToken;
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
   * A unique string used as a suffix creating a new execution. The Job will
   * become ready when the execution is successfully started. The sum of job
   * name and token length must be fewer than 63 characters.
   *
   * @param string $startExecutionToken
   */
  public function setStartExecutionToken($startExecutionToken)
  {
    $this->startExecutionToken = $startExecutionToken;
  }
  /**
   * @return string
   */
  public function getStartExecutionToken()
  {
    return $this->startExecutionToken;
  }
  /**
   * Required. The template used to create executions for this Job.
   *
   * @param GoogleCloudRunV2ExecutionTemplate $template
   */
  public function setTemplate(GoogleCloudRunV2ExecutionTemplate $template)
  {
    $this->template = $template;
  }
  /**
   * @return GoogleCloudRunV2ExecutionTemplate
   */
  public function getTemplate()
  {
    return $this->template;
  }
  /**
   * Output only. The Condition of this Job, containing its readiness status,
   * and detailed error information in case it did not reach the desired state.
   *
   * @param GoogleCloudRunV2Condition $terminalCondition
   */
  public function setTerminalCondition(GoogleCloudRunV2Condition $terminalCondition)
  {
    $this->terminalCondition = $terminalCondition;
  }
  /**
   * @return GoogleCloudRunV2Condition
   */
  public function getTerminalCondition()
  {
    return $this->terminalCondition;
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
class_alias(GoogleCloudRunV2Job::class, 'Google_Service_CloudRun_GoogleCloudRunV2Job');
