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

class Student extends \Google\Model
{
  /**
   * Identifier of the course. Read-only.
   *
   * @var string
   */
  public $courseId;
  protected $profileType = UserProfile::class;
  protected $profileDataType = '';
  protected $studentWorkFolderType = DriveFolder::class;
  protected $studentWorkFolderDataType = '';
  /**
   * Identifier of the user. When specified as a parameter of a request, this
   * identifier can be one of the following: * the numeric identifier for the
   * user * the email address of the user * the string literal `"me"`,
   * indicating the requesting user
   *
   * @var string
   */
  public $userId;

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
   * Global user information for the student. Read-only.
   *
   * @param UserProfile $profile
   */
  public function setProfile(UserProfile $profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return UserProfile
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Information about a Drive Folder for this student's work in this course.
   * Only visible to the student and domain administrators. Read-only.
   *
   * @param DriveFolder $studentWorkFolder
   */
  public function setStudentWorkFolder(DriveFolder $studentWorkFolder)
  {
    $this->studentWorkFolder = $studentWorkFolder;
  }
  /**
   * @return DriveFolder
   */
  public function getStudentWorkFolder()
  {
    return $this->studentWorkFolder;
  }
  /**
   * Identifier of the user. When specified as a parameter of a request, this
   * identifier can be one of the following: * the numeric identifier for the
   * user * the email address of the user * the string literal `"me"`,
   * indicating the requesting user
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
class_alias(Student::class, 'Google_Service_Classroom_Student');
