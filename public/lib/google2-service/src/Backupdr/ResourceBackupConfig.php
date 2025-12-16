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

namespace Google\Service\Backupdr;

class ResourceBackupConfig extends \Google\Collection
{
  /**
   * Resource type not set.
   */
  public const TARGET_RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Cloud SQL instance.
   */
  public const TARGET_RESOURCE_TYPE_CLOUD_SQL_INSTANCE = 'CLOUD_SQL_INSTANCE';
  /**
   * Compute Engine VM.
   */
  public const TARGET_RESOURCE_TYPE_COMPUTE_ENGINE_VM = 'COMPUTE_ENGINE_VM';
  /**
   * Compute Engine Disk.
   */
  public const TARGET_RESOURCE_TYPE_COMPUTE_ENGINE_DISK = 'COMPUTE_ENGINE_DISK';
  /**
   * Compute Engine Regional Disk.
   */
  public const TARGET_RESOURCE_TYPE_COMPUTE_ENGINE_REGIONAL_DISK = 'COMPUTE_ENGINE_REGIONAL_DISK';
  protected $collection_key = 'backupConfigsDetails';
  protected $backupConfigsDetailsType = BackupConfigDetails::class;
  protected $backupConfigsDetailsDataType = 'array';
  /**
   * Output only. Whether the target resource is configured for backup. This is
   * true if the backup_configs_details is not empty.
   *
   * @var bool
   */
  public $backupConfigured;
  /**
   * Identifier. The resource name of the ResourceBackupConfig. Format:
   * projects/{project}/locations/{location}/resourceBackupConfigs/{uid}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the cloud resource that this
   * configuration applies to. Supported resource types are
   * ResourceBackupConfig.ResourceType.
   *
   * @var string
   */
  public $targetResource;
  /**
   * Output only. The human friendly name of the target resource.
   *
   * @var string
   */
  public $targetResourceDisplayName;
  /**
   * Labels associated with the target resource.
   *
   * @var string[]
   */
  public $targetResourceLabels;
  /**
   * Output only. The type of the target resource.
   *
   * @var string
   */
  public $targetResourceType;
  /**
   * Output only. The unique identifier of the resource backup config.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Whether the target resource is protected by a backup vault.
   * This is true if the backup_configs_details is not empty and any of the
   * ResourceBackupConfig.backup_configs_details has a backup configuration with
   * BackupConfigDetails.backup_vault set. set.
   *
   * @var bool
   */
  public $vaulted;

  /**
   * Backup configurations applying to the target resource, including those
   * targeting its related/child resources. For example, backup configuration
   * applicable to Compute Engine disks will be populated in this field for a
   * Compute Engine VM which has the disk associated.
   *
   * @param BackupConfigDetails[] $backupConfigsDetails
   */
  public function setBackupConfigsDetails($backupConfigsDetails)
  {
    $this->backupConfigsDetails = $backupConfigsDetails;
  }
  /**
   * @return BackupConfigDetails[]
   */
  public function getBackupConfigsDetails()
  {
    return $this->backupConfigsDetails;
  }
  /**
   * Output only. Whether the target resource is configured for backup. This is
   * true if the backup_configs_details is not empty.
   *
   * @param bool $backupConfigured
   */
  public function setBackupConfigured($backupConfigured)
  {
    $this->backupConfigured = $backupConfigured;
  }
  /**
   * @return bool
   */
  public function getBackupConfigured()
  {
    return $this->backupConfigured;
  }
  /**
   * Identifier. The resource name of the ResourceBackupConfig. Format:
   * projects/{project}/locations/{location}/resourceBackupConfigs/{uid}
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
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the cloud resource that this
   * configuration applies to. Supported resource types are
   * ResourceBackupConfig.ResourceType.
   *
   * @param string $targetResource
   */
  public function setTargetResource($targetResource)
  {
    $this->targetResource = $targetResource;
  }
  /**
   * @return string
   */
  public function getTargetResource()
  {
    return $this->targetResource;
  }
  /**
   * Output only. The human friendly name of the target resource.
   *
   * @param string $targetResourceDisplayName
   */
  public function setTargetResourceDisplayName($targetResourceDisplayName)
  {
    $this->targetResourceDisplayName = $targetResourceDisplayName;
  }
  /**
   * @return string
   */
  public function getTargetResourceDisplayName()
  {
    return $this->targetResourceDisplayName;
  }
  /**
   * Labels associated with the target resource.
   *
   * @param string[] $targetResourceLabels
   */
  public function setTargetResourceLabels($targetResourceLabels)
  {
    $this->targetResourceLabels = $targetResourceLabels;
  }
  /**
   * @return string[]
   */
  public function getTargetResourceLabels()
  {
    return $this->targetResourceLabels;
  }
  /**
   * Output only. The type of the target resource.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, CLOUD_SQL_INSTANCE,
   * COMPUTE_ENGINE_VM, COMPUTE_ENGINE_DISK, COMPUTE_ENGINE_REGIONAL_DISK
   *
   * @param self::TARGET_RESOURCE_TYPE_* $targetResourceType
   */
  public function setTargetResourceType($targetResourceType)
  {
    $this->targetResourceType = $targetResourceType;
  }
  /**
   * @return self::TARGET_RESOURCE_TYPE_*
   */
  public function getTargetResourceType()
  {
    return $this->targetResourceType;
  }
  /**
   * Output only. The unique identifier of the resource backup config.
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
   * Output only. Whether the target resource is protected by a backup vault.
   * This is true if the backup_configs_details is not empty and any of the
   * ResourceBackupConfig.backup_configs_details has a backup configuration with
   * BackupConfigDetails.backup_vault set. set.
   *
   * @param bool $vaulted
   */
  public function setVaulted($vaulted)
  {
    $this->vaulted = $vaulted;
  }
  /**
   * @return bool
   */
  public function getVaulted()
  {
    return $this->vaulted;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceBackupConfig::class, 'Google_Service_Backupdr_ResourceBackupConfig');
