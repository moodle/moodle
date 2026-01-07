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

class GradeHistory extends \Google\Model
{
  /**
   * No grade change type specified. This should never be returned.
   */
  public const GRADE_CHANGE_TYPE_UNKNOWN_GRADE_CHANGE_TYPE = 'UNKNOWN_GRADE_CHANGE_TYPE';
  /**
   * A change in the numerator of the draft grade.
   */
  public const GRADE_CHANGE_TYPE_DRAFT_GRADE_POINTS_EARNED_CHANGE = 'DRAFT_GRADE_POINTS_EARNED_CHANGE';
  /**
   * A change in the numerator of the assigned grade.
   */
  public const GRADE_CHANGE_TYPE_ASSIGNED_GRADE_POINTS_EARNED_CHANGE = 'ASSIGNED_GRADE_POINTS_EARNED_CHANGE';
  /**
   * A change in the denominator of the grade.
   */
  public const GRADE_CHANGE_TYPE_MAX_POINTS_CHANGE = 'MAX_POINTS_CHANGE';
  /**
   * The teacher who made the grade change.
   *
   * @var string
   */
  public $actorUserId;
  /**
   * The type of grade change at this time in the submission grade history.
   *
   * @var string
   */
  public $gradeChangeType;
  /**
   * When the grade of the submission was changed.
   *
   * @var string
   */
  public $gradeTimestamp;
  /**
   * The denominator of the grade at this time in the submission grade history.
   *
   * @var 
   */
  public $maxPoints;
  /**
   * The numerator of the grade at this time in the submission grade history.
   *
   * @var 
   */
  public $pointsEarned;

  /**
   * The teacher who made the grade change.
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
   * The type of grade change at this time in the submission grade history.
   *
   * Accepted values: UNKNOWN_GRADE_CHANGE_TYPE,
   * DRAFT_GRADE_POINTS_EARNED_CHANGE, ASSIGNED_GRADE_POINTS_EARNED_CHANGE,
   * MAX_POINTS_CHANGE
   *
   * @param self::GRADE_CHANGE_TYPE_* $gradeChangeType
   */
  public function setGradeChangeType($gradeChangeType)
  {
    $this->gradeChangeType = $gradeChangeType;
  }
  /**
   * @return self::GRADE_CHANGE_TYPE_*
   */
  public function getGradeChangeType()
  {
    return $this->gradeChangeType;
  }
  /**
   * When the grade of the submission was changed.
   *
   * @param string $gradeTimestamp
   */
  public function setGradeTimestamp($gradeTimestamp)
  {
    $this->gradeTimestamp = $gradeTimestamp;
  }
  /**
   * @return string
   */
  public function getGradeTimestamp()
  {
    return $this->gradeTimestamp;
  }
  public function setMaxPoints($maxPoints)
  {
    $this->maxPoints = $maxPoints;
  }
  public function getMaxPoints()
  {
    return $this->maxPoints;
  }
  public function setPointsEarned($pointsEarned)
  {
    $this->pointsEarned = $pointsEarned;
  }
  public function getPointsEarned()
  {
    return $this->pointsEarned;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GradeHistory::class, 'Google_Service_Classroom_GradeHistory');
