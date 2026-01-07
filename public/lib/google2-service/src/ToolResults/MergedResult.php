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

class MergedResult extends \Google\Collection
{
  /**
   * Should never be in this state. Exists for proto deserialization backward
   * compatibility.
   */
  public const STATE_unknownState = 'unknownState';
  /**
   * The Execution/Step is created, ready to run, but not running yet. If an
   * Execution/Step is created without initial state, it is assumed that the
   * Execution/Step is in PENDING state.
   */
  public const STATE_pending = 'pending';
  /**
   * The Execution/Step is in progress.
   */
  public const STATE_inProgress = 'inProgress';
  /**
   * The finalized, immutable state. Steps/Executions in this state cannot be
   * modified.
   */
  public const STATE_complete = 'complete';
  protected $collection_key = 'testSuiteOverviews';
  protected $outcomeType = Outcome::class;
  protected $outcomeDataType = '';
  /**
   * State of the resource
   *
   * @var string
   */
  public $state;
  protected $testSuiteOverviewsType = TestSuiteOverview::class;
  protected $testSuiteOverviewsDataType = 'array';

  /**
   * Outcome of the resource
   *
   * @param Outcome $outcome
   */
  public function setOutcome(Outcome $outcome)
  {
    $this->outcome = $outcome;
  }
  /**
   * @return Outcome
   */
  public function getOutcome()
  {
    return $this->outcome;
  }
  /**
   * State of the resource
   *
   * Accepted values: unknownState, pending, inProgress, complete
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The combined and rolled-up result of each test suite that was run as part
   * of this environment. Combining: When the test cases from a suite are run in
   * different steps (sharding), the results are added back together in one
   * overview. (e.g., if shard1 has 2 failures and shard2 has 1 failure than the
   * overview failure_count = 3). Rollup: When test cases from the same suite
   * are run multiple times (flaky), the results are combined (e.g., if
   * testcase1.run1 fails, testcase1.run2 passes, and both testcase2.run1 and
   * testcase2.run2 fail then the overview flaky_count = 1 and failure_count =
   * 1).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MergedResult::class, 'Google_Service_ToolResults_MergedResult');
