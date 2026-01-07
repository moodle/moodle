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

class AutonomousDatabaseStandbySummary extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the Autonomous Database is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the Autonomous Database is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the Autonomous Database is in stopping state.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * Indicates that the Autonomous Database is in stopped state.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Indicates that the Autonomous Database is in starting state.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * Indicates that the Autonomous Database is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the Autonomous Database is in terminated state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the Autonomous Database is in unavailable state.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Indicates that the Autonomous Database restore is in progress.
   */
  public const STATE_RESTORE_IN_PROGRESS = 'RESTORE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database failed to restore.
   */
  public const STATE_RESTORE_FAILED = 'RESTORE_FAILED';
  /**
   * Indicates that the Autonomous Database backup is in progress.
   */
  public const STATE_BACKUP_IN_PROGRESS = 'BACKUP_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database scale is in progress.
   */
  public const STATE_SCALE_IN_PROGRESS = 'SCALE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is available but needs attention
   * state.
   */
  public const STATE_AVAILABLE_NEEDS_ATTENTION = 'AVAILABLE_NEEDS_ATTENTION';
  /**
   * Indicates that the Autonomous Database is in updating state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the Autonomous Database's maintenance is in progress state.
   */
  public const STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is in restarting state.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * Indicates that the Autonomous Database is in recreating state.
   */
  public const STATE_RECREATING = 'RECREATING';
  /**
   * Indicates that the Autonomous Database's role change is in progress state.
   */
  public const STATE_ROLE_CHANGE_IN_PROGRESS = 'ROLE_CHANGE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is in upgrading state.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * Indicates that the Autonomous Database is in inaccessible state.
   */
  public const STATE_INACCESSIBLE = 'INACCESSIBLE';
  /**
   * Indicates that the Autonomous Database is in standby state.
   */
  public const STATE_STANDBY = 'STANDBY';
  /**
   * Output only. The date and time the Autonomous Data Guard role was switched
   * for the standby Autonomous Database.
   *
   * @var string
   */
  public $dataGuardRoleChangedTime;
  /**
   * Output only. The date and time the Disaster Recovery role was switched for
   * the standby Autonomous Database.
   *
   * @var string
   */
  public $disasterRecoveryRoleChangedTime;
  /**
   * Output only. The amount of time, in seconds, that the data of the standby
   * database lags in comparison to the data of the primary database.
   *
   * @var string
   */
  public $lagTimeDuration;
  /**
   * Output only. The additional details about the current lifecycle state of
   * the Autonomous Database.
   *
   * @var string
   */
  public $lifecycleDetails;
  /**
   * Output only. The current lifecycle state of the Autonomous Database.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The date and time the Autonomous Data Guard role was switched
   * for the standby Autonomous Database.
   *
   * @param string $dataGuardRoleChangedTime
   */
  public function setDataGuardRoleChangedTime($dataGuardRoleChangedTime)
  {
    $this->dataGuardRoleChangedTime = $dataGuardRoleChangedTime;
  }
  /**
   * @return string
   */
  public function getDataGuardRoleChangedTime()
  {
    return $this->dataGuardRoleChangedTime;
  }
  /**
   * Output only. The date and time the Disaster Recovery role was switched for
   * the standby Autonomous Database.
   *
   * @param string $disasterRecoveryRoleChangedTime
   */
  public function setDisasterRecoveryRoleChangedTime($disasterRecoveryRoleChangedTime)
  {
    $this->disasterRecoveryRoleChangedTime = $disasterRecoveryRoleChangedTime;
  }
  /**
   * @return string
   */
  public function getDisasterRecoveryRoleChangedTime()
  {
    return $this->disasterRecoveryRoleChangedTime;
  }
  /**
   * Output only. The amount of time, in seconds, that the data of the standby
   * database lags in comparison to the data of the primary database.
   *
   * @param string $lagTimeDuration
   */
  public function setLagTimeDuration($lagTimeDuration)
  {
    $this->lagTimeDuration = $lagTimeDuration;
  }
  /**
   * @return string
   */
  public function getLagTimeDuration()
  {
    return $this->lagTimeDuration;
  }
  /**
   * Output only. The additional details about the current lifecycle state of
   * the Autonomous Database.
   *
   * @param string $lifecycleDetails
   */
  public function setLifecycleDetails($lifecycleDetails)
  {
    $this->lifecycleDetails = $lifecycleDetails;
  }
  /**
   * @return string
   */
  public function getLifecycleDetails()
  {
    return $this->lifecycleDetails;
  }
  /**
   * Output only. The current lifecycle state of the Autonomous Database.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, STOPPING,
   * STOPPED, STARTING, TERMINATING, TERMINATED, UNAVAILABLE,
   * RESTORE_IN_PROGRESS, RESTORE_FAILED, BACKUP_IN_PROGRESS, SCALE_IN_PROGRESS,
   * AVAILABLE_NEEDS_ATTENTION, UPDATING, MAINTENANCE_IN_PROGRESS, RESTARTING,
   * RECREATING, ROLE_CHANGE_IN_PROGRESS, UPGRADING, INACCESSIBLE, STANDBY
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
class_alias(AutonomousDatabaseStandbySummary::class, 'Google_Service_OracleDatabase_AutonomousDatabaseStandbySummary');
