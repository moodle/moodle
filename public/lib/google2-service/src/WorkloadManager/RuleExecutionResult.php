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

class RuleExecutionResult extends \Google\Model
{
  /**
   * Unknown state
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * execution completed successfully
   */
  public const STATE_STATE_SUCCESS = 'STATE_SUCCESS';
  /**
   * execution completed with failures
   */
  public const STATE_STATE_FAILURE = 'STATE_FAILURE';
  /**
   * execution was not executed
   */
  public const STATE_STATE_SKIPPED = 'STATE_SKIPPED';
  /**
   * Execution message, if any
   *
   * @var string
   */
  public $message;
  /**
   * Number of violations
   *
   * @var string
   */
  public $resultCount;
  /**
   * rule name
   *
   * @var string
   */
  public $rule;
  /**
   * Number of total scanned resources
   *
   * @var string
   */
  public $scannedResourceCount;
  /**
   * Output only. The execution status
   *
   * @var string
   */
  public $state;

  /**
   * Execution message, if any
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Number of violations
   *
   * @param string $resultCount
   */
  public function setResultCount($resultCount)
  {
    $this->resultCount = $resultCount;
  }
  /**
   * @return string
   */
  public function getResultCount()
  {
    return $this->resultCount;
  }
  /**
   * rule name
   *
   * @param string $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return string
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * Number of total scanned resources
   *
   * @param string $scannedResourceCount
   */
  public function setScannedResourceCount($scannedResourceCount)
  {
    $this->scannedResourceCount = $scannedResourceCount;
  }
  /**
   * @return string
   */
  public function getScannedResourceCount()
  {
    return $this->scannedResourceCount;
  }
  /**
   * Output only. The execution status
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_SUCCESS, STATE_FAILURE,
   * STATE_SKIPPED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuleExecutionResult::class, 'Google_Service_WorkloadManager_RuleExecutionResult');
