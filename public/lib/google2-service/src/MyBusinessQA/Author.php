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

namespace Google\Service\MyBusinessQA;

class Author extends \Google\Model
{
  /**
   * This should not be used.
   */
  public const TYPE_AUTHOR_TYPE_UNSPECIFIED = 'AUTHOR_TYPE_UNSPECIFIED';
  /**
   * A regular user.
   */
  public const TYPE_REGULAR_USER = 'REGULAR_USER';
  /**
   * A Local Guide
   */
  public const TYPE_LOCAL_GUIDE = 'LOCAL_GUIDE';
  /**
   * The owner/manager of the location
   */
  public const TYPE_MERCHANT = 'MERCHANT';
  /**
   * The display name of the user
   *
   * @var string
   */
  public $displayName;
  /**
   * The profile photo URI of the user.
   *
   * @var string
   */
  public $profilePhotoUri;
  /**
   * The type of user the author is.
   *
   * @var string
   */
  public $type;

  /**
   * The display name of the user
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
   * The profile photo URI of the user.
   *
   * @param string $profilePhotoUri
   */
  public function setProfilePhotoUri($profilePhotoUri)
  {
    $this->profilePhotoUri = $profilePhotoUri;
  }
  /**
   * @return string
   */
  public function getProfilePhotoUri()
  {
    return $this->profilePhotoUri;
  }
  /**
   * The type of user the author is.
   *
   * Accepted values: AUTHOR_TYPE_UNSPECIFIED, REGULAR_USER, LOCAL_GUIDE,
   * MERCHANT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Author::class, 'Google_Service_MyBusinessQA_Author');
