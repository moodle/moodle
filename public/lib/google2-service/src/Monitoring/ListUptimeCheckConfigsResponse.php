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

namespace Google\Service\Monitoring;

class ListUptimeCheckConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'uptimeCheckConfigs';
  /**
   * This field represents the pagination token to retrieve the next page of
   * results. If the value is empty, it means no further results for the
   * request. To retrieve the next page of results, the value of the
   * next_page_token is passed to the subsequent List method call (in the
   * request message's page_token field).
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of Uptime check configurations for the project,
   * irrespective of any pagination.
   *
   * @var int
   */
  public $totalSize;
  protected $uptimeCheckConfigsType = UptimeCheckConfig::class;
  protected $uptimeCheckConfigsDataType = 'array';

  /**
   * This field represents the pagination token to retrieve the next page of
   * results. If the value is empty, it means no further results for the
   * request. To retrieve the next page of results, the value of the
   * next_page_token is passed to the subsequent List method call (in the
   * request message's page_token field).
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
   * The total number of Uptime check configurations for the project,
   * irrespective of any pagination.
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
  /**
   * The returned Uptime check configurations.
   *
   * @param UptimeCheckConfig[] $uptimeCheckConfigs
   */
  public function setUptimeCheckConfigs($uptimeCheckConfigs)
  {
    $this->uptimeCheckConfigs = $uptimeCheckConfigs;
  }
  /**
   * @return UptimeCheckConfig[]
   */
  public function getUptimeCheckConfigs()
  {
    return $this->uptimeCheckConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListUptimeCheckConfigsResponse::class, 'Google_Service_Monitoring_ListUptimeCheckConfigsResponse');
