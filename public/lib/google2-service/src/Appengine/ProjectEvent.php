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

class ProjectEvent extends \Google\Model
{
  public const PHASE_CONTAINER_EVENT_PHASE_UNSPECIFIED = 'CONTAINER_EVENT_PHASE_UNSPECIFIED';
  public const PHASE_BEFORE_RESOURCE_HANDLING = 'BEFORE_RESOURCE_HANDLING';
  public const PHASE_AFTER_RESOURCE_HANDLING = 'AFTER_RESOURCE_HANDLING';
  /**
   * The unique ID for this project event. CLHs can use this value to dedup
   * repeated calls. required
   *
   * @var string
   */
  public $eventId;
  /**
   * Phase indicates when in the container event propagation this event is being
   * communicated. Events are sent before and after the per-resource events are
   * propagated. required
   *
   * @var string
   */
  public $phase;
  protected $projectMetadataType = ProjectsMetadata::class;
  protected $projectMetadataDataType = '';
  protected $stateType = ContainerState::class;
  protected $stateDataType = '';

  /**
   * The unique ID for this project event. CLHs can use this value to dedup
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
   * Phase indicates when in the container event propagation this event is being
   * communicated. Events are sent before and after the per-resource events are
   * propagated. required
   *
   * Accepted values: CONTAINER_EVENT_PHASE_UNSPECIFIED,
   * BEFORE_RESOURCE_HANDLING, AFTER_RESOURCE_HANDLING
   *
   * @param self::PHASE_* $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return self::PHASE_*
   */
  public function getPhase()
  {
    return $this->phase;
  }
  /**
   * The projects metadata for this project. required
   *
   * @param ProjectsMetadata $projectMetadata
   */
  public function setProjectMetadata(ProjectsMetadata $projectMetadata)
  {
    $this->projectMetadata = $projectMetadata;
  }
  /**
   * @return ProjectsMetadata
   */
  public function getProjectMetadata()
  {
    return $this->projectMetadata;
  }
  /**
   * The state of the organization that led to this event.
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
class_alias(ProjectEvent::class, 'Google_Service_Appengine_ProjectEvent');
