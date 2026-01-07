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

namespace Google\Service\Dataproc;

class SearchSessionSparkApplicationsResponse extends \Google\Collection
{
  protected $collection_key = 'sparkApplications';
  /**
   * This token is included in the response if there are more results to fetch.
   * To fetch additional results, provide this value as the page_token in a
   * subsequent SearchSessionSparkApplicationsRequest.
   *
   * @var string
   */
  public $nextPageToken;
  protected $sparkApplicationsType = SparkApplication::class;
  protected $sparkApplicationsDataType = 'array';

  /**
   * This token is included in the response if there are more results to fetch.
   * To fetch additional results, provide this value as the page_token in a
   * subsequent SearchSessionSparkApplicationsRequest.
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
   * Output only. High level information corresponding to an application.
   *
   * @param SparkApplication[] $sparkApplications
   */
  public function setSparkApplications($sparkApplications)
  {
    $this->sparkApplications = $sparkApplications;
  }
  /**
   * @return SparkApplication[]
   */
  public function getSparkApplications()
  {
    return $this->sparkApplications;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchSessionSparkApplicationsResponse::class, 'Google_Service_Dataproc_SearchSessionSparkApplicationsResponse');
