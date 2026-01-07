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

class GoogleCloudDatacatalogLineageV1ListRunsResponse extends \Google\Collection
{
  protected $collection_key = 'runs';
  /**
   * The token to specify as `page_token` in the next call to get the next page.
   * If this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $runsType = GoogleCloudDatacatalogLineageV1Run::class;
  protected $runsDataType = 'array';

  /**
   * The token to specify as `page_token` in the next call to get the next page.
   * If this field is omitted, there are no subsequent pages.
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
   * The runs from the specified project and location.
   *
   * @param GoogleCloudDatacatalogLineageV1Run[] $runs
   */
  public function setRuns($runs)
  {
    $this->runs = $runs;
  }
  /**
   * @return GoogleCloudDatacatalogLineageV1Run[]
   */
  public function getRuns()
  {
    return $this->runs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogLineageV1ListRunsResponse::class, 'Google_Service_Datalineage_GoogleCloudDatacatalogLineageV1ListRunsResponse');
