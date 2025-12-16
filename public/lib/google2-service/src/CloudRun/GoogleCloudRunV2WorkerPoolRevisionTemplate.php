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

class GoogleCloudRunV2WorkerPoolRevisionTemplate extends \Google\Collection
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
  protected $collection_key = 'volumes';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects. Cloud Run API v2 does not support
   * annotations with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system annotations in v1 now have a corresponding
   * field in v2 WorkerPoolRevisionTemplate. This field follows Kubernetes
   * annotations' namespacing, limits, and rules.
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
   * Optional. True if GPU zonal redundancy is disabled on this worker pool.
   *
   * @var bool
   */
  public $gpuZonalRedundancyDisabled;
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
   * v2 WorkerPoolRevisionTemplate.
   *
   * @var string[]
   */
  public $labels;
  protected $nodeSelectorType = GoogleCloudRunV2NodeSelector::class;
  protected $nodeSelectorDataType = '';
  /**
   * Optional. The unique name for the revision. If this field is omitted, it
   * will be automatically generated based on the WorkerPool name.
   *
   * @var string
   */
  public $revision;
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
   * field in v2 WorkerPoolRevisionTemplate. This field follows Kubernetes
   * annotations' namespacing, limits, and rules.
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
   * Holds list of the containers that defines the unit of execution for this
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
   * Optional. True if GPU zonal redundancy is disabled on this worker pool.
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
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 WorkerPoolRevisionTemplate.
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
   * will be automatically generated based on the WorkerPool name.
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
class_alias(GoogleCloudRunV2WorkerPoolRevisionTemplate::class, 'Google_Service_CloudRun_GoogleCloudRunV2WorkerPoolRevisionTemplate');
