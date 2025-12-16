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

class GoogleCloudDataplexV1ListDataScanJobsResponse extends \Google\Collection
{
  protected $collection_key = 'dataScanJobs';
  protected $dataScanJobsType = GoogleCloudDataplexV1DataScanJob::class;
  protected $dataScanJobsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * DataScanJobs (BASIC view only) under a given dataScan.
   *
   * @param GoogleCloudDataplexV1DataScanJob[] $dataScanJobs
   */
  public function setDataScanJobs($dataScanJobs)
  {
    $this->dataScanJobs = $dataScanJobs;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanJob[]
   */
  public function getDataScanJobs()
  {
    return $this->dataScanJobs;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
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
class_alias(GoogleCloudDataplexV1ListDataScanJobsResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ListDataScanJobsResponse');
