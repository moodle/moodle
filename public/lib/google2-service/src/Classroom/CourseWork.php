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

class CourseWork extends \Google\Collection
{
  /**
   * No mode specified. This is never returned.
   */
  public const ASSIGNEE_MODE_ASSIGNEE_MODE_UNSPECIFIED = 'ASSIGNEE_MODE_UNSPECIFIED';
  /**
   * All students can see the item. This is the default state.
   */
  public const ASSIGNEE_MODE_ALL_STUDENTS = 'ALL_STUDENTS';
  /**
   * A subset of the students can see the item.
   */
  public const ASSIGNEE_MODE_INDIVIDUAL_STUDENTS = 'INDIVIDUAL_STUDENTS';
  /**
   * No state specified. This is never returned.
   */
  public const STATE_COURSE_WORK_STATE_UNSPECIFIED = 'COURSE_WORK_STATE_UNSPECIFIED';
  /**
   * Status for work that has been published. This is the default state.
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  /**
   * Status for work that is not yet published. Work in this state is visible
   * only to course teachers and domain administrators.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * Status for work that was published but is now deleted. Work in this state
   * is visible only to course teachers and domain administrators. Work in this
   * state is deleted after some time.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * No modification mode specified. This is never returned.
   */
  public const SUBMISSION_MODIFICATION_MODE_SUBMISSION_MODIFICATION_MODE_UNSPECIFIED = 'SUBMISSION_MODIFICATION_MODE_UNSPECIFIED';
  /**
   * Submissions can be modified before being turned in.
   */
  public const SUBMISSION_MODIFICATION_MODE_MODIFIABLE_UNTIL_TURNED_IN = 'MODIFIABLE_UNTIL_TURNED_IN';
  /**
   * Submissions can be modified at any time.
   */
  public const SUBMISSION_MODIFICATION_MODE_MODIFIABLE = 'MODIFIABLE';
  /**
   * No work type specified. This is never returned.
   */
  public const WORK_TYPE_COURSE_WORK_TYPE_UNSPECIFIED = 'COURSE_WORK_TYPE_UNSPECIFIED';
  /**
   * An assignment.
   */
  public const WORK_TYPE_ASSIGNMENT = 'ASSIGNMENT';
  /**
   * A short answer question.
   */
  public const WORK_TYPE_SHORT_ANSWER_QUESTION = 'SHORT_ANSWER_QUESTION';
  /**
   * A multiple-choice question.
   */
  public const WORK_TYPE_MULTIPLE_CHOICE_QUESTION = 'MULTIPLE_CHOICE_QUESTION';
  protected $collection_key = 'materials';
  /**
   * Absolute link to this course work in the Classroom web UI. This is only
   * populated if `state` is `PUBLISHED`. Read-only.
   *
   * @var string
   */
  public $alternateLink;
  /**
   * Assignee mode of the coursework. If unspecified, the default value is
   * `ALL_STUDENTS`.
   *
   * @var string
   */
  public $assigneeMode;
  protected $assignmentType = Assignment::class;
  protected $assignmentDataType = '';
  /**
   * Whether this course work item is associated with the Developer Console
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
   * Timestamp when this course work was created. Read-only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Identifier for the user that created the coursework. Read-only.
   *
   * @var string
   */
  public $creatorUserId;
  /**
   * Optional description of this course work. If set, the description must be a
   * valid UTF-8 string containing no more than 30,000 characters.
   *
   * @var string
   */
  public $description;
  protected $dueDateType = Date::class;
  protected $dueDateDataType = '';
  protected $dueTimeType = TimeOfDay::class;
  protected $dueTimeDataType = '';
  protected $gradeCategoryType = GradeCategory::class;
  protected $gradeCategoryDataType = '';
  /**
   * Identifier of the grading period associated with the coursework. * At
   * creation, if unspecified, the grading period ID will be set based on the
   * `dueDate` (or `scheduledTime` if no `dueDate` is set). * To indicate no
   * association to any grading period, set this field to an empty string ("").
   * * If specified, it must match an existing grading period ID in the course.
   *
   * @var string
   */
  public $gradingPeriodId;
  /**
   * Classroom-assigned identifier of this course work, unique per course. Read-
   * only.
   *
   * @var string
   */
  public $id;
  protected $individualStudentsOptionsType = IndividualStudentsOptions::class;
  protected $individualStudentsOptionsDataType = '';
  protected $materialsType = Material::class;
  protected $materialsDataType = 'array';
  /**
   * Maximum grade for this course work. If zero or unspecified, this assignment
   * is considered ungraded. This must be a non-negative integer value.
   *
   * @var 
   */
  public $maxPoints;
  protected $multipleChoiceQuestionType = MultipleChoiceQuestion::class;
  protected $multipleChoiceQuestionDataType = '';
  /**
   * Optional timestamp when this course work is scheduled to be published.
   *
   * @var string
   */
  public $scheduledTime;
  /**
   * Status of this course work. If unspecified, the default state is `DRAFT`.
   *
   * @var string
   */
  public $state;
  /**
   * Setting to determine when students are allowed to modify submissions. If
   * unspecified, the default value is `MODIFIABLE_UNTIL_TURNED_IN`.
   *
   * @var string
   */
  public $submissionModificationMode;
  /**
   * Title of this course work. The title must be a valid UTF-8 string
   * containing between 1 and 3000 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Identifier for the topic that this coursework is associated with. Must
   * match an existing topic in the course.
   *
   * @var string
   */
  public $topicId;
  /**
   * Timestamp of the most recent change to this course work. Read-only.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Type of this course work. The type is set when the course work is created
   * and cannot be changed.
   *
   * @var string
   */
  public $workType;

