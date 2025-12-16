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

namespace Google\Service\DriveActivity;

class Assignment extends \Google\Model
{
  /**
   * Subtype not available.
   */
  public const SUBTYPE_SUBTYPE_UNSPECIFIED = 'SUBTYPE_UNSPECIFIED';
  /**
   * An assignment was added.
   */
  public const SUBTYPE_ADDED = 'ADDED';
  /**
   * An assignment was deleted.
   */
  public const SUBTYPE_DELETED = 'DELETED';
  /**
   * An assignment reply was added.
   */
  public const SUBTYPE_REPLY_ADDED = 'REPLY_ADDED';
  /**
   * An assignment reply was deleted.
   */
  public const SUBTYPE_REPLY_DELETED = 'REPLY_DELETED';
  /**
   * An assignment was resolved.
   */
  public const SUBTYPE_RESOLVED = 'RESOLVED';
  /**
   * A resolved assignment was reopened.
   */
  public const SUBTYPE_REOPENED = 'REOPENED';
  /**
   * An assignment was reassigned.
   */
  public const SUBTYPE_REASSIGNED = 'REASSIGNED';
  protected $assignedUserType = User::class;
  protected $assignedUserDataType = '';
  /**
   * The sub-type of this event.
   *
   * @var string
   */
  public $subtype;

  /**
   * The user to whom the comment was assigned.
   *
   * @param User $assignedUser
   */
  public function setAssignedUser(User $assignedUser)
  {
    $this->assignedUser = $assignedUser;
  }
  /**
   * @return User
   */
  public function getAssignedUser()
  {
    return $this->assignedUser;
  }
  /**
   * The sub-type of this event.
   *
   * Accepted values: SUBTYPE_UNSPECIFIED, ADDED, DELETED, REPLY_ADDED,
   * REPLY_DELETED, RESOLVED, REOPENED, REASSIGNED
   *
   * @param self::SUBTYPE_* $subtype
   */
  public function setSubtype($subtype)
  {
    $this->subtype = $subtype;
  }
  /**
   * @return self::SUBTYPE_*
   */
  public function getSubtype()
  {
    return $this->subtype;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Assignment::class, 'Google_Service_DriveActivity_Assignment');
