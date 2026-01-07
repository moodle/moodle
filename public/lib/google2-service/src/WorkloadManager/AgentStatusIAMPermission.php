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

namespace Google\Service\WorkloadManager;

class AgentStatusIAMPermission extends \Google\Model
{
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const GRANTED_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const GRANTED_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const GRANTED_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const GRANTED_ERROR_STATE = 'ERROR_STATE';
  /**
   * Output only. Whether the permission is granted.
   *
   * @var string
   */
  public $granted;
  /**
   * Output only. The name of the permission.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Whether the permission is granted.
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::GRANTED_* $granted
   */
  public function setGranted($granted)
  {
    $this->granted = $granted;
  }
  /**
   * @return self::GRANTED_*
   */
  public function getGranted()
  {
    return $this->granted;
  }
  /**
   * Output only. The name of the permission.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentStatusIAMPermission::class, 'Google_Service_WorkloadManager_AgentStatusIAMPermission');
