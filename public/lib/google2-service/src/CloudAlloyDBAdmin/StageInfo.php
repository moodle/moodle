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

class StageInfo extends \Google\Model
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
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const STATUS_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const STATUS_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const STATUS_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  /**
   * logs_url is the URL for the logs associated with a stage if that stage has
   * logs. Right now, only three stages have logs: ALLOYDB_PRECHECK,
   * PG_UPGRADE_CHECK, PRIMARY_INSTANCE_UPGRADE.
   *
   * @var string
   */
  public $logsUrl;
  /**
   * The stage.
   *
   * @var string
   */
  public $stage;
  /**
   * Status of the stage.
   *
   * @var string
   */
  public $status;

  /**
   * logs_url is the URL for the logs associated with a stage if that stage has
   * logs. Right now, only three stages have logs: ALLOYDB_PRECHECK,
   * PG_UPGRADE_CHECK, PRIMARY_INSTANCE_UPGRADE.
   *
   * @param string $logsUrl
   */
  public function setLogsUrl($logsUrl)
  {
    $this->logsUrl = $logsUrl;
  }
  /**
   * @return string
   */
  public function getLogsUrl()
  {
    return $this->logsUrl;
  }
  /**
   * The stage.
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
   * Status of the stage.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageInfo::class, 'Google_Service_CloudAlloyDBAdmin_StageInfo');
