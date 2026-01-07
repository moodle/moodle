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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AssistantContentCodeExecutionResult extends \Google\Model
{
  /**
   * Unspecified status. This value should not be used.
   */
  public const OUTCOME_OUTCOME_UNSPECIFIED = 'OUTCOME_UNSPECIFIED';
  /**
   * Code execution completed successfully.
   */
  public const OUTCOME_OUTCOME_OK = 'OUTCOME_OK';
  /**
   * Code execution finished but with a failure. `stderr` should contain the
   * reason.
   */
  public const OUTCOME_OUTCOME_FAILED = 'OUTCOME_FAILED';
  /**
   * Code execution ran for too long, and was cancelled. There may or may not be
   * a partial output present.
   */
  public const OUTCOME_OUTCOME_DEADLINE_EXCEEDED = 'OUTCOME_DEADLINE_EXCEEDED';
  /**
   * Required. Outcome of the code execution.
   *
   * @var string
   */
  public $outcome;
  /**
   * Optional. Contains stdout when code execution is successful, stderr or
   * other description otherwise.
   *
   * @var string
   */
  public $output;

  /**
   * Required. Outcome of the code execution.
   *
   * Accepted values: OUTCOME_UNSPECIFIED, OUTCOME_OK, OUTCOME_FAILED,
   * OUTCOME_DEADLINE_EXCEEDED
   *
   * @param self::OUTCOME_* $outcome
   */
  public function setOutcome($outcome)
  {
    $this->outcome = $outcome;
  }
  /**
   * @return self::OUTCOME_*
   */
  public function getOutcome()
  {
    return $this->outcome;
  }
  /**
   * Optional. Contains stdout when code execution is successful, stderr or
   * other description otherwise.
   *
   * @param string $output
   */
  public function setOutput($output)
  {
    $this->output = $output;
  }
  /**
   * @return string
   */
  public function getOutput()
  {
    return $this->output;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantContentCodeExecutionResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantContentCodeExecutionResult');
