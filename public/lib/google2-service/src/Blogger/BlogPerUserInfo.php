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

namespace Google\Service\Blogger;

class BlogPerUserInfo extends \Google\Model
{
  public const ROLE_VIEW_TYPE_UNSPECIFIED = 'VIEW_TYPE_UNSPECIFIED';
  public const ROLE_READER = 'READER';
  public const ROLE_AUTHOR = 'AUTHOR';
  public const ROLE_ADMIN = 'ADMIN';
  /**
   * ID of the Blog resource.
   *
   * @var string
   */
  public $blogId;
  /**
   * True if the user has Admin level access to the blog.
   *
   * @var bool
   */
  public $hasAdminAccess;
  /**
   * The kind of this entity. Always blogger#blogPerUserInfo.
   *
   * @var string
   */
  public $kind;
  /**
   * The Photo Album Key for the user when adding photos to the blog.
   *
   * @var string
   */
  public $photosAlbumKey;
  /**
   * Access permissions that the user has for the blog (ADMIN, AUTHOR, or
   * READER).
   *
   * @var string
   */
  public $role;
  /**
   * ID of the User.
   *
   * @var string
   */
  public $userId;

  /**
   * ID of the Blog resource.
   *
   * @param string $blogId
   */
  public function setBlogId($blogId)
  {
    $this->blogId = $blogId;
  }
  /**
   * @return string
   */
  public function getBlogId()
  {
    return $this->blogId;
  }
  /**
   * True if the user has Admin level access to the blog.
   *
   * @param bool $hasAdminAccess
   */
  public function setHasAdminAccess($hasAdminAccess)
  {
    $this->hasAdminAccess = $hasAdminAccess;
  }
  /**
   * @return bool
   */
  public function getHasAdminAccess()
  {
    return $this->hasAdminAccess;
  }
  /**
   * The kind of this entity. Always blogger#blogPerUserInfo.
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
   * The Photo Album Key for the user when adding photos to the blog.
   *
   * @param string $photosAlbumKey
   */
  public function setPhotosAlbumKey($photosAlbumKey)
  {
    $this->photosAlbumKey = $photosAlbumKey;
  }
  /**
   * @return string
   */
  public function getPhotosAlbumKey()
  {
    return $this->photosAlbumKey;
  }
  /**
   * Access permissions that the user has for the blog (ADMIN, AUTHOR, or
   * READER).
   *
   * Accepted values: VIEW_TYPE_UNSPECIFIED, READER, AUTHOR, ADMIN
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
   * ID of the User.
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
class_alias(BlogPerUserInfo::class, 'Google_Service_Blogger_BlogPerUserInfo');
