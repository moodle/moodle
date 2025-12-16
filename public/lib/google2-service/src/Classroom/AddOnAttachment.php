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

class AddOnAttachment extends \Google\Collection
{
  protected $collection_key = 'copyHistory';
  protected $copyHistoryType = CopyHistory::class;
  protected $copyHistoryDataType = 'array';
  /**
   * Immutable. Identifier of the course.
   *
   * @var string
   */
  public $courseId;
  protected $dueDateType = Date::class;
  protected $dueDateDataType = '';
  protected $dueTimeType = TimeOfDay::class;
  protected $dueTimeDataType = '';
  /**
   * Immutable. Classroom-assigned identifier for this attachment, unique per
   * post.
   *
   * @var string
   */
  public $id;
  /**
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. Unique per
   * course.
   *
   * @var string
   */
  public $itemId;
  /**
   * Maximum grade for this attachment. Can only be set if
   * `studentWorkReviewUri` is set. Set to a non-zero value to indicate that the
   * attachment supports grade passback. If set, this must be a non-negative
   * integer value. When set to zero, the attachment will not support grade
   * passback.
   *
   * @var 
   */
  public $maxPoints;
  /**
   * Immutable. Deprecated, use `item_id` instead.
   *
   * @deprecated
   * @var string
   */
  public $postId;
  protected $studentViewUriType = EmbedUri::class;
  protected $studentViewUriDataType = '';
  protected $studentWorkReviewUriType = EmbedUri::class;
  protected $studentWorkReviewUriDataType = '';
  protected $teacherViewUriType = EmbedUri::class;
  protected $teacherViewUriDataType = '';
  /**
   * Required. Title of this attachment. The title must be between 1 and 1000
   * characters.
   *
   * @var string
   */
  public $title;

  /**
   * Output only. Identifiers of attachments that were previous copies of this
   * attachment. If the attachment was previously copied by virtue of its parent
   * post being copied, this enumerates the identifiers of attachments that were
   * its previous copies in ascending chronological order of copy.
   *
   * @param CopyHistory[] $copyHistory
   */
  public function setCopyHistory($copyHistory)
  {
    $this->copyHistory = $copyHistory;
  }
  /**
   * @return CopyHistory[]
   */
  public function getCopyHistory()
  {
    return $this->copyHistory;
  }
  /**
   * Immutable. Identifier of the course.
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
   * Date, in UTC, that work on this attachment is due. This must be specified
   * if `due_time` is specified.
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
   * Time of day, in UTC, that work on this attachment is due. This must be
   * specified if `due_date` is specified.
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
   * Immutable. Classroom-assigned identifier for this attachment, unique per
   * post.
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
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. Unique per
   * course.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
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
   * Immutable. Deprecated, use `item_id` instead.
   *
   * @deprecated
   * @param string $postId
   */
  public function setPostId($postId)
  {
    $this->postId = $postId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPostId()
  {
    return $this->postId;
  }
  /**
   * Required. URI to show the student view of the attachment. The URI will be
   * opened in an iframe with the `courseId`, `itemId`, `itemType`, and
   * `attachmentId` query parameters set.
   *
   * @param EmbedUri $studentViewUri
   */
  public function setStudentViewUri(EmbedUri $studentViewUri)
  {
    $this->studentViewUri = $studentViewUri;
  }
  /**
   * @return EmbedUri
   */
  public function getStudentViewUri()
  {
    return $this->studentViewUri;
  }
  /**
   * URI for the teacher to see student work on the attachment, if applicable.
   * The URI will be opened in an iframe with the `courseId`, `itemId`,
   * `itemType`, `attachmentId`, and `submissionId` query parameters set. This
   * is the same `submissionId` returned in the [`AddOnContext.studentContext`](
   * //devsite.google.com/classroom/reference/rest/v1/AddOnContext#StudentContex
   * t) field when a student views the attachment. If the URI is omitted or
   * removed, `max_points` will also be discarded.
   *
   * @param EmbedUri $studentWorkReviewUri
   */
  public function setStudentWorkReviewUri(EmbedUri $studentWorkReviewUri)
  {
    $this->studentWorkReviewUri = $studentWorkReviewUri;
  }
  /**
   * @return EmbedUri
   */
  public function getStudentWorkReviewUri()
  {
    return $this->studentWorkReviewUri;
  }
  /**
   * Required. URI to show the teacher view of the attachment. The URI will be
   * opened in an iframe with the `courseId`, `itemId`, `itemType`, and
   * `attachmentId` query parameters set.
   *
   * @param EmbedUri $teacherViewUri
   */
  public function setTeacherViewUri(EmbedUri $teacherViewUri)
  {
    $this->teacherViewUri = $teacherViewUri;
  }
  /**
   * @return EmbedUri
   */
  public function getTeacherViewUri()
  {
    return $this->teacherViewUri;
  }
  /**
   * Required. Title of this attachment. The title must be between 1 and 1000
   * characters.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddOnAttachment::class, 'Google_Service_Classroom_AddOnAttachment');
