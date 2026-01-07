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

namespace Google\Service\WorkflowExecutions;

class StepEntryMetadata extends \Google\Model
{
  /**
   * Current step entry does not have any progress data.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_UNSPECIFIED = 'PROGRESS_TYPE_UNSPECIFIED';
  /**
   * Current step entry is in progress of a FOR step.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_FOR = 'PROGRESS_TYPE_FOR';
  /**
   * Current step entry is in progress of a SWITCH step.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_SWITCH = 'PROGRESS_TYPE_SWITCH';
  /**
   * Current step entry is in progress of a RETRY step.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_RETRY = 'PROGRESS_TYPE_RETRY';
  /**
   * Current step entry is in progress of a PARALLEL FOR step.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_PARALLEL_FOR = 'PROGRESS_TYPE_PARALLEL_FOR';
  /**
   * Current step entry is in progress of a PARALLEL BRANCH step.
   */
  public const PROGRESS_TYPE_PROGRESS_TYPE_PARALLEL_BRANCH = 'PROGRESS_TYPE_PARALLEL_BRANCH';
  /**
   * Expected iteration represents the expected number of iterations in the
   * step's progress.
   *
   * @var string
   */
  public $expectedIteration;
  /**
   * Progress number represents the current state of the current progress. eg: A
   * step entry represents the 4th iteration in a progress of PROGRESS_TYPE_FOR.
   * Note: This field is only populated when an iteration exists and the
   * starting value is 1.
   *
   * @var string
   */
  public $progressNumber;
  /**
   * Progress type of this step entry.
   *
   * @var string
   */
  public $progressType;
  /**
   * Child thread id that this step entry belongs to.
   *
   * @var string
   */
  public $threadId;

  /**
   * Expected iteration represents the expected number of iterations in the
   * step's progress.
   *
   * @param string $expectedIteration
   */
  public function setExpectedIteration($expectedIteration)
  {
    $this->expectedIteration = $expectedIteration;
  }
  /**
   * @return string
   */
  public function getExpectedIteration()
  {
    return $this->expectedIteration;
  }
  /**
   * Progress number represents the current state of the current progress. eg: A
   * step entry represents the 4th iteration in a progress of PROGRESS_TYPE_FOR.
   * Note: This field is only populated when an iteration exists and the
   * starting value is 1.
   *
   * @param string $progressNumber
   */
  public function setProgressNumber($progressNumber)
  {
    $this->progressNumber = $progressNumber;
  }
  /**
   * @return string
   */
  public function getProgressNumber()
  {
    return $this->progressNumber;
  }
  /**
   * Progress type of this step entry.
   *
   * Accepted values: PROGRESS_TYPE_UNSPECIFIED, PROGRESS_TYPE_FOR,
   * PROGRESS_TYPE_SWITCH, PROGRESS_TYPE_RETRY, PROGRESS_TYPE_PARALLEL_FOR,
   * PROGRESS_TYPE_PARALLEL_BRANCH
   *
   * @param self::PROGRESS_TYPE_* $progressType
   */
  public function setProgressType($progressType)
  {
    $this->progressType = $progressType;
  }
  /**
   * @return self::PROGRESS_TYPE_*
   */
  public function getProgressType()
  {
    return $this->progressType;
  }
  /**
   * Child thread id that this step entry belongs to.
   *
   * @param string $threadId
   */
  public function setThreadId($threadId)
  {
    $this->threadId = $threadId;
  }
  /**
   * @return string
   */
  public function getThreadId()
  {
    return $this->threadId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StepEntryMetadata::class, 'Google_Service_WorkflowExecutions_StepEntryMetadata');
