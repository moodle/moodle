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

class GoogleCloudIntegrationsV1alphaAssertionResult extends \Google\Model
{
  /**
   * Unspecified assertion status
   */
  public const STATUS_ASSERTION_STATUS_UNSPECIFIED = 'ASSERTION_STATUS_UNSPECIFIED';
  /**
   * Assertion succeeded
   */
  public const STATUS_SUCCEEDED = 'SUCCEEDED';
  /**
   * Assertion failed
   */
  public const STATUS_FAILED = 'FAILED';
  protected $assertionType = GoogleCloudIntegrationsV1alphaAssertion::class;
  protected $assertionDataType = '';
  /**
   * Details of the assertion failure
   *
   * @var string
   */
  public $failureMessage;
  /**
   * Status of assertion to signify if the assertion succeeded or failed
   *
   * @var string
   */
  public $status;
  /**
   * Task name of task where the assertion was run.
   *
   * @var string
   */
  public $taskName;
  /**
   * Task number of task where the assertion was run.
   *
   * @var string
   */
  public $taskNumber;

  /**
   * Assertion that was run.
   *
   * @param GoogleCloudIntegrationsV1alphaAssertion $assertion
   */
  public function setAssertion(GoogleCloudIntegrationsV1alphaAssertion $assertion)
  {
    $this->assertion = $assertion;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAssertion
   */
  public function getAssertion()
  {
    return $this->assertion;
  }
  /**
   * Details of the assertion failure
   *
   * @param string $failureMessage
   */
  public function setFailureMessage($failureMessage)
  {
    $this->failureMessage = $failureMessage;
  }
  /**
   * @return string
   */
  public function getFailureMessage()
  {
    return $this->failureMessage;
  }
  /**
   * Status of assertion to signify if the assertion succeeded or failed
   *
   * Accepted values: ASSERTION_STATUS_UNSPECIFIED, SUCCEEDED, FAILED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Task name of task where the assertion was run.
   *
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
   * Task number of task where the assertion was run.
   *
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaAssertionResult::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaAssertionResult');
