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

namespace Google\Service\Container;

class ListUsableSubnetworksResponse extends \Google\Collection
{
  protected $collection_key = 'subnetworks';
  /**
   * This token allows you to get the next page of results for list requests. If
   * the number of results is larger than `page_size`, use the `next_page_token`
   * as a value for the query parameter `page_token` in the next request. The
   * value will become empty when there are no more pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $subnetworksType = UsableSubnetwork::class;
  protected $subnetworksDataType = 'array';

  /**
   * This token allows you to get the next page of results for list requests. If
   * the number of results is larger than `page_size`, use the `next_page_token`
   * as a value for the query parameter `page_token` in the next request. The
   * value will become empty when there are no more pages.
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
   * A list of usable subnetworks in the specified network project.
   *
   * @param UsableSubnetwork[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return UsableSubnetwork[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListUsableSubnetworksResponse::class, 'Google_Service_Container_ListUsableSubnetworksResponse');
