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

namespace Google\Service\GKEHub;

class ConfigManagementPolicyControllerMigration extends \Google\Model
{
  /**
   * Unknown state of migration.
   */
  public const STAGE_STAGE_UNSPECIFIED = 'STAGE_UNSPECIFIED';
  /**
   * ACM Hub/Operator manages policycontroller. No migration yet completed.
   */
  public const STAGE_ACM_MANAGED = 'ACM_MANAGED';
  /**
   * All migrations steps complete; Poco Hub now manages policycontroller.
   */
  public const STAGE_POCO_MANAGED = 'POCO_MANAGED';
  /**
   * Last time this membership spec was copied to PoCo feature.
   *
   * @var string
   */
  public $copyTime;
  /**
   * Stage of the migration.
   *
   * @var string
   */
  public $stage;

  /**
   * Last time this membership spec was copied to PoCo feature.
   *
   * @param string $copyTime
   */
  public function setCopyTime($copyTime)
  {
    $this->copyTime = $copyTime;
  }
  /**
   * @return string
   */
  public function getCopyTime()
  {
    return $this->copyTime;
  }
  /**
   * Stage of the migration.
   *
   * Accepted values: STAGE_UNSPECIFIED, ACM_MANAGED, POCO_MANAGED
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementPolicyControllerMigration::class, 'Google_Service_GKEHub_ConfigManagementPolicyControllerMigration');
