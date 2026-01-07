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

class GoogleCloudRunV2Revision extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const ENCRYPTION_KEY_REVOCATION_ACTION_ENCRYPTION_KEY_REVOCATION_ACTION_UNSPECIFIED = 'ENCRYPTION_KEY_REVOCATION_ACTION_UNSPECIFIED';
  /**
   * Prevents the creation of new instances.
   */
  public const ENCRYPTION_KEY_REVOCATION_ACTION_PREVENT_NEW = 'PREVENT_NEW';
  /**
   * Shuts down existing instances, and prevents creation of new ones.
   */
  public const ENCRYPTION_KEY_REVOCATION_ACTION_SHUTDOWN = 'SHUTDOWN';
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
  protected $collection_key = 'volumes';
  /**
   * Output only. Unstructured key value map that may be set by external tools
   * to store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  protected $conditionsType = GoogleCloudRunV2Condition::class;
  protected $conditionsDataType = 'array';
  protected $containersType = GoogleCloudRunV2Container::class;
  protected $containersDataType = 'array';
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
   * Output only. For a deleted resource, the deletion time. It is only
   * populated as a response to a Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * A reference to a customer managed encryption key (CMEK) to use to encrypt
   * this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @var string
   */
  public $encryptionKey;
  /**
   * The action to take if the encryption key is revoked.
   *
   * @var string
   */
  public $encryptionKeyRevocationAction;
  /**
   * If encryption_key_revocation_action is SHUTDOWN, the duration before
   * shutting down all instances. The minimum increment is 1 hour.
   *
   * @var string
   */
  public $encryptionKeyShutdownDuration;
  /**
   * Output only. A system-generated fingerprint for this version of the
   * resource. May be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * The execution environment being used to host this Revision.
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
   * revision.
   *
   * @var bool
   */
  public $gpuZonalRedundancyDisabled;
  /**
   * Output only. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels.
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
   * Output only. The Google Console URI to obtain logs for the Revision.
   *
   * @var string
   */
  public $logUri;
  /**
   * Sets the maximum number of requests that each serving instance can receive.
   *
   * @var int
   */
  public $maxInstanceRequestConcurrency;
  /**
   * Output only. The unique name of this Revision.
   *
   * @var string
   */
  public $name;
  protected $nodeSelectorType = GoogleCloudRunV2NodeSelector::class;
  protected $nodeSelectorDataType = '';
  /**
   * Output only. The generation of this Revision currently serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run.
   *
   * @var string
   */
  public $observedGeneration;
  /**
   * Output only. Indicates whether the resource's reconciliation is still in
   * progress. See comments in `Service.reconciling` for additional information
   * on reconciliation process in Cloud Run.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $scalingType = GoogleCloudRunV2RevisionScaling::class;
  protected $scalingDataType = '';
  protected $scalingStatusType = GoogleCloudRunV2RevisionScalingStatus::class;
  protected $scalingStatusDataType = '';
  /**
   * Output only. The name of the parent service.
   *
   * @var string
   */
  public $service;
  /**
   * Email address of the IAM service account associated with the revision of
   * the service. The service account represents the identity of the running
   * revision, and determines what permissions the revision has.
   *
   * @var string
   */
  public $serviceAccount;
  protected $serviceMeshType = GoogleCloudRunV2ServiceMesh::class;
  protected $serviceMeshDataType = '';
  /**
   * Enable session affinity.
   *
   * @var bool
   */
  public $sessionAffinity;
  /**
   * Max allowed time for an instance to respond to a request.
   *
   * @var string
   */
  public $timeout;
  /**
   * Output only. Server assigned unique identifier for the Revision. The value
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
   * Output only. The Condition of this Revision, containing its readiness
   * status, and detailed error information in case it did not reach a serving
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
   * Holds the single container that defines the unit of execution for this
   * Revision.
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
   * A reference to a customer managed encryption key (CMEK) to use to encrypt
   * this container image. For more information, go to
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
   * The action to take if the encryption key is revoked.
   *
   * Accepted values: ENCRYPTION_KEY_REVOCATION_ACTION_UNSPECIFIED, PREVENT_NEW,
   * SHUTDOWN
   *
   * @param self::ENCRYPTION_KEY_REVOCATION_ACTION_* $encryptionKeyRevocationAction
   */
  public function setEncryptionKeyRevocationAction($encryptionKeyRevocationAction)
  {
    $this->encryptionKeyRevocationAction = $encryptionKeyRevocationAction;
  }
  /**
   * @return self::ENCRYPTION_KEY_REVOCATION_ACTION_*
   */
  public function getEncryptionKeyRevocationAction()
  {
    return $this->encryptionKeyRevocationAction;
  }
  /**
   * If encryption_key_revocation_action is SHUTDOWN, the duration before
   * shutting down all instances. The minimum increment is 1 hour.
   *
   * @param string $encryptionKeyShutdownDuration
   */
  public function setEncryptionKeyShutdownDuration($encryptionKeyShutdownDuration)
  {
    $this->encryptionKeyShutdownDuration = $encryptionKeyShutdownDuration;
  }
  /**
   * @return string
   */
  public function getEncryptionKeyShutdownDuration()
  {
    return $this->encryptionKeyShutdownDuration;
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
   * The execution environment being used to host this Revision.
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
   * revision.
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
   * Output only. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels.
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
   * Output only. The Google Console URI to obtain logs for the Revision.
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
   * Sets the maximum number of requests that each serving instance can receive.
   *
   * @param int $maxInstanceRequestConcurrency
   */
  public function setMaxInstanceRequestConcurrency($maxInstanceRequestConcurrency)
  {
    $this->maxInstanceRequestConcurrency = $maxInstanceRequestConcurrency;
  }
  /**
   * @return int
   */
  public function getMaxInstanceRequestConcurrency()
  {
    return $this->maxInstanceRequestConcurrency;
  }
  /**
   * Output only. The unique name of this Revision.
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
   * The node selector for the revision.
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
   * Output only. The generation of this Revision currently serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run.
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
   * progress. See comments in `Service.reconciling` for additional information
   * on reconciliation process in Cloud Run.
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
   * Scaling settings for this revision.
   *
   * @param GoogleCloudRunV2RevisionScaling $scaling
   */
  public function setScaling(GoogleCloudRunV2RevisionScaling $scaling)
  {
    $this->scaling = $scaling;
  }
  /**
   * @return GoogleCloudRunV2RevisionScaling
   */
  public function getScaling()
  {
    return $this->scaling;
  }
  /**
   * Output only. The current effective scaling settings for the revision.
   *
   * @param GoogleCloudRunV2RevisionScalingStatus $scalingStatus
   */
  public function setScalingStatus(GoogleCloudRunV2RevisionScalingStatus $scalingStatus)
  {
    $this->scalingStatus = $scalingStatus;
  }
  /**
   * @return GoogleCloudRunV2RevisionScalingStatus
   */
  public function getScalingStatus()
  {
    return $this->scalingStatus;
  }
  /**
   * Output only. The name of the parent service.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Email address of the IAM service account associated with the revision of
   * the service. The service account represents the identity of the running
   * revision, and determines what permissions the revision has.
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
   * Enables service mesh connectivity.
   *
   * @param GoogleCloudRunV2ServiceMesh $serviceMesh
   */
  public function setServiceMesh(GoogleCloudRunV2ServiceMesh $serviceMesh)
  {
    $this->serviceMesh = $serviceMesh;
  }
  /**
   * @return GoogleCloudRunV2ServiceMesh
   */
  public function getServiceMesh()
  {
    return $this->serviceMesh;
  }
  /**
   * Enable session affinity.
   *
   * @param bool $sessionAffinity
   */
  public function setSessionAffinity($sessionAffinity)
  {
    $this->sessionAffinity = $sessionAffinity;
  }
  /**
   * @return bool
   */
  public function getSessionAffinity()
  {
    return $this->sessionAffinity;
  }
  /**
   * Max allowed time for an instance to respond to a request.
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
   * Output only. Server assigned unique identifier for the Revision. The value
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
   * VPC Access configuration for this Revision. For more information, visit
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
class_alias(GoogleCloudRunV2Revision::class, 'Google_Service_CloudRun_GoogleCloudRunV2Revision');
