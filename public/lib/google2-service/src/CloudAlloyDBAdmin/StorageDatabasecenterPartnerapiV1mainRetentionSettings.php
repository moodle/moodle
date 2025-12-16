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

class StorageDatabasecenterPartnerapiV1mainRetentionSettings extends \Google\Model
{
  /**
   * Backup retention unit is unspecified, will be treated as COUNT.
   */
  public const RETENTION_UNIT_RETENTION_UNIT_UNSPECIFIED = 'RETENTION_UNIT_UNSPECIFIED';
  /**
   * Retention will be by count, eg. "retain the most recent 7 backups".
   */
  public const RETENTION_UNIT_COUNT = 'COUNT';
  /**
   * Retention will be by Time, eg. "retain backups till a specific time" i.e.
   * till 2024-05-01T00:00:00Z.
   */
  public const RETENTION_UNIT_TIME = 'TIME';
  /**
   * Retention will be by duration, eg. "retain the backups for 172800 seconds
   * (2 days)".
   */
  public const RETENTION_UNIT_DURATION = 'DURATION';
  /**
   * For rest of the other category
   */
  public const RETENTION_UNIT_RETENTION_UNIT_OTHER = 'RETENTION_UNIT_OTHER';
  /**
   * Duration based retention period i.e. 172800 seconds (2 days)
   *
   * @var string
   */
  public $durationBasedRetention;
  /**
   * @var int
   */
  public $quantityBasedRetention;
  /**
   * The unit that 'retained_backups' represents.
   *
   * @deprecated
   * @var string
   */
  public $retentionUnit;
  /**
   * @deprecated
   * @var string
   */
  public $timeBasedRetention;
  /**
   * Timestamp based retention period i.e. 2024-05-01T00:00:00Z
   *
   * @var string
   */
  public $timestampBasedRetentionTime;

  /**
   * Duration based retention period i.e. 172800 seconds (2 days)
   *
   * @param string $durationBasedRetention
   */
  public function setDurationBasedRetention($durationBasedRetention)
  {
    $this->durationBasedRetention = $durationBasedRetention;
  }
  /**
   * @return string
   */
  public function getDurationBasedRetention()
  {
    return $this->durationBasedRetention;
  }
  /**
   * @param int $quantityBasedRetention
   */
  public function setQuantityBasedRetention($quantityBasedRetention)
  {
    $this->quantityBasedRetention = $quantityBasedRetention;
  }
  /**
   * @return int
   */
  public function getQuantityBasedRetention()
  {
    return $this->quantityBasedRetention;
  }
  /**
   * The unit that 'retained_backups' represents.
   *
   * Accepted values: RETENTION_UNIT_UNSPECIFIED, COUNT, TIME, DURATION,
   * RETENTION_UNIT_OTHER
   *
   * @deprecated
   * @param self::RETENTION_UNIT_* $retentionUnit
   */
  public function setRetentionUnit($retentionUnit)
  {
    $this->retentionUnit = $retentionUnit;
  }
  /**
   * @deprecated
   * @return self::RETENTION_UNIT_*
   */
  public function getRetentionUnit()
  {
    return $this->retentionUnit;
  }
  /**
   * @deprecated
   * @param string $timeBasedRetention
   */
  public function setTimeBasedRetention($timeBasedRetention)
  {
    $this->timeBasedRetention = $timeBasedRetention;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTimeBasedRetention()
  {
    return $this->timeBasedRetention;
  }
  /**
   * Timestamp based retention period i.e. 2024-05-01T00:00:00Z
   *
   * @param string $timestampBasedRetentionTime
   */
  public function setTimestampBasedRetentionTime($timestampBasedRetentionTime)
  {
    $this->timestampBasedRetentionTime = $timestampBasedRetentionTime;
  }
  /**
   * @return string
   */
  public function getTimestampBasedRetentionTime()
  {
    return $this->timestampBasedRetentionTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainRetentionSettings::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainRetentionSettings');
