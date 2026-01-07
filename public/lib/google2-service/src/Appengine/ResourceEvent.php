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

namespace Google\Service\Appengine;

class ResourceEvent extends \Google\Model
{
  /**
   * The unique ID for this per-resource event. CLHs can use this value to dedup
   * repeated calls. required
   *
   * @var string
   */
  public $eventId;
  /**
   * The name of the resource for which this event is. required
   *
   * @var string
   */
  public $name;
  protected $stateType = ContainerState::class;
  protected $stateDataType = '';

  /**
   * The unique ID for this per-resource event. CLHs can use this value to dedup
   * repeated calls. required
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * The name of the resource for which this event is. required
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The state of the project that led to this event.
   *
   * @param ContainerState $state
   */
  public function setState(ContainerState $state)
  {
    $this->state = $state;
  }
  /**
   * @return ContainerState
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceEvent::class, 'Google_Service_Appengine_ResourceEvent');
