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

class BackupDrPlanRule extends \Google\Model
{
  /**
   * Output only. Timestamp of the latest successful backup created via this
   * backup rule.
   *
   * @var string
   */
  public $lastSuccessfulBackupTime;
  /**
   * Output only. Unique Id of the backup rule.
   *
   * @var string
   */
  public $ruleId;

  /**
   * Output only. Timestamp of the latest successful backup created via this
   * backup rule.
   *
   * @param string $lastSuccessfulBackupTime
   */
  public function setLastSuccessfulBackupTime($lastSuccessfulBackupTime)
  {
    $this->lastSuccessfulBackupTime = $lastSuccessfulBackupTime;
  }
  /**
   * @return string
   */
  public function getLastSuccessfulBackupTime()
  {
    return $this->lastSuccessfulBackupTime;
  }
  /**
   * Output only. Unique Id of the backup rule.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDrPlanRule::class, 'Google_Service_Backupdr_BackupDrPlanRule');
