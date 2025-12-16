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

class User extends \Google\Model
{
  /**
   * Profile summary information.
   *
   * @var string
   */
  public $about;
  protected $blogsType = UserBlogs::class;
  protected $blogsDataType = '';
  /**
   * The timestamp of when this profile was created, in seconds since epoch.
   *
   * @var string
   */
  public $created;
  /**
   * The display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The identifier for this User.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of this entity. Always blogger#user.
   *
   * @var string
   */
  public $kind;
  protected $localeType = UserLocale::class;
  protected $localeDataType = '';
  /**
   * The API REST URL to fetch this resource from.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The user's profile page.
   *
   * @var string
   */
  public $url;

  /**
   * Profile summary information.
   *
   * @param string $about
   */
  public function setAbout($about)
  {
    $this->about = $about;
  }
  /**
   * @return string
   */
  public function getAbout()
  {
    return $this->about;
  }
  /**
   * The container of blogs for this user.
   *
   * @param UserBlogs $blogs
   */
  public function setBlogs(UserBlogs $blogs)
  {
    $this->blogs = $blogs;
  }
  /**
   * @return UserBlogs
   */
  public function getBlogs()
  {
    return $this->blogs;
  }
  /**
   * The timestamp of when this profile was created, in seconds since epoch.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * The display name.
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
   * The identifier for this User.
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
   * The kind of this entity. Always blogger#user.
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
   * This user's locale
   *
   * @param UserLocale $locale
   */
  public function setLocale(UserLocale $locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return UserLocale
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * The API REST URL to fetch this resource from.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The user's profile page.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_Blogger_User');
