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

class BackupPlan extends \Google\Collection
{
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The resource has been created and is fully usable.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource has been created but is not usable.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The resource is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'supportedResourceTypes';
  protected $backupRulesType = BackupRule::class;
  protected $backupRulesDataType = 'array';
  /**
   * Required. Resource name of backup vault which will be used as storage
   * location for backups. Format:
   * projects/{project}/locations/{location}/backupVaults/{backupvault}
   *
   * @var string
   */
  public $backupVault;
  /**
   * Output only. The Google Cloud Platform Service Account to be used by the
   * BackupVault for taking backups. Specify the email address of the Backup
   * Vault Service Account.
   *
   * @var string
   */
  public $backupVaultServiceAccount;
  /**
   * Output only. When the `BackupPlan` was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the `BackupPlan` resource. The description
   * allows for additional details about `BackupPlan` and its use cases to be
   * provided. An example description is the following: "This is a backup plan
   * that performs a daily backup at 6pm and retains data for 3 months". The
   * description must be at most 2048 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. `etag` is returned from the service in the response. As a user of
   * the service, you may provide an etag value in this field to prevent stale
   * resources.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. This collection of key/value pairs allows for custom labels to be
   * supplied by the user. Example, {"tag": "Weekly"}.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Applicable only for CloudSQL resource_type. Configures how long
   * logs will be stored. It is defined in “days”. This value should be greater
   * than or equal to minimum enforced log retention duration of the backup
   * vault.
   *
   * @var string
   */
  public $logRetentionDays;
  /**
   * Optional. Optional field to configure the maximum number of days for which
   * a backup can be retained. This field is only applicable for on-demand
   * backups taken with custom retention value.
   *
   * @var int
   */
  public $maxCustomOnDemandRetentionDays;
  /**
   * Output only. Identifier. The resource name of the `BackupPlan`. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The resource type to which the `BackupPlan` will be applied.
   * Examples include, "compute.googleapis.com/Instance",
   * "sqladmin.googleapis.com/Instance", "alloydb.googleapis.com/Cluster",
   * "compute.googleapis.com/Disk".
   *
   * @var string
   */
  public $resourceType;
  /**
   * Output only. The user friendly revision ID of the `BackupPlanRevision`.
   * Example: v0, v1, v2, etc.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Output only. The resource id of the `BackupPlanRevision`. Format: `projects
   * /{project}/locations/{location}/backupPlans/{backup_plan}/revisions/{revisi
   * on_id}`
   *
   * @var string
   */
  public $revisionName;
  /**
   * Output only. The `State` for the `BackupPlan`.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. All resource types to which backupPlan can be applied.
   *
   * @var string[]
   */
  public $supportedResourceTypes;
  /**
   * Output only. When the `BackupPlan` was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The backup rules for this `BackupPlan`.
   *
   * @param BackupRule[] $backupRules
   */
  public function setBackupRules($backupRules)
  {
    $this->backupRules = $backupRules;
  }
  /**
   * @return BackupRule[]
   */
  public function getBackupRules()
  {
    return $this->backupRules;
  }
  /**
   * Required. Resource name of backup vault which will be used as storage
   * location for backups. Format:
   * projects/{project}/locations/{location}/backupVaults/{backupvault}
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
   * Output only. The Google Cloud Platform Service Account to be used by the
   * BackupVault for taking backups. Specify the email address of the Backup
   * Vault Service Account.
   *
   * @param string $backupVaultServiceAccount
   */
  public function setBackupVaultServiceAccount($backupVaultServiceAccount)
  {
    $this->backupVaultServiceAccount = $backupVaultServiceAccount;
  }
  /**
   * @return string
   */
  public function getBackupVaultServiceAccount()
  {
    return $this->backupVaultServiceAccount;
  }
  /**
   * Output only. When the `BackupPlan` was created.
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
   * Optional. The description of the `BackupPlan` resource. The description
   * allows for additional details about `BackupPlan` and its use cases to be
   * provided. An example description is the following: "This is a backup plan
   * that performs a daily backup at 6pm and retains data for 3 months". The
   * description must be at most 2048 characters.
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
   * Optional. `etag` is returned from the service in the response. As a user of
   * the service, you may provide an etag value in this field to prevent stale
   * resources.
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
   * Optional. This collection of key/value pairs allows for custom labels to be
   * supplied by the user. Example, {"tag": "Weekly"}.
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
   * Optional. Applicable only for CloudSQL resource_type. Configures how long
   * logs will be stored. It is defined in “days”. This value should be greater
   * than or equal to minimum enforced log retention duration of the backup
   * vault.
   *
   * @param string $logRetentionDays
   */
  public function setLogRetentionDays($logRetentionDays)
  {
    $this->logRetentionDays = $logRetentionDays;
  }
  /**
   * @return string
   */
  public function getLogRetentionDays()
  {
    return $this->logRetentionDays;
  }
  /**
   * Optional. Optional field to configure the maximum number of days for which
   * a backup can be retained. This field is only applicable for on-demand
   * backups taken with custom retention value.
   *
   * @param int $maxCustomOnDemandRetentionDays
   */
  public function setMaxCustomOnDemandRetentionDays($maxCustomOnDemandRetentionDays)
  {
    $this->maxCustomOnDemandRetentionDays = $maxCustomOnDemandRetentionDays;
  }
  /**
   * @return int
   */
  public function getMaxCustomOnDemandRetentionDays()
  {
    return $this->maxCustomOnDemandRetentionDays;
  }
  /**
   * Output only. Identifier. The resource name of the `BackupPlan`. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`
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
   * Required. The resource type to which the `BackupPlan` will be applied.
   * Examples include, "compute.googleapis.com/Instance",
   * "sqladmin.googleapis.com/Instance", "alloydb.googleapis.com/Cluster",
   * "compute.googleapis.com/Disk".
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Output only. The user friendly revision ID of the `BackupPlanRevision`.
   * Example: v0, v1, v2, etc.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. The resource id of the `BackupPlanRevision`. Format: `projects
   * /{project}/locations/{location}/backupPlans/{backup_plan}/revisions/{revisi
   * on_id}`
   *
   * @param string $revisionName
   */
  public function setRevisionName($revisionName)
  {
    $this->revisionName = $revisionName;
  }
  /**
   * @return string
   */
  public function getRevisionName()
  {
    return $this->revisionName;
  }
  /**
   * Output only. The `State` for the `BackupPlan`.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, INACTIVE,
   * UPDATING
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
   * Output only. All resource types to which backupPlan can be applied.
   *
   * @param string[] $supportedResourceTypes
   */
  public function setSupportedResourceTypes($supportedResourceTypes)
  {
    $this->supportedResourceTypes = $supportedResourceTypes;
  }
  /**
   * @return string[]
   */
  public function getSupportedResourceTypes()
  {
    return $this->supportedResourceTypes;
  }
  /**
   * Output only. When the `BackupPlan` was last updated.
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
class_alias(BackupPlan::class, 'Google_Service_Backupdr_BackupPlan');
