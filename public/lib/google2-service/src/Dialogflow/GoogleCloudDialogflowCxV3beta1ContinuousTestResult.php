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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1ContinuousTestResult extends \Google\Collection
{
  /**
   * Not specified. Should never be used.
   */
  public const RESULT_AGGREGATED_TEST_RESULT_UNSPECIFIED = 'AGGREGATED_TEST_RESULT_UNSPECIFIED';
  /**
   * All the tests passed.
   */
  public const RESULT_PASSED = 'PASSED';
  /**
   * At least one test did not pass.
   */
  public const RESULT_FAILED = 'FAILED';
  protected $collection_key = 'testCaseResults';
  /**
   * The resource name for the continuous test result. Format:
   * `projects//locations//agents//environments//continuousTestResults/`.
   *
   * @var string
   */
  public $name;
  /**
   * The result of this continuous test run, i.e. whether all the tests in this
   * continuous test run pass or not.
   *
   * @var string
   */
  public $result;
  /**
   * Time when the continuous testing run starts.
   *
   * @var string
   */
  public $runTime;
  /**
   * A list of individual test case results names in this continuous test run.
   *
   * @var string[]
   */
  public $testCaseResults;

  /**
   * The resource name for the continuous test result. Format:
   * `projects//locations//agents//environments//continuousTestResults/`.
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
   * The result of this continuous test run, i.e. whether all the tests in this
   * continuous test run pass or not.
   *
   * Accepted values: AGGREGATED_TEST_RESULT_UNSPECIFIED, PASSED, FAILED
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Time when the continuous testing run starts.
   *
   * @param string $runTime
   */
  public function setRunTime($runTime)
  {
    $this->runTime = $runTime;
  }
  /**
   * @return string
   */
  public function getRunTime()
  {
    return $this->runTime;
  }
  /**
   * A list of individual test case results names in this continuous test run.
   *
   * @param string[] $testCaseResults
   */
  public function setTestCaseResults($testCaseResults)
  {
    $this->testCaseResults = $testCaseResults;
  }
  /**
   * @return string[]
   */
  public function getTestCaseResults()
  {
    return $this->testCaseResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1ContinuousTestResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ContinuousTestResult');
