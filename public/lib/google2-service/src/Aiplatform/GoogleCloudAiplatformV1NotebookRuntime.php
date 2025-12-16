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

class GoogleCloudAiplatformV1NotebookRuntime extends \Google\Collection
{
  /**
   * Unspecified health state.
   */
  public const HEALTH_STATE_HEALTH_STATE_UNSPECIFIED = 'HEALTH_STATE_UNSPECIFIED';
  /**
   * NotebookRuntime is in healthy state. Applies to ACTIVE state.
   */
  public const HEALTH_STATE_HEALTHY = 'HEALTHY';
  /**
   * NotebookRuntime is in unhealthy state. Applies to ACTIVE state.
   */
  public const HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
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
  /**
   * Unspecified runtime state.
   */
  public const RUNTIME_STATE_RUNTIME_STATE_UNSPECIFIED = 'RUNTIME_STATE_UNSPECIFIED';
  /**
   * NotebookRuntime is in running state.
   */
  public const RUNTIME_STATE_RUNNING = 'RUNNING';
  /**
   * NotebookRuntime is in starting state. This is when the runtime is being
   * started from a stopped state.
   */
  public const RUNTIME_STATE_BEING_STARTED = 'BEING_STARTED';
  /**
   * NotebookRuntime is in stopping state.
   */
  public const RUNTIME_STATE_BEING_STOPPED = 'BEING_STOPPED';
  /**
   * NotebookRuntime is in stopped state.
   */
  public const RUNTIME_STATE_STOPPED = 'STOPPED';
  /**
   * NotebookRuntime is in upgrading state. It is in the middle of upgrading
   * process.
   */
  public const RUNTIME_STATE_BEING_UPGRADED = 'BEING_UPGRADED';
  /**
   * NotebookRuntime was unable to start/stop properly.
   */
  public const RUNTIME_STATE_ERROR = 'ERROR';
  /**
   * NotebookRuntime is in invalid state. Cannot be recovered.
   */
  public const RUNTIME_STATE_INVALID = 'INVALID';
  protected $collection_key = 'networkTags';
  /**
   * Output only. Timestamp when this NotebookRuntime was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataPersistentDiskSpecType = GoogleCloudAiplatformV1PersistentDiskSpec::class;
  protected $dataPersistentDiskSpecDataType = '';
  /**
   * The description of the NotebookRuntime.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the NotebookRuntime. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  protected $eucConfigType = GoogleCloudAiplatformV1NotebookEucConfig::class;
  protected $eucConfigDataType = '';
  /**
   * Output only. Timestamp when this NotebookRuntime will be expired: 1. System
   * Predefined NotebookRuntime: 24 hours after creation. After expiration,
   * system predifined runtime will be deleted. 2. User created NotebookRuntime:
   * 6 months after last upgrade. After expiration, user created runtime will be
   * stopped and allowed for upgrade.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * Output only. The health state of the NotebookRuntime.
   *
   * @var string
   */
  public $healthState;
  protected $idleShutdownConfigType = GoogleCloudAiplatformV1NotebookIdleShutdownConfig::class;
  protected $idleShutdownConfigDataType = '';
  /**
   * Output only. Whether NotebookRuntime is upgradable.
   *
   * @var bool
   */
  public $isUpgradable;
  /**
   * The labels with user-defined metadata to organize your NotebookRuntime.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. No more than
   * 64 user labels can be associated with one NotebookRuntime (System labels
   * are excluded). See https://goo.gl/xmQnxf for more information and examples
   * of labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable. Following system labels
   * exist for NotebookRuntime: *
   * "aiplatform.googleapis.com/notebook_runtime_gce_instance_id": output only,
   * its value is the Compute Engine instance id. *
   * "aiplatform.googleapis.com/colab_enterprise_entry_service": its value is
   * either "bigquery" or "vertex"; if absent, it should be "vertex". This is to
   * describe the entry service, either BigQuery or Vertex.
   *
   * @var string[]
   */
  public $labels;
  protected $machineSpecType = GoogleCloudAiplatformV1MachineSpec::class;
  protected $machineSpecDataType = '';
  /**
   * Output only. The resource name of the NotebookRuntime.
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
  protected $notebookRuntimeTemplateRefType = GoogleCloudAiplatformV1NotebookRuntimeTemplateRef::class;
  protected $notebookRuntimeTemplateRefDataType = '';
  /**
   * Output only. The type of the notebook runtime.
   *
   * @var string
   */
  public $notebookRuntimeType;
  /**
   * Output only. The proxy endpoint used to access the NotebookRuntime.
   *
   * @var string
   */
  public $proxyUri;
  protected $reservationAffinityType = GoogleCloudAiplatformV1NotebookReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * Output only. The runtime (instance) state of the NotebookRuntime.
   *
   * @var string
   */
  public $runtimeState;
  /**
   * Required. The user email of the NotebookRuntime.
   *
   * @var string
   */
  public $runtimeUser;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Deprecated: This field is no longer used and the "Vertex AI
   * Notebook Service Account" (service-PROJECT_NUMBER@gcp-sa-aiplatform-
   * vm.iam.gserviceaccount.com) is used for the runtime workload identity. See
   * https://cloud.google.com/iam/docs/service-agents#vertex-ai-notebook-
   * service-account for more details. The service account that the
   * NotebookRuntime workload runs as.
   *
   * @var string
   */
  public $serviceAccount;
  protected $shieldedVmConfigType = GoogleCloudAiplatformV1ShieldedVmConfig::class;
  protected $shieldedVmConfigDataType = '';
  protected $softwareConfigType = GoogleCloudAiplatformV1NotebookSoftwareConfig::class;
  protected $softwareConfigDataType = '';
  /**
   * Output only. Timestamp when this NotebookRuntime was most recently updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The VM os image version of NotebookRuntime.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Timestamp when this NotebookRuntime was created.
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
   * Output only. The specification of persistent disk attached to the notebook
   * runtime as data disk storage.
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
   * The description of the NotebookRuntime.
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
   * Required. The display name of the NotebookRuntime. The name can be up to
   * 128 characters long and can consist of any UTF-8 characters.
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
   * Output only. Customer-managed encryption key spec for the notebook runtime.
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
   * Output only. EUC configuration of the notebook runtime.
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
   * Output only. Timestamp when this NotebookRuntime will be expired: 1. System
   * Predefined NotebookRuntime: 24 hours after creation. After expiration,
   * system predifined runtime will be deleted. 2. User created NotebookRuntime:
   * 6 months after last upgrade. After expiration, user created runtime will be
   * stopped and allowed for upgrade.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Output only. The health state of the NotebookRuntime.
   *
   * Accepted values: HEALTH_STATE_UNSPECIFIED, HEALTHY, UNHEALTHY
   *
   * @param self::HEALTH_STATE_* $healthState
   */
  public function setHealthState($healthState)
  {
    $this->healthState = $healthState;
  }
  /**
   * @return self::HEALTH_STATE_*
   */
  public function getHealthState()
  {
    return $this->healthState;
  }
  /**
   * Output only. The idle shutdown configuration of the notebook runtime.
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
   * Output only. Whether NotebookRuntime is upgradable.
   *
   * @param bool $isUpgradable
   */
  public function setIsUpgradable($isUpgradable)
  {
    $this->isUpgradable = $isUpgradable;
  }
  /**
   * @return bool
   */
  public function getIsUpgradable()
  {
    return $this->isUpgradable;
  }
  /**
   * The labels with user-defined metadata to organize your NotebookRuntime.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. No more than
   * 64 user labels can be associated with one NotebookRuntime (System labels
   * are excluded). See https://goo.gl/xmQnxf for more information and examples
   * of labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable. Following system labels
   * exist for NotebookRuntime: *
   * "aiplatform.googleapis.com/notebook_runtime_gce_instance_id": output only,
   * its value is the Compute Engine instance id. *
   * "aiplatform.googleapis.com/colab_enterprise_entry_service": its value is
   * either "bigquery" or "vertex"; if absent, it should be "vertex". This is to
   * describe the entry service, either BigQuery or Vertex.
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
   * Output only. The specification of a single machine used by the notebook
   * runtime.
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
   * Output only. The resource name of the NotebookRuntime.
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
   * Output only. Network spec of the notebook runtime.
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
   * Output only. The pointer to NotebookRuntimeTemplate this NotebookRuntime is
   * created from.
   *
   * @param GoogleCloudAiplatformV1NotebookRuntimeTemplateRef $notebookRuntimeTemplateRef
   */
  public function setNotebookRuntimeTemplateRef(GoogleCloudAiplatformV1NotebookRuntimeTemplateRef $notebookRuntimeTemplateRef)
  {
    $this->notebookRuntimeTemplateRef = $notebookRuntimeTemplateRef;
  }
  /**
   * @return GoogleCloudAiplatformV1NotebookRuntimeTemplateRef
   */
  public function getNotebookRuntimeTemplateRef()
  {
    return $this->notebookRuntimeTemplateRef;
  }
  /**
   * Output only. The type of the notebook runtime.
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
   * Output only. The proxy endpoint used to access the NotebookRuntime.
   *
   * @param string $proxyUri
   */
  public function setProxyUri($proxyUri)
  {
    $this->proxyUri = $proxyUri;
  }
  /**
   * @return string
   */
  public function getProxyUri()
  {
    return $this->proxyUri;
  }
  /**
   * Output only. Reservation Affinity of the notebook runtime.
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
   * Output only. The runtime (instance) state of the NotebookRuntime.
   *
   * Accepted values: RUNTIME_STATE_UNSPECIFIED, RUNNING, BEING_STARTED,
   * BEING_STOPPED, STOPPED, BEING_UPGRADED, ERROR, INVALID
   *
   * @param self::RUNTIME_STATE_* $runtimeState
   */
  public function setRuntimeState($runtimeState)
  {
    $this->runtimeState = $runtimeState;
  }
  /**
   * @return self::RUNTIME_STATE_*
   */
  public function getRuntimeState()
  {
    return $this->runtimeState;
  }
  /**
   * Required. The user email of the NotebookRuntime.
   *
   * @param string $runtimeUser
   */
  public function setRuntimeUser($runtimeUser)
  {
    $this->runtimeUser = $runtimeUser;
  }
  /**
   * @return string
   */
  public function getRuntimeUser()
  {
    return $this->runtimeUser;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
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
   * Output only. Deprecated: This field is no longer used and the "Vertex AI
   * Notebook Service Account" (service-PROJECT_NUMBER@gcp-sa-aiplatform-
   * vm.iam.gserviceaccount.com) is used for the runtime workload identity. See
   * https://cloud.google.com/iam/docs/service-agents#vertex-ai-notebook-
   * service-account for more details. The service account that the
   * NotebookRuntime workload runs as.
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
   * Output only. Runtime Shielded VM spec.
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
   * Output only. Software config of the notebook runtime.
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
   * Output only. Timestamp when this NotebookRuntime was most recently updated.
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
   * Output only. The VM os image version of NotebookRuntime.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookRuntime::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookRuntime');
