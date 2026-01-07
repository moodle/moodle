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

class FinalBackupConfig extends \Google\Model
{
  /**
   * Whether the final backup is enabled for the instance.
   *
   * @var bool
   */
  public $enabled;
  /**
   * The number of days to retain the final backup after the instance deletion.
   * The final backup will be purged at (time_of_instance_deletion +
   * retention_days).
   *
   * @var int
   */
  public $retentionDays;

  /**
   * Whether the final backup is enabled for the instance.
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
   * The number of days to retain the final backup after the instance deletion.
   * The final backup will be purged at (time_of_instance_deletion +
   * retention_days).
   *
   * @param int $retentionDays
   */
  public function setRetentionDays($retentionDays)
  {
    $this->retentionDays = $retentionDays;
  }
  /**
   * @return int
   */
  public function getRetentionDays()
  {
    return $this->retentionDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinalBackupConfig::class, 'Google_Service_SQLAdmin_FinalBackupConfig');
