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

class GlobalPermission extends \Google\Model
{
  /**
   * No permission is specified. This is not returned and is not a valid value.
   */
  public const PERMISSION_PERMISSION_UNSPECIFIED = 'PERMISSION_UNSPECIFIED';
  /**
   * User is permitted to create a course.
   */
  public const PERMISSION_CREATE_COURSE = 'CREATE_COURSE';
  /**
   * Permission value.
   *
   * @var string
   */
  public $permission;

  /**
   * Permission value.
   *
   * Accepted values: PERMISSION_UNSPECIFIED, CREATE_COURSE
   *
   * @param self::PERMISSION_* $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return self::PERMISSION_*
   */
  public function getPermission()
  {
    return $this->permission;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GlobalPermission::class, 'Google_Service_Classroom_GlobalPermission');
