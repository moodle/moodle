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

class ModifyCourseWorkAssigneesRequest extends \Google\Model
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
   * Mode of the coursework describing whether it will be assigned to all
   * students or specified individual students.
   *
   * @var string
   */
  public $assigneeMode;
  protected $modifyIndividualStudentsOptionsType = ModifyIndividualStudentsOptions::class;
  protected $modifyIndividualStudentsOptionsDataType = '';

  /**
   * Mode of the coursework describing whether it will be assigned to all
   * students or specified individual students.
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
   * Set which students are assigned or not assigned to the coursework. Must be
   * specified only when `assigneeMode` is `INDIVIDUAL_STUDENTS`.
   *
   * @param ModifyIndividualStudentsOptions $modifyIndividualStudentsOptions
   */
  public function setModifyIndividualStudentsOptions(ModifyIndividualStudentsOptions $modifyIndividualStudentsOptions)
  {
    $this->modifyIndividualStudentsOptions = $modifyIndividualStudentsOptions;
  }
  /**
   * @return ModifyIndividualStudentsOptions
   */
  public function getModifyIndividualStudentsOptions()
  {
    return $this->modifyIndividualStudentsOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModifyCourseWorkAssigneesRequest::class, 'Google_Service_Classroom_ModifyCourseWorkAssigneesRequest');
