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

class GoogleCloudDialogflowCxV3beta1TestCase extends \Google\Collection
{
  protected $collection_key = 'testCaseConversationTurns';
  /**
   * Output only. When the test was created.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Required. The human-readable name of the test case, unique within the
   * agent. Limit of 200 characters.
   *
   * @var string
   */
  public $displayName;
  protected $lastTestResultType = GoogleCloudDialogflowCxV3beta1TestCaseResult::class;
  protected $lastTestResultDataType = '';
  /**
   * The unique identifier of the test case. TestCases.CreateTestCase will
   * populate the name automatically. Otherwise use format:
   * `projects//locations//agents//testCases/`.
   *
   * @var string
   */
  public $name;
  /**
   * Additional freeform notes about the test case. Limit of 400 characters.
   *
   * @var string
   */
  public $notes;
  /**
   * Tags are short descriptions that users may apply to test cases for
   * organizational and filtering purposes. Each tag should start with "#" and
   * has a limit of 30 characters.
   *
   * @var string[]
   */
  public $tags;
  protected $testCaseConversationTurnsType = GoogleCloudDialogflowCxV3beta1ConversationTurn::class;
  protected $testCaseConversationTurnsDataType = 'array';
  protected $testConfigType = GoogleCloudDialogflowCxV3beta1TestConfig::class;
  protected $testConfigDataType = '';

  /**
   * Output only. When the test was created.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Required. The human-readable name of the test case, unique within the
   * agent. Limit of 200 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The latest test result.
   *
   * @param GoogleCloudDialogflowCxV3beta1TestCaseResult $lastTestResult
   */
  public function setLastTestResult(GoogleCloudDialogflowCxV3beta1TestCaseResult $lastTestResult)
  {
    $this->lastTestResult = $lastTestResult;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1TestCaseResult
   */
  public function getLastTestResult()
  {
    return $this->lastTestResult;
  }
  /**
   * The unique identifier of the test case. TestCases.CreateTestCase will
   * populate the name automatically. Otherwise use format:
   * `projects//locations//agents//testCases/`.
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
   * Additional freeform notes about the test case. Limit of 400 characters.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Tags are short descriptions that users may apply to test cases for
   * organizational and filtering purposes. Each tag should start with "#" and
   * has a limit of 30 characters.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The conversation turns uttered when the test case was created, in
   * chronological order. These include the canonical set of agent utterances
   * that should occur when the agent is working properly.
   *
   * @param GoogleCloudDialogflowCxV3beta1ConversationTurn[] $testCaseConversationTurns
   */
  public function setTestCaseConversationTurns($testCaseConversationTurns)
  {
    $this->testCaseConversationTurns = $testCaseConversationTurns;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1ConversationTurn[]
   */
  public function getTestCaseConversationTurns()
  {
    return $this->testCaseConversationTurns;
  }
  /**
   * Config for the test case.
   *
   * @param GoogleCloudDialogflowCxV3beta1TestConfig $testConfig
   */
  public function setTestConfig(GoogleCloudDialogflowCxV3beta1TestConfig $testConfig)
  {
    $this->testConfig = $testConfig;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1TestConfig
   */
  public function getTestConfig()
  {
    return $this->testConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1TestCase::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1TestCase');
