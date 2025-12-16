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

class InventorySourceStatus extends \Google\Model
{
  /**
   * The approval status is not specified or is unknown in this version.
   */
  public const CONFIG_STATUS_INVENTORY_SOURCE_CONFIG_STATUS_UNSPECIFIED = 'INVENTORY_SOURCE_CONFIG_STATUS_UNSPECIFIED';
  /**
   * The beginning state of a guaranteed inventory source. The inventory source
   * in this state needs to be configured.
   */
  public const CONFIG_STATUS_INVENTORY_SOURCE_CONFIG_STATUS_PENDING = 'INVENTORY_SOURCE_CONFIG_STATUS_PENDING';
  /**
   * The state after the buyer configures a guaranteed inventory source.
   */
  public const CONFIG_STATUS_INVENTORY_SOURCE_CONFIG_STATUS_COMPLETED = 'INVENTORY_SOURCE_CONFIG_STATUS_COMPLETED';
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
   * Default value when status is not specified or is unknown in this version.
   */
  public const SELLER_STATUS_ENTITY_STATUS_UNSPECIFIED = 'ENTITY_STATUS_UNSPECIFIED';
  /**
   * The entity is enabled to bid and spend budget.
   */
  public const SELLER_STATUS_ENTITY_STATUS_ACTIVE = 'ENTITY_STATUS_ACTIVE';
  /**
   * The entity is archived. Bidding and budget spending are disabled. An entity
   * can be deleted after archived. Deleted entities cannot be retrieved.
   */
  public const SELLER_STATUS_ENTITY_STATUS_ARCHIVED = 'ENTITY_STATUS_ARCHIVED';
  /**
   * The entity is under draft. Bidding and budget spending are disabled.
   */
  public const SELLER_STATUS_ENTITY_STATUS_DRAFT = 'ENTITY_STATUS_DRAFT';
  /**
   * Bidding and budget spending are paused for the entity.
   */
  public const SELLER_STATUS_ENTITY_STATUS_PAUSED = 'ENTITY_STATUS_PAUSED';
  /**
   * The entity is scheduled for deletion.
   */
  public const SELLER_STATUS_ENTITY_STATUS_SCHEDULED_FOR_DELETION = 'ENTITY_STATUS_SCHEDULED_FOR_DELETION';
  /**
   * Output only. The configuration status of the inventory source. Only
   * applicable for guaranteed inventory sources. Acceptable values are
   * `INVENTORY_SOURCE_CONFIG_STATUS_PENDING` and
   * `INVENTORY_SOURCE_CONFIG_STATUS_COMPLETED`. An inventory source must be
   * configured (fill in the required fields, choose creatives, and select a
   * default campaign) before it can serve.
   *
   * @var string
   */
  public $configStatus;
  /**
   * The user-provided reason for pausing this inventory source. Must not exceed
   * 100 characters. Only applicable when entity_status is set to
   * `ENTITY_STATUS_PAUSED`.
   *
   * @var string
   */
  public $entityPauseReason;
  /**
   * Whether or not the inventory source is servable. Acceptable values are
   * `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_ARCHIVED`, and
   * `ENTITY_STATUS_PAUSED`. Default value is `ENTITY_STATUS_ACTIVE`.
   *
   * @var string
   */
  public $entityStatus;
  /**
   * Output only. The seller-provided reason for pausing this inventory source.
   * Only applicable for inventory sources synced directly from the publishers
   * and when seller_status is set to `ENTITY_STATUS_PAUSED`.
   *
   * @var string
   */
  public $sellerPauseReason;
  /**
   * Output only. The status set by the seller for the inventory source. Only
   * applicable for inventory sources synced directly from the publishers.
   * Acceptable values are `ENTITY_STATUS_ACTIVE` and `ENTITY_STATUS_PAUSED`.
   *
   * @var string
   */
  public $sellerStatus;

  /**
   * Output only. The configuration status of the inventory source. Only
   * applicable for guaranteed inventory sources. Acceptable values are
   * `INVENTORY_SOURCE_CONFIG_STATUS_PENDING` and
   * `INVENTORY_SOURCE_CONFIG_STATUS_COMPLETED`. An inventory source must be
   * configured (fill in the required fields, choose creatives, and select a
   * default campaign) before it can serve.
   *
   * Accepted values: INVENTORY_SOURCE_CONFIG_STATUS_UNSPECIFIED,
   * INVENTORY_SOURCE_CONFIG_STATUS_PENDING,
   * INVENTORY_SOURCE_CONFIG_STATUS_COMPLETED
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
   * The user-provided reason for pausing this inventory source. Must not exceed
   * 100 characters. Only applicable when entity_status is set to
   * `ENTITY_STATUS_PAUSED`.
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
   * Whether or not the inventory source is servable. Acceptable values are
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
  /**
   * Output only. The seller-provided reason for pausing this inventory source.
   * Only applicable for inventory sources synced directly from the publishers
   * and when seller_status is set to `ENTITY_STATUS_PAUSED`.
   *
   * @param string $sellerPauseReason
   */
  public function setSellerPauseReason($sellerPauseReason)
  {
    $this->sellerPauseReason = $sellerPauseReason;
  }
  /**
   * @return string
   */
  public function getSellerPauseReason()
  {
    return $this->sellerPauseReason;
  }
  /**
   * Output only. The status set by the seller for the inventory source. Only
   * applicable for inventory sources synced directly from the publishers.
   * Acceptable values are `ENTITY_STATUS_ACTIVE` and `ENTITY_STATUS_PAUSED`.
   *
   * Accepted values: ENTITY_STATUS_UNSPECIFIED, ENTITY_STATUS_ACTIVE,
   * ENTITY_STATUS_ARCHIVED, ENTITY_STATUS_DRAFT, ENTITY_STATUS_PAUSED,
   * ENTITY_STATUS_SCHEDULED_FOR_DELETION
   *
   * @param self::SELLER_STATUS_* $sellerStatus
   */
  public function setSellerStatus($sellerStatus)
  {
    $this->sellerStatus = $sellerStatus;
  }
  /**
   * @return self::SELLER_STATUS_*
   */
  public function getSellerStatus()
  {
    return $this->sellerStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InventorySourceStatus::class, 'Google_Service_DisplayVideo_InventorySourceStatus');
