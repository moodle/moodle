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

class ListFloodlightActivitiesResponse extends \Google\Collection
{
  protected $collection_key = 'floodlightActivities';
  protected $floodlightActivitiesType = FloodlightActivity::class;
  protected $floodlightActivitiesDataType = 'array';
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to `ListFloodlightActivities`
   * method to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of Floodlight activities. This list will be absent if empty.
   *
   * @param FloodlightActivity[] $floodlightActivities
   */
  public function setFloodlightActivities($floodlightActivities)
  {
    $this->floodlightActivities = $floodlightActivities;
  }
  /**
   * @return FloodlightActivity[]
   */
  public function getFloodlightActivities()
  {
    return $this->floodlightActivities;
  }
  /**
   * A token to retrieve the next page of results. Pass this value in the
   * page_token field in the subsequent call to `ListFloodlightActivities`
   * method to retrieve the next page of results.
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
class_alias(ListFloodlightActivitiesResponse::class, 'Google_Service_DisplayVideo_ListFloodlightActivitiesResponse');
