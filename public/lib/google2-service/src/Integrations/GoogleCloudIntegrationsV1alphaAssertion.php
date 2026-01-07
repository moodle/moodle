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

class GoogleCloudIntegrationsV1alphaAssertion extends \Google\Model
{
  /**
   * Unspecified Assertion strategy
   */
  public const ASSERTION_STRATEGY_ASSERTION_STRATEGY_UNSPECIFIED = 'ASSERTION_STRATEGY_UNSPECIFIED';
  /**
   * Test a successful execution
   */
  public const ASSERTION_STRATEGY_ASSERT_SUCCESSFUL_EXECUTION = 'ASSERT_SUCCESSFUL_EXECUTION';
  /**
   * Test a failed execution
   */
  public const ASSERTION_STRATEGY_ASSERT_FAILED_EXECUTION = 'ASSERT_FAILED_EXECUTION';
  /**
   * Test that the task was never executed
   */
  public const ASSERTION_STRATEGY_ASSERT_NO_EXECUTION = 'ASSERT_NO_EXECUTION';
  /**
   * Test the parameter selected is equal to the expected value
   */
  public const ASSERTION_STRATEGY_ASSERT_EQUALS = 'ASSERT_EQUALS';
  /**
   * Test the parameter selected is not equal to the expected value
   */
  public const ASSERTION_STRATEGY_ASSERT_NOT_EQUALS = 'ASSERT_NOT_EQUALS';
  /**
   * Test the parameter selected contains the configured value
   */
  public const ASSERTION_STRATEGY_ASSERT_CONTAINS = 'ASSERT_CONTAINS';
  /**
   * Test a specific condition
   */
  public const ASSERTION_STRATEGY_ASSERT_CONDITION = 'ASSERT_CONDITION';
  /**
   * Optional. The type of assertion to perform.
   *
   * @var string
   */
  public $assertionStrategy;
  /**
   * Optional. Standard filter expression for ASSERT_CONDITION to succeed
   *
   * @var string
   */
  public $condition;
  protected $parameterType = GoogleCloudIntegrationsV1alphaEventParameter::class;
  protected $parameterDataType = '';
  /**
   * Number of times given task should be retried in case of
   * ASSERT_FAILED_EXECUTION
   *
   * @var int
   */
  public $retryCount;

  /**
   * Optional. The type of assertion to perform.
   *
   * Accepted values: ASSERTION_STRATEGY_UNSPECIFIED,
   * ASSERT_SUCCESSFUL_EXECUTION, ASSERT_FAILED_EXECUTION, ASSERT_NO_EXECUTION,
   * ASSERT_EQUALS, ASSERT_NOT_EQUALS, ASSERT_CONTAINS, ASSERT_CONDITION
   *
   * @param self::ASSERTION_STRATEGY_* $assertionStrategy
   */
  public function setAssertionStrategy($assertionStrategy)
  {
    $this->assertionStrategy = $assertionStrategy;
  }
  /**
   * @return self::ASSERTION_STRATEGY_*
   */
  public function getAssertionStrategy()
  {
    return $this->assertionStrategy;
  }
  /**
   * Optional. Standard filter expression for ASSERT_CONDITION to succeed
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Optional. Key-value pair for ASSERT_EQUALS, ASSERT_NOT_EQUALS,
   * ASSERT_CONTAINS to succeed
   *
   * @param GoogleCloudIntegrationsV1alphaEventParameter $parameter
   */
  public function setParameter(GoogleCloudIntegrationsV1alphaEventParameter $parameter)
  {
    $this->parameter = $parameter;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaEventParameter
   */
  public function getParameter()
  {
    return $this->parameter;
  }
  /**
   * Number of times given task should be retried in case of
   * ASSERT_FAILED_EXECUTION
   *
   * @param int $retryCount
   */
  public function setRetryCount($retryCount)
  {
    $this->retryCount = $retryCount;
  }
  /**
   * @return int
   */
  public function getRetryCount()
  {
    return $this->retryCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaAssertion::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaAssertion');
