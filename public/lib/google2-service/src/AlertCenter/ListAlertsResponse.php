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

namespace Google\Service\AlertCenter;

class ListAlertsResponse extends \Google\Collection
{
  protected $collection_key = 'alerts';
  protected $alertsType = Alert::class;
  protected $alertsDataType = 'array';
  /**
   * The token for the next page. If not empty, indicates that there may be more
   * alerts that match the listing request; this value can be used in a
   * subsequent ListAlertsRequest to get alerts continuing from last result of
   * the current list call.
   *
   * @var string
   */
  public $nextPageToken;

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
   * The token for the next page. If not empty, indicates that there may be more
   * alerts that match the listing request; this value can be used in a
   * subsequent ListAlertsRequest to get alerts continuing from last result of
   * the current list call.
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
class_alias(ListAlertsResponse::class, 'Google_Service_AlertCenter_ListAlertsResponse');
