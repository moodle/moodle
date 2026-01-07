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

class StudentSubmission extends \Google\Collection
{
  /**
   * No work type specified. This is never returned.
   */
  public const COURSE_WORK_TYPE_COURSE_WORK_TYPE_UNSPECIFIED = 'COURSE_WORK_TYPE_UNSPECIFIED';
  /**
   * An assignment.
   */
  public const COURSE_WORK_TYPE_ASSIGNMENT = 'ASSIGNMENT';
  /**
   * A short answer question.
   */
  public const COURSE_WORK_TYPE_SHORT_ANSWER_QUESTION = 'SHORT_ANSWER_QUESTION';
  /**
   * A multiple-choice question.
   */
  public const COURSE_WORK_TYPE_MULTIPLE_CHOICE_QUESTION = 'MULTIPLE_CHOICE_QUESTION';
  /**
   * No state specified. This should never be returned.
   */
  public const STATE_SUBMISSION_STATE_UNSPECIFIED = 'SUBMISSION_STATE_UNSPECIFIED';
  /**
   * The student has never accessed this submission. Attachments are not
   * returned and timestamps is not set.
   */
  public const STATE_NEW = 'NEW';
  /**
   * Has been created.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * Has been turned in to the teacher.
   */
  public const STATE_TURNED_IN = 'TURNED_IN';
  /**
   * Has been returned to the student.
   */
  public const STATE_RETURNED = 'RETURNED';
  /**
   * Student chose to "unsubmit" the assignment.
   */
  public const STATE_RECLAIMED_BY_STUDENT = 'RECLAIMED_BY_STUDENT';
  protected $collection_key = 'submissionHistory';
  /**
   * Absolute link to the submission in the Classroom web UI. Read-only.
   *
   * @var string
   */
  public $alternateLink;
  /**
   * Optional grade. If unset, no grade was set. This value must be non-
   * negative. Decimal (that is, non-integer) values are allowed, but are
   * rounded to two decimal places. This may be modified only by course
   * teachers.
   *
   * @var 
   */
  public $assignedGrade;
  protected $assignedRubricGradesType = RubricGrade::class;
  protected $assignedRubricGradesDataType = 'map';
  protected $assignmentSubmissionType = AssignmentSubmission::class;
  protected $assignmentSubmissionDataType = '';
  /**
   * Whether this student submission is associated with the Developer Console
   * project making the request. See CreateCourseWork for more details. Read-
   * only.
   *
   * @var bool
   */
  public $associatedWithDeveloper;
  /**
   * Identifier of the course. Read-only.
   *
   * @var string
   */
  public $courseId;
  /**
   * Identifier for the course work this corresponds to. Read-only.
   *
   * @var string
   */
  public $courseWorkId;
  /**
   * Type of course work this submission is for. Read-only.
   *
   * @var string
   */
  public $courseWorkType;
  /**
   * Creation time of this submission. This may be unset if the student has not
   * accessed this item. Read-only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Optional pending grade. If unset, no grade was set. This value must be non-
   * negative. Decimal (that is, non-integer) values are allowed, but are
   * rounded to two decimal places. This is only visible to and modifiable by
   * course teachers.
   *
   * @var 
   */
  public $draftGrade;
  protected $draftRubricGradesType = RubricGrade::class;
  protected $draftRubricGradesDataType = 'map';
  /**
   * Classroom-assigned Identifier for the student submission. This is unique
   * among submissions for the relevant course work. Read-only.
   *
   * @var string
   */
  public $id;
  /**
   * Whether this submission is late. Read-only.
   *
   * @var bool
   */
  public $late;
  protected $multipleChoiceSubmissionType = MultipleChoiceSubmission::class;
  protected $multipleChoiceSubmissionDataType = '';
  protected $shortAnswerSubmissionType = ShortAnswerSubmission::class;
  protected $shortAnswerSubmissionDataType = '';
  /**
   * State of this submission. Read-only.
   *
   * @var string
   */
  public $state;
  protected $submissionHistoryType = SubmissionHistory::class;
  protected $submissionHistoryDataType = 'array';
  /**
   * Last update time of this submission. This may be unset if the student has
   * not accessed this item. Read-only.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Identifier for the student that owns this submission. Read-only.
   *
   * @var string
   */
  public $userId;

