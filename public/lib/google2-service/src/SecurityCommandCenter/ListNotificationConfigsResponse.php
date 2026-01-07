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

namespace Google\Service\SecurityCommandCenter;

class ListNotificationConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'notificationConfigs';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $notificationConfigsType = NotificationConfig::class;
  protected $notificationConfigsDataType = 'array';

  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results.
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
   * Notification configs belonging to the requested parent.
   *
   * @param NotificationConfig[] $notificationConfigs
   */
  public function setNotificationConfigs($notificationConfigs)
  {
    $this->notificationConfigs = $notificationConfigs;
  }
  /**
   * @return NotificationConfig[]
   */
  public function getNotificationConfigs()
  {
    return $this->notificationConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListNotificationConfigsResponse::class, 'Google_Service_SecurityCommandCenter_ListNotificationConfigsResponse');
