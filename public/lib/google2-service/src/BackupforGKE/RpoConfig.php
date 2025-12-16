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

namespace Google\Service\BackupforGKE;

class RpoConfig extends \Google\Collection
{
  protected $collection_key = 'exclusionWindows';
  protected $exclusionWindowsType = ExclusionWindow::class;
  protected $exclusionWindowsDataType = 'array';
  /**
   * Required. Defines the target RPO for the BackupPlan in minutes, which means
   * the target maximum data loss in time that is acceptable for this
   * BackupPlan. This must be at least 60, i.e., 1 hour, and at most 86400,
   * i.e., 60 days.
   *
   * @var int
   */
  public $targetRpoMinutes;

  /**
   * Optional. User specified time windows during which backup can NOT happen
   * for this BackupPlan - backups should start and finish outside of any given
   * exclusion window. Note: backup jobs will be scheduled to start and finish
   * outside the duration of the window as much as possible, but running jobs
   * will not get canceled when it runs into the window. All the time and date
   * values in exclusion_windows entry in the API are in UTC. We only allow <=1
   * recurrence (daily or weekly) exclusion window for a BackupPlan while no
   * restriction on number of single occurrence windows.
   *
   * @param ExclusionWindow[] $exclusionWindows
   */
  public function setExclusionWindows($exclusionWindows)
  {
    $this->exclusionWindows = $exclusionWindows;
  }
  /**
   * @return ExclusionWindow[]
   */
  public function getExclusionWindows()
  {
    return $this->exclusionWindows;
  }
  /**
   * Required. Defines the target RPO for the BackupPlan in minutes, which means
   * the target maximum data loss in time that is acceptable for this
   * BackupPlan. This must be at least 60, i.e., 1 hour, and at most 86400,
   * i.e., 60 days.
   *
   * @param int $targetRpoMinutes
   */
  public function setTargetRpoMinutes($targetRpoMinutes)
  {
    $this->targetRpoMinutes = $targetRpoMinutes;
  }
  /**
   * @return int
   */
  public function getTargetRpoMinutes()
  {
    return $this->targetRpoMinutes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RpoConfig::class, 'Google_Service_BackupforGKE_RpoConfig');
