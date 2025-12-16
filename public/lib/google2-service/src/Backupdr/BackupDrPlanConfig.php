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

class BackupDrPlanConfig extends \Google\Collection
{
  protected $collection_key = 'backupDrPlanRules';
  protected $backupDrPlanRulesType = BackupDrPlanRule::class;
  protected $backupDrPlanRulesDataType = 'array';

  /**
   * Backup rules of the backup plan resource.
   *
   * @param BackupDrPlanRule[] $backupDrPlanRules
   */
  public function setBackupDrPlanRules($backupDrPlanRules)
  {
    $this->backupDrPlanRules = $backupDrPlanRules;
  }
  /**
   * @return BackupDrPlanRule[]
   */
  public function getBackupDrPlanRules()
  {
    return $this->backupDrPlanRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDrPlanConfig::class, 'Google_Service_Backupdr_BackupDrPlanConfig');
