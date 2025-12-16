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

namespace Google\Service\PeopleService;

class ListDirectoryPeopleResponse extends \Google\Collection
{
  protected $collection_key = 'people';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A token, which can be sent as `sync_token` to retrieve changes since the
   * last request. Request must set `request_sync_token` to return the sync
   * token.
   *
   * @var string
   */
  public $nextSyncToken;
  protected $peopleType = Person::class;
  protected $peopleDataType = 'array';

  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
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
   * A token, which can be sent as `sync_token` to retrieve changes since the
   * last request. Request must set `request_sync_token` to return the sync
   * token.
   *
   * @param string $nextSyncToken
   */
  public function setNextSyncToken($nextSyncToken)
  {
    $this->nextSyncToken = $nextSyncToken;
  }
  /**
   * @return string
   */
  public function getNextSyncToken()
  {
    return $this->nextSyncToken;
  }
  /**
   * The list of people in the domain directory.
   *
   * @param Person[] $people
   */
  public function setPeople($people)
  {
    $this->people = $people;
  }
  /**
   * @return Person[]
   */
  public function getPeople()
  {
    return $this->people;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListDirectoryPeopleResponse::class, 'Google_Service_PeopleService_ListDirectoryPeopleResponse');
