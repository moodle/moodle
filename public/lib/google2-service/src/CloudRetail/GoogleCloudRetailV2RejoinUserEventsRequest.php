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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2RejoinUserEventsRequest extends \Google\Model
{
  /**
   * Rejoin all events with the latest product catalog, including both joined
   * events and unjoined events.
   */
  public const USER_EVENT_REJOIN_SCOPE_USER_EVENT_REJOIN_SCOPE_UNSPECIFIED = 'USER_EVENT_REJOIN_SCOPE_UNSPECIFIED';
  /**
   * Only rejoin joined events with the latest product catalog.
   */
  public const USER_EVENT_REJOIN_SCOPE_JOINED_EVENTS = 'JOINED_EVENTS';
  /**
   * Only rejoin unjoined events with the latest product catalog.
   */
  public const USER_EVENT_REJOIN_SCOPE_UNJOINED_EVENTS = 'UNJOINED_EVENTS';
  /**
   * The type of the user event rejoin to define the scope and range of the user
   * events to be rejoined with the latest product catalog. Defaults to
   * `USER_EVENT_REJOIN_SCOPE_UNSPECIFIED` if this field is not set, or set to
   * an invalid integer value.
   *
   * @var string
   */
  public $userEventRejoinScope;

  /**
   * The type of the user event rejoin to define the scope and range of the user
   * events to be rejoined with the latest product catalog. Defaults to
   * `USER_EVENT_REJOIN_SCOPE_UNSPECIFIED` if this field is not set, or set to
   * an invalid integer value.
   *
   * Accepted values: USER_EVENT_REJOIN_SCOPE_UNSPECIFIED, JOINED_EVENTS,
   * UNJOINED_EVENTS
   *
   * @param self::USER_EVENT_REJOIN_SCOPE_* $userEventRejoinScope
   */
  public function setUserEventRejoinScope($userEventRejoinScope)
  {
    $this->userEventRejoinScope = $userEventRejoinScope;
  }
  /**
   * @return self::USER_EVENT_REJOIN_SCOPE_*
   */
  public function getUserEventRejoinScope()
  {
    return $this->userEventRejoinScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2RejoinUserEventsRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2RejoinUserEventsRequest');
