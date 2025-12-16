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

class Outcome extends \Google\Model
{
  /**
   * Do not use. For proto versioning only.
   */
  public const SUMMARY_unset = 'unset';
  /**
   * The test matrix run was successful, for instance: - All the test cases
   * passed. - Robo did not detect a crash of the application under test.
   */
  public const SUMMARY_success = 'success';
  /**
   * A run failed, for instance: - One or more test case failed. - A test timed
   * out. - The application under test crashed.
   */
  public const SUMMARY_failure = 'failure';
  /**
   * Something unexpected happened. The run should still be considered
   * unsuccessful but this is likely a transient problem and re-running the test
   * might be successful.
   */
  public const SUMMARY_inconclusive = 'inconclusive';
  /**
   * All tests were skipped, for instance: - All device configurations were
   * incompatible.
   */
  public const SUMMARY_skipped = 'skipped';
  /**
   * A group of steps that were run with the same configuration had both failure
   * and success outcomes.
   */
  public const SUMMARY_flaky = 'flaky';
  protected $failureDetailType = FailureDetail::class;
  protected $failureDetailDataType = '';
  protected $inconclusiveDetailType = InconclusiveDetail::class;
  protected $inconclusiveDetailDataType = '';
  protected $skippedDetailType = SkippedDetail::class;
  protected $skippedDetailDataType = '';
  protected $successDetailType = SuccessDetail::class;
  protected $successDetailDataType = '';
  /**
   * The simplest way to interpret a result. Required
   *
   * @var string
   */
  public $summary;

  /**
   * More information about a FAILURE outcome. Returns INVALID_ARGUMENT if this
   * field is set but the summary is not FAILURE. Optional
   *
   * @param FailureDetail $failureDetail
   */
  public function setFailureDetail(FailureDetail $failureDetail)
  {
    $this->failureDetail = $failureDetail;
  }
  /**
   * @return FailureDetail
   */
  public function getFailureDetail()
  {
    return $this->failureDetail;
  }
  /**
   * More information about an INCONCLUSIVE outcome. Returns INVALID_ARGUMENT if
   * this field is set but the summary is not INCONCLUSIVE. Optional
   *
   * @param InconclusiveDetail $inconclusiveDetail
   */
  public function setInconclusiveDetail(InconclusiveDetail $inconclusiveDetail)
  {
    $this->inconclusiveDetail = $inconclusiveDetail;
  }
  /**
   * @return InconclusiveDetail
   */
  public function getInconclusiveDetail()
  {
    return $this->inconclusiveDetail;
  }
  /**
   * More information about a SKIPPED outcome. Returns INVALID_ARGUMENT if this
   * field is set but the summary is not SKIPPED. Optional
   *
   * @param SkippedDetail $skippedDetail
   */
  public function setSkippedDetail(SkippedDetail $skippedDetail)
  {
    $this->skippedDetail = $skippedDetail;
  }
  /**
   * @return SkippedDetail
   */
  public function getSkippedDetail()
  {
    return $this->skippedDetail;
  }
  /**
   * More information about a SUCCESS outcome. Returns INVALID_ARGUMENT if this
   * field is set but the summary is not SUCCESS. Optional
   *
   * @param SuccessDetail $successDetail
   */
  public function setSuccessDetail(SuccessDetail $successDetail)
  {
    $this->successDetail = $successDetail;
  }
  /**
   * @return SuccessDetail
   */
  public function getSuccessDetail()
  {
    return $this->successDetail;
  }
  /**
   * The simplest way to interpret a result. Required
   *
   * Accepted values: unset, success, failure, inconclusive, skipped, flaky
   *
   * @param self::SUMMARY_* $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return self::SUMMARY_*
   */
  public function getSummary()
  {
    return $this->summary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Outcome::class, 'Google_Service_ToolResults_Outcome');
