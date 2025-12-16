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

class ListAlertsResponse extends \Google\Collection
{
  protected $collection_key = 'alerts';
  protected $alertsType = Alert::class;
  protected $alertsDataType = 'array';
  /**
   * If not empty, indicates that there may be more results that match the
   * request. Use the value in the page_token field in a subsequent request to
   * fetch the next set of results. The token is encrypted and only guaranteed
   * to return correct results for 72 hours after it is created. If empty, all
   * results have been returned.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The estimated total number of matching results for this query.
   *
   * @var int
   */
  public $totalSize;

  /**
   * The list of alerts.
   *
   * @param Alert[] $alerts
   */
  public function setAlerts($alerts)
  {
    $this->alerts = $alerts;
  }
  /**
   * @return Alert[]
   */
  public function getAlerts()
  {
    return $this->alerts;
  }
  /**
   * If not empty, indicates that there may be more results that match the
   * request. Use the value in the page_token field in a subsequent request to
   * fetch the next set of results. The token is encrypted and only guaranteed
   * to return correct results for 72 hours after it is created. If empty, all
   * results have been returned.
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
   * The estimated total number of matching results for this query.
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
class_alias(ListAlertsResponse::class, 'Google_Service_Monitoring_ListAlertsResponse');
