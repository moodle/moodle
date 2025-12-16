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

namespace Google\Service\DisplayVideo;

class ListCustomListsResponse extends \Google\Collection
{
  protected $collection_key = 'customLists';
  protected $customListsType = CustomList::class;
  protected $customListsDataType = 'array';
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to `ListCustomLists` method to
   * retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of custom lists. This list will be absent if empty.
   *
   * @param CustomList[] $customLists
   */
  public function setCustomLists($customLists)
  {
    $this->customLists = $customLists;
  }
  /**
   * @return CustomList[]
   */
  public function getCustomLists()
  {
    return $this->customLists;
  }
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to `ListCustomLists` method to
   * retrieve the next page of results.
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
class_alias(ListCustomListsResponse::class, 'Google_Service_DisplayVideo_ListCustomListsResponse');
