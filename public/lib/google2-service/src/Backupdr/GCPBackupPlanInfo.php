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

class GCPBackupPlanInfo extends \Google\Model
{
  /**
   * Resource name of backup plan by which workload is protected at the time of
   * the backup. Format:
   * projects/{project}/locations/{location}/backupPlans/{backupPlanId}
   *
   * @var string
   */
  public $backupPlan;
  /**
   * The user friendly id of the backup plan revision which triggered this
   * backup in case of scheduled backup or used for on demand backup.
   *
   * @var string
   */
  public $backupPlanRevisionId;
  /**
   * Resource name of the backup plan revision which triggered this backup in
   * case of scheduled backup or used for on demand backup. Format: projects/{pr
   * oject}/locations/{location}/backupPlans/{backupPlanId}/revisions/{revisionI
   * d}
   *
   * @var string
   */
  public $backupPlanRevisionName;
  /**
   * The rule id of the backup plan which triggered this backup in case of
   * scheduled backup or used for
   *
   * @var string
   */
  public $backupPlanRuleId;

  /**
   * Resource name of backup plan by which workload is protected at the time of
   * the backup. Format:
   * projects/{project}/locations/{location}/backupPlans/{backupPlanId}
   *
   * @param string $backupPlan
   */
  public function setBackupPlan($backupPlan)
  {
    $this->backupPlan = $backupPlan;
  }
  /**
   * @return string
   */
  public function getBackupPlan()
  {
    return $this->backupPlan;
  }
  /**
   * The user friendly id of the backup plan revision which triggered this
   * backup in case of scheduled backup or used for on demand backup.
   *
   * @param string $backupPlanRevisionId
   */
  public function setBackupPlanRevisionId($backupPlanRevisionId)
  {
    $this->backupPlanRevisionId = $backupPlanRevisionId;
  }
  /**
   * @return string
   */
  public function getBackupPlanRevisionId()
  {
    return $this->backupPlanRevisionId;
  }
  /**
   * Resource name of the backup plan revision which triggered this backup in
   * case of scheduled backup or used for on demand backup. Format: projects/{pr
   * oject}/locations/{location}/backupPlans/{backupPlanId}/revisions/{revisionI
   * d}
   *
   * @param string $backupPlanRevisionName
   */
  public function setBackupPlanRevisionName($backupPlanRevisionName)
  {
    $this->backupPlanRevisionName = $backupPlanRevisionName;
  }
  /**
   * @return string
   */
  public function getBackupPlanRevisionName()
  {
    return $this->backupPlanRevisionName;
  }
  /**
   * The rule id of the backup plan which triggered this backup in case of
   * scheduled backup or used for
   *
   * @param string $backupPlanRuleId
   */
  public function setBackupPlanRuleId($backupPlanRuleId)
  {
    $this->backupPlanRuleId = $backupPlanRuleId;
  }
  /**
   * @return string
   */
  public function getBackupPlanRuleId()
  {
    return $this->backupPlanRuleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GCPBackupPlanInfo::class, 'Google_Service_Backupdr_GCPBackupPlanInfo');
