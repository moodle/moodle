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

class BackupApplianceBackupProperties extends \Google\Model
{
  /**
   * Output only. The time when this backup object was finalized (if none,
   * backup is not finalized).
   *
   * @var string
   */
  public $finalizeTime;
  /**
   * Output only. The numeric generation ID of the backup (monotonically
   * increasing).
   *
   * @var int
   */
  public $generationId;
  /**
   * Optional. The latest timestamp of data available in this Backup.
   *
   * @var string
   */
  public $recoveryRangeEndTime;
  /**
   * Optional. The earliest timestamp of data available in this Backup.
   *
   * @var string
   */
  public $recoveryRangeStartTime;

  /**
   * Output only. The time when this backup object was finalized (if none,
   * backup is not finalized).
   *
   * @param string $finalizeTime
   */
  public function setFinalizeTime($finalizeTime)
  {
    $this->finalizeTime = $finalizeTime;
  }
  /**
   * @return string
   */
  public function getFinalizeTime()
  {
    return $this->finalizeTime;
  }
  /**
   * Output only. The numeric generation ID of the backup (monotonically
   * increasing).
   *
   * @param int $generationId
   */
  public function setGenerationId($generationId)
  {
    $this->generationId = $generationId;
  }
  /**
   * @return int
   */
  public function getGenerationId()
  {
    return $this->generationId;
  }
  /**
   * Optional. The latest timestamp of data available in this Backup.
   *
   * @param string $recoveryRangeEndTime
   */
  public function setRecoveryRangeEndTime($recoveryRangeEndTime)
  {
    $this->recoveryRangeEndTime = $recoveryRangeEndTime;
  }
  /**
   * @return string
   */
  public function getRecoveryRangeEndTime()
  {
    return $this->recoveryRangeEndTime;
  }
  /**
   * Optional. The earliest timestamp of data available in this Backup.
   *
   * @param string $recoveryRangeStartTime
   */
  public function setRecoveryRangeStartTime($recoveryRangeStartTime)
  {
    $this->recoveryRangeStartTime = $recoveryRangeStartTime;
  }
  /**
   * @return string
   */
  public function getRecoveryRangeStartTime()
  {
    return $this->recoveryRangeStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupApplianceBackupProperties::class, 'Google_Service_Backupdr_BackupApplianceBackupProperties');
