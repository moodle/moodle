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

class UserProfile extends \Google\Collection
{
  protected $collection_key = 'permissions';
  /**
   * Email address of the user. Must request
   * `https://www.googleapis.com/auth/classroom.profile.emails` scope for this
   * field to be populated in a response body. Read-only.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Identifier of the user. Read-only.
   *
   * @var string
   */
  public $id;
  protected $nameType = Name::class;
  protected $nameDataType = '';
  protected $permissionsType = GlobalPermission::class;
  protected $permissionsDataType = 'array';
  /**
   * URL of user's profile photo. Must request
   * `https://www.googleapis.com/auth/classroom.profile.photos` scope for this
   * field to be populated in a response body. Read-only.
   *
   * @var string
   */
  public $photoUrl;
  /**
   * Represents whether a Google Workspace for Education user's domain
   * administrator has explicitly verified them as being a teacher. This field
   * is always false if the user is not a member of a Google Workspace for
   * Education domain. Read-only
   *
   * @var bool
   */
  public $verifiedTeacher;

  /**
   * Email address of the user. Must request
   * `https://www.googleapis.com/auth/classroom.profile.emails` scope for this
   * field to be populated in a response body. Read-only.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * Identifier of the user. Read-only.
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
   * Name of the user. Read-only.
   *
   * @param Name $name
   */
  public function setName(Name $name)
  {
    $this->name = $name;
  }
  /**
   * @return Name
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Global permissions of the user. Read-only.
   *
   * @param GlobalPermission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return GlobalPermission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * URL of user's profile photo. Must request
   * `https://www.googleapis.com/auth/classroom.profile.photos` scope for this
   * field to be populated in a response body. Read-only.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
  /**
   * Represents whether a Google Workspace for Education user's domain
   * administrator has explicitly verified them as being a teacher. This field
   * is always false if the user is not a member of a Google Workspace for
   * Education domain. Read-only
   *
   * @param bool $verifiedTeacher
   */
  public function setVerifiedTeacher($verifiedTeacher)
  {
    $this->verifiedTeacher = $verifiedTeacher;
  }
  /**
   * @return bool
   */
  public function getVerifiedTeacher()
  {
    return $this->verifiedTeacher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserProfile::class, 'Google_Service_Classroom_UserProfile');
