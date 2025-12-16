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

class IndividualOutcome extends \Google\Model
{
  /**
   * Do not use. For proto versioning only.
   */
  public const OUTCOME_SUMMARY_unset = 'unset';
  /**
   * The test matrix run was successful, for instance: - All the test cases
   * passed. - Robo did not detect a crash of the application under test.
   */
  public const OUTCOME_SUMMARY_success = 'success';
  /**
   * A run failed, for instance: - One or more test case failed. - A test timed
   * out. - The application under test crashed.
   */
  public const OUTCOME_SUMMARY_failure = 'failure';
  /**
   * Something unexpected happened. The run should still be considered
   * unsuccessful but this is likely a transient problem and re-running the test
   * might be successful.
   */
  public const OUTCOME_SUMMARY_inconclusive = 'inconclusive';
  /**
   * All tests were skipped, for instance: - All device configurations were
   * incompatible.
   */
  public const OUTCOME_SUMMARY_skipped = 'skipped';
  /**
   * A group of steps that were run with the same configuration had both failure
   * and success outcomes.
   */
  public const OUTCOME_SUMMARY_flaky = 'flaky';
  /**
   * Unique int given to each step. Ranges from 0(inclusive) to total number of
   * steps(exclusive). The primary step is 0.
   *
   * @var int
   */
  public $multistepNumber;
  /**
   * @var string
   */
  public $outcomeSummary;
  protected $runDurationType = Duration::class;
  protected $runDurationDataType = '';
  /**
   * @var string
   */
  public $stepId;

  /**
   * Unique int given to each step. Ranges from 0(inclusive) to total number of
   * steps(exclusive). The primary step is 0.
   *
   * @param int $multistepNumber
   */
  public function setMultistepNumber($multistepNumber)
  {
    $this->multistepNumber = $multistepNumber;
  }
  /**
   * @return int
   */
  public function getMultistepNumber()
  {
    return $this->multistepNumber;
  }
  /**
   * @param self::OUTCOME_SUMMARY_* $outcomeSummary
   */
  public function setOutcomeSummary($outcomeSummary)
  {
    $this->outcomeSummary = $outcomeSummary;
  }
  /**
   * @return self::OUTCOME_SUMMARY_*
   */
  public function getOutcomeSummary()
  {
    return $this->outcomeSummary;
  }
  /**
   * How long it took for this step to run.
   *
   * @param Duration $runDuration
   */
  public function setRunDuration(Duration $runDuration)
  {
    $this->runDuration = $runDuration;
  }
  /**
   * @return Duration
   */
  public function getRunDuration()
  {
    return $this->runDuration;
  }
  /**
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndividualOutcome::class, 'Google_Service_ToolResults_IndividualOutcome');
