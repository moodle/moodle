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

namespace Google\Service\HangoutsChat;

class ListSpaceEventsResponse extends \Google\Collection
{
  protected $collection_key = 'spaceEvents';
  /**
   * Continuation token used to fetch more events. If this field is omitted,
   * there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $spaceEventsType = SpaceEvent::class;
  protected $spaceEventsDataType = 'array';

  /**
   * Continuation token used to fetch more events. If this field is omitted,
   * there are no subsequent pages.
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
   * Results are returned in chronological order (oldest event first). Note: The
   * `permissionSettings` field is not returned in the Space object for list
   * requests.
   *
   * @param SpaceEvent[] $spaceEvents
   */
  public function setSpaceEvents($spaceEvents)
  {
    $this->spaceEvents = $spaceEvents;
  }
  /**
   * @return SpaceEvent[]
   */
  public function getSpaceEvents()
  {
    return $this->spaceEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListSpaceEventsResponse::class, 'Google_Service_HangoutsChat_ListSpaceEventsResponse');
