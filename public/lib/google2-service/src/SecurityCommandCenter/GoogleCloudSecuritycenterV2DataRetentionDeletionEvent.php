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

class GoogleCloudSecuritycenterV2DataRetentionDeletionEvent extends \Google\Model
{
  /**
   * Unspecified event type.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * The maximum retention time has been exceeded.
   */
  public const EVENT_TYPE_EVENT_TYPE_MAX_TTL_EXCEEDED = 'EVENT_TYPE_MAX_TTL_EXCEEDED';
  /**
   * Max TTL from the asset's creation time.
   */
  public const EVENT_TYPE_EVENT_TYPE_MAX_TTL_FROM_CREATION = 'EVENT_TYPE_MAX_TTL_FROM_CREATION';
  /**
   * Max TTL from the asset's last modification time.
   */
  public const EVENT_TYPE_EVENT_TYPE_MAX_TTL_FROM_LAST_MODIFICATION = 'EVENT_TYPE_MAX_TTL_FROM_LAST_MODIFICATION';
  /**
   * Number of objects that violated the policy for this resource. If the number
   * is less than 1,000, then the value of this field is the exact number. If
   * the number of objects that violated the policy is greater than or equal to
   * 1,000, then the value of this field is 1000.
   *
   * @var string
   */
  public $dataObjectCount;
  /**
   * Timestamp indicating when the event was detected.
   *
   * @var string
   */
  public $eventDetectionTime;
  /**
   * Type of the DRD event.
   *
   * @var string
   */
  public $eventType;
  /**
   * Maximum duration of retention allowed from the DRD control. This comes from
   * the DRD control where users set a max TTL for their data. For example,
   * suppose that a user sets the max TTL for a Cloud Storage bucket to 90 days.
   * However, an object in that bucket is 100 days old. In this case, a
   * DataRetentionDeletionEvent will be generated for that Cloud Storage bucket,
   * and the max_retention_allowed is 90 days.
   *
   * @var string
   */
  public $maxRetentionAllowed;

  /**
   * Number of objects that violated the policy for this resource. If the number
   * is less than 1,000, then the value of this field is the exact number. If
   * the number of objects that violated the policy is greater than or equal to
   * 1,000, then the value of this field is 1000.
   *
   * @param string $dataObjectCount
   */
  public function setDataObjectCount($dataObjectCount)
  {
    $this->dataObjectCount = $dataObjectCount;
  }
  /**
   * @return string
   */
  public function getDataObjectCount()
  {
    return $this->dataObjectCount;
  }
  /**
   * Timestamp indicating when the event was detected.
   *
   * @param string $eventDetectionTime
   */
  public function setEventDetectionTime($eventDetectionTime)
  {
    $this->eventDetectionTime = $eventDetectionTime;
  }
  /**
   * @return string
   */
  public function getEventDetectionTime()
  {
    return $this->eventDetectionTime;
  }
  /**
   * Type of the DRD event.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, EVENT_TYPE_MAX_TTL_EXCEEDED,
   * EVENT_TYPE_MAX_TTL_FROM_CREATION, EVENT_TYPE_MAX_TTL_FROM_LAST_MODIFICATION
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
   * Maximum duration of retention allowed from the DRD control. This comes from
   * the DRD control where users set a max TTL for their data. For example,
   * suppose that a user sets the max TTL for a Cloud Storage bucket to 90 days.
   * However, an object in that bucket is 100 days old. In this case, a
   * DataRetentionDeletionEvent will be generated for that Cloud Storage bucket,
   * and the max_retention_allowed is 90 days.
   *
   * @param string $maxRetentionAllowed
   */
  public function setMaxRetentionAllowed($maxRetentionAllowed)
  {
    $this->maxRetentionAllowed = $maxRetentionAllowed;
  }
  /**
   * @return string
   */
  public function getMaxRetentionAllowed()
  {
    return $this->maxRetentionAllowed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2DataRetentionDeletionEvent::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2DataRetentionDeletionEvent');
