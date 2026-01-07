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

class AgentStatusServiceStatus extends \Google\Collection
{
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const FULLY_FUNCTIONAL_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const FULLY_FUNCTIONAL_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const FULLY_FUNCTIONAL_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const FULLY_FUNCTIONAL_ERROR_STATE = 'ERROR_STATE';
  /**
   * The state is unspecified and has not been checked yet.
   */
  public const STATE_UNSPECIFIED_STATE = 'UNSPECIFIED_STATE';
  /**
   * The state is successful (enabled, granted, fully functional).
   */
  public const STATE_SUCCESS_STATE = 'SUCCESS_STATE';
  /**
   * The state is failed (disabled, denied, not fully functional).
   */
  public const STATE_FAILURE_STATE = 'FAILURE_STATE';
  /**
   * There was an internal error while checking the state, state is unknown.
   */
  public const STATE_ERROR_STATE = 'ERROR_STATE';
  protected $collection_key = 'iamPermissions';
  protected $configValuesType = AgentStatusConfigValue::class;
  protected $configValuesDataType = 'array';
  /**
   * Output only. The error message for the service if it is not fully
   * functional.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Output only. Whether the service is fully functional (all checks passed).
   *
   * @var string
   */
  public $fullyFunctional;
  protected $iamPermissionsType = AgentStatusIAMPermission::class;
  protected $iamPermissionsDataType = 'array';
  /**
   * Output only. The name of the service.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the service (enabled or disabled in the
   * configuration).
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The message to display when the service state is unspecified.
   *
   * @var string
   */
  public $unspecifiedStateMessage;

  /**
   * Output only. The configuration values for the service.
   *
   * @param AgentStatusConfigValue[] $configValues
   */
  public function setConfigValues($configValues)
  {
    $this->configValues = $configValues;
  }
  /**
   * @return AgentStatusConfigValue[]
   */
  public function getConfigValues()
  {
    return $this->configValues;
  }
  /**
   * Output only. The error message for the service if it is not fully
   * functional.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. Whether the service is fully functional (all checks passed).
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::FULLY_FUNCTIONAL_* $fullyFunctional
   */
  public function setFullyFunctional($fullyFunctional)
  {
    $this->fullyFunctional = $fullyFunctional;
  }
  /**
   * @return self::FULLY_FUNCTIONAL_*
   */
  public function getFullyFunctional()
  {
    return $this->fullyFunctional;
  }
  /**
   * Output only. The permissions required for the service.
   *
   * @param AgentStatusIAMPermission[] $iamPermissions
   */
  public function setIamPermissions($iamPermissions)
  {
    $this->iamPermissions = $iamPermissions;
  }
  /**
   * @return AgentStatusIAMPermission[]
   */
  public function getIamPermissions()
  {
    return $this->iamPermissions;
  }
  /**
   * Output only. The name of the service.
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
  /**
   * Output only. The state of the service (enabled or disabled in the
   * configuration).
   *
   * Accepted values: UNSPECIFIED_STATE, SUCCESS_STATE, FAILURE_STATE,
   * ERROR_STATE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The message to display when the service state is unspecified.
   *
   * @param string $unspecifiedStateMessage
   */
  public function setUnspecifiedStateMessage($unspecifiedStateMessage)
  {
    $this->unspecifiedStateMessage = $unspecifiedStateMessage;
  }
  /**
   * @return string
   */
  public function getUnspecifiedStateMessage()
  {
    return $this->unspecifiedStateMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentStatusServiceStatus::class, 'Google_Service_WorkloadManager_AgentStatusServiceStatus');
