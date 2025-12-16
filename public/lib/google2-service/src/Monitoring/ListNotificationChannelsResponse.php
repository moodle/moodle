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

class ListNotificationChannelsResponse extends \Google\Collection
{
  protected $collection_key = 'notificationChannels';
  /**
   * If not empty, indicates that there may be more results that match the
   * request. Use the value in the page_token field in a subsequent request to
   * fetch the next set of results. If empty, all results have been returned.
   *
   * @var string
   */
  public $nextPageToken;
  protected $notificationChannelsType = NotificationChannel::class;
  protected $notificationChannelsDataType = 'array';
  /**
   * The total number of notification channels in all pages. This number is only
   * an estimate, and may change in subsequent pages. https://aip.dev/158
   *
   * @var int
   */
  public $totalSize;

  /**
   * If not empty, indicates that there may be more results that match the
   * request. Use the value in the page_token field in a subsequent request to
   * fetch the next set of results. If empty, all results have been returned.
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
   * The notification channels defined for the specified project.
   *
   * @param NotificationChannel[] $notificationChannels
   */
  public function setNotificationChannels($notificationChannels)
  {
    $this->notificationChannels = $notificationChannels;
  }
  /**
   * @return NotificationChannel[]
   */
  public function getNotificationChannels()
  {
    return $this->notificationChannels;
  }
  /**
   * The total number of notification channels in all pages. This number is only
   * an estimate, and may change in subsequent pages. https://aip.dev/158
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
class_alias(ListNotificationChannelsResponse::class, 'Google_Service_Monitoring_ListNotificationChannelsResponse');
