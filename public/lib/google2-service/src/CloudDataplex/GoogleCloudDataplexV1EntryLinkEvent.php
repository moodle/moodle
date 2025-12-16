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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EntryLinkEvent extends \Google\Model
{
  /**
   * An unspecified event type.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * EntryLink create event.
   */
  public const EVENT_TYPE_ENTRY_LINK_CREATE = 'ENTRY_LINK_CREATE';
  /**
   * EntryLink delete event.
   */
  public const EVENT_TYPE_ENTRY_LINK_DELETE = 'ENTRY_LINK_DELETE';
  /**
   * The type of the event.
   *
   * @var string
   */
  public $eventType;
  /**
   * The log message.
   *
   * @var string
   */
  public $message;
  /**
   * Name of the resource.
   *
   * @var string
   */
  public $resource;

  /**
   * The type of the event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, ENTRY_LINK_CREATE,
   * ENTRY_LINK_DELETE
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * The log message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Name of the resource.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EntryLinkEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EntryLinkEvent');
