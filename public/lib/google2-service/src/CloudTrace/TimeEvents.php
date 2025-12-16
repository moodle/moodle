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

namespace Google\Service\CloudTrace;

class TimeEvents extends \Google\Collection
{
  protected $collection_key = 'timeEvent';
  /**
   * The number of dropped annotations in all the included time events. If the
   * value is 0, then no annotations were dropped.
   *
   * @var int
   */
  public $droppedAnnotationsCount;
  /**
   * The number of dropped message events in all the included time events. If
   * the value is 0, then no message events were dropped.
   *
   * @var int
   */
  public $droppedMessageEventsCount;
  protected $timeEventType = TimeEvent::class;
  protected $timeEventDataType = 'array';

  /**
   * The number of dropped annotations in all the included time events. If the
   * value is 0, then no annotations were dropped.
   *
   * @param int $droppedAnnotationsCount
   */
  public function setDroppedAnnotationsCount($droppedAnnotationsCount)
  {
    $this->droppedAnnotationsCount = $droppedAnnotationsCount;
  }
  /**
   * @return int
   */
  public function getDroppedAnnotationsCount()
  {
    return $this->droppedAnnotationsCount;
  }
  /**
   * The number of dropped message events in all the included time events. If
   * the value is 0, then no message events were dropped.
   *
   * @param int $droppedMessageEventsCount
   */
  public function setDroppedMessageEventsCount($droppedMessageEventsCount)
  {
    $this->droppedMessageEventsCount = $droppedMessageEventsCount;
  }
  /**
   * @return int
   */
  public function getDroppedMessageEventsCount()
  {
    return $this->droppedMessageEventsCount;
  }
  /**
   * A collection of `TimeEvent`s.
   *
   * @param TimeEvent[] $timeEvent
   */
  public function setTimeEvent($timeEvent)
  {
    $this->timeEvent = $timeEvent;
  }
  /**
   * @return TimeEvent[]
   */
  public function getTimeEvent()
  {
    return $this->timeEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeEvents::class, 'Google_Service_CloudTrace_TimeEvents');
