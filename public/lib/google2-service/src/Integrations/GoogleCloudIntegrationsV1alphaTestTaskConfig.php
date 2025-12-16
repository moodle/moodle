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

class GoogleCloudIntegrationsV1alphaTestTaskConfig extends \Google\Collection
{
  protected $collection_key = 'assertions';
  protected $assertionsType = GoogleCloudIntegrationsV1alphaAssertion::class;
  protected $assertionsDataType = 'array';
  protected $mockConfigType = GoogleCloudIntegrationsV1alphaMockConfig::class;
  protected $mockConfigDataType = '';
  /**
   * Required. This defines in the test case, the task name in integration which
   * will be mocked by this test task config
   *
   * @var string
   */
  public $task;
  protected $taskConfigType = GoogleCloudIntegrationsV1alphaTaskConfig::class;
  protected $taskConfigDataType = '';
  /**
   * Required. This defines in the test case, the task in integration which will
   * be mocked by this test task config
   *
   * @var string
   */
  public $taskNumber;

  /**
   * Optional. List of conditions or expressions which should be evaluated to
   * true unless there is a bug/problem in the integration. These are evaluated
   * one the task execution is completed as per the mock strategy in test case
   *
   * @param GoogleCloudIntegrationsV1alphaAssertion[] $assertions
   */
  public function setAssertions($assertions)
  {
    $this->assertions = $assertions;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAssertion[]
   */
  public function getAssertions()
  {
    return $this->assertions;
  }
  /**
   * Optional. Defines how to mock the given task during test execution
   *
   * @param GoogleCloudIntegrationsV1alphaMockConfig $mockConfig
   */
  public function setMockConfig(GoogleCloudIntegrationsV1alphaMockConfig $mockConfig)
  {
    $this->mockConfig = $mockConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaMockConfig
   */
  public function getMockConfig()
  {
    return $this->mockConfig;
  }
  /**
   * Required. This defines in the test case, the task name in integration which
   * will be mocked by this test task config
   *
   * @param string $task
   */
  public function setTask($task)
  {
    $this->task = $task;
  }
  /**
   * @return string
   */
  public function getTask()
  {
    return $this->task;
  }
  /**
   * Optional. Auto-generated.
   *
   * @param GoogleCloudIntegrationsV1alphaTaskConfig $taskConfig
   */
  public function setTaskConfig(GoogleCloudIntegrationsV1alphaTaskConfig $taskConfig)
  {
    $this->taskConfig = $taskConfig;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaTaskConfig
   */
  public function getTaskConfig()
  {
    return $this->taskConfig;
  }
  /**
   * Required. This defines in the test case, the task in integration which will
   * be mocked by this test task config
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
class_alias(GoogleCloudIntegrationsV1alphaTestTaskConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTestTaskConfig');
