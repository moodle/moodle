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

namespace Google\Service\GKEOnPrem;

class ValidationCheck extends \Google\Model
{
  /**
   * Default value. Standard preflight validation check will be used.
   */
  public const OPTION_OPTIONS_UNSPECIFIED = 'OPTIONS_UNSPECIFIED';
  /**
   * Prevent failed preflight checks from failing.
   */
  public const OPTION_SKIP_VALIDATION_CHECK_BLOCKING = 'SKIP_VALIDATION_CHECK_BLOCKING';
  /**
   * Skip all preflight check validations.
   */
  public const OPTION_SKIP_VALIDATION_ALL = 'SKIP_VALIDATION_ALL';
  /**
   * Default value. This value is unused.
   */
  public const SCENARIO_SCENARIO_UNSPECIFIED = 'SCENARIO_UNSPECIFIED';
  /**
   * The validation check occurred during a create flow.
   */
  public const SCENARIO_CREATE = 'CREATE';
  /**
   * The validation check occurred during an update flow.
   */
  public const SCENARIO_UPDATE = 'UPDATE';
  /**
   * Options used for the validation check
   *
   * @var string
   */
  public $option;
  /**
   * Output only. The scenario when the preflight checks were run.
   *
   * @var string
   */
  public $scenario;
  protected $statusType = ValidationCheckStatus::class;
  protected $statusDataType = '';

  /**
   * Options used for the validation check
   *
   * Accepted values: OPTIONS_UNSPECIFIED, SKIP_VALIDATION_CHECK_BLOCKING,
   * SKIP_VALIDATION_ALL
   *
   * @param self::OPTION_* $option
   */
  public function setOption($option)
  {
    $this->option = $option;
  }
  /**
   * @return self::OPTION_*
   */
  public function getOption()
  {
    return $this->option;
  }
  /**
   * Output only. The scenario when the preflight checks were run.
   *
   * Accepted values: SCENARIO_UNSPECIFIED, CREATE, UPDATE
   *
   * @param self::SCENARIO_* $scenario
   */
  public function setScenario($scenario)
  {
    $this->scenario = $scenario;
  }
  /**
   * @return self::SCENARIO_*
   */
  public function getScenario()
  {
    return $this->scenario;
  }
  /**
   * Output only. The detailed validation check status.
   *
   * @param ValidationCheckStatus $status
   */
  public function setStatus(ValidationCheckStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ValidationCheckStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidationCheck::class, 'Google_Service_GKEOnPrem_ValidationCheck');
