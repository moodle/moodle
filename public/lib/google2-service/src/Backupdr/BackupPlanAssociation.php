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

class BackupPlanAssociation extends \Google\Collection
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
  protected $collection_key = 'rulesConfigInfo';
  /**
   * Required. Resource name of backup plan which needs to be applied on
   * workload. Format:
   * projects/{project}/locations/{location}/backupPlans/{backupPlanId}
   *
   * @var string
   */
  public $backupPlan;
  /**
   * Output only. The user friendly revision ID of the `BackupPlanRevision`.
   * Example: v0, v1, v2, etc.
   *
   * @var string
   */
  public $backupPlanRevisionId;
  /**
   * Output only. The resource id of the `BackupPlanRevision`. Format: `projects
   * /{project}/locations/{location}/backupPlans/{backup_plan}/revisions/{revisi
   * on_id}`
   *
   * @var string
   */
  public $backupPlanRevisionName;
  protected $cloudSqlInstanceBackupPlanAssociationPropertiesType = CloudSqlInstanceBackupPlanAssociationProperties::class;
  protected $cloudSqlInstanceBackupPlanAssociationPropertiesDataType = '';
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Resource name of data source which will be used as storage
   * location for backups taken. Format : projects/{project}/locations/{location
   * }/backupVaults/{backupvault}/dataSources/{datasource}
   *
   * @var string
   */
  public $dataSource;
  /**
   * Output only. Identifier. The resource name of BackupPlanAssociation in
   * below format Format : projects/{project}/locations/{location}/backupPlanAss
   * ociations/{backupPlanAssociationId}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Resource name of workload on which the backup plan is
   * applied. The format can either be the resource name (e.g., "projects/my-
   * project/zones/us-central1-a/instances/my-instance") or the full resource
   * URI (e.g., "https://www.googleapis.com/compute/v1/projects/my-
   * project/zones/us-central1-a/instances/my-instance").
   *
   * @var string
   */
  public $resource;
  /**
   * Required. Immutable. Resource type of workload on which backupplan is
   * applied
   *
   * @var string
   */
  public $resourceType;
  protected $rulesConfigInfoType = RuleConfigInfo::class;
  protected $rulesConfigInfoDataType = 'array';
  /**
   * Output only. The BackupPlanAssociation resource state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Resource name of backup plan which needs to be applied on
   * workload. Format:
   * projects/{project}/locations/{location}/backupPlans/{backupPlanId}
   *
   * @param string $backupPlan
   */
  public function setBackupPlan($backupPlan)
  {
    $this->backupPlan = $backupPlan;
  }
  /**
   * @return string
   */
  public function getBackupPlan()
  {
    return $this->backupPlan;
  }
  /**
   * Output only. The user friendly revision ID of the `BackupPlanRevision`.
   * Example: v0, v1, v2, etc.
   *
   * @param string $backupPlanRevisionId
   */
  public function setBackupPlanRevisionId($backupPlanRevisionId)
  {
    $this->backupPlanRevisionId = $backupPlanRevisionId;
  }
  /**
   * @return string
   */
  public function getBackupPlanRevisionId()
  {
    return $this->backupPlanRevisionId;
  }
  /**
   * Output only. The resource id of the `BackupPlanRevision`. Format: `projects
   * /{project}/locations/{location}/backupPlans/{backup_plan}/revisions/{revisi
   * on_id}`
   *
   * @param string $backupPlanRevisionName
   */
  public function setBackupPlanRevisionName($backupPlanRevisionName)
  {
    $this->backupPlanRevisionName = $backupPlanRevisionName;
  }
  /**
   * @return string
   */
  public function getBackupPlanRevisionName()
  {
    return $this->backupPlanRevisionName;
  }
  /**
   * Output only. Cloud SQL instance's backup plan association properties.
   *
   * @param CloudSqlInstanceBackupPlanAssociationProperties $cloudSqlInstanceBackupPlanAssociationProperties
   */
  public function setCloudSqlInstanceBackupPlanAssociationProperties(CloudSqlInstanceBackupPlanAssociationProperties $cloudSqlInstanceBackupPlanAssociationProperties)
  {
    $this->cloudSqlInstanceBackupPlanAssociationProperties = $cloudSqlInstanceBackupPlanAssociationProperties;
  }
  /**
   * @return CloudSqlInstanceBackupPlanAssociationProperties
   */
  public function getCloudSqlInstanceBackupPlanAssociationProperties()
  {
    return $this->cloudSqlInstanceBackupPlanAssociationProperties;
  }
  /**
   * Output only. The time when the instance was created.
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
   * Output only. Resource name of data source which will be used as storage
   * location for backups taken. Format : projects/{project}/locations/{location
   * }/backupVaults/{backupvault}/dataSources/{datasource}
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Output only. Identifier. The resource name of BackupPlanAssociation in
   * below format Format : projects/{project}/locations/{location}/backupPlanAss
   * ociations/{backupPlanAssociationId}
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
   * Required. Immutable. Resource name of workload on which the backup plan is
   * applied. The format can either be the resource name (e.g., "projects/my-
   * project/zones/us-central1-a/instances/my-instance") or the full resource
   * URI (e.g., "https://www.googleapis.com/compute/v1/projects/my-
   * project/zones/us-central1-a/instances/my-instance").
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Required. Immutable. Resource type of workload on which backupplan is
   * applied
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
   * Output only. The config info related to backup rules.
   *
   * @param RuleConfigInfo[] $rulesConfigInfo
   */
  public function setRulesConfigInfo($rulesConfigInfo)
  {
    $this->rulesConfigInfo = $rulesConfigInfo;
  }
  /**
   * @return RuleConfigInfo[]
   */
  public function getRulesConfigInfo()
  {
    return $this->rulesConfigInfo;
  }
  /**
   * Output only. The BackupPlanAssociation resource state.
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
   * Output only. The time when the instance was updated.
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
class_alias(BackupPlanAssociation::class, 'Google_Service_Backupdr_BackupPlanAssociation');
