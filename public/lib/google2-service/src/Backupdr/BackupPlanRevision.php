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

class BackupPlanRevision extends \Google\Model
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
  protected $backupPlanSnapshotType = BackupPlan::class;
  protected $backupPlanSnapshotDataType = '';
  /**
   * Output only. The timestamp that the revision was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Identifier. The resource name of the `BackupPlanRevision`.
   * Format: `projects/{project}/locations/{location}/backupPlans/{backup_plan}/
   * revisions/{revision}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The user friendly revision ID of the `BackupPlanRevision`.
   * Example: v0, v1, v2, etc.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Output only. Resource State
   *
   * @var string
   */
  public $state;

  /**
   * The Backup Plan being encompassed by this revision.
   *
   * @param BackupPlan $backupPlanSnapshot
   */
  public function setBackupPlanSnapshot(BackupPlan $backupPlanSnapshot)
  {
    $this->backupPlanSnapshot = $backupPlanSnapshot;
  }
  /**
   * @return BackupPlan
   */
  public function getBackupPlanSnapshot()
  {
    return $this->backupPlanSnapshot;
  }
  /**
   * Output only. The timestamp that the revision was created.
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
   * Output only. Identifier. The resource name of the `BackupPlanRevision`.
   * Format: `projects/{project}/locations/{location}/backupPlans/{backup_plan}/
   * revisions/{revision}`
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
   * Output only. Resource State
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, INACTIVE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupPlanRevision::class, 'Google_Service_Backupdr_BackupPlanRevision');
