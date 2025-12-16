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

class GoogleCloudAiplatformV1NotebookRuntimeTemplate extends \Google\Collection
{
  /**
   * Unspecified notebook runtime type, NotebookRuntimeType will default to
   * USER_DEFINED.
   */
  public const NOTEBOOK_RUNTIME_TYPE_NOTEBOOK_RUNTIME_TYPE_UNSPECIFIED = 'NOTEBOOK_RUNTIME_TYPE_UNSPECIFIED';
  /**
   * runtime or template with coustomized configurations from user.
   */
  public const NOTEBOOK_RUNTIME_TYPE_USER_DEFINED = 'USER_DEFINED';
  /**
   * runtime or template with system defined configurations.
   */
  public const NOTEBOOK_RUNTIME_TYPE_ONE_CLICK = 'ONE_CLICK';
  protected $collection_key = 'networkTags';
  /**
   * Output only. Timestamp when this NotebookRuntimeTemplate was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataPersistentDiskSpecType = GoogleCloudAiplatformV1PersistentDiskSpec::class;
  protected $dataPersistentDiskSpecDataType = '';
  /**
   * The description of the NotebookRuntimeTemplate.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the NotebookRuntimeTemplate. The name can be
   * up to 128 characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  protected $eucConfigType = GoogleCloudAiplatformV1NotebookEucConfig::class;
  protected $eucConfigDataType = '';
  protected $idleShutdownConfigType = GoogleCloudAiplatformV1NotebookIdleShutdownConfig::class;
  protected $idleShutdownConfigDataType = '';
  /**
   * Output only. Deprecated: This field has no behavior. Use
   * notebook_runtime_type = 'ONE_CLICK' instead. The default template to use if
   * not specified.
   *
   * @deprecated
   * @var bool
   */
  public $isDefault;
  /**
   * The labels with user-defined metadata to organize the
   * NotebookRuntimeTemplates. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
   *
   * @var string[]
   */
  public $labels;
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  /**
   * The resource name of the NotebookRuntimeTemplate.
   *
   * @var string
   */
  public $name;
  protected $networkSpecType = GoogleCloudAiplatformV1NetworkSpec::class;
  protected $networkSpecDataType = '';
  /**
   * Optional. The Compute Engine tags to add to runtime (see [Tagging
   * instances](https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * Optional. Immutable. The type of the notebook runtime template.
   *
   * @var string
   */
  public $notebookRuntimeType;
  protected $reservationAffinityType = GoogleCloudAiplatformV1NotebookReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Deprecated: This field is ignored and the "Vertex AI Notebook Service
   * Account" (service-PROJECT_NUMBER@gcp-sa-aiplatform-
   * vm.iam.gserviceaccount.com) is used for the runtime workload identity. See
   * https://cloud.google.com/iam/docs/service-agents#vertex-ai-notebook-
   * service-account for more details. For NotebookExecutionJob, use
   * NotebookExecutionJob.service_account instead. The service account that the
   * runtime workload runs as. You can use any service account within the same
   * project, but you must have the service account user permission to use the
   * instance. If not specified, the [Compute Engine default service
   * account](https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account) is used.
   *
   * @deprecated
   * @var string
   */
  public $serviceAccount;
  protected $shieldedVmConfigType = GoogleCloudAiplatformV1ShieldedVmConfig::class;
  protected $shieldedVmConfigDataType = '';
  protected $softwareConfigType = GoogleCloudAiplatformV1NotebookSoftwareConfig::class;
  protected $softwareConfigDataType = '';
  /**
   * Output only. Timestamp when this NotebookRuntimeTemplate was most recently
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this NotebookRuntimeTemplate was created.
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
   * Optional. The specification of persistent disk attached to the runtime as
   * data disk storage.
   *
   * @param GoogleCloudAiplatformV1PersistentDiskSpec $dataPersistentDiskSpec
   */
  public function setDataPersistentDiskSpec(GoogleCloudAiplatformV1PersistentDiskSpec $dataPersistentDiskSpec)
  {
    $this->dataPersistentDiskSpec = $dataPersistentDiskSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1PersistentDiskSpec
   */
  public function getDataPersistentDiskSpec()
  {
    return $this->dataPersistentDiskSpec;
  }
  /**
   * The description of the NotebookRuntimeTemplate.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the NotebookRuntimeTemplate. The name can be
   * up to 128 characters long and can consist of any UTF-8 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Customer-managed encryption key spec for the notebook runtime.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Used to perform consistent read-modify-write updates. If not set, a blind
   * "overwrite" update happens.
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
   * EUC configuration of the NotebookRuntimeTemplate.
   *
   * @param GoogleCloudAiplatformV1NotebookEucConfig $eucConfig
   */
  public function setEucConfig(GoogleCloudAiplatformV1NotebookEucConfig $eucConfig)
  {
    $this->eucConfig = $eucConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookEucConfig
   */
  public function getEucConfig()
  {
    return $this->eucConfig;
  }
  /**
   * The idle shutdown configuration of NotebookRuntimeTemplate. This config
   * will only be set when idle shutdown is enabled.
   *
   * @param GoogleCloudAiplatformV1NotebookIdleShutdownConfig $idleShutdownConfig
   */
  public function setIdleShutdownConfig(GoogleCloudAiplatformV1NotebookIdleShutdownConfig $idleShutdownConfig)
  {
    $this->idleShutdownConfig = $idleShutdownConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookIdleShutdownConfig
   */
  public function getIdleShutdownConfig()
  {
    return $this->idleShutdownConfig;
  }
  /**
   * Output only. Deprecated: This field has no behavior. Use
   * notebook_runtime_type = 'ONE_CLICK' instead. The default template to use if
   * not specified.
   *
   * @deprecated
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * The labels with user-defined metadata to organize the
   * NotebookRuntimeTemplates. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information and examples of
   * labels.
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
   * Optional. Immutable. The specification of a single machine for the
   * template.
   *
   * @param GoogleCloudAiplatformV1MachineSpec $machineSpec
   */
  public function setMachineSpec(GoogleCloudAiplatformV1MachineSpec $machineSpec)
  {
    $this->machineSpec = $machineSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1MachineSpec
   */
  public function getMachineSpec()
  {
    return $this->machineSpec;
  }
  /**
   * The resource name of the NotebookRuntimeTemplate.
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
   * Optional. Network spec.
   *
   * @param GoogleCloudAiplatformV1NetworkSpec $networkSpec
   */
  public function setNetworkSpec(GoogleCloudAiplatformV1NetworkSpec $networkSpec)
  {
    $this->networkSpec = $networkSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1NetworkSpec
   */
  public function getNetworkSpec()
  {
    return $this->networkSpec;
  }
  /**
   * Optional. The Compute Engine tags to add to runtime (see [Tagging
   * instances](https://cloud.google.com/vpc/docs/add-remove-network-tags)).
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * Optional. Immutable. The type of the notebook runtime template.
   *
   * Accepted values: NOTEBOOK_RUNTIME_TYPE_UNSPECIFIED, USER_DEFINED, ONE_CLICK
   *
   * @param self::NOTEBOOK_RUNTIME_TYPE_* $notebookRuntimeType
   */
  public function setNotebookRuntimeType($notebookRuntimeType)
  {
    $this->notebookRuntimeType = $notebookRuntimeType;
  }
  /**
   * @return self::NOTEBOOK_RUNTIME_TYPE_*
   */
  public function getNotebookRuntimeType()
  {
    return $this->notebookRuntimeType;
  }
  /**
   * Optional. Reservation Affinity of the notebook runtime template.
   *
   * @param GoogleCloudAiplatformV1NotebookReservationAffinity $reservationAffinity
   */
  public function setReservationAffinity(GoogleCloudAiplatformV1NotebookReservationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookReservationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * Deprecated: This field is ignored and the "Vertex AI Notebook Service
   * Account" (service-PROJECT_NUMBER@gcp-sa-aiplatform-
   * vm.iam.gserviceaccount.com) is used for the runtime workload identity. See
   * https://cloud.google.com/iam/docs/service-agents#vertex-ai-notebook-
   * service-account for more details. For NotebookExecutionJob, use
   * NotebookExecutionJob.service_account instead. The service account that the
   * runtime workload runs as. You can use any service account within the same
   * project, but you must have the service account user permission to use the
   * instance. If not specified, the [Compute Engine default service
   * account](https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account) is used.
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
   * Optional. Immutable. Runtime Shielded VM spec.
   *
   * @param GoogleCloudAiplatformV1ShieldedVmConfig $shieldedVmConfig
   */
  public function setShieldedVmConfig(GoogleCloudAiplatformV1ShieldedVmConfig $shieldedVmConfig)
  {
    $this->shieldedVmConfig = $shieldedVmConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ShieldedVmConfig
   */
  public function getShieldedVmConfig()
  {
    return $this->shieldedVmConfig;
  }
  /**
   * Optional. The notebook software configuration of the notebook runtime.
   *
   * @param GoogleCloudAiplatformV1NotebookSoftwareConfig $softwareConfig
   */
  public function setSoftwareConfig(GoogleCloudAiplatformV1NotebookSoftwareConfig $softwareConfig)
  {
    $this->softwareConfig = $softwareConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookSoftwareConfig
   */
  public function getSoftwareConfig()
  {
    return $this->softwareConfig;
  }
  /**
   * Output only. Timestamp when this NotebookRuntimeTemplate was most recently
   * updated.
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
class_alias(GoogleCloudAiplatformV1NotebookRuntimeTemplate::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookRuntimeTemplate');
