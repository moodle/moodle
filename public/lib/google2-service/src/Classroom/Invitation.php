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

class Invitation extends \Google\Model
{
  /**
   * No course role.
   */
  public const ROLE_COURSE_ROLE_UNSPECIFIED = 'COURSE_ROLE_UNSPECIFIED';
  /**
   * Student in the course.
   */
  public const ROLE_STUDENT = 'STUDENT';
  /**
   * Teacher of the course.
   */
  public const ROLE_TEACHER = 'TEACHER';
  /**
   * Owner of the course.
   */
  public const ROLE_OWNER = 'OWNER';
  /**
   * Identifier of the course to invite the user to.
   *
   * @var string
   */
  public $courseId;
  /**
   * Identifier assigned by Classroom. Read-only.
   *
   * @var string
   */
  public $id;
  /**
   * Role to invite the user to have. Must not be `COURSE_ROLE_UNSPECIFIED`.
   *
   * @var string
   */
  public $role;
  /**
   * Identifier of the invited user. When specified as a parameter of a request,
   * this identifier can be set to one of the following: * the numeric
   * identifier for the user * the email address of the user * the string
   * literal `"me"`, indicating the requesting user
   *
   * @var string
   */
  public $userId;

  /**
   * Identifier of the course to invite the user to.
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
   * Identifier assigned by Classroom. Read-only.
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
   * Role to invite the user to have. Must not be `COURSE_ROLE_UNSPECIFIED`.
   *
   * Accepted values: COURSE_ROLE_UNSPECIFIED, STUDENT, TEACHER, OWNER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Identifier of the invited user. When specified as a parameter of a request,
   * this identifier can be set to one of the following: * the numeric
   * identifier for the user * the email address of the user * the string
   * literal `"me"`, indicating the requesting user
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
class_alias(Invitation::class, 'Google_Service_Classroom_Invitation');
