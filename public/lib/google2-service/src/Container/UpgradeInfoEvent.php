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

namespace Google\Service\Container;

class UpgradeInfoEvent extends \Google\Model
{
  /**
   * EVENT_TYPE_UNSPECIFIED indicates the event type is unspecified.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * END_OF_SUPPORT indicates GKE version reaches end of support, check
   * standard_support_end_time and extended_support_end_time for more details.
   */
  public const EVENT_TYPE_END_OF_SUPPORT = 'END_OF_SUPPORT';
  /**
   * COS_MILESTONE_VERSION_UPDATE indicates that the COS node image will update
   * COS milestone version for new patch versions starting with the one in the
   * description.
   */
  public const EVENT_TYPE_COS_MILESTONE_VERSION_UPDATE = 'COS_MILESTONE_VERSION_UPDATE';
  /**
   * UPGRADE_LIFECYCLE indicates the event is about the upgrade lifecycle.
   */
  public const EVENT_TYPE_UPGRADE_LIFECYCLE = 'UPGRADE_LIFECYCLE';
  /**
   * DISRUPTION_EVENT indicates the event is about the disruption.
   */
  public const EVENT_TYPE_DISRUPTION_EVENT = 'DISRUPTION_EVENT';
  /**
   * Default value. This shouldn't be used.
   */
  public const RESOURCE_TYPE_UPGRADE_RESOURCE_TYPE_UNSPECIFIED = 'UPGRADE_RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Master / control plane
   */
  public const RESOURCE_TYPE_MASTER = 'MASTER';
  /**
   * Node pool
   */
  public const RESOURCE_TYPE_NODE_POOL = 'NODE_POOL';
  /**
   * STATE_UNSPECIFIED indicates the state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * STARTED indicates the upgrade has started.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * SUCCEEDED indicates the upgrade has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * FAILED indicates the upgrade has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * CANCELED indicates the upgrade has canceled.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * The current version before the upgrade.
   *
   * @var string
   */
  public $currentVersion;
  /**
   * A brief description of the event.
   *
   * @var string
   */
  public $description;
  protected $disruptionEventType = DisruptionEvent::class;
  protected $disruptionEventDataType = '';
  /**
   * The time when the operation ended.
   *
   * @var string
   */
  public $endTime;
  /**
   * The type of the event.
   *
   * @var string
   */
  public $eventType;
  /**
   * The end of extended support timestamp.
   *
   * @var string
   */
  public $extendedSupportEndTime;
  /**
   * The operation associated with this upgrade.
   *
   * @var string
   */
  public $operation;
  /**
   * Optional relative path to the resource. For example in node pool upgrades,
   * the relative path of the node pool.
   *
   * @var string
   */
  public $resource;
  /**
   * The resource type associated with the upgrade.
   *
   * @var string
   */
  public $resourceType;
  /**
   * The end of standard support timestamp.
   *
   * @var string
   */
  public $standardSupportEndTime;
  /**
   * The time when the operation was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The state of the upgrade.
   *
   * @var string
   */
  public $state;
  /**
   * The target version for the upgrade.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * The current version before the upgrade.
   *
   * @param string $currentVersion
   */
  public function setCurrentVersion($currentVersion)
  {
    $this->currentVersion = $currentVersion;
  }
  /**
   * @return string
   */
  public function getCurrentVersion()
  {
    return $this->currentVersion;
  }
  /**
   * A brief description of the event.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The information about the disruption event. This field is only populated
   * when event_type is DISRUPTION_EVENT.
   *
   * @param DisruptionEvent $disruptionEvent
   */
  public function setDisruptionEvent(DisruptionEvent $disruptionEvent)
  {
    $this->disruptionEvent = $disruptionEvent;
  }
  /**
   * @return DisruptionEvent
   */
  public function getDisruptionEvent()
  {
    return $this->disruptionEvent;
  }
  /**
   * The time when the operation ended.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The type of the event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, END_OF_SUPPORT,
   * COS_MILESTONE_VERSION_UPDATE, UPGRADE_LIFECYCLE, DISRUPTION_EVENT
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
   * The end of extended support timestamp.
   *
   * @param string $extendedSupportEndTime
   */
  public function setExtendedSupportEndTime($extendedSupportEndTime)
  {
    $this->extendedSupportEndTime = $extendedSupportEndTime;
  }
  /**
   * @return string
   */
  public function getExtendedSupportEndTime()
  {
    return $this->extendedSupportEndTime;
  }
  /**
   * The operation associated with this upgrade.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Optional relative path to the resource. For example in node pool upgrades,
   * the relative path of the node pool.
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
  /**
   * The resource type associated with the upgrade.
   *
   * Accepted values: UPGRADE_RESOURCE_TYPE_UNSPECIFIED, MASTER, NODE_POOL
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * The end of standard support timestamp.
   *
   * @param string $standardSupportEndTime
   */
  public function setStandardSupportEndTime($standardSupportEndTime)
  {
    $this->standardSupportEndTime = $standardSupportEndTime;
  }
  /**
   * @return string
   */
  public function getStandardSupportEndTime()
  {
    return $this->standardSupportEndTime;
  }
  /**
   * The time when the operation was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The state of the upgrade.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTED, SUCCEEDED, FAILED, CANCELED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The target version for the upgrade.
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeInfoEvent::class, 'Google_Service_Container_UpgradeInfoEvent');
