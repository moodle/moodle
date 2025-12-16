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

class Page extends \Google\Model
{
  public const STATUS_LIVE = 'LIVE';
  public const STATUS_DRAFT = 'DRAFT';
  public const STATUS_SOFT_TRASHED = 'SOFT_TRASHED';
  protected $authorType = PageAuthor::class;
  protected $authorDataType = '';
  protected $blogType = PageBlog::class;
  protected $blogDataType = '';
  /**
   * The body content of this Page, in HTML.
   *
   * @var string
   */
  public $content;
  /**
   * Etag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The identifier for this resource.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of this entity. Always blogger#page.
   *
   * @var string
   */
  public $kind;
  /**
   * RFC 3339 date-time when this Page was published.
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
   * The status of the page for admin resources (either LIVE or DRAFT).
   *
   * @var string
   */
  public $status;
  /**
   * The title of this entity. This is the name displayed in the Admin user
   * interface.
   *
   * @var string
   */
  public $title;
  /**
   * RFC 3339 date-time when this Page was trashed.
   *
   * @var string
   */
  public $trashed;
  /**
   * RFC 3339 date-time when this Page was last updated.
   *
   * @var string
   */
  public $updated;
  /**
   * The URL that this Page is displayed at.
   *
   * @var string
   */
  public $url;

  /**
   * The author of this Page.
   *
   * @param PageAuthor $author
   */
  public function setAuthor(PageAuthor $author)
  {
    $this->author = $author;
  }
  /**
   * @return PageAuthor
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Data about the blog containing this Page.
   *
   * @param PageBlog $blog
   */
  public function setBlog(PageBlog $blog)
  {
    $this->blog = $blog;
  }
  /**
   * @return PageBlog
   */
  public function getBlog()
  {
    return $this->blog;
  }
  /**
   * The body content of this Page, in HTML.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Etag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
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
   * The kind of this entity. Always blogger#page.
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
   * RFC 3339 date-time when this Page was published.
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
   * The status of the page for admin resources (either LIVE or DRAFT).
   *
   * Accepted values: LIVE, DRAFT, SOFT_TRASHED
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
   * The title of this entity. This is the name displayed in the Admin user
   * interface.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * RFC 3339 date-time when this Page was trashed.
   *
   * @param string $trashed
   */
  public function setTrashed($trashed)
  {
    $this->trashed = $trashed;
  }
  /**
   * @return string
   */
  public function getTrashed()
  {
    return $this->trashed;
  }
  /**
   * RFC 3339 date-time when this Page was last updated.
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
   * The URL that this Page is displayed at.
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
class_alias(Page::class, 'Google_Service_Blogger_Page');
