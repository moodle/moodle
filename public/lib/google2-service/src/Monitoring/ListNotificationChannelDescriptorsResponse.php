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

class ListNotificationChannelDescriptorsResponse extends \Google\Collection
{
  protected $collection_key = 'channelDescriptors';
  protected $channelDescriptorsType = NotificationChannelDescriptor::class;
  protected $channelDescriptorsDataType = 'array';
  /**
   * If not empty, indicates that there may be more results that match the
   * request. Use the value in the page_token field in a subsequent request to
   * fetch the next set of results. If empty, all results have been returned.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The monitored resource descriptors supported for the specified project,
   * optionally filtered.
   *
   * @param NotificationChannelDescriptor[] $channelDescriptors
   */
  public function setChannelDescriptors($channelDescriptors)
  {
    $this->channelDescriptors = $channelDescriptors;
  }
  /**
   * @return NotificationChannelDescriptor[]
   */
  public function getChannelDescriptors()
  {
    return $this->channelDescriptors;
  }
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListNotificationChannelDescriptorsResponse::class, 'Google_Service_Monitoring_ListNotificationChannelDescriptorsResponse');
