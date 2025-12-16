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

namespace Google\Service\Datalineage;

class GoogleCloudDatacatalogLineageV1SearchLinksResponse extends \Google\Collection
{
  protected $collection_key = 'links';
  protected $linksType = GoogleCloudDatacatalogLineageV1Link::class;
  protected $linksDataType = 'array';
  /**
   * The token to specify as `page_token` in the subsequent call to get the next
   * page. Omitted if there are no more pages in the response.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of links for a given asset. Can be empty if the asset has no
   * relations of requested type (source or target).
   *
   * @param GoogleCloudDatacatalogLineageV1Link[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1Link[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * The token to specify as `page_token` in the subsequent call to get the next
   * page. Omitted if there are no more pages in the response.
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
class_alias(GoogleCloudDatacatalogLineageV1SearchLinksResponse::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1SearchLinksResponse');
