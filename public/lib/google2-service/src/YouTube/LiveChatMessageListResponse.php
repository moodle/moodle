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

class LiveChatMessageListResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $activePollItemType = LiveChatMessage::class;
  protected $activePollItemDataType = '';
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
  protected $itemsType = LiveChatMessage::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#liveChatMessageListResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * @var string
   */
  public $nextPageToken;
  /**
   * The date and time when the underlying stream went offline.
   *
   * @var string
   */
  public $offlineAt;
  protected $pageInfoType = PageInfo::class;
  protected $pageInfoDataType = '';
  /**
   * The amount of time the client should wait before polling again.
   *
   * @var string
   */
  public $pollingIntervalMillis;
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
   * Set when there is an active poll.
   *
   * @param LiveChatMessage $activePollItem
   */
  public function setActivePollItem(LiveChatMessage $activePollItem)
  {
    $this->activePollItem = $activePollItem;
  }
  /**
   * @return LiveChatMessage
   */
  public function getActivePollItem()
  {
    return $this->activePollItem;
  }
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
   * @param LiveChatMessage[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return LiveChatMessage[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#liveChatMessageListResponse".
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
   * The date and time when the underlying stream went offline.
   *
   * @param string $offlineAt
   */
  public function setOfflineAt($offlineAt)
  {
    $this->offlineAt = $offlineAt;
  }
  /**
   * @return string
   */
  public function getOfflineAt()
  {
    return $this->offlineAt;
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
   * The amount of time the client should wait before polling again.
   *
   * @param string $pollingIntervalMillis
   */
  public function setPollingIntervalMillis($pollingIntervalMillis)
  {
    $this->pollingIntervalMillis = $pollingIntervalMillis;
  }
  /**
   * @return string
   */
  public function getPollingIntervalMillis()
  {
    return $this->pollingIntervalMillis;
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
class_alias(LiveChatMessageListResponse::class, 'Google_Service_YouTube_LiveChatMessageListResponse');
