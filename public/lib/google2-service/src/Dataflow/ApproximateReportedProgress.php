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

namespace Google\Service\Dataflow;

class ApproximateReportedProgress extends \Google\Model
{
  protected $consumedParallelismType = ReportedParallelism::class;
  protected $consumedParallelismDataType = '';
  /**
   * Completion as fraction of the input consumed, from 0.0 (beginning, nothing
   * consumed), to 1.0 (end of the input, entire input consumed).
   *
   * @var 
   */
  public $fractionConsumed;
  protected $positionType = Position::class;
  protected $positionDataType = '';
  protected $remainingParallelismType = ReportedParallelism::class;
  protected $remainingParallelismDataType = '';

  /**
   * Total amount of parallelism in the portion of input of this task that has
   * already been consumed and is no longer active. In the first two examples
   * above (see remaining_parallelism), the value should be 29 or 2
   * respectively. The sum of remaining_parallelism and consumed_parallelism
   * should equal the total amount of parallelism in this work item. If
   * specified, must be finite.
   *
   * @param ReportedParallelism $consumedParallelism
   */
  public function setConsumedParallelism(ReportedParallelism $consumedParallelism)
  {
    $this->consumedParallelism = $consumedParallelism;
  }
  /**
   * @return ReportedParallelism
   */
  public function getConsumedParallelism()
  {
    return $this->consumedParallelism;
  }
  public function setFractionConsumed($fractionConsumed)
  {
    $this->fractionConsumed = $fractionConsumed;
  }
  public function getFractionConsumed()
  {
    return $this->fractionConsumed;
  }
  /**
   * A Position within the work to represent a progress.
   *
   * @param Position $position
   */
  public function setPosition(Position $position)
  {
    $this->position = $position;
  }
  /**
   * @return Position
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Total amount of parallelism in the input of this task that remains, (i.e.
   * can be delegated to this task and any new tasks via dynamic splitting).
   * Always at least 1 for non-finished work items and 0 for finished. "Amount
   * of parallelism" refers to how many non-empty parts of the input can be read
   * in parallel. This does not necessarily equal number of records. An input
   * that can be read in parallel down to the individual records is called
   * "perfectly splittable". An example of non-perfectly parallelizable input is
   * a block-compressed file format where a block of records has to be read as a
   * whole, but different blocks can be read in parallel. Examples: * If we are
   * processing record #30 (starting at 1) out of 50 in a perfectly splittable
   * 50-record input, this value should be 21 (20 remaining + 1 current). * If
   * we are reading through block 3 in a block-compressed file consisting of 5
   * blocks, this value should be 3 (since blocks 4 and 5 can be processed in
   * parallel by new tasks via dynamic splitting and the current task remains
   * processing block 3). * If we are reading through the last block in a block-
   * compressed file, or reading or processing the last record in a perfectly
   * splittable input, this value should be 1, because apart from the current
   * task, no additional remainder can be split off.
   *
   * @param ReportedParallelism $remainingParallelism
   */
  public function setRemainingParallelism(ReportedParallelism $remainingParallelism)
  {
    $this->remainingParallelism = $remainingParallelism;
  }
  /**
   * @return ReportedParallelism
   */
  public function getRemainingParallelism()
  {
    return $this->remainingParallelism;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApproximateReportedProgress::class, 'Google_Service_Dataflow_ApproximateReportedProgress');
