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

class AddOnAttachmentStudentSubmission extends \Google\Model
{
  /**
   * No state specified. This should never be returned.
   */
  public const POST_SUBMISSION_STATE_SUBMISSION_STATE_UNSPECIFIED = 'SUBMISSION_STATE_UNSPECIFIED';
  /**
   * The student has never accessed this submission. Attachments are not
   * returned and timestamps is not set.
   */
  public const POST_SUBMISSION_STATE_NEW = 'NEW';
  /**
   * Has been created.
   */
  public const POST_SUBMISSION_STATE_CREATED = 'CREATED';
  /**
   * Has been turned in to the teacher.
   */
  public const POST_SUBMISSION_STATE_TURNED_IN = 'TURNED_IN';
  /**
   * Has been returned to the student.
   */
  public const POST_SUBMISSION_STATE_RETURNED = 'RETURNED';
  /**
   * Student chose to "unsubmit" the assignment.
   */
  public const POST_SUBMISSION_STATE_RECLAIMED_BY_STUDENT = 'RECLAIMED_BY_STUDENT';
  /**
   * Student grade on this attachment. If unset, no grade was set.
   *
   * @var 
   */
  public $pointsEarned;
  /**
   * Submission state of add-on attachment's parent post (i.e. assignment).
   *
   * @var string
   */
  public $postSubmissionState;

  public function setPointsEarned($pointsEarned)
  {
    $this->pointsEarned = $pointsEarned;
  }
  public function getPointsEarned()
  {
    return $this->pointsEarned;
  }
  /**
   * Submission state of add-on attachment's parent post (i.e. assignment).
   *
   * Accepted values: SUBMISSION_STATE_UNSPECIFIED, NEW, CREATED, TURNED_IN,
   * RETURNED, RECLAIMED_BY_STUDENT
   *
   * @param self::POST_SUBMISSION_STATE_* $postSubmissionState
   */
  public function setPostSubmissionState($postSubmissionState)
  {
    $this->postSubmissionState = $postSubmissionState;
  }
  /**
   * @return self::POST_SUBMISSION_STATE_*
   */
  public function getPostSubmissionState()
  {
    return $this->postSubmissionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddOnAttachmentStudentSubmission::class, 'Google_Service_Classroom_AddOnAttachmentStudentSubmission');
