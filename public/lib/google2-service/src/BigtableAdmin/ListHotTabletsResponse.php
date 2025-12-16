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

namespace Google\Service\BigtableAdmin;

class ListHotTabletsResponse extends \Google\Collection
{
  protected $collection_key = 'hotTablets';
  protected $hotTabletsType = HotTablet::class;
  protected $hotTabletsDataType = 'array';
  /**
   * Set if not all hot tablets could be returned in a single response. Pass
   * this value to `page_token` in another request to get the next page of
   * results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of hot tablets in the tables of the requested cluster that fall within
   * the requested time range. Hot tablets are ordered by node cpu usage
   * percent. If there are multiple hot tablets that correspond to the same
   * tablet within a 15-minute interval, only the hot tablet with the highest
   * node cpu usage will be included in the response.
   *
   * @param HotTablet[] $hotTablets
   */
  public function setHotTablets($hotTablets)
  {
    $this->hotTablets = $hotTablets;
  }
  /**
   * @return HotTablet[]
   */
  public function getHotTablets()
  {
    return $this->hotTablets;
  }
  /**
   * Set if not all hot tablets could be returned in a single response. Pass
   * this value to `page_token` in another request to get the next page of
   * results.
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
class_alias(ListHotTabletsResponse::class, 'Google_Service_BigtableAdmin_ListHotTabletsResponse');
