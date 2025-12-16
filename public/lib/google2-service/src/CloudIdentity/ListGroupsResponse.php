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

namespace Google\Service\CloudIdentity;

class ListGroupsResponse extends \Google\Collection
{
  protected $collection_key = 'groups';
  protected $groupsType = Group::class;
  protected $groupsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results available for listing.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Groups returned in response to list request. The results are not sorted.
   *
   * @param Group[] $groups
   */
  public function setGroups($groups)
  {
    $this->groups = $groups;
  }
  /**
   * @return Group[]
   */
  public function getGroups()
  {
    return $this->groups;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results available for listing.
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
class_alias(ListGroupsResponse::class, 'Google_Service_CloudIdentity_ListGroupsResponse');
