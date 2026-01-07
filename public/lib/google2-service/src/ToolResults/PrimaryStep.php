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

class PrimaryStep extends \Google\Collection
{
  /**
   * Do not use. For proto versioning only.
   */
  public const ROLL_UP_unset = 'unset';
  /**
   * The test matrix run was successful, for instance: - All the test cases
   * passed. - Robo did not detect a crash of the application under test.
   */
  public const ROLL_UP_success = 'success';
  /**
   * A run failed, for instance: - One or more test case failed. - A test timed
   * out. - The application under test crashed.
   */
  public const ROLL_UP_failure = 'failure';
  /**
   * Something unexpected happened. The run should still be considered
   * unsuccessful but this is likely a transient problem and re-running the test
   * might be successful.
   */
  public const ROLL_UP_inconclusive = 'inconclusive';
  /**
   * All tests were skipped, for instance: - All device configurations were
   * incompatible.
   */
  public const ROLL_UP_skipped = 'skipped';
  /**
   * A group of steps that were run with the same configuration had both failure
   * and success outcomes.
   */
  public const ROLL_UP_flaky = 'flaky';
  protected $collection_key = 'individualOutcome';
  protected $individualOutcomeType = IndividualOutcome::class;
  protected $individualOutcomeDataType = 'array';
  /**
   * Rollup test status of multiple steps that were run with the same
   * configuration as a group.
   *
   * @var string
   */
  public $rollUp;

  /**
   * Step Id and outcome of each individual step.
   *
   * @param IndividualOutcome[] $individualOutcome
   */
  public function setIndividualOutcome($individualOutcome)
  {
    $this->individualOutcome = $individualOutcome;
  }
  /**
   * @return IndividualOutcome[]
   */
  public function getIndividualOutcome()
  {
    return $this->individualOutcome;
  }
  /**
   * Rollup test status of multiple steps that were run with the same
   * configuration as a group.
   *
   * Accepted values: unset, success, failure, inconclusive, skipped, flaky
   *
   * @param self::ROLL_UP_* $rollUp
   */
  public function setRollUp($rollUp)
  {
    $this->rollUp = $rollUp;
  }
  /**
   * @return self::ROLL_UP_*
   */
  public function getRollUp()
  {
    return $this->rollUp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrimaryStep::class, 'Google_Service_ToolResults_PrimaryStep');
