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

namespace Google\Service\DisplayVideo;

class GuaranteedOrderStatus extends \Google\Model
{
  /**
   * The approval status is not specified or is unknown in this version.
   */
  public const CONFIG_STATUS_GUARANTEED_ORDER_CONFIG_STATUS_UNSPECIFIED = 'GUARANTEED_ORDER_CONFIG_STATUS_UNSPECIFIED';
  /**
   * The beginning state of a guaranteed order. The guaranteed order in this
   * state needs to be configured before it can serve.
   */
  public const CONFIG_STATUS_PENDING = 'PENDING';
  /**
   * The state after the buyer configures a guaranteed order.
   */
  public const CONFIG_STATUS_COMPLETED = 'COMPLETED';
  /**
   * Default value when status is not specified or is unknown in this version.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const ENTITY_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * Output only. The configuration status of the guaranteed order. Acceptable
   * values are `PENDING` and `COMPLETED`. A guaranteed order must be configured
   * (fill in the required fields, choose creatives, and select a default
   * campaign) before it can serve. Currently the configuration action can only
   * be performed via UI.
   *
   * @var string
   */
  public $configStatus;
  /**
   * The user-provided reason for pausing this guaranteed order. Must be UTF-8
   * encoded with a maximum length of 100 bytes. Only applicable when
   * entity_status is set to `ENTITY_STATUS_PAUSED`.
   *
   * @var string
   */
  public $entityPauseReason;
  /**
   * Whether or not the guaranteed order is servable. Acceptable values are
   * `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_ARCHIVED`, and
   * `ENTITY_STATUS_PAUSED`. Default value is `ENTITY_STATUS_ACTIVE`.
   *
   * @var string
   */
  public $entityStatus;

  /**
   * Output only. The configuration status of the guaranteed order. Acceptable
   * values are `PENDING` and `COMPLETED`. A guaranteed order must be configured
   * (fill in the required fields, choose creatives, and select a default
   * campaign) before it can serve. Currently the configuration action can only
   * be performed via UI.
   *
   * Accepted values: GUARANTEED_ORDER_CONFIG_STATUS_UNSPECIFIED, PENDING,
   * COMPLETED
   *
   * @param self::CONFIG_STATUS_* $configStatus
   */
  public function setConfigStatus($configStatus)
  {
    $this->configStatus = $configStatus;
  }
  /**
   * @return self::CONFIG_STATUS_*
   */
  public function getConfigStatus()
  {
    return $this->configStatus;
  }
  /**
   * The user-provided reason for pausing this guaranteed order. Must be UTF-8
   * encoded with a maximum length of 100 bytes. Only applicable when
   * entity_status is set to `ENTITY_STATUS_PAUSED`.
   *
   * @param string $entityPauseReason
   */
  public function setEntityPauseReason($entityPauseReason)
  {
    $this->entityPauseReason = $entityPauseReason;
  }
  /**
   * @return string
   */
  public function getEntityPauseReason()
  {
    return $this->entityPauseReason;
  }
  /**
   * Whether or not the guaranteed order is servable. Acceptable values are
   * `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_ARCHIVED`, and
   * `ENTITY_STATUS_PAUSED`. Default value is `ENTITY_STATUS_ACTIVE`.
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::ENTITY_STATUS_* $entityStatus
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return self::ENTITY_STATUS_*
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuaranteedOrderStatus::class, 'Google_Service_DisplayVideo_GuaranteedOrderStatus');
