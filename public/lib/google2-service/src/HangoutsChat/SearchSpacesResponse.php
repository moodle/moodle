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

class SearchSpacesResponse extends \Google\Collection
{
  protected $collection_key = 'spaces';
  /**
   * A token that can be used to retrieve the next page. If this field is empty,
   * there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $spacesType = Space::class;
  protected $spacesDataType = 'array';
  /**
   * The total number of spaces that match the query, across all pages. If the
   * result is over 10,000 spaces, this value is an estimate.
   *
   * @var int
   */
  public $totalSize;

  /**
   * A token that can be used to retrieve the next page. If this field is empty,
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
   * A page of the requested spaces.
   *
   * @param Space[] $spaces
   */
  public function setSpaces($spaces)
  {
    $this->spaces = $spaces;
  }
  /**
   * @return Space[]
   */
  public function getSpaces()
  {
    return $this->spaces;
  }
  /**
   * The total number of spaces that match the query, across all pages. If the
   * result is over 10,000 spaces, this value is an estimate.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchSpacesResponse::class, 'Google_Service_HangoutsChat_SearchSpacesResponse');
