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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse extends \Google\Collection
{
  protected $collection_key = 'workloadUpdates';
  /**
   * The next page token. Return empty if reached the last page.
   *
   * @var string
   */
  public $nextPageToken;
  protected $workloadUpdatesType = GoogleCloudAssuredworkloadsV1WorkloadUpdate::class;
  protected $workloadUpdatesDataType = 'array';

  /**
   * The next page token. Return empty if reached the last page.
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
   * The list of workload updates for a given workload.
   *
   * @param GoogleCloudAssuredworkloadsV1WorkloadUpdate[] $workloadUpdates
   */
  public function setWorkloadUpdates($workloadUpdates)
  {
    $this->workloadUpdates = $workloadUpdates;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1WorkloadUpdate[]
   */
  public function getWorkloadUpdates()
  {
    return $this->workloadUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1ListWorkloadUpdatesResponse');
