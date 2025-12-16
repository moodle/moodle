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

namespace Google\Service\Classroom;

class StateHistory extends \Google\Model
{
  /**
   * No state specified. This should never be returned.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Submission has been created.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * The student has turned in an assigned document, which may or may not be a
   * template.
   */
  public const STATE_TURNED_IN = 'TURNED_IN';
  /**
   * The teacher has returned the assigned document to the student.
   */
  public const STATE_RETURNED = 'RETURNED';
  /**
   * The student turned in the assigned document, and then chose to "unsubmit"
   * the assignment, giving the student control again as the owner.
   */
  public const STATE_RECLAIMED_BY_STUDENT = 'RECLAIMED_BY_STUDENT';
  /**
   * The student edited their submission after turning it in. Currently, only
   * used by Questions, when the student edits their answer.
   */
  public const STATE_STUDENT_EDITED_AFTER_TURN_IN = 'STUDENT_EDITED_AFTER_TURN_IN';
  /**
   * The teacher or student who made the change.
   *
   * @var string
   */
  public $actorUserId;
  /**
   * The workflow pipeline stage.
   *
   * @var string
   */
  public $state;
  /**
   * When the submission entered this state.
   *
   * @var string
   */
  public $stateTimestamp;

  /**
   * The teacher or student who made the change.
   *
   * @param string $actorUserId
   */
  public function setActorUserId($actorUserId)
  {
    $this->actorUserId = $actorUserId;
  }
  /**
   * @return string
   */
  public function getActorUserId()
  {
    return $this->actorUserId;
  }
  /**
   * The workflow pipeline stage.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATED, TURNED_IN, RETURNED,
   * RECLAIMED_BY_STUDENT, STUDENT_EDITED_AFTER_TURN_IN
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
   * When the submission entered this state.
   *
   * @param string $stateTimestamp
   */
  public function setStateTimestamp($stateTimestamp)
  {
    $this->stateTimestamp = $stateTimestamp;
  }
  /**
   * @return string
   */
  public function getStateTimestamp()
  {
    return $this->stateTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StateHistory::class, 'Google_Service_Classroom_StateHistory');
