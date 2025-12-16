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

class RuleConfigInfo extends \Google\Model
{
  /**
   * State not set.
   */
  public const LAST_BACKUP_STATE_LAST_BACKUP_STATE_UNSPECIFIED = 'LAST_BACKUP_STATE_UNSPECIFIED';
  /**
   * The first backup is pending.
   */
  public const LAST_BACKUP_STATE_FIRST_BACKUP_PENDING = 'FIRST_BACKUP_PENDING';
  /**
   * The most recent backup could not be run/failed because of the lack of
   * permissions.
   */
  public const LAST_BACKUP_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * The last backup operation succeeded.
   */
  public const LAST_BACKUP_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The last backup operation failed.
   */
  public const LAST_BACKUP_STATE_FAILED = 'FAILED';
  protected $lastBackupErrorType = Status::class;
  protected $lastBackupErrorDataType = '';
  /**
   * Output only. The last backup state for rule.
   *
   * @var string
   */
  public $lastBackupState;
  /**
   * Output only. The point in time when the last successful backup was captured
   * from the source.
   *
   * @var string
   */
  public $lastSuccessfulBackupConsistencyTime;
  /**
   * Output only. Backup Rule id fetched from backup plan.
   *
   * @var string
   */
  public $ruleId;

  /**
   * Output only. google.rpc.Status object to store the last backup error.
   *
   * @param Status $lastBackupError
   */
  public function setLastBackupError(Status $lastBackupError)
  {
    $this->lastBackupError = $lastBackupError;
  }
  /**
   * @return Status
   */
  public function getLastBackupError()
  {
    return $this->lastBackupError;
  }
  /**
   * Output only. The last backup state for rule.
   *
   * Accepted values: LAST_BACKUP_STATE_UNSPECIFIED, FIRST_BACKUP_PENDING,
   * PERMISSION_DENIED, SUCCEEDED, FAILED
   *
   * @param self::LAST_BACKUP_STATE_* $lastBackupState
   */
  public function setLastBackupState($lastBackupState)
  {
    $this->lastBackupState = $lastBackupState;
  }
  /**
   * @return self::LAST_BACKUP_STATE_*
   */
  public function getLastBackupState()
  {
    return $this->lastBackupState;
  }
  /**
   * Output only. The point in time when the last successful backup was captured
   * from the source.
   *
   * @param string $lastSuccessfulBackupConsistencyTime
   */
  public function setLastSuccessfulBackupConsistencyTime($lastSuccessfulBackupConsistencyTime)
  {
    $this->lastSuccessfulBackupConsistencyTime = $lastSuccessfulBackupConsistencyTime;
  }
  /**
   * @return string
   */
  public function getLastSuccessfulBackupConsistencyTime()
  {
    return $this->lastSuccessfulBackupConsistencyTime;
  }
  /**
   * Output only. Backup Rule id fetched from backup plan.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuleConfigInfo::class, 'Google_Service_Backupdr_RuleConfigInfo');
