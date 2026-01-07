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

class Blog extends \Google\Model
{
  public const STATUS_LIVE = 'LIVE';
  public const STATUS_DELETED = 'DELETED';
  /**
   * The JSON custom meta-data for the Blog.
   *
   * @deprecated
   * @var string
   */
  public $customMetaData;
  /**
   * The description of this blog. This is displayed underneath the title.
   *
   * @var string
   */
  public $description;
  /**
   * The identifier for this resource.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of this entry. Always blogger#blog.
   *
   * @var string
   */
  public $kind;
  protected $localeType = BlogLocale::class;
  protected $localeDataType = '';
  /**
   * The name of this blog. This is displayed as the title.
   *
   * @var string
   */
  public $name;
  protected $pagesType = BlogPages::class;
  protected $pagesDataType = '';
  protected $postsType = BlogPosts::class;
  protected $postsDataType = '';
  /**
   * RFC 3339 date-time when this blog was published.
   *
   * @var string
   */
  public $published;
  /**
   * The API REST URL to fetch this resource from.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The status of the blog.
   *
   * @var string
   */
  public $status;
  /**
   * RFC 3339 date-time when this blog was last updated.
   *
   * @var string
   */
  public $updated;
  /**
   * The URL where this blog is published.
   *
   * @var string
   */
  public $url;

  /**
   * The JSON custom meta-data for the Blog.
   *
   * @deprecated
   * @param string $customMetaData
   */
  public function setCustomMetaData($customMetaData)
  {
    $this->customMetaData = $customMetaData;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCustomMetaData()
  {
    return $this->customMetaData;
  }
  /**
   * The description of this blog. This is displayed underneath the title.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The identifier for this resource.
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
   * The kind of this entry. Always blogger#blog.
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
   * The locale this Blog is set to.
   *
   * @param BlogLocale $locale
   */
  public function setLocale(BlogLocale $locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return BlogLocale
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * The name of this blog. This is displayed as the title.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The container of pages in this blog.
   *
   * @param BlogPages $pages
   */
  public function setPages(BlogPages $pages)
  {
    $this->pages = $pages;
  }
  /**
   * @return BlogPages
   */
  public function getPages()
  {
    return $this->pages;
  }
  /**
   * The container of posts in this blog.
   *
   * @param BlogPosts $posts
   */
  public function setPosts(BlogPosts $posts)
  {
    $this->posts = $posts;
  }
  /**
   * @return BlogPosts
   */
  public function getPosts()
  {
    return $this->posts;
  }
  /**
   * RFC 3339 date-time when this blog was published.
   *
   * @param string $published
   */
  public function setPublished($published)
  {
    $this->published = $published;
  }
  /**
   * @return string
   */
  public function getPublished()
  {
    return $this->published;
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
   * The status of the blog.
   *
   * Accepted values: LIVE, DELETED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * RFC 3339 date-time when this blog was last updated.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * The URL where this blog is published.
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
class_alias(Blog::class, 'Google_Service_Blogger_Blog');
