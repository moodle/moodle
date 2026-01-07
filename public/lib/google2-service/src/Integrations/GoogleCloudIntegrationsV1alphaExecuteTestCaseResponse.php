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

class GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse extends \Google\Collection
{
  /**
   * Unspecified state
   */
  public const TEST_EXECUTION_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Test case execution passed
   */
  public const TEST_EXECUTION_STATE_PASSED = 'PASSED';
  /**
   * Test case execution failed
   */
  public const TEST_EXECUTION_STATE_FAILED = 'FAILED';
  protected $collection_key = 'assertionResults';
  protected $assertionResultsType = GoogleCloudIntegrationsV1alphaAssertionResult::class;
  protected $assertionResultsDataType = 'array';
  /**
   * The id of the execution corresponding to this run of integration.
   *
   * @var string
   */
  public $executionId;
  /**
   * OUTPUT parameters in format of Map. Where Key is the name of the parameter.
   * Note: Name of the system generated parameters are wrapped by backtick(`) to
   * distinguish them from the user defined parameters.
   *
   * @var array[]
   */
  public $outputParameters;
  /**
   * State of the test case execution
   *
   * @var string
   */
  public $testExecutionState;

  /**
   * Results of each assertions ran during execution of test case.
   *
   * @param GoogleCloudIntegrationsV1alphaAssertionResult[] $assertionResults
   */
  public function setAssertionResults($assertionResults)
  {
    $this->assertionResults = $assertionResults;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAssertionResult[]
   */
  public function getAssertionResults()
  {
    return $this->assertionResults;
  }
  /**
   * The id of the execution corresponding to this run of integration.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * OUTPUT parameters in format of Map. Where Key is the name of the parameter.
   * Note: Name of the system generated parameters are wrapped by backtick(`) to
   * distinguish them from the user defined parameters.
   *
   * @param array[] $outputParameters
   */
  public function setOutputParameters($outputParameters)
  {
    $this->outputParameters = $outputParameters;
  }
  /**
   * @return array[]
   */
  public function getOutputParameters()
  {
    return $this->outputParameters;
  }
  /**
   * State of the test case execution
   *
   * Accepted values: STATE_UNSPECIFIED, PASSED, FAILED
   *
   * @param self::TEST_EXECUTION_STATE_* $testExecutionState
   */
  public function setTestExecutionState($testExecutionState)
  {
    $this->testExecutionState = $testExecutionState;
  }
  /**
   * @return self::TEST_EXECUTION_STATE_*
   */
  public function getTestExecutionState()
  {
    return $this->testExecutionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaExecuteTestCaseResponse');
