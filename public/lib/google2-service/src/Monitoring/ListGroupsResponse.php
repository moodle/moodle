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

namespace Google\Service\Monitoring;

class ListGroupsResponse extends \Google\Collection
{
  protected $collection_key = 'group';
  protected $groupType = Group::class;
  protected $groupDataType = 'array';
  /**
   * If there are more results than have been returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The groups that match the specified filters.
   *
   * @param Group[] $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return Group[]
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * If there are more results than have been returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
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
class_alias(ListGroupsResponse::class, 'Google_Service_Monitoring_ListGroupsResponse');
