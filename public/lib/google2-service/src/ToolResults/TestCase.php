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

namespace Google\Service\ToolResults;

class TestCase extends \Google\Collection
{
  /**
   * Test passed.
   */
  public const STATUS_passed = 'passed';
  /**
   * Test failed.
   */
  public const STATUS_failed = 'failed';
  /**
   * Test encountered an error
   */
  public const STATUS_error = 'error';
  /**
   * Test skipped
   */
  public const STATUS_skipped = 'skipped';
  /**
   * Test flaked. Present only for rollup test cases; test cases from steps that
   * were run with the same configuration had both failure and success outcomes.
   */
  public const STATUS_flaky = 'flaky';
  protected $collection_key = 'toolOutputs';
  protected $elapsedTimeType = Duration::class;
  protected $elapsedTimeDataType = '';
  protected $endTimeType = Timestamp::class;
  protected $endTimeDataType = '';
  /**
   * Why the test case was skipped. Present only for skipped test case
   *
   * @var string
   */
  public $skippedMessage;
  protected $stackTracesType = StackTrace::class;
  protected $stackTracesDataType = 'array';
  protected $startTimeType = Timestamp::class;
  protected $startTimeDataType = '';
  /**
   * The status of the test case. Required.
   *
   * @var string
   */
  public $status;
  /**
   * A unique identifier within a Step for this Test Case.
   *
   * @var string
   */
  public $testCaseId;
  protected $testCaseReferenceType = TestCaseReference::class;
  protected $testCaseReferenceDataType = '';
  protected $toolOutputsType = ToolOutputReference::class;
  protected $toolOutputsDataType = 'array';

  /**
   * The elapsed run time of the test case. Required.
   *
   * @param Duration $elapsedTime
   */
  public function setElapsedTime(Duration $elapsedTime)
  {
    $this->elapsedTime = $elapsedTime;
  }
  /**
   * @return Duration
   */
  public function getElapsedTime()
  {
    return $this->elapsedTime;
  }
  /**
   * The end time of the test case.
   *
   * @param Timestamp $endTime
   */
  public function setEndTime(Timestamp $endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return Timestamp
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Why the test case was skipped. Present only for skipped test case
   *
   * @param string $skippedMessage
   */
  public function setSkippedMessage($skippedMessage)
  {
    $this->skippedMessage = $skippedMessage;
  }
  /**
   * @return string
   */
  public function getSkippedMessage()
  {
    return $this->skippedMessage;
  }
  /**
   * The stack trace details if the test case failed or encountered an error.
   * The maximum size of the stack traces is 100KiB, beyond which the stack
   * track will be truncated. Zero if the test case passed.
   *
   * @param StackTrace[] $stackTraces
   */
  public function setStackTraces($stackTraces)
  {
    $this->stackTraces = $stackTraces;
  }
  /**
   * @return StackTrace[]
   */
  public function getStackTraces()
  {
    return $this->stackTraces;
  }
  /**
   * The start time of the test case.
   *
   * @param Timestamp $startTime
   */
  public function setStartTime(Timestamp $startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return Timestamp
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The status of the test case. Required.
   *
   * Accepted values: passed, failed, error, skipped, flaky
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
   * A unique identifier within a Step for this Test Case.
   *
   * @param string $testCaseId
   */
  public function setTestCaseId($testCaseId)
  {
    $this->testCaseId = $testCaseId;
  }
  /**
   * @return string
   */
  public function getTestCaseId()
  {
    return $this->testCaseId;
  }
  /**
   * Test case reference, e.g. name, class name and test suite name. Required.
   *
   * @param TestCaseReference $testCaseReference
   */
  public function setTestCaseReference(TestCaseReference $testCaseReference)
  {
    $this->testCaseReference = $testCaseReference;
  }
  /**
   * @return TestCaseReference
   */
  public function getTestCaseReference()
  {
    return $this->testCaseReference;
  }
  /**
   * References to opaque files of any format output by the tool execution.
   * @OutputOnly
   *
   * @param ToolOutputReference[] $toolOutputs
   */
  public function setToolOutputs($toolOutputs)
  {
    $this->toolOutputs = $toolOutputs;
  }
  /**
   * @return ToolOutputReference[]
   */
  public function getToolOutputs()
  {
    return $this->toolOutputs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestCase::class, 'Google_Service_ToolResults_TestCase');