  /**
   * Absolute link to the submission in the Classroom web UI. Read-only.
   *
   * @param string $alternateLink
   */
  public function setAlternateLink($alternateLink)
  {
    $this->alternateLink = $alternateLink;
  }
  /**
   * @return string
   */
  public function getAlternateLink()
  {
    return $this->alternateLink;
  }
  public function setAssignedGrade($assignedGrade)
  {
    $this->assignedGrade = $assignedGrade;
  }
  public function getAssignedGrade()
  {
    return $this->assignedGrade;
  }
  /**
   * Assigned rubric grades based on the rubric's Criteria. This map is empty if
   * there is no rubric attached to this course work or if a rubric is attached,
   * but no grades have been set on any Criteria. Entries are only populated for
   * grades that have been set. Key: The rubric's criterion ID. Read-only.
   *
   * @param RubricGrade[] $assignedRubricGrades
   */
  public function setAssignedRubricGrades($assignedRubricGrades)
  {
    $this->assignedRubricGrades = $assignedRubricGrades;
  }
  /**
   * @return RubricGrade[]
   */
  public function getAssignedRubricGrades()
  {
    return $this->assignedRubricGrades;
  }
  /**
   * Submission content when course_work_type is ASSIGNMENT. Students can modify
   * this content using ModifyAttachments.
   *
   * @param AssignmentSubmission $assignmentSubmission
   */
  public function setAssignmentSubmission(AssignmentSubmission $assignmentSubmission)
  {
    $this->assignmentSubmission = $assignmentSubmission;
  }
  /**
   * @return AssignmentSubmission
   */
  public function getAssignmentSubmission()
  {
    return $this->assignmentSubmission;
  }
  /**
   * Whether this student submission is associated with the Developer Console
   * project making the request. See CreateCourseWork for more details. Read-
   * only.
   *
   * @param bool $associatedWithDeveloper
   */
  public function setAssociatedWithDeveloper($associatedWithDeveloper)
  {
    $this->associatedWithDeveloper = $associatedWithDeveloper;
  }
  /**
   * @return bool
   */
  public function getAssociatedWithDeveloper()
  {
    return $this->associatedWithDeveloper;
  }
  /**
   * Identifier of the course. Read-only.
   *
   * @param string $courseId
   */
  public function setCourseId($courseId)
  {
    $this->courseId = $courseId;
  }
  /**
   * @return string
   */
  public function getCourseId()
  {
    return $this->courseId;
  }
  /**
   * Identifier for the course work this corresponds to. Read-only.
   *
   * @param string $courseWorkId
   */
  public function setCourseWorkId($courseWorkId)
  {
    $this->courseWorkId = $courseWorkId;
  }
  /**
   * @return string
   */
  public function getCourseWorkId()
  {
    return $this->courseWorkId;
  }
  /**
   * Type of course work this submission is for. Read-only.
   *
   * Accepted values: COURSE_WORK_TYPE_UNSPECIFIED, ASSIGNMENT,
   * SHORT_ANSWER_QUESTION, MULTIPLE_CHOICE_QUESTION
   *
   * @param self::COURSE_WORK_TYPE_* $courseWorkType
   */
  public function setCourseWorkType($courseWorkType)
  {
    $this->courseWorkType = $courseWorkType;
  }
  /**
   * @return self::COURSE_WORK_TYPE_*
   */
  public function getCourseWorkType()
  {
    return $this->courseWorkType;
  }
  /**
   * Creation time of this submission. This may be unset if the student has not
   * accessed this item. Read-only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  public function setDraftGrade($draftGrade)
  {
    $this->draftGrade = $draftGrade;
  }
  public function getDraftGrade()
  {
    return $this->draftGrade;
  }
  /**
   * Pending rubric grades based on the rubric's criteria. This map is empty if
   * there is no rubric attached to this course work or if a rubric is attached,
   * but no grades have been set on any criteria. Entries are only populated for
   * grades that have been set. Key: The rubric's criterion ID. Read-only.
   *
   * @param RubricGrade[] $draftRubricGrades
   */
  public function setDraftRubricGrades($draftRubricGrades)
  {
    $this->draftRubricGrades = $draftRubricGrades;
  }
  /**
   * @return RubricGrade[]
   */
  public function getDraftRubricGrades()
  {
    return $this->draftRubricGrades;
  }
  /**
   * Classroom-assigned Identifier for the student submission. This is unique
   * among submissions for the relevant course work. Read-only.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Whether this submission is late. Read-only.
   *
   * @param bool $late
   */
  public function setLate($late)
  {
    $this->late = $late;
  }
  /**
   * @return bool
   */
  public function getLate()
  {
    return $this->late;
  }
  /**
   * Submission content when course_work_type is MULTIPLE_CHOICE_QUESTION.
   *
   * @param MultipleChoiceSubmission $multipleChoiceSubmission
   */
  public function setMultipleChoiceSubmission(MultipleChoiceSubmission $multipleChoiceSubmission)
  {
    $this->multipleChoiceSubmission = $multipleChoiceSubmission;
  }
  /**
   * @return MultipleChoiceSubmission
   */
  public function getMultipleChoiceSubmission()
  {
    return $this->multipleChoiceSubmission;
  }
  /**
   * Submission content when course_work_type is SHORT_ANSWER_QUESTION.
   *
   * @param ShortAnswerSubmission $shortAnswerSubmission
   */
  public function setShortAnswerSubmission(ShortAnswerSubmission $shortAnswerSubmission)
  {
    $this->shortAnswerSubmission = $shortAnswerSubmission;
  }
  /**
   * @return ShortAnswerSubmission
   */
  public function getShortAnswerSubmission()
  {
    return $this->shortAnswerSubmission;
  }
  /**
   * State of this submission. Read-only.
   *
   * Accepted values: SUBMISSION_STATE_UNSPECIFIED, NEW, CREATED, TURNED_IN,
   * RETURNED, RECLAIMED_BY_STUDENT
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
   * The history of the submission (includes state and grade histories). Read-
   * only.
   *
   * @param SubmissionHistory[] $submissionHistory
   */
  public function setSubmissionHistory($submissionHistory)
  {
    $this->submissionHistory = $submissionHistory;
  }
  /**
   * @return SubmissionHistory[]
   */
  public function getSubmissionHistory()
  {
    return $this->submissionHistory;
  }
  /**
   * Last update time of this submission. This may be unset if the student has
   * not accessed this item. Read-only.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Identifier for the student that owns this submission. Read-only.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StudentSubmission::class, 'Google_Service_Classroom_StudentSubmission');
