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

class PageList extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Etag of the response.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = Page::class;
  protected $itemsDataType = 'array';
  /**
   * The kind of this entity. Always blogger#pageList.
   *
   * @var string
   */
  public $kind;
  /**
   * Pagination token to fetch the next page, if one exists.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Etag of the response.
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
   * The list of Pages for a Blog.
   *
   * @param Page[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Page[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * The kind of this entity. Always blogger#pageList.
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
   * Pagination token to fetch the next page, if one exists.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageList::class, 'Google_Service_Blogger_PageList');