  /**
   * Absolute link to this course work in the Classroom web UI. This is only
   * populated if `state` is `PUBLISHED`. Read-only.
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
  /**
   * Assignee mode of the coursework. If unspecified, the default value is
   * `ALL_STUDENTS`.
   *
   * Accepted values: ASSIGNEE_MODE_UNSPECIFIED, ALL_STUDENTS,
   * INDIVIDUAL_STUDENTS
   *
   * @param self::ASSIGNEE_MODE_* $assigneeMode
   */
  public function setAssigneeMode($assigneeMode)
  {
    $this->assigneeMode = $assigneeMode;
  }
  /**
   * @return self::ASSIGNEE_MODE_*
   */
  public function getAssigneeMode()
  {
    return $this->assigneeMode;
  }
  /**
   * Assignment details. This is populated only when `work_type` is
   * `ASSIGNMENT`. Read-only.
   *
   * @param Assignment $assignment
   */
  public function setAssignment(Assignment $assignment)
  {
    $this->assignment = $assignment;
  }
  /**
   * @return Assignment
   */
  public function getAssignment()
  {
    return $this->assignment;
  }
  /**
   * Whether this course work item is associated with the Developer Console
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
   * Timestamp when this course work was created. Read-only.
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
  /**
   * Identifier for the user that created the coursework. Read-only.
   *
   * @param string $creatorUserId
   */
  public function setCreatorUserId($creatorUserId)
  {
    $this->creatorUserId = $creatorUserId;
  }
  /**
   * @return string
   */
  public function getCreatorUserId()
  {
    return $this->creatorUserId;
  }
  /**
   * Optional description of this course work. If set, the description must be a
   * valid UTF-8 string containing no more than 30,000 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional date, in UTC, that submissions for this course work are due. This
   * must be specified if `due_time` is specified.
   *
   * @param Date $dueDate
   */
  public function setDueDate(Date $dueDate)
  {
    $this->dueDate = $dueDate;
  }
  /**
   * @return Date
   */
  public function getDueDate()
  {
    return $this->dueDate;
  }
  /**
   * Optional time of day, in UTC, that submissions for this course work are
   * due. This must be specified if `due_date` is specified.
   *
   * @param TimeOfDay $dueTime
   */
  public function setDueTime(TimeOfDay $dueTime)
  {
    $this->dueTime = $dueTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getDueTime()
  {
    return $this->dueTime;
  }
  /**
   * The category that this coursework's grade contributes to. Present only when
   * a category has been chosen for the coursework. May be used in calculating
   * the overall grade. Read-only.
   *
   * @param GradeCategory $gradeCategory
   */
  public function setGradeCategory(GradeCategory $gradeCategory)
  {
    $this->gradeCategory = $gradeCategory;
  }
  /**
   * @return GradeCategory
   */
  public function getGradeCategory()
  {
    return $this->gradeCategory;
  }
  /**
   * Identifier of the grading period associated with the coursework. * At
   * creation, if unspecified, the grading period ID will be set based on the
   * `dueDate` (or `scheduledTime` if no `dueDate` is set). * To indicate no
   * association to any grading period, set this field to an empty string ("").
   * * If specified, it must match an existing grading period ID in the course.
   *
   * @param string $gradingPeriodId
   */
  public function setGradingPeriodId($gradingPeriodId)
  {
    $this->gradingPeriodId = $gradingPeriodId;
  }
  /**
   * @return string
   */
  public function getGradingPeriodId()
  {
    return $this->gradingPeriodId;
  }
  /**
   * Classroom-assigned identifier of this course work, unique per course. Read-
   * only.
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
   * Identifiers of students with access to the coursework. This field is set
   * only if `assigneeMode` is `INDIVIDUAL_STUDENTS`. If the `assigneeMode` is
   * `INDIVIDUAL_STUDENTS`, then only students specified in this field are
   * assigned the coursework.
   *
   * @param IndividualStudentsOptions $individualStudentsOptions
   */
  public function setIndividualStudentsOptions(IndividualStudentsOptions $individualStudentsOptions)
  {
    $this->individualStudentsOptions = $individualStudentsOptions;
  }
  /**
   * @return IndividualStudentsOptions
   */
  public function getIndividualStudentsOptions()
  {
    return $this->individualStudentsOptions;
  }
  /**
   * Additional materials. CourseWork must have no more than 20 material items.
   *
   * @param Material[] $materials
   */
  public function setMaterials($materials)
  {
    $this->materials = $materials;
  }
  /**
   * @return Material[]
   */
  public function getMaterials()
  {
    return $this->materials;
  }
  public function setMaxPoints($maxPoints)
  {
    $this->maxPoints = $maxPoints;
  }
  public function getMaxPoints()
  {
    return $this->maxPoints;
  }
  /**
   * Multiple choice question details. For read operations, this field is
   * populated only when `work_type` is `MULTIPLE_CHOICE_QUESTION`. For write
   * operations, this field must be specified when creating course work with a
   * `work_type` of `MULTIPLE_CHOICE_QUESTION`, and it must not be set
   * otherwise.
   *
   * @param MultipleChoiceQuestion $multipleChoiceQuestion
   */
  public function setMultipleChoiceQuestion(MultipleChoiceQuestion $multipleChoiceQuestion)
  {
    $this->multipleChoiceQuestion = $multipleChoiceQuestion;
  }
  /**
   * @return MultipleChoiceQuestion
   */
  public function getMultipleChoiceQuestion()
  {
    return $this->multipleChoiceQuestion;
  }
  /**
   * Optional timestamp when this course work is scheduled to be published.
   *
   * @param string $scheduledTime
   */
  public function setScheduledTime($scheduledTime)
  {
    $this->scheduledTime = $scheduledTime;
  }
  /**
   * @return string
   */
  public function getScheduledTime()
  {
    return $this->scheduledTime;
  }
  /**
   * Status of this course work. If unspecified, the default state is `DRAFT`.
   *
   * Accepted values: COURSE_WORK_STATE_UNSPECIFIED, PUBLISHED, DRAFT, DELETED
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
   * Setting to determine when students are allowed to modify submissions. If
   * unspecified, the default value is `MODIFIABLE_UNTIL_TURNED_IN`.
   *
   * Accepted values: SUBMISSION_MODIFICATION_MODE_UNSPECIFIED,
   * MODIFIABLE_UNTIL_TURNED_IN, MODIFIABLE
   *
   * @param self::SUBMISSION_MODIFICATION_MODE_* $submissionModificationMode
   */
  public function setSubmissionModificationMode($submissionModificationMode)
  {
    $this->submissionModificationMode = $submissionModificationMode;
  }
  /**
   * @return self::SUBMISSION_MODIFICATION_MODE_*
   */
  public function getSubmissionModificationMode()
  {
    return $this->submissionModificationMode;
  }
  /**
   * Title of this course work. The title must be a valid UTF-8 string
   * containing between 1 and 3000 characters.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Identifier for the topic that this coursework is associated with. Must
   * match an existing topic in the course.
   *
   * @param string $topicId
   */
  public function setTopicId($topicId)
  {
    $this->topicId = $topicId;
  }
  /**
   * @return string
   */
  public function getTopicId()
  {
    return $this->topicId;
  }
  /**
   * Timestamp of the most recent change to this course work. Read-only.
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
   * Type of this course work. The type is set when the course work is created
   * and cannot be changed.
   *
   * Accepted values: COURSE_WORK_TYPE_UNSPECIFIED, ASSIGNMENT,
   * SHORT_ANSWER_QUESTION, MULTIPLE_CHOICE_QUESTION
   *
   * @param self::WORK_TYPE_* $workType
   */
  public function setWorkType($workType)
  {
    $this->workType = $workType;
  }
  /**
   * @return self::WORK_TYPE_*
   */
  public function getWorkType()
  {
    return $this->workType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CourseWork::class, 'Google_Service_Classroom_CourseWork');
