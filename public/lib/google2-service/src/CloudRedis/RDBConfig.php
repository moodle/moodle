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

class RDBConfig extends \Google\Model
{
  /**
   * Not set.
   */
  public const RDB_SNAPSHOT_PERIOD_SNAPSHOT_PERIOD_UNSPECIFIED = 'SNAPSHOT_PERIOD_UNSPECIFIED';
  /**
   * One hour.
   */
  public const RDB_SNAPSHOT_PERIOD_ONE_HOUR = 'ONE_HOUR';
  /**
   * Six hours.
   */
  public const RDB_SNAPSHOT_PERIOD_SIX_HOURS = 'SIX_HOURS';
  /**
   * Twelve hours.
   */
  public const RDB_SNAPSHOT_PERIOD_TWELVE_HOURS = 'TWELVE_HOURS';
  /**
   * Twenty four hours.
   */
  public const RDB_SNAPSHOT_PERIOD_TWENTY_FOUR_HOURS = 'TWENTY_FOUR_HOURS';
  /**
   * Optional. Period between RDB snapshots.
   *
   * @var string
   */
  public $rdbSnapshotPeriod;
  /**
   * Optional. The time that the first snapshot was/will be attempted, and to
   * which future snapshots will be aligned. If not provided, the current time
   * will be used.
   *
   * @var string
   */
  public $rdbSnapshotStartTime;

  /**
   * Optional. Period between RDB snapshots.
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
   * Optional. The time that the first snapshot was/will be attempted, and to
   * which future snapshots will be aligned. If not provided, the current time
   * will be used.
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
class_alias(RDBConfig::class, 'Google_Service_CloudRedis_RDBConfig');
