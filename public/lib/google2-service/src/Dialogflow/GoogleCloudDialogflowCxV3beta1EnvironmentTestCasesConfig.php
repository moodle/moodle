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

class GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig extends \Google\Collection
{
  protected $collection_key = 'testCases';
  /**
   * Whether to run test cases in TestCasesConfig.test_cases periodically.
   * Default false. If set to true, run once a day.
   *
   * @var bool
   */
  public $enableContinuousRun;
  /**
   * Whether to run test cases in TestCasesConfig.test_cases before deploying a
   * flow version to the environment. Default false.
   *
   * @var bool
   */
  public $enablePredeploymentRun;
  /**
   * A list of test case names to run. They should be under the same agent.
   * Format of each test case name: `projects//locations//agents//testCases/`
   *
   * @var string[]
   */
  public $testCases;

  /**
   * Whether to run test cases in TestCasesConfig.test_cases periodically.
   * Default false. If set to true, run once a day.
   *
   * @param bool $enableContinuousRun
   */
  public function setEnableContinuousRun($enableContinuousRun)
  {
    $this->enableContinuousRun = $enableContinuousRun;
  }
  /**
   * @return bool
   */
  public function getEnableContinuousRun()
  {
    return $this->enableContinuousRun;
  }
  /**
   * Whether to run test cases in TestCasesConfig.test_cases before deploying a
   * flow version to the environment. Default false.
   *
   * @param bool $enablePredeploymentRun
   */
  public function setEnablePredeploymentRun($enablePredeploymentRun)
  {
    $this->enablePredeploymentRun = $enablePredeploymentRun;
  }
  /**
   * @return bool
   */
  public function getEnablePredeploymentRun()
  {
    return $this->enablePredeploymentRun;
  }
  /**
   * A list of test case names to run. They should be under the same agent.
   * Format of each test case name: `projects//locations//agents//testCases/`
   *
   * @param string[] $testCases
   */
  public function setTestCases($testCases)
  {
    $this->testCases = $testCases;
  }
  /**
   * @return string[]
   */
  public function getTestCases()
  {
    return $this->testCases;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1EnvironmentTestCasesConfig');
