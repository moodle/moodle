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

class GcpBackupConfig extends \Google\Collection
{
  protected $collection_key = 'backupPlanRules';
  /**
   * The name of the backup plan.
   *
   * @var string
   */
  public $backupPlan;
  /**
   * The name of the backup plan association.
   *
   * @var string
   */
  public $backupPlanAssociation;
  /**
   * The description of the backup plan.
   *
   * @var string
   */
  public $backupPlanDescription;
  /**
   * The user friendly id of the backup plan revision. E.g. v0, v1 etc.
   *
   * @var string
   */
  public $backupPlanRevisionId;
  /**
   * The name of the backup plan revision.
   *
   * @var string
   */
  public $backupPlanRevisionName;
  /**
   * The names of the backup plan rules which point to this backupvault
   *
   * @var string[]
   */
  public $backupPlanRules;

  /**
   * The name of the backup plan.
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
   * The name of the backup plan association.
   *
   * @param string $backupPlanAssociation
   */
  public function setBackupPlanAssociation($backupPlanAssociation)
  {
    $this->backupPlanAssociation = $backupPlanAssociation;
  }
  /**
   * @return string
   */
  public function getBackupPlanAssociation()
  {
    return $this->backupPlanAssociation;
  }
  /**
   * The description of the backup plan.
   *
   * @param string $backupPlanDescription
   */
  public function setBackupPlanDescription($backupPlanDescription)
  {
    $this->backupPlanDescription = $backupPlanDescription;
  }
  /**
   * @return string
   */
  public function getBackupPlanDescription()
  {
    return $this->backupPlanDescription;
  }
  /**
   * The user friendly id of the backup plan revision. E.g. v0, v1 etc.
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
   * The name of the backup plan revision.
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
   * The names of the backup plan rules which point to this backupvault
   *
   * @param string[] $backupPlanRules
   */
  public function setBackupPlanRules($backupPlanRules)
  {
    $this->backupPlanRules = $backupPlanRules;
  }
  /**
   * @return string[]
   */
  public function getBackupPlanRules()
  {
    return $this->backupPlanRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcpBackupConfig::class, 'Google_Service_Backupdr_GcpBackupConfig');
