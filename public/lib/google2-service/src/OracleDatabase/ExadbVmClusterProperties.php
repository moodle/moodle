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

namespace Google\Service\OracleDatabase;

class ExadbVmClusterProperties extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const LICENSE_MODEL_LICENSE_MODEL_UNSPECIFIED = 'LICENSE_MODEL_UNSPECIFIED';
  /**
   * Default is license included.
   */
  public const LICENSE_MODEL_LICENSE_INCLUDED = 'LICENSE_INCLUDED';
  /**
   * Bring your own license.
   */
  public const LICENSE_MODEL_BRING_YOUR_OWN_LICENSE = 'BRING_YOUR_OWN_LICENSE';
  /**
   * Default unspecified value.
   */
  public const LIFECYCLE_STATE_EXADB_VM_CLUSTER_LIFECYCLE_STATE_UNSPECIFIED = 'EXADB_VM_CLUSTER_LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const LIFECYCLE_STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const LIFECYCLE_STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in updating state.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const LIFECYCLE_STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in terminated state.
   */
  public const LIFECYCLE_STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  /**
   * Indicates that the resource is in maintenance in progress state.
   */
  public const LIFECYCLE_STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  /**
   * Default unspecified value.
   */
  public const SHAPE_ATTRIBUTE_SHAPE_ATTRIBUTE_UNSPECIFIED = 'SHAPE_ATTRIBUTE_UNSPECIFIED';
  /**
   * Indicates that the resource is in smart storage.
   */
  public const SHAPE_ATTRIBUTE_SMART_STORAGE = 'SMART_STORAGE';
  /**
   * Indicates that the resource is in block storage.
   */
  public const SHAPE_ATTRIBUTE_BLOCK_STORAGE = 'BLOCK_STORAGE';
  protected $collection_key = 'sshPublicKeys';
  /**
   * Optional. Immutable. The number of additional ECPUs per node for an Exadata
   * VM cluster on exascale infrastructure.
   *
   * @var int
   */
  public $additionalEcpuCountPerNode;
  /**
   * Optional. Immutable. The cluster name for Exascale vm cluster. The cluster
   * name must begin with an alphabetic character and may contain hyphens(-) but
   * can not contain underscores(_). It should be not more than 11 characters
   * and is not case sensitive. OCI Cluster name.
   *
   * @var string
   */
  public $clusterName;
  protected $dataCollectionOptionsType = DataCollectionOptionsCommon::class;
  protected $dataCollectionOptionsDataType = '';
  /**
   * Required. Immutable. The number of ECPUs enabled per node for an exadata vm
   * cluster on exascale infrastructure.
   *
   * @var int
   */
  public $enabledEcpuCountPerNode;
  /**
   * Required. Immutable. The name of ExascaleDbStorageVault associated with the
   * ExadbVmCluster. It can refer to an existing ExascaleDbStorageVault. Or a
   * new one can be created during the ExadbVmCluster creation (requires
   * storage_vault_properties to be set). Format: projects/{project}/locations/{
   * location}/exascaleDbStorageVaults/{exascale_db_storage_vault}
   *
   * @var string
   */
  public $exascaleDbStorageVault;
  /**
   * Output only. The Oracle Grid Infrastructure (GI) software version.
   *
   * @var string
   */
  public $giVersion;
  /**
   * Required. Immutable. Grid Infrastructure Version.
   *
   * @var string
   */
  public $gridImageId;
  /**
   * Output only. The hostname of the ExadbVmCluster.
   *
   * @var string
   */
  public $hostname;
  /**
   * Required. Immutable. Prefix for VM cluster host names.
   *
   * @var string
   */
  public $hostnamePrefix;
  /**
   * Optional. Immutable. The license type of the ExadbVmCluster.
   *
   * @var string
   */
  public $licenseModel;
  /**
   * Output only. State of the cluster.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Output only. Memory per VM (GB) (Read-only): Shows the amount of memory
   * allocated to each VM. Memory is calculated based on 2.75 GB per Total
   * ECPUs.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Required. The number of nodes/VMs in the ExadbVmCluster.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @var string
   */
  public $ociUri;
  /**
   * Optional. Immutable. SCAN listener port - TCP
   *
   * @var int
   */
  public $scanListenerPortTcp;
  /**
   * Required. Immutable. The shape attribute of the VM cluster. The type of
   * Exascale storage used for Exadata VM cluster. The default is SMART_STORAGE
   * which supports Oracle Database 23ai and later
   *
   * @var string
   */
  public $shapeAttribute;
  /**
   * Required. Immutable. The SSH public keys for the ExadbVmCluster.
   *
   * @var string[]
   */
  public $sshPublicKeys;
  protected $timeZoneType = TimeZone::class;
  protected $timeZoneDataType = '';
  protected $vmFileSystemStorageType = ExadbVmClusterStorageDetails::class;
  protected $vmFileSystemStorageDataType = '';

  /**
   * Optional. Immutable. The number of additional ECPUs per node for an Exadata
   * VM cluster on exascale infrastructure.
   *
   * @param int $additionalEcpuCountPerNode
   */
  public function setAdditionalEcpuCountPerNode($additionalEcpuCountPerNode)
  {
    $this->additionalEcpuCountPerNode = $additionalEcpuCountPerNode;
  }
  /**
   * @return int
   */
  public function getAdditionalEcpuCountPerNode()
  {
    return $this->additionalEcpuCountPerNode;
  }
  /**
   * Optional. Immutable. The cluster name for Exascale vm cluster. The cluster
   * name must begin with an alphabetic character and may contain hyphens(-) but
   * can not contain underscores(_). It should be not more than 11 characters
   * and is not case sensitive. OCI Cluster name.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Optional. Immutable. Indicates user preference for data collection options.
   *
   * @param DataCollectionOptionsCommon $dataCollectionOptions
   */
  public function setDataCollectionOptions(DataCollectionOptionsCommon $dataCollectionOptions)
  {
    $this->dataCollectionOptions = $dataCollectionOptions;
  }
  /**
   * @return DataCollectionOptionsCommon
   */
  public function getDataCollectionOptions()
  {
    return $this->dataCollectionOptions;
  }
  /**
   * Required. Immutable. The number of ECPUs enabled per node for an exadata vm
   * cluster on exascale infrastructure.
   *
   * @param int $enabledEcpuCountPerNode
   */
  public function setEnabledEcpuCountPerNode($enabledEcpuCountPerNode)
  {
    $this->enabledEcpuCountPerNode = $enabledEcpuCountPerNode;
  }
  /**
   * @return int
   */
  public function getEnabledEcpuCountPerNode()
  {
    return $this->enabledEcpuCountPerNode;
  }
  /**
   * Required. Immutable. The name of ExascaleDbStorageVault associated with the
   * ExadbVmCluster. It can refer to an existing ExascaleDbStorageVault. Or a
   * new one can be created during the ExadbVmCluster creation (requires
   * storage_vault_properties to be set). Format: projects/{project}/locations/{
   * location}/exascaleDbStorageVaults/{exascale_db_storage_vault}
   *
   * @param string $exascaleDbStorageVault
   */
  public function setExascaleDbStorageVault($exascaleDbStorageVault)
  {
    $this->exascaleDbStorageVault = $exascaleDbStorageVault;
  }
  /**
   * @return string
   */
  public function getExascaleDbStorageVault()
  {
    return $this->exascaleDbStorageVault;
  }
  /**
   * Output only. The Oracle Grid Infrastructure (GI) software version.
   *
   * @param string $giVersion
   */
  public function setGiVersion($giVersion)
  {
    $this->giVersion = $giVersion;
  }
  /**
   * @return string
   */
  public function getGiVersion()
  {
    return $this->giVersion;
  }
  /**
   * Required. Immutable. Grid Infrastructure Version.
   *
   * @param string $gridImageId
   */
  public function setGridImageId($gridImageId)
  {
    $this->gridImageId = $gridImageId;
  }
  /**
   * @return string
   */
  public function getGridImageId()
  {
    return $this->gridImageId;
  }
  /**
   * Output only. The hostname of the ExadbVmCluster.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Required. Immutable. Prefix for VM cluster host names.
   *
   * @param string $hostnamePrefix
   */
  public function setHostnamePrefix($hostnamePrefix)
  {
    $this->hostnamePrefix = $hostnamePrefix;
  }
  /**
   * @return string
   */
  public function getHostnamePrefix()
  {
    return $this->hostnamePrefix;
  }
  /**
   * Optional. Immutable. The license type of the ExadbVmCluster.
   *
   * Accepted values: LICENSE_MODEL_UNSPECIFIED, LICENSE_INCLUDED,
   * BRING_YOUR_OWN_LICENSE
   *
   * @param self::LICENSE_MODEL_* $licenseModel
   */
  public function setLicenseModel($licenseModel)
  {
    $this->licenseModel = $licenseModel;
  }
  /**
   * @return self::LICENSE_MODEL_*
   */
  public function getLicenseModel()
  {
    return $this->licenseModel;
  }
  /**
   * Output only. State of the cluster.
   *
   * Accepted values: EXADB_VM_CLUSTER_LIFECYCLE_STATE_UNSPECIFIED,
   * PROVISIONING, AVAILABLE, UPDATING, TERMINATING, TERMINATED, FAILED,
   * MAINTENANCE_IN_PROGRESS
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Output only. Memory per VM (GB) (Read-only): Shows the amount of memory
   * allocated to each VM. Memory is calculated based on 2.75 GB per Total
   * ECPUs.
   *
   * @param int $memorySizeGb
   */
  public function setMemorySizeGb($memorySizeGb)
  {
    $this->memorySizeGb = $memorySizeGb;
  }
  /**
   * @return int
   */
  public function getMemorySizeGb()
  {
    return $this->memorySizeGb;
  }
  /**
   * Required. The number of nodes/VMs in the ExadbVmCluster.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * Output only. Deep link to the OCI console to view this resource.
   *
   * @param string $ociUri
   */
  public function setOciUri($ociUri)
  {
    $this->ociUri = $ociUri;
  }
  /**
   * @return string
   */
  public function getOciUri()
  {
    return $this->ociUri;
  }
  /**
   * Optional. Immutable. SCAN listener port - TCP
   *
   * @param int $scanListenerPortTcp
   */
  public function setScanListenerPortTcp($scanListenerPortTcp)
  {
    $this->scanListenerPortTcp = $scanListenerPortTcp;
  }
  /**
   * @return int
   */
  public function getScanListenerPortTcp()
  {
    return $this->scanListenerPortTcp;
  }
  /**
   * Required. Immutable. The shape attribute of the VM cluster. The type of
   * Exascale storage used for Exadata VM cluster. The default is SMART_STORAGE
   * which supports Oracle Database 23ai and later
   *
   * Accepted values: SHAPE_ATTRIBUTE_UNSPECIFIED, SMART_STORAGE, BLOCK_STORAGE
   *
   * @param self::SHAPE_ATTRIBUTE_* $shapeAttribute
   */
  public function setShapeAttribute($shapeAttribute)
  {
    $this->shapeAttribute = $shapeAttribute;
  }
  /**
   * @return self::SHAPE_ATTRIBUTE_*
   */
  public function getShapeAttribute()
  {
    return $this->shapeAttribute;
  }
  /**
   * Required. Immutable. The SSH public keys for the ExadbVmCluster.
   *
   * @param string[] $sshPublicKeys
   */
  public function setSshPublicKeys($sshPublicKeys)
  {
    $this->sshPublicKeys = $sshPublicKeys;
  }
  /**
   * @return string[]
   */
  public function getSshPublicKeys()
  {
    return $this->sshPublicKeys;
  }
  /**
   * Optional. Immutable. The time zone of the ExadbVmCluster.
   *
   * @param TimeZone $timeZone
   */
  public function setTimeZone(TimeZone $timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return TimeZone
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Required. Immutable. Total storage details for the ExadbVmCluster.
   *
   * @param ExadbVmClusterStorageDetails $vmFileSystemStorage
   */
  public function setVmFileSystemStorage(ExadbVmClusterStorageDetails $vmFileSystemStorage)
  {
    $this->vmFileSystemStorage = $vmFileSystemStorage;
  }
  /**
   * @return ExadbVmClusterStorageDetails
   */
  public function getVmFileSystemStorage()
  {
    return $this->vmFileSystemStorage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExadbVmClusterProperties::class, 'Google_Service_OracleDatabase_ExadbVmClusterProperties');
