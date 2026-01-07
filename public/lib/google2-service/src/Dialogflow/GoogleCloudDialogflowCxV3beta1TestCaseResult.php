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

class GoogleCloudDialogflowCxV3beta1TestCaseResult extends \Google\Collection
{
  /**
   * Not specified. Should never be used.
   */
  public const TEST_RESULT_TEST_RESULT_UNSPECIFIED = 'TEST_RESULT_UNSPECIFIED';
  /**
   * The test passed.
   */
  public const TEST_RESULT_PASSED = 'PASSED';
  /**
   * The test did not pass.
   */
  public const TEST_RESULT_FAILED = 'FAILED';
  protected $collection_key = 'conversationTurns';
  protected $conversationTurnsType = GoogleCloudDialogflowCxV3beta1ConversationTurn::class;
  protected $conversationTurnsDataType = 'array';
  /**
   * Environment where the test was run. If not set, it indicates the draft
   * environment.
   *
   * @var string
   */
  public $environment;
  /**
   * The resource name for the test case result. Format:
   * `projects//locations//agents//testCases//results/`.
   *
   * @var string
   */
  public $name;
  /**
   * Whether the test case passed in the agent environment.
   *
   * @var string
   */
  public $testResult;
  /**
   * The time that the test was run.
   *
   * @var string
   */
  public $testTime;

  /**
   * The conversation turns uttered during the test case replay in chronological
   * order.
   *
   * @param GoogleCloudDialogflowCxV3beta1ConversationTurn[] $conversationTurns
   */
  public function setConversationTurns($conversationTurns)
  {
    $this->conversationTurns = $conversationTurns;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1ConversationTurn[]
   */
  public function getConversationTurns()
  {
    return $this->conversationTurns;
  }
  /**
   * Environment where the test was run. If not set, it indicates the draft
   * environment.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * The resource name for the test case result. Format:
   * `projects//locations//agents//testCases//results/`.
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
   * Whether the test case passed in the agent environment.
   *
   * Accepted values: TEST_RESULT_UNSPECIFIED, PASSED, FAILED
   *
   * @param self::TEST_RESULT_* $testResult
   */
  public function setTestResult($testResult)
  {
    $this->testResult = $testResult;
  }
  /**
   * @return self::TEST_RESULT_*
   */
  public function getTestResult()
  {
    return $this->testResult;
  }
  /**
   * The time that the test was run.
   *
   * @param string $testTime
   */
  public function setTestTime($testTime)
  {
    $this->testTime = $testTime;
  }
  /**
   * @return string
   */
  public function getTestTime()
  {
    return $this->testTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1TestCaseResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1TestCaseResult');
