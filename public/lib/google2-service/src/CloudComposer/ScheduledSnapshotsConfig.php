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

namespace Google\Service\CloudComposer;

class ScheduledSnapshotsConfig extends \Google\Model
{
  /**
   * Optional. Whether scheduled snapshots creation is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. The cron expression representing the time when snapshots creation
   * mechanism runs. This field is subject to additional validation around
   * frequency of execution.
   *
   * @var string
   */
  public $snapshotCreationSchedule;
  /**
   * Optional. The Cloud Storage location for storing automatically created
   * snapshots.
   *
   * @var string
   */
  public $snapshotLocation;
  /**
   * Optional. Time zone that sets the context to interpret
   * snapshot_creation_schedule.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Optional. Whether scheduled snapshots creation is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. The cron expression representing the time when snapshots creation
   * mechanism runs. This field is subject to additional validation around
   * frequency of execution.
   *
   * @param string $snapshotCreationSchedule
   */
  public function setSnapshotCreationSchedule($snapshotCreationSchedule)
  {
    $this->snapshotCreationSchedule = $snapshotCreationSchedule;
  }
  /**
   * @return string
   */
  public function getSnapshotCreationSchedule()
  {
    return $this->snapshotCreationSchedule;
  }
  /**
   * Optional. The Cloud Storage location for storing automatically created
   * snapshots.
   *
   * @param string $snapshotLocation
   */
  public function setSnapshotLocation($snapshotLocation)
  {
    $this->snapshotLocation = $snapshotLocation;
  }
  /**
   * @return string
   */
  public function getSnapshotLocation()
  {
    return $this->snapshotLocation;
  }
  /**
   * Optional. Time zone that sets the context to interpret
   * snapshot_creation_schedule.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScheduledSnapshotsConfig::class, 'Google_Service_CloudComposer_ScheduledSnapshotsConfig');
