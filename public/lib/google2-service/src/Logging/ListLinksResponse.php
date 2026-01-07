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

namespace Google\Service\Logging;

class ListLinksResponse extends \Google\Collection
{
  protected $collection_key = 'links';
  protected $linksType = Link::class;
  protected $linksDataType = 'array';
  /**
   * If there might be more results than those appearing in this response, then
   * nextPageToken is included. To get the next set of results, call the same
   * method again using the value of nextPageToken as pageToken.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * A list of links.
   *
   * @param Link[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return Link[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * If there might be more results than those appearing in this response, then
   * nextPageToken is included. To get the next set of results, call the same
   * method again using the value of nextPageToken as pageToken.
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
class_alias(ListLinksResponse::class, 'Google_Service_Logging_ListLinksResponse');
