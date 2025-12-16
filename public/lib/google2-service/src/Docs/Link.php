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

namespace Google\Service\Docs;

class Link extends \Google\Model
{
  protected $bookmarkType = BookmarkLink::class;
  protected $bookmarkDataType = '';
  /**
   * The ID of a bookmark in this document. Legacy field: Instead, set
   * includeTabsContent to `true` and use Link.bookmark for read and write
   * operations. This field is only returned when includeTabsContent is set to
   * `false` in documents containing a single tab and links to a bookmark within
   * the singular tab. Otherwise, Link.bookmark is returned. If this field is
   * used in a write request, the bookmark is considered to be from the tab ID
   * specified in the request. If a tab ID is not specified in the request, it
   * is considered to be from the first tab in the document.
   *
   * @var string
   */
  public $bookmarkId;
  protected $headingType = HeadingLink::class;
  protected $headingDataType = '';
  /**
   * The ID of a heading in this document. Legacy field: Instead, set
   * includeTabsContent to `true` and use Link.heading for read and write
   * operations. This field is only returned when includeTabsContent is set to
   * `false` in documents containing a single tab and links to a heading within
   * the singular tab. Otherwise, Link.heading is returned. If this field is
   * used in a write request, the heading is considered to be from the tab ID
   * specified in the request. If a tab ID is not specified in the request, it
   * is considered to be from the first tab in the document.
   *
   * @var string
   */
  public $headingId;
  /**
   * The ID of a tab in this document.
   *
   * @var string
   */
  public $tabId;
  /**
   * An external URL.
   *
   * @var string
   */
  public $url;

  /**
   * A bookmark in this document. In documents containing a single tab, links to
   * bookmarks within the singular tab continue to return Link.bookmarkId when
   * the includeTabsContent parameter is set to `false` or unset. Otherwise,
   * this field is returned.
   *
   * @param BookmarkLink $bookmark
   */
  public function setBookmark(BookmarkLink $bookmark)
  {
    $this->bookmark = $bookmark;
  }
  /**
   * @return BookmarkLink
   */
  public function getBookmark()
  {
    return $this->bookmark;
  }
  /**
   * The ID of a bookmark in this document. Legacy field: Instead, set
   * includeTabsContent to `true` and use Link.bookmark for read and write
   * operations. This field is only returned when includeTabsContent is set to
   * `false` in documents containing a single tab and links to a bookmark within
   * the singular tab. Otherwise, Link.bookmark is returned. If this field is
   * used in a write request, the bookmark is considered to be from the tab ID
   * specified in the request. If a tab ID is not specified in the request, it
   * is considered to be from the first tab in the document.
   *
   * @param string $bookmarkId
   */
  public function setBookmarkId($bookmarkId)
  {
    $this->bookmarkId = $bookmarkId;
  }
  /**
   * @return string
   */
  public function getBookmarkId()
  {
    return $this->bookmarkId;
  }
  /**
   * A heading in this document. In documents containing a single tab, links to
   * headings within the singular tab continue to return Link.headingId when the
   * includeTabsContent parameter is set to `false` or unset. Otherwise, this
   * field is returned.
   *
   * @param HeadingLink $heading
   */
  public function setHeading(HeadingLink $heading)
  {
    $this->heading = $heading;
  }
  /**
   * @return HeadingLink
   */
  public function getHeading()
  {
    return $this->heading;
  }
  /**
   * The ID of a heading in this document. Legacy field: Instead, set
   * includeTabsContent to `true` and use Link.heading for read and write
   * operations. This field is only returned when includeTabsContent is set to
   * `false` in documents containing a single tab and links to a heading within
   * the singular tab. Otherwise, Link.heading is returned. If this field is
   * used in a write request, the heading is considered to be from the tab ID
   * specified in the request. If a tab ID is not specified in the request, it
   * is considered to be from the first tab in the document.
   *
   * @param string $headingId
   */
  public function setHeadingId($headingId)
  {
    $this->headingId = $headingId;
  }
  /**
   * @return string
   */
  public function getHeadingId()
  {
    return $this->headingId;
  }
  /**
   * The ID of a tab in this document.
   *
   * @param string $tabId
   */
  public function setTabId($tabId)
  {
    $this->tabId = $tabId;
  }
  /**
   * @return string
   */
  public function getTabId()
  {
    return $this->tabId;
  }
  /**
   * An external URL.
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
class_alias(Link::class, 'Google_Service_Docs_Link');
