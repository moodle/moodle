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

class BackupConfigDetails extends \Google\Collection
{
  /**
   * Backup config state not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The config is in an active state protecting the resource
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The config is currently not protecting the resource. Either because it is
   * disabled or the owning project has been deleted without cleanup of the
   * actual resource.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The config still exists but because of some error state it is not
   * protecting the resource. Like the source project is deleted. For eg.
   * PlanAssociation, BackupPlan is deleted.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Backup config type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Backup config is Cloud SQL instance's automated backup config.
   */
  public const TYPE_CLOUD_SQL_INSTANCE_BACKUP_CONFIG = 'CLOUD_SQL_INSTANCE_BACKUP_CONFIG';
  /**
   * Backup config is Compute Engine Resource Policy.
   */
  public const TYPE_COMPUTE_ENGINE_RESOURCE_POLICY = 'COMPUTE_ENGINE_RESOURCE_POLICY';
  /**
   * Backup config is Backup and DR's Backup Plan.
   */
  public const TYPE_BACKUPDR_BACKUP_PLAN = 'BACKUPDR_BACKUP_PLAN';
  /**
   * Backup config is Backup and DR's Template.
   */
  public const TYPE_BACKUPDR_TEMPLATE = 'BACKUPDR_TEMPLATE';
  protected $collection_key = 'backupLocations';
  /**
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the resource that is applicable for
   * the backup configuration. Example: "//compute.googleapis.com/projects/{proj
   * ect}/zones/{zone}/instances/{instance}"
   *
   * @var string
   */
  public $applicableResource;
  /**
   * Output only. The full resource name of the backup config source resource.
   * For example, "//backupdr.googleapis.com/v1/projects/{project}/locations/{re
   * gion}/backupPlans/{backupplanId}" or "//compute.googleapis.com/projects/{pr
   * oject}/locations/{region}/resourcePolicies/{resourcePolicyId}".
   *
   * @var string
   */
  public $backupConfigSource;
  /**
   * Output only. The display name of the backup config source resource.
   *
   * @var string
   */
  public $backupConfigSourceDisplayName;
  protected $backupDrPlanConfigType = BackupDrPlanConfig::class;
  protected $backupDrPlanConfigDataType = '';
  protected $backupDrTemplateConfigType = BackupDrTemplateConfig::class;
  protected $backupDrTemplateConfigDataType = '';
  protected $backupLocationsType = BackupLocation::class;
  protected $backupLocationsDataType = 'array';
  /**
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the backup vault that will store
   * the backups generated through this backup configuration. Example: "//backup
   * dr.googleapis.com/v1/projects/{project}/locations/{region}/backupVaults/{ba
   * ckupvaultId}"
   *
   * @var string
   */
  public $backupVault;
  /**
   * Output only. Timestamp of the latest successful backup created via this
   * backup configuration.
   *
   * @var string
   */
  public $latestSuccessfulBackupTime;
  protected $pitrSettingsType = PitrSettings::class;
  protected $pitrSettingsDataType = '';
  /**
   * Output only. The state of the backup config resource.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The type of the backup config resource.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the resource that is applicable for
   * the backup configuration. Example: "//compute.googleapis.com/projects/{proj
   * ect}/zones/{zone}/instances/{instance}"
   *
   * @param string $applicableResource
   */
  public function setApplicableResource($applicableResource)
  {
    $this->applicableResource = $applicableResource;
  }
  /**
   * @return string
   */
  public function getApplicableResource()
  {
    return $this->applicableResource;
  }
  /**
   * Output only. The full resource name of the backup config source resource.
   * For example, "//backupdr.googleapis.com/v1/projects/{project}/locations/{re
   * gion}/backupPlans/{backupplanId}" or "//compute.googleapis.com/projects/{pr
   * oject}/locations/{region}/resourcePolicies/{resourcePolicyId}".
   *
   * @param string $backupConfigSource
   */
  public function setBackupConfigSource($backupConfigSource)
  {
    $this->backupConfigSource = $backupConfigSource;
  }
  /**
   * @return string
   */
  public function getBackupConfigSource()
  {
    return $this->backupConfigSource;
  }
  /**
   * Output only. The display name of the backup config source resource.
   *
   * @param string $backupConfigSourceDisplayName
   */
  public function setBackupConfigSourceDisplayName($backupConfigSourceDisplayName)
  {
    $this->backupConfigSourceDisplayName = $backupConfigSourceDisplayName;
  }
  /**
   * @return string
   */
  public function getBackupConfigSourceDisplayName()
  {
    return $this->backupConfigSourceDisplayName;
  }
  /**
   * Backup and DR's Backup Plan specific data.
   *
   * @param BackupDrPlanConfig $backupDrPlanConfig
   */
  public function setBackupDrPlanConfig(BackupDrPlanConfig $backupDrPlanConfig)
  {
    $this->backupDrPlanConfig = $backupDrPlanConfig;
  }
  /**
   * @return BackupDrPlanConfig
   */
  public function getBackupDrPlanConfig()
  {
    return $this->backupDrPlanConfig;
  }
  /**
   * Backup and DR's Template specific data.
   *
   * @param BackupDrTemplateConfig $backupDrTemplateConfig
   */
  public function setBackupDrTemplateConfig(BackupDrTemplateConfig $backupDrTemplateConfig)
  {
    $this->backupDrTemplateConfig = $backupDrTemplateConfig;
  }
  /**
   * @return BackupDrTemplateConfig
   */
  public function getBackupDrTemplateConfig()
  {
    return $this->backupDrTemplateConfig;
  }
  /**
   * The locations where the backups are to be stored.
   *
   * @param BackupLocation[] $backupLocations
   */
  public function setBackupLocations($backupLocations)
  {
    $this->backupLocations = $backupLocations;
  }
  /**
   * @return BackupLocation[]
   */
  public function getBackupLocations()
  {
    return $this->backupLocations;
  }
  /**
   * Output only. The [full resource name](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the backup vault that will store
   * the backups generated through this backup configuration. Example: "//backup
   * dr.googleapis.com/v1/projects/{project}/locations/{region}/backupVaults/{ba
   * ckupvaultId}"
   *
   * @param string $backupVault
   */
  public function setBackupVault($backupVault)
  {
    $this->backupVault = $backupVault;
  }
  /**
   * @return string
   */
  public function getBackupVault()
  {
    return $this->backupVault;
  }
  /**
   * Output only. Timestamp of the latest successful backup created via this
   * backup configuration.
   *
   * @param string $latestSuccessfulBackupTime
   */
  public function setLatestSuccessfulBackupTime($latestSuccessfulBackupTime)
  {
    $this->latestSuccessfulBackupTime = $latestSuccessfulBackupTime;
  }
  /**
   * @return string
   */
  public function getLatestSuccessfulBackupTime()
  {
    return $this->latestSuccessfulBackupTime;
  }
  /**
   * Output only. Point in time recovery settings of the backup configuration
   * resource.
   *
   * @param PitrSettings $pitrSettings
   */
  public function setPitrSettings(PitrSettings $pitrSettings)
  {
    $this->pitrSettings = $pitrSettings;
  }
  /**
   * @return PitrSettings
   */
  public function getPitrSettings()
  {
    return $this->pitrSettings;
  }
  /**
   * Output only. The state of the backup config resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE, ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The type of the backup config resource.
   *
   * Accepted values: TYPE_UNSPECIFIED, CLOUD_SQL_INSTANCE_BACKUP_CONFIG,
   * COMPUTE_ENGINE_RESOURCE_POLICY, BACKUPDR_BACKUP_PLAN, BACKUPDR_TEMPLATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupConfigDetails::class, 'Google_Service_Backupdr_BackupConfigDetails');
