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

class TestSuiteOverview extends \Google\Model
{
  protected $elapsedTimeType = Duration::class;
  protected $elapsedTimeDataType = '';
  /**
   * Number of test cases in error, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @var int
   */
  public $errorCount;
  /**
   * Number of failed test cases, typically set by the service by parsing the
   * xml_source. May also be set by the user. - In create/response: always set -
   * In update request: never
   *
   * @var int
   */
  public $failureCount;
  /**
   * Number of flaky test cases, set by the service by rolling up flaky test
   * attempts. Present only for rollup test suite overview at environment level.
   * A step cannot have flaky test cases.
   *
   * @var int
   */
  public $flakyCount;
  /**
   * The name of the test suite. - In create/response: always set - In update
   * request: never
   *
   * @var string
   */
  public $name;
  /**
   * Number of test cases not run, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @var int
   */
  public $skippedCount;
  /**
   * Number of test cases, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @var int
   */
  public $totalCount;
  protected $xmlSourceType = FileReference::class;
  protected $xmlSourceDataType = '';

  /**
   * Elapsed time of test suite.
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
   * Number of test cases in error, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @param int $errorCount
   */
  public function setErrorCount($errorCount)
  {
    $this->errorCount = $errorCount;
  }
  /**
   * @return int
   */
  public function getErrorCount()
  {
    return $this->errorCount;
  }
  /**
   * Number of failed test cases, typically set by the service by parsing the
   * xml_source. May also be set by the user. - In create/response: always set -
   * In update request: never
   *
   * @param int $failureCount
   */
  public function setFailureCount($failureCount)
  {
    $this->failureCount = $failureCount;
  }
  /**
   * @return int
   */
  public function getFailureCount()
  {
    return $this->failureCount;
  }
  /**
   * Number of flaky test cases, set by the service by rolling up flaky test
   * attempts. Present only for rollup test suite overview at environment level.
   * A step cannot have flaky test cases.
   *
   * @param int $flakyCount
   */
  public function setFlakyCount($flakyCount)
  {
    $this->flakyCount = $flakyCount;
  }
  /**
   * @return int
   */
  public function getFlakyCount()
  {
    return $this->flakyCount;
  }
  /**
   * The name of the test suite. - In create/response: always set - In update
   * request: never
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
   * Number of test cases not run, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @param int $skippedCount
   */
  public function setSkippedCount($skippedCount)
  {
    $this->skippedCount = $skippedCount;
  }
  /**
   * @return int
   */
  public function getSkippedCount()
  {
    return $this->skippedCount;
  }
  /**
   * Number of test cases, typically set by the service by parsing the
   * xml_source. - In create/response: always set - In update request: never
   *
   * @param int $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
  /**
   * If this test suite was parsed from XML, this is the URI where the original
   * XML file is stored. Note: Multiple test suites can share the same
   * xml_source Returns INVALID_ARGUMENT if the uri format is not supported. -
   * In create/response: optional - In update request: never
   *
   * @param FileReference $xmlSource
   */
  public function setXmlSource(FileReference $xmlSource)
  {
    $this->xmlSource = $xmlSource;
  }
  /**
   * @return FileReference
   */
  public function getXmlSource()
  {
    return $this->xmlSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestSuiteOverview::class, 'Google_Service_ToolResults_TestSuiteOverview');
