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

namespace Google\Service\CloudAlloyDBAdmin;

class StageStatus extends \Google\Model
{
  /**
   * Unspecified stage.
   */
  public const STAGE_STAGE_UNSPECIFIED = 'STAGE_UNSPECIFIED';
  /**
   * Pre-upgrade custom checks, not covered by pg_upgrade.
   */
  public const STAGE_ALLOYDB_PRECHECK = 'ALLOYDB_PRECHECK';
  /**
   * Pre-upgrade pg_upgrade checks.
   */
  public const STAGE_PG_UPGRADE_CHECK = 'PG_UPGRADE_CHECK';
  /**
   * Clone the original cluster.
   */
  public const STAGE_PREPARE_FOR_UPGRADE = 'PREPARE_FOR_UPGRADE';
  /**
   * Upgrade the primary instance(downtime).
   */
  public const STAGE_PRIMARY_INSTANCE_UPGRADE = 'PRIMARY_INSTANCE_UPGRADE';
  /**
   * This stage is read pool upgrade.
   */
  public const STAGE_READ_POOL_INSTANCES_UPGRADE = 'READ_POOL_INSTANCES_UPGRADE';
  /**
   * Rollback in case of critical failures.
   */
  public const STAGE_ROLLBACK = 'ROLLBACK';
  /**
   * Cleanup.
   */
  public const STAGE_CLEANUP = 'CLEANUP';
  /**
   * Unspecified status.
   */
  public const STATE_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const STATE_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const STATE_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const STATE_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  protected $readPoolInstancesUpgradeType = ReadPoolInstancesUpgradeStageStatus::class;
  protected $readPoolInstancesUpgradeDataType = '';
  /**
   * Upgrade stage.
   *
   * @var string
   */
  public $stage;
  /**
   * State of this stage.
   *
   * @var string
   */
  public $state;

  /**
   * Read pool instances upgrade metadata.
   *
   * @param ReadPoolInstancesUpgradeStageStatus $readPoolInstancesUpgrade
   */
  public function setReadPoolInstancesUpgrade(ReadPoolInstancesUpgradeStageStatus $readPoolInstancesUpgrade)
  {
    $this->readPoolInstancesUpgrade = $readPoolInstancesUpgrade;
  }
  /**
   * @return ReadPoolInstancesUpgradeStageStatus
   */
  public function getReadPoolInstancesUpgrade()
  {
    return $this->readPoolInstancesUpgrade;
  }
  /**
   * Upgrade stage.
   *
   * Accepted values: STAGE_UNSPECIFIED, ALLOYDB_PRECHECK, PG_UPGRADE_CHECK,
   * PREPARE_FOR_UPGRADE, PRIMARY_INSTANCE_UPGRADE, READ_POOL_INSTANCES_UPGRADE,
   * ROLLBACK, CLEANUP
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
  /**
   * State of this stage.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
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
class_alias(StageStatus::class, 'Google_Service_CloudAlloyDBAdmin_StageStatus');
