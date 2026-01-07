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

class Post extends \Google\Collection
{
  public const READER_COMMENTS_ALLOW = 'ALLOW';
  public const READER_COMMENTS_DONT_ALLOW_SHOW_EXISTING = 'DONT_ALLOW_SHOW_EXISTING';
  public const READER_COMMENTS_DONT_ALLOW_HIDE_EXISTING = 'DONT_ALLOW_HIDE_EXISTING';
  public const STATUS_LIVE = 'LIVE';
  public const STATUS_DRAFT = 'DRAFT';
  public const STATUS_SCHEDULED = 'SCHEDULED';
  public const STATUS_SOFT_TRASHED = 'SOFT_TRASHED';
  protected $collection_key = 'labels';
  protected $authorType = PostAuthor::class;
  protected $authorDataType = '';
  protected $blogType = PostBlog::class;
  protected $blogDataType = '';
  /**
   * The content of the Post. May contain HTML markup.
   *
   * @var string
   */
  public $content;
  /**
   * The JSON meta-data for the Post.
   *
   * @deprecated
   * @var string
   */
  public $customMetaData;
  /**
   * Etag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The identifier of this Post.
   *
   * @var string
   */
  public $id;
  protected $imagesType = PostImages::class;
  protected $imagesDataType = 'array';
  /**
   * The kind of this entity. Always blogger#post.
   *
   * @var string
   */
  public $kind;
  /**
   * The list of labels this Post was tagged with.
   *
   * @var string[]
   */
  public $labels;
  protected $locationType = PostLocation::class;
  protected $locationDataType = '';
  /**
   * RFC 3339 date-time when this Post was published.
   *
   * @var string
   */
  public $published;
  /**
   * Comment control and display setting for readers of this post.
   *
   * @var string
   */
  public $readerComments;
  protected $repliesType = PostReplies::class;
  protected $repliesDataType = '';
  /**
   * The API REST URL to fetch this resource from.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Status of the post. Only set for admin-level requests.
   *
   * @var string
   */
  public $status;
  /**
   * The title of the Post.
   *
   * @var string
   */
  public $title;
  /**
   * The title link URL, similar to atom's related link.
   *
   * @var string
   */
  public $titleLink;
  /**
   * RFC 3339 date-time when this Post was last trashed.
   *
   * @var string
   */
  public $trashed;
  /**
   * RFC 3339 date-time when this Post was last updated.
   *
   * @var string
   */
  public $updated;
  /**
   * The URL where this Post is displayed.
   *
   * @var string
   */
  public $url;

  /**
   * The author of this Post.
   *
   * @param PostAuthor $author
   */
  public function setAuthor(PostAuthor $author)
  {
    $this->author = $author;
  }
  /**
   * @return PostAuthor
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Data about the blog containing this Post.
   *
   * @param PostBlog $blog
   */
  public function setBlog(PostBlog $blog)
  {
    $this->blog = $blog;
  }
  /**
   * @return PostBlog
   */
  public function getBlog()
  {
    return $this->blog;
  }
  /**
   * The content of the Post. May contain HTML markup.
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
   * The JSON meta-data for the Post.
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
   * The identifier of this Post.
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
   * Display image for the Post.
   *
   * @param PostImages[] $images
   */
  public function setImages($images)
  {
    $this->images = $images;
  }
  /**
   * @return PostImages[]
   */
  public function getImages()
  {
    return $this->images;
  }
  /**
   * The kind of this entity. Always blogger#post.
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
   * The list of labels this Post was tagged with.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The location for geotagged posts.
   *
   * @param PostLocation $location
   */
  public function setLocation(PostLocation $location)
  {
    $this->location = $location;
  }
  /**
   * @return PostLocation
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * RFC 3339 date-time when this Post was published.
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
   * Comment control and display setting for readers of this post.
   *
   * Accepted values: ALLOW, DONT_ALLOW_SHOW_EXISTING, DONT_ALLOW_HIDE_EXISTING
   *
   * @param self::READER_COMMENTS_* $readerComments
   */
  public function setReaderComments($readerComments)
  {
    $this->readerComments = $readerComments;
  }
  /**
   * @return self::READER_COMMENTS_*
   */
  public function getReaderComments()
  {
    return $this->readerComments;
  }
  /**
   * The container of comments on this Post.
   *
   * @param PostReplies $replies
   */
  public function setReplies(PostReplies $replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return PostReplies
   */
  public function getReplies()
  {
    return $this->replies;
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
   * Status of the post. Only set for admin-level requests.
   *
   * Accepted values: LIVE, DRAFT, SCHEDULED, SOFT_TRASHED
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
   * The title of the Post.
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
   * The title link URL, similar to atom's related link.
   *
   * @param string $titleLink
   */
  public function setTitleLink($titleLink)
  {
    $this->titleLink = $titleLink;
  }
  /**
   * @return string
   */
  public function getTitleLink()
  {
    return $this->titleLink;
  }
  /**
   * RFC 3339 date-time when this Post was last trashed.
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
   * RFC 3339 date-time when this Post was last updated.
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
   * The URL where this Post is displayed.
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
class_alias(Post::class, 'Google_Service_Blogger_Post');
