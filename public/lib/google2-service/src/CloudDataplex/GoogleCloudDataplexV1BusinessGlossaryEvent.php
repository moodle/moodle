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

class GoogleCloudDataplexV1BusinessGlossaryEvent extends \Google\Model
{
  /**
   * An unspecified event type.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Glossary create event.
   */
  public const EVENT_TYPE_GLOSSARY_CREATE = 'GLOSSARY_CREATE';
  /**
   * Glossary update event.
   */
  public const EVENT_TYPE_GLOSSARY_UPDATE = 'GLOSSARY_UPDATE';
  /**
   * Glossary delete event.
   */
  public const EVENT_TYPE_GLOSSARY_DELETE = 'GLOSSARY_DELETE';
  /**
   * Glossary category create event.
   */
  public const EVENT_TYPE_GLOSSARY_CATEGORY_CREATE = 'GLOSSARY_CATEGORY_CREATE';
  /**
   * Glossary category update event.
   */
  public const EVENT_TYPE_GLOSSARY_CATEGORY_UPDATE = 'GLOSSARY_CATEGORY_UPDATE';
  /**
   * Glossary category delete event.
   */
  public const EVENT_TYPE_GLOSSARY_CATEGORY_DELETE = 'GLOSSARY_CATEGORY_DELETE';
  /**
   * Glossary term create event.
   */
  public const EVENT_TYPE_GLOSSARY_TERM_CREATE = 'GLOSSARY_TERM_CREATE';
  /**
   * Glossary term update event.
   */
  public const EVENT_TYPE_GLOSSARY_TERM_UPDATE = 'GLOSSARY_TERM_UPDATE';
  /**
   * Glossary term delete event.
   */
  public const EVENT_TYPE_GLOSSARY_TERM_DELETE = 'GLOSSARY_TERM_DELETE';
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
   * Accepted values: EVENT_TYPE_UNSPECIFIED, GLOSSARY_CREATE, GLOSSARY_UPDATE,
   * GLOSSARY_DELETE, GLOSSARY_CATEGORY_CREATE, GLOSSARY_CATEGORY_UPDATE,
   * GLOSSARY_CATEGORY_DELETE, GLOSSARY_TERM_CREATE, GLOSSARY_TERM_UPDATE,
   * GLOSSARY_TERM_DELETE
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
class_alias(GoogleCloudDataplexV1BusinessGlossaryEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1BusinessGlossaryEvent');
