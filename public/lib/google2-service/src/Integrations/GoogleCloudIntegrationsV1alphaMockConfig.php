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

class GoogleCloudIntegrationsV1alphaMockConfig extends \Google\Collection
{
  /**
   * This should never be used to annotate a field
   */
  public const MOCK_STRATEGY_MOCK_STRATEGY_UNSPECIFIED = 'MOCK_STRATEGY_UNSPECIFIED';
  /**
   * Execute actual task
   */
  public const MOCK_STRATEGY_NO_MOCK_STRATEGY = 'NO_MOCK_STRATEGY';
  /**
   * Don't execute actual task, instead use the values specified by user for
   * output of the task
   */
  public const MOCK_STRATEGY_SPECIFIC_MOCK_STRATEGY = 'SPECIFIC_MOCK_STRATEGY';
  /**
   * Don't execute actual task, instead return task failure
   */
  public const MOCK_STRATEGY_FAILURE_MOCK_STRATEGY = 'FAILURE_MOCK_STRATEGY';
  /**
   * Don't execute actual task, instead mark it as successful
   */
  public const MOCK_STRATEGY_SKIP_MOCK_STRATEGY = 'SKIP_MOCK_STRATEGY';
  protected $collection_key = 'parameters';
  /**
   * Optional. Number of times the given task should fail for failure mock
   * strategy
   *
   * @var string
   */
  public $failedExecutions;
  /**
   * Mockstrategy defines how the particular task should be mocked during test
   * execution
   *
   * @var string
   */
  public $mockStrategy;
  protected $parametersType = GoogleCloudIntegrationsV1alphaEventParameter::class;
  protected $parametersDataType = 'array';

  /**
   * Optional. Number of times the given task should fail for failure mock
   * strategy
   *
   * @param string $failedExecutions
   */
  public function setFailedExecutions($failedExecutions)
  {
    $this->failedExecutions = $failedExecutions;
  }
  /**
   * @return string
   */
  public function getFailedExecutions()
  {
    return $this->failedExecutions;
  }
  /**
   * Mockstrategy defines how the particular task should be mocked during test
   * execution
   *
   * Accepted values: MOCK_STRATEGY_UNSPECIFIED, NO_MOCK_STRATEGY,
   * SPECIFIC_MOCK_STRATEGY, FAILURE_MOCK_STRATEGY, SKIP_MOCK_STRATEGY
   *
   * @param self::MOCK_STRATEGY_* $mockStrategy
   */
  public function setMockStrategy($mockStrategy)
  {
    $this->mockStrategy = $mockStrategy;
  }
  /**
   * @return self::MOCK_STRATEGY_*
   */
  public function getMockStrategy()
  {
    return $this->mockStrategy;
  }
  /**
   * Optional. List of key-value pairs for specific mock strategy
   *
   * @param GoogleCloudIntegrationsV1alphaEventParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaEventParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaMockConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaMockConfig');
