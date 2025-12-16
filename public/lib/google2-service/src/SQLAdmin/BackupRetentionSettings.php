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

namespace Google\Service\SQLAdmin;

class BackupRetentionSettings extends \Google\Model
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
   * Depending on the value of retention_unit, this is used to determine if a
   * backup needs to be deleted. If retention_unit is 'COUNT', we will retain
   * this many backups.
   *
   * @var int
   */
  public $retainedBackups;
  /**
   * The unit that 'retained_backups' represents.
   *
   * @var string
   */
  public $retentionUnit;

  /**
   * Depending on the value of retention_unit, this is used to determine if a
   * backup needs to be deleted. If retention_unit is 'COUNT', we will retain
   * this many backups.
   *
   * @param int $retainedBackups
   */
  public function setRetainedBackups($retainedBackups)
  {
    $this->retainedBackups = $retainedBackups;
  }
  /**
   * @return int
   */
  public function getRetainedBackups()
  {
    return $this->retainedBackups;
  }
  /**
   * The unit that 'retained_backups' represents.
   *
   * Accepted values: RETENTION_UNIT_UNSPECIFIED, COUNT
   *
   * @param self::RETENTION_UNIT_* $retentionUnit
   */
  public function setRetentionUnit($retentionUnit)
  {
    $this->retentionUnit = $retentionUnit;
  }
  /**
   * @return self::RETENTION_UNIT_*
   */
  public function getRetentionUnit()
  {
    return $this->retentionUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupRetentionSettings::class, 'Google_Service_SQLAdmin_BackupRetentionSettings');
