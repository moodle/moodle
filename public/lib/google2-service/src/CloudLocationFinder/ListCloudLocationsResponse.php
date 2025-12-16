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

namespace Google\Service\CloudLocationFinder;

class ListCloudLocationsResponse extends \Google\Collection
{
  protected $collection_key = 'cloudLocations';
  protected $cloudLocationsType = CloudLocation::class;
  protected $cloudLocationsDataType = 'array';
  /**
   * Output only. The continuation token, used to page through large result
   * sets. Provide this value in a subsequent request as page_token in
   * subsequent requests to retrieve the next page. If this field is not
   * present, there are no subsequent results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Output only. List of cloud locations.
   *
   * @param CloudLocation[] $cloudLocations
   */
  public function setCloudLocations($cloudLocations)
  {
    $this->cloudLocations = $cloudLocations;
  }
  /**
   * @return CloudLocation[]
   */
  public function getCloudLocations()
  {
    return $this->cloudLocations;
  }
  /**
   * Output only. The continuation token, used to page through large result
   * sets. Provide this value in a subsequent request as page_token in
   * subsequent requests to retrieve the next page. If this field is not
   * present, there are no subsequent results.
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
class_alias(ListCloudLocationsResponse::class, 'Google_Service_CloudLocationFinder_ListCloudLocationsResponse');
