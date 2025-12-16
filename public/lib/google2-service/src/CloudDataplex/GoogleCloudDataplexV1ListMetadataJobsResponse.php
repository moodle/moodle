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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ListMetadataJobsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachableLocations';
  protected $metadataJobsType = GoogleCloudDataplexV1MetadataJob::class;
  protected $metadataJobsDataType = 'array';
  /**
   * A token to retrieve the next page of results. If there are no more results
   * in the list, the value is empty.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that the service couldn't reach.
   *
   * @var string[]
   */
  public $unreachableLocations;

  /**
   * Metadata jobs under the specified parent location.
   *
   * @param GoogleCloudDataplexV1MetadataJob[] $metadataJobs
   */
  public function setMetadataJobs($metadataJobs)
  {
    $this->metadataJobs = $metadataJobs;
  }
  /**
   * @return GoogleCloudDataplexV1MetadataJob[]
   */
  public function getMetadataJobs()
  {
    return $this->metadataJobs;
  }
  /**
   * A token to retrieve the next page of results. If there are no more results
   * in the list, the value is empty.
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
   * Locations that the service couldn't reach.
   *
   * @param string[] $unreachableLocations
   */
  public function setUnreachableLocations($unreachableLocations)
  {
    $this->unreachableLocations = $unreachableLocations;
  }
  /**
   * @return string[]
   */
  public function getUnreachableLocations()
  {
    return $this->unreachableLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ListMetadataJobsResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ListMetadataJobsResponse');
