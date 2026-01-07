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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusStatsDimensions extends \Google\Model
{
  public const ENUM_FILTER_TYPE_DEFAULT_INCLUSIVE = 'DEFAULT_INCLUSIVE';
  public const ENUM_FILTER_TYPE_EXCLUSIVE = 'EXCLUSIVE';
  public const RETRY_ATTEMPT_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Task has completed successfully or has depleted all retry attempts.
   */
  public const RETRY_ATTEMPT_FINAL = 'FINAL';
  /**
   * Task has failed but may be retried.
   */
  public const RETRY_ATTEMPT_RETRYABLE = 'RETRYABLE';
  /**
   * Task has been deliberately canceled.
   */
  public const RETRY_ATTEMPT_CANCELED = 'CANCELED';
  /**
   * @var string
   */
  public $clientId;
  /**
   * Whether to include or exclude the enums matching the regex.
   *
   * @var string
   */
  public $enumFilterType;
  /**
   * @var string
   */
  public $errorEnumString;
  /**
   * @var string
   */
  public $retryAttempt;
  /**
   * @var string
   */
  public $taskName;
  /**
   * @var string
   */
  public $taskNumber;
  /**
   * Stats have been or will be aggregated on set fields for any semantically-
   * meaningful combination.
   *
   * @var string
   */
  public $triggerId;
  /**
   * @var string
   */
  public $warningEnumString;
  /**
   * @var string
   */
  public $workflowId;
  /**
   * @var string
   */
  public $workflowName;

  /**
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Whether to include or exclude the enums matching the regex.
   *
   * Accepted values: DEFAULT_INCLUSIVE, EXCLUSIVE
   *
   * @param self::ENUM_FILTER_TYPE_* $enumFilterType
   */
  public function setEnumFilterType($enumFilterType)
  {
    $this->enumFilterType = $enumFilterType;
  }
  /**
   * @return self::ENUM_FILTER_TYPE_*
   */
  public function getEnumFilterType()
  {
    return $this->enumFilterType;
  }
  /**
   * @param string $errorEnumString
   */
  public function setErrorEnumString($errorEnumString)
  {
    $this->errorEnumString = $errorEnumString;
  }
  /**
   * @return string
   */
  public function getErrorEnumString()
  {
    return $this->errorEnumString;
  }
  /**
   * @param self::RETRY_ATTEMPT_* $retryAttempt
   */
  public function setRetryAttempt($retryAttempt)
  {
    $this->retryAttempt = $retryAttempt;
  }
  /**
   * @return self::RETRY_ATTEMPT_*
   */
  public function getRetryAttempt()
  {
    return $this->retryAttempt;
  }
  /**
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * @param string $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return string
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
  /**
   * Stats have been or will be aggregated on set fields for any semantically-
   * meaningful combination.
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
  /**
   * @param string $warningEnumString
   */
  public function setWarningEnumString($warningEnumString)
  {
    $this->warningEnumString = $warningEnumString;
  }
  /**
   * @return string
   */
  public function getWarningEnumString()
  {
    return $this->warningEnumString;
  }
  /**
   * @param string $workflowId
   */
  public function setWorkflowId($workflowId)
  {
    $this->workflowId = $workflowId;
  }
  /**
   * @return string
   */
  public function getWorkflowId()
  {
    return $this->workflowId;
  }
  /**
   * @param string $workflowName
   */
  public function setWorkflowName($workflowName)
  {
    $this->workflowName = $workflowName;
  }
  /**
   * @return string
   */
  public function getWorkflowName()
  {
    return $this->workflowName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusStatsDimensions::class, 'Google_Service_Integrations_EnterpriseCrmEventbusStatsDimensions');
