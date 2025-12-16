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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1ListTargetSitesResponse extends \Google\Collection
{
  protected $collection_key = 'targetSites';
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $targetSitesType = GoogleCloudDiscoveryengineV1TargetSite::class;
  protected $targetSitesDataType = 'array';
  /**
   * The total number of items matching the request. This will always be
   * populated in the response.
   *
   * @var int
   */
  public $totalSize;

  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted, there are no subsequent pages.
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
   * List of TargetSites.
   *
   * @param GoogleCloudDiscoveryengineV1TargetSite[] $targetSites
   */
  public function setTargetSites($targetSites)
  {
    $this->targetSites = $targetSites;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1TargetSite[]
   */
  public function getTargetSites()
  {
    return $this->targetSites;
  }
  /**
   * The total number of items matching the request. This will always be
   * populated in the response.
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
class_alias(GoogleCloudDiscoveryengineV1ListTargetSitesResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ListTargetSitesResponse');
