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

namespace Google\Service\CloudRedis;

class PersistenceConfig extends \Google\Model
{
  /**
   * Not set.
   */
  public const PERSISTENCE_MODE_PERSISTENCE_MODE_UNSPECIFIED = 'PERSISTENCE_MODE_UNSPECIFIED';
  /**
   * Persistence is disabled for the instance, and any existing snapshots are
   * deleted.
   */
  public const PERSISTENCE_MODE_DISABLED = 'DISABLED';
  /**
   * RDB based Persistence is enabled.
   */
  public const PERSISTENCE_MODE_RDB = 'RDB';
  /**
   * Not set.
   */
  public const RDB_SNAPSHOT_PERIOD_SNAPSHOT_PERIOD_UNSPECIFIED = 'SNAPSHOT_PERIOD_UNSPECIFIED';
  /**
   * Snapshot every 1 hour.
   */
  public const RDB_SNAPSHOT_PERIOD_ONE_HOUR = 'ONE_HOUR';
  /**
   * Snapshot every 6 hours.
   */
  public const RDB_SNAPSHOT_PERIOD_SIX_HOURS = 'SIX_HOURS';
  /**
   * Snapshot every 12 hours.
   */
  public const RDB_SNAPSHOT_PERIOD_TWELVE_HOURS = 'TWELVE_HOURS';
  /**
   * Snapshot every 24 hours.
   */
  public const RDB_SNAPSHOT_PERIOD_TWENTY_FOUR_HOURS = 'TWENTY_FOUR_HOURS';
  /**
   * Optional. Controls whether Persistence features are enabled. If not
   * provided, the existing value will be used.
   *
   * @var string
   */
  public $persistenceMode;
  /**
   * Output only. The next time that a snapshot attempt is scheduled to occur.
   *
   * @var string
   */
  public $rdbNextSnapshotTime;
  /**
   * Optional. Period between RDB snapshots. Snapshots will be attempted every
   * period starting from the provided snapshot start time. For example, a start
   * time of 01/01/2033 06:45 and SIX_HOURS snapshot period will do nothing
   * until 01/01/2033, and then trigger snapshots every day at 06:45, 12:45,
   * 18:45, and 00:45 the next day, and so on. If not provided,
   * TWENTY_FOUR_HOURS will be used as default.
   *
   * @var string
   */
  public $rdbSnapshotPeriod;
  /**
   * Optional. Date and time that the first snapshot was/will be attempted, and
   * to which future snapshots will be aligned. If not provided, the current
   * time will be used.
   *
   * @var string
   */
  public $rdbSnapshotStartTime;

  /**
   * Optional. Controls whether Persistence features are enabled. If not
   * provided, the existing value will be used.
   *
   * Accepted values: PERSISTENCE_MODE_UNSPECIFIED, DISABLED, RDB
   *
   * @param self::PERSISTENCE_MODE_* $persistenceMode
   */
  public function setPersistenceMode($persistenceMode)
  {
    $this->persistenceMode = $persistenceMode;
  }
  /**
   * @return self::PERSISTENCE_MODE_*
   */
  public function getPersistenceMode()
  {
    return $this->persistenceMode;
  }
  /**
   * Output only. The next time that a snapshot attempt is scheduled to occur.
   *
   * @param string $rdbNextSnapshotTime
   */
  public function setRdbNextSnapshotTime($rdbNextSnapshotTime)
  {
    $this->rdbNextSnapshotTime = $rdbNextSnapshotTime;
  }
  /**
   * @return string
   */
  public function getRdbNextSnapshotTime()
  {
    return $this->rdbNextSnapshotTime;
  }
  /**
   * Optional. Period between RDB snapshots. Snapshots will be attempted every
   * period starting from the provided snapshot start time. For example, a start
   * time of 01/01/2033 06:45 and SIX_HOURS snapshot period will do nothing
   * until 01/01/2033, and then trigger snapshots every day at 06:45, 12:45,
   * 18:45, and 00:45 the next day, and so on. If not provided,
   * TWENTY_FOUR_HOURS will be used as default.
   *
   * Accepted values: SNAPSHOT_PERIOD_UNSPECIFIED, ONE_HOUR, SIX_HOURS,
   * TWELVE_HOURS, TWENTY_FOUR_HOURS
   *
   * @param self::RDB_SNAPSHOT_PERIOD_* $rdbSnapshotPeriod
   */
  public function setRdbSnapshotPeriod($rdbSnapshotPeriod)
  {
    $this->rdbSnapshotPeriod = $rdbSnapshotPeriod;
  }
  /**
   * @return self::RDB_SNAPSHOT_PERIOD_*
   */
  public function getRdbSnapshotPeriod()
  {
    return $this->rdbSnapshotPeriod;
  }
  /**
   * Optional. Date and time that the first snapshot was/will be attempted, and
   * to which future snapshots will be aligned. If not provided, the current
   * time will be used.
   *
   * @param string $rdbSnapshotStartTime
   */
  public function setRdbSnapshotStartTime($rdbSnapshotStartTime)
  {
    $this->rdbSnapshotStartTime = $rdbSnapshotStartTime;
  }
  /**
   * @return string
   */
  public function getRdbSnapshotStartTime()
  {
    return $this->rdbSnapshotStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersistenceConfig::class, 'Google_Service_CloudRedis_PersistenceConfig');
