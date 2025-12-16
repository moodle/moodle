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

class ListUptimeCheckIpsResponse extends \Google\Collection
{
  protected $collection_key = 'uptimeCheckIps';
  /**
   * This field represents the pagination token to retrieve the next page of
   * results. If the value is empty, it means no further results for the
   * request. To retrieve the next page of results, the value of the
   * next_page_token is passed to the subsequent List method call (in the
   * request message's page_token field). NOTE: this field is not yet
   * implemented
   *
   * @var string
   */
  public $nextPageToken;
  protected $uptimeCheckIpsType = UptimeCheckIp::class;
  protected $uptimeCheckIpsDataType = 'array';

  /**
   * This field represents the pagination token to retrieve the next page of
   * results. If the value is empty, it means no further results for the
   * request. To retrieve the next page of results, the value of the
   * next_page_token is passed to the subsequent List method call (in the
   * request message's page_token field). NOTE: this field is not yet
   * implemented
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
   * The returned list of IP addresses (including region and location) that the
   * checkers run from.
   *
   * @param UptimeCheckIp[] $uptimeCheckIps
   */
  public function setUptimeCheckIps($uptimeCheckIps)
  {
    $this->uptimeCheckIps = $uptimeCheckIps;
  }
  /**
   * @return UptimeCheckIp[]
   */
  public function getUptimeCheckIps()
  {
    return $this->uptimeCheckIps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListUptimeCheckIpsResponse::class, 'Google_Service_Monitoring_ListUptimeCheckIpsResponse');
