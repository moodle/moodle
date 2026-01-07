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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ListEventsResponse extends \Google\Collection
{
  protected $collection_key = 'sessionEvents';
  /**
   * A token, which can be sent as ListEventsRequest.page_token to retrieve the
   * next page. Absence of this field indicates there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $sessionEventsType = GoogleCloudAiplatformV1SessionEvent::class;
  protected $sessionEventsDataType = 'array';

  /**
   * A token, which can be sent as ListEventsRequest.page_token to retrieve the
   * next page. Absence of this field indicates there are no subsequent pages.
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
   * A list of events matching the request. Ordered by timestamp in ascending
   * order.
   *
   * @param GoogleCloudAiplatformV1SessionEvent[] $sessionEvents
   */
  public function setSessionEvents($sessionEvents)
  {
    $this->sessionEvents = $sessionEvents;
  }
  /**
   * @return GoogleCloudAiplatformV1SessionEvent[]
   */
  public function getSessionEvents()
  {
    return $this->sessionEvents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ListEventsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ListEventsResponse');
