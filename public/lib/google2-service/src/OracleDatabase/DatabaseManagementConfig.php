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

namespace Google\Service\OracleDatabase;

class DatabaseManagementConfig extends \Google\Model
{
  /**
   * The status is not specified.
   */
  public const MANAGEMENT_STATE_MANAGEMENT_STATE_UNSPECIFIED = 'MANAGEMENT_STATE_UNSPECIFIED';
  /**
   * The Database Management service is enabling.
   */
  public const MANAGEMENT_STATE_ENABLING = 'ENABLING';
  /**
   * The Database Management service is enabled.
   */
  public const MANAGEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * The Database Management service is disabling.
   */
  public const MANAGEMENT_STATE_DISABLING = 'DISABLING';
  /**
   * The Database Management service is disabled.
   */
  public const MANAGEMENT_STATE_DISABLED = 'DISABLED';
  /**
   * The Database Management service is updating.
   */
  public const MANAGEMENT_STATE_UPDATING = 'UPDATING';
  /**
   * The Database Management service failed to enable.
   */
  public const MANAGEMENT_STATE_FAILED_ENABLING = 'FAILED_ENABLING';
  /**
   * The Database Management service failed to disable.
   */
  public const MANAGEMENT_STATE_FAILED_DISABLING = 'FAILED_DISABLING';
  /**
   * The Database Management service failed to update.
   */
  public const MANAGEMENT_STATE_FAILED_UPDATING = 'FAILED_UPDATING';
  /**
   * The type is not specified.
   */
  public const MANAGEMENT_TYPE_MANAGEMENT_TYPE_UNSPECIFIED = 'MANAGEMENT_TYPE_UNSPECIFIED';
  /**
   * Basic Database Management.
   */
  public const MANAGEMENT_TYPE_BASIC = 'BASIC';
  /**
   * Advanced Database Management.
   */
  public const MANAGEMENT_TYPE_ADVANCED = 'ADVANCED';
  /**
   * Output only. The status of the Database Management service.
   *
   * @var string
   */
  public $managementState;
  /**
   * Output only. The Database Management type.
   *
   * @var string
   */
  public $managementType;

  /**
   * Output only. The status of the Database Management service.
   *
   * Accepted values: MANAGEMENT_STATE_UNSPECIFIED, ENABLING, ENABLED,
   * DISABLING, DISABLED, UPDATING, FAILED_ENABLING, FAILED_DISABLING,
   * FAILED_UPDATING
   *
   * @param self::MANAGEMENT_STATE_* $managementState
   */
  public function setManagementState($managementState)
  {
    $this->managementState = $managementState;
  }
  /**
   * @return self::MANAGEMENT_STATE_*
   */
  public function getManagementState()
  {
    return $this->managementState;
  }
  /**
   * Output only. The Database Management type.
   *
   * Accepted values: MANAGEMENT_TYPE_UNSPECIFIED, BASIC, ADVANCED
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseManagementConfig::class, 'Google_Service_OracleDatabase_DatabaseManagementConfig');
