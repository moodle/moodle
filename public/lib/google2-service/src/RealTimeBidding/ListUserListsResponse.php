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

namespace Google\Service\RealTimeBidding;

class ListUserListsResponse extends \Google\Collection
{
  protected $collection_key = 'userLists';
  /**
   * The continuation page token to send back to the server in a subsequent
   * request. Due to a currently known issue, it is recommended that the caller
   * keep invoking the list method until the time a next page token is not
   * returned, even if the result set is empty.
   *
   * @var string
   */
  public $nextPageToken;
  protected $userListsType = UserList::class;
  protected $userListsDataType = 'array';

  /**
   * The continuation page token to send back to the server in a subsequent
   * request. Due to a currently known issue, it is recommended that the caller
   * keep invoking the list method until the time a next page token is not
   * returned, even if the result set is empty.
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
   * List of user lists from the search.
   *
   * @param UserList[] $userLists
   */
  public function setUserLists($userLists)
  {
    $this->userLists = $userLists;
  }
  /**
   * @return UserList[]
   */
  public function getUserLists()
  {
    return $this->userLists;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListUserListsResponse::class, 'Google_Service_RealTimeBidding_ListUserListsResponse');
