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

class TestExecutionStep extends \Google\Collection
{
  protected $collection_key = 'testSuiteOverviews';
  protected $testIssuesType = TestIssue::class;
  protected $testIssuesDataType = 'array';
  protected $testSuiteOverviewsType = TestSuiteOverview::class;
  protected $testSuiteOverviewsDataType = 'array';
  protected $testTimingType = TestTiming::class;
  protected $testTimingDataType = '';
  protected $toolExecutionType = ToolExecution::class;
  protected $toolExecutionDataType = '';

  /**
   * Issues observed during the test execution. For example, if the mobile app
   * under test crashed during the test, the error message and the stack trace
   * content can be recorded here to assist debugging. - In response: present if
   * set by create or update - In create/update request: optional
   *
   * @param TestIssue[] $testIssues
   */
  public function setTestIssues($testIssues)
  {
    $this->testIssues = $testIssues;
  }
  /**
   * @return TestIssue[]
   */
  public function getTestIssues()
  {
    return $this->testIssues;
  }
  /**
   * List of test suite overview contents. This could be parsed from xUnit XML
   * log by server, or uploaded directly by user. This references should only be
   * called when test suites are fully parsed or uploaded. The maximum allowed
   * number of test suite overviews per step is 1000. - In response: always set
   * - In create request: optional - In update request: never (use
   * publishXunitXmlFiles custom method instead)
   *
   * @param TestSuiteOverview[] $testSuiteOverviews
   */
  public function setTestSuiteOverviews($testSuiteOverviews)
  {
    $this->testSuiteOverviews = $testSuiteOverviews;
  }
  /**
   * @return TestSuiteOverview[]
   */
  public function getTestSuiteOverviews()
  {
    return $this->testSuiteOverviews;
  }
  /**
   * The timing break down of the test execution. - In response: present if set
   * by create or update - In create/update request: optional
   *
   * @param TestTiming $testTiming
   */
  public function setTestTiming(TestTiming $testTiming)
  {
    $this->testTiming = $testTiming;
  }
  /**
   * @return TestTiming
   */
  public function getTestTiming()
  {
    return $this->testTiming;
  }
  /**
   * Represents the execution of the test runner. The exit code of this tool
   * will be used to determine if the test passed. - In response: always set -
   * In create/update request: optional
   *
   * @param ToolExecution $toolExecution
   */
  public function setToolExecution(ToolExecution $toolExecution)
  {
    $this->toolExecution = $toolExecution;
  }
  /**
   * @return ToolExecution
   */
  public function getToolExecution()
  {
    return $this->toolExecution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestExecutionStep::class, 'Google_Service_ToolResults_TestExecutionStep');
