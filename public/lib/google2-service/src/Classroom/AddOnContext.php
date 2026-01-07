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

class AddOnContext extends \Google\Model
{
  /**
   * Immutable. Identifier of the course.
   *
   * @var string
   */
  public $courseId;
  /**
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached.
   *
   * @var string
   */
  public $itemId;
  /**
   * Immutable. Deprecated, use `item_id` instead.
   *
   * @deprecated
   * @var string
   */
  public $postId;
  protected $studentContextType = StudentContext::class;
  protected $studentContextDataType = '';
  /**
   * Optional. Whether the post allows the teacher to see student work and
   * passback grades.
   *
   * @var bool
   */
  public $supportsStudentWork;
  protected $teacherContextType = TeacherContext::class;
  protected $teacherContextDataType = '';

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
   * Immutable. Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached.
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
   * Add-on context corresponding to the requesting user's role as a student.
   * Its presence implies that the requesting user is a student in the course.
   *
   * @param StudentContext $studentContext
   */
  public function setStudentContext(StudentContext $studentContext)
  {
    $this->studentContext = $studentContext;
  }
  /**
   * @return StudentContext
   */
  public function getStudentContext()
  {
    return $this->studentContext;
  }
  /**
   * Optional. Whether the post allows the teacher to see student work and
   * passback grades.
   *
   * @param bool $supportsStudentWork
   */
  public function setSupportsStudentWork($supportsStudentWork)
  {
    $this->supportsStudentWork = $supportsStudentWork;
  }
  /**
   * @return bool
   */
  public function getSupportsStudentWork()
  {
    return $this->supportsStudentWork;
  }
  /**
   * Add-on context corresponding to the requesting user's role as a teacher.
   * Its presence implies that the requesting user is a teacher in the course.
   *
   * @param TeacherContext $teacherContext
   */
  public function setTeacherContext(TeacherContext $teacherContext)
  {
    $this->teacherContext = $teacherContext;
  }
  /**
   * @return TeacherContext
   */
  public function getTeacherContext()
  {
    return $this->teacherContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddOnContext::class, 'Google_Service_Classroom_AddOnContext');
