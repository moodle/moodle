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

class BackupRule extends \Google\Model
{
  /**
   * Required. Configures the duration for which backup data will be kept. It is
   * defined in “days”. The value should be greater than or equal to minimum
   * enforced retention of the backup vault. Minimum value is 1 and maximum
   * value is 36159 for custom retention on-demand backup. Minimum and maximum
   * values are workload specific for all other rules. Note: Longer retention
   * can lead to higher storage costs post introductory trial. We recommend
   * starting with a short duration of 3 days or less.
   *
   * @var int
   */
  public $backupRetentionDays;
  /**
   * Required. Immutable. The unique id of this `BackupRule`. The `rule_id` is
   * unique per `BackupPlan`.The `rule_id` must start with a lowercase letter
   * followed by up to 62 lowercase letters, numbers, or hyphens. Pattern,
   * /a-z{,62}/.
   *
   * @var string
   */
  public $ruleId;
  protected $standardScheduleType = StandardSchedule::class;
  protected $standardScheduleDataType = '';

  /**
   * Required. Configures the duration for which backup data will be kept. It is
   * defined in “days”. The value should be greater than or equal to minimum
   * enforced retention of the backup vault. Minimum value is 1 and maximum
   * value is 36159 for custom retention on-demand backup. Minimum and maximum
   * values are workload specific for all other rules. Note: Longer retention
   * can lead to higher storage costs post introductory trial. We recommend
   * starting with a short duration of 3 days or less.
   *
   * @param int $backupRetentionDays
   */
  public function setBackupRetentionDays($backupRetentionDays)
  {
    $this->backupRetentionDays = $backupRetentionDays;
  }
  /**
   * @return int
   */
  public function getBackupRetentionDays()
  {
    return $this->backupRetentionDays;
  }
  /**
   * Required. Immutable. The unique id of this `BackupRule`. The `rule_id` is
   * unique per `BackupPlan`.The `rule_id` must start with a lowercase letter
   * followed by up to 62 lowercase letters, numbers, or hyphens. Pattern,
   * /a-z{,62}/.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
  /**
   * Optional. Defines a schedule that runs within the confines of a defined
   * window of time.
   *
   * @param StandardSchedule $standardSchedule
   */
  public function setStandardSchedule(StandardSchedule $standardSchedule)
  {
    $this->standardSchedule = $standardSchedule;
  }
  /**
   * @return StandardSchedule
   */
  public function getStandardSchedule()
  {
    return $this->standardSchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupRule::class, 'Google_Service_Backupdr_BackupRule');
