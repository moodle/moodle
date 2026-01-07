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

class GoogleCloudRunV2RevisionTemplate extends \Google\Collection
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
  protected $collection_key = 'volumes';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects. Cloud Run API v2 does not support
   * annotations with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system annotations in v1 now have a corresponding
   * field in v2 RevisionTemplate. This field follows Kubernetes annotations'
   * namespacing, limits, and rules.
   *
   * @var string[]
   */
  public $annotations;
  protected $containersType = GoogleCloudRunV2Container::class;
  protected $containersDataType = 'array';
  /**
   * A reference to a customer managed encryption key (CMEK) to use to encrypt
   * this container image. For more information, go to
   * https://cloud.google.com/run/docs/securing/using-cmek
   *
   * @var string
   */
  public $encryptionKey;
  /**
   * Optional. The action to take if the encryption key is revoked.
   *
   * @var string
   */
  public $encryptionKeyRevocationAction;
  /**
   * Optional. If encryption_key_revocation_action is SHUTDOWN, the duration
   * before shutting down all instances. The minimum increment is 1 hour.
   *
   * @var string
   */
  public $encryptionKeyShutdownDuration;
  /**
   * Optional. The sandbox environment to host this Revision.
   *
   * @var string
   */
  public $executionEnvironment;
  /**
   * Optional. True if GPU zonal redundancy is disabled on this revision.
   *
   * @var bool
   */
  public $gpuZonalRedundancyDisabled;
  /**
   * Optional. Disables health checking containers during deployment.
   *
   * @var bool
   */
  public $healthCheckDisabled;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 RevisionTemplate.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Sets the maximum number of requests that each serving instance
   * can receive. If not specified or 0, concurrency defaults to 80 when
   * requested `CPU >= 1` and defaults to 1 when requested `CPU < 1`.
   *
   * @var int
   */
  public $maxInstanceRequestConcurrency;
  protected $nodeSelectorType = GoogleCloudRunV2NodeSelector::class;
  protected $nodeSelectorDataType = '';
  /**
   * Optional. The unique name for the revision. If this field is omitted, it
   * will be automatically generated based on the Service name.
   *
   * @var string
   */
  public $revision;
  protected $scalingType = GoogleCloudRunV2RevisionScaling::class;
  protected $scalingDataType = '';
  /**
   * Optional. Email address of the IAM service account associated with the
   * revision of the service. The service account represents the identity of the
   * running revision, and determines what permissions the revision has. If not
   * provided, the revision will use the project's default service account.
   *
   * @var string
   */
  public $serviceAccount;
  protected $serviceMeshType = GoogleCloudRunV2ServiceMesh::class;
  protected $serviceMeshDataType = '';
  /**
   * Optional. Enable session affinity.
   *
   * @var bool
   */
  public $sessionAffinity;
  /**
   * Optional. Max allowed time for an instance to respond to a request.
   *
   * @var string
   */
  public $timeout;
  protected $volumesType = GoogleCloudRunV2Volume::class;
  protected $volumesDataType = 'array';
  protected $vpcAccessType = GoogleCloudRunV2VpcAccess::class;
  protected $vpcAccessDataType = '';

  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects. Cloud Run API v2 does not support
   * annotations with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system annotations in v1 now have a corresponding
   * field in v2 RevisionTemplate. This field follows Kubernetes annotations'
   * namespacing, limits, and rules.
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
   * Optional. The action to take if the encryption key is revoked.
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
   * Optional. If encryption_key_revocation_action is SHUTDOWN, the duration
   * before shutting down all instances. The minimum increment is 1 hour.
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
   * Optional. The sandbox environment to host this Revision.
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
   * Optional. True if GPU zonal redundancy is disabled on this revision.
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
   * Optional. Disables health checking containers during deployment.
   *
   * @param bool $healthCheckDisabled
   */
  public function setHealthCheckDisabled($healthCheckDisabled)
  {
    $this->healthCheckDisabled = $healthCheckDisabled;
  }
  /**
   * @return bool
   */
  public function getHealthCheckDisabled()
  {
    return $this->healthCheckDisabled;
  }
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 RevisionTemplate.
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
   * Optional. Sets the maximum number of requests that each serving instance
   * can receive. If not specified or 0, concurrency defaults to 80 when
   * requested `CPU >= 1` and defaults to 1 when requested `CPU < 1`.
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
   * Optional. The node selector for the revision template.
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
   * Optional. The unique name for the revision. If this field is omitted, it
   * will be automatically generated based on the Service name.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Optional. Scaling settings for this Revision.
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
   * Optional. Email address of the IAM service account associated with the
   * revision of the service. The service account represents the identity of the
   * running revision, and determines what permissions the revision has. If not
   * provided, the revision will use the project's default service account.
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
   * Optional. Enables service mesh connectivity.
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
   * Optional. Enable session affinity.
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
   * Optional. Max allowed time for an instance to respond to a request.
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
   * Optional. A list of Volumes to make available to containers.
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
   * Optional. VPC Access configuration to use for this Revision. For more
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
class_alias(GoogleCloudRunV2RevisionTemplate::class, 'Google_Service_CloudRun_GoogleCloudRunV2RevisionTemplate');
