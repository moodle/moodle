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

namespace Google\Service\YouTube;

class CommentListResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Serialized EventId of the request which produced this response.
   *
   * @deprecated
   * @var string
   */
  public $eventId;
  protected $itemsType = Comment::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#commentListResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The token that can be used as the value of the pageToken parameter to
   * retrieve the next page in the result set.
   *
   * @var string
   */
  public $nextPageToken;
  protected $pageInfoType = PageInfo::class;
  protected $pageInfoDataType = '';
  protected $tokenPaginationType = TokenPagination::class;
  protected $tokenPaginationDataType = '';
  /**
   * The visitorId identifies the visitor.
   *
   * @deprecated
   * @var string
   */
  public $visitorId;

  /**
   * Etag of this resource.
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
   * Serialized EventId of the request which produced this response.
   *
   * @deprecated
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * A list of comments that match the request criteria.
   *
   * @param Comment[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Comment[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#commentListResponse".
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
   * The token that can be used as the value of the pageToken parameter to
   * retrieve the next page in the result set.
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
  /**
   * General pagination information.
   *
   * @param PageInfo $pageInfo
   */
  public function setPageInfo(PageInfo $pageInfo)
  {
    $this->pageInfo = $pageInfo;
  }
  /**
   * @return PageInfo
   */
  public function getPageInfo()
  {
    return $this->pageInfo;
  }
  /**
   * @deprecated
   * @param TokenPagination $tokenPagination
   */
  public function setTokenPagination(TokenPagination $tokenPagination)
  {
    $this->tokenPagination = $tokenPagination;
  }
  /**
   * @deprecated
   * @return TokenPagination
   */
  public function getTokenPagination()
  {
    return $this->tokenPagination;
  }
  /**
   * The visitorId identifies the visitor.
   *
   * @deprecated
   * @param string $visitorId
   */
  public function setVisitorId($visitorId)
  {
    $this->visitorId = $visitorId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getVisitorId()
  {
    return $this->visitorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommentListResponse::class, 'Google_Service_YouTube_CommentListResponse');
