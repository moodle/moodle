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

class GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse extends \Google\Collection
{
  protected $collection_key = 'processLinks';
  /**
   * The token to specify as `page_token` in the subsequent call to get the next
   * page. Omitted if there are no more pages in the response.
   *
   * @var string
   */
  public $nextPageToken;
  protected $processLinksType = GoogleCloudDatacatalogLineageV1ProcessLinks::class;
  protected $processLinksDataType = 'array';

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
  /**
   * An array of processes associated with the specified links.
   *
   * @param GoogleCloudDatacatalogLineageV1ProcessLinks[] $processLinks
   */
  public function setProcessLinks($processLinks)
  {
    $this->processLinks = $processLinks;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1ProcessLinks[]
   */
  public function getProcessLinks()
  {
    return $this->processLinks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1BatchSearchLinkProcessesResponse');
