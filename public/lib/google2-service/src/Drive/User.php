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

namespace Google\Service\Drive;

class User extends \Google\Model
{
  /**
   * Output only. A plain text displayable name for this user.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The email address of the user. This may not be present in
   * certain contexts if the user has not made their email address visible to
   * the requester.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `drive#user`.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. Whether this user is the requesting user.
   *
   * @var bool
   */
  public $me;
  /**
   * Output only. The user's ID as visible in Permission resources.
   *
   * @var string
   */
  public $permissionId;
  /**
   * Output only. A link to the user's profile photo, if available.
   *
   * @var string
   */
  public $photoLink;

  /**
   * Output only. A plain text displayable name for this user.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The email address of the user. This may not be present in
   * certain contexts if the user has not made their email address visible to
   * the requester.
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
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `drive#user`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. Whether this user is the requesting user.
   *
   * @param bool $me
   */
  public function setMe($me)
  {
    $this->me = $me;
  }
  /**
   * @return bool
   */
  public function getMe()
  {
    return $this->me;
  }
  /**
   * Output only. The user's ID as visible in Permission resources.
   *
   * @param string $permissionId
   */
  public function setPermissionId($permissionId)
  {
    $this->permissionId = $permissionId;
  }
  /**
   * @return string
   */
  public function getPermissionId()
  {
    return $this->permissionId;
  }
  /**
   * Output only. A link to the user's profile photo, if available.
   *
   * @param string $photoLink
   */
  public function setPhotoLink($photoLink)
  {
    $this->photoLink = $photoLink;
  }
  /**
   * @return string
   */
  public function getPhotoLink()
  {
    return $this->photoLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_Drive_User');
