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

class Partner extends \Google\Model
{
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
  protected $adServerConfigType = PartnerAdServerConfig::class;
  protected $adServerConfigDataType = '';
  protected $billingConfigType = PartnerBillingConfig::class;
  protected $billingConfigDataType = '';
  protected $dataAccessConfigType = PartnerDataAccessConfig::class;
  protected $dataAccessConfigDataType = '';
  /**
   * The display name of the partner. Must be UTF-8 encoded with a maximum size
   * of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The status of the partner.
   *
   * @var string
   */
  public $entityStatus;
  protected $exchangeConfigType = ExchangeConfig::class;
  protected $exchangeConfigDataType = '';
  protected $generalConfigType = PartnerGeneralConfig::class;
  protected $generalConfigDataType = '';
  /**
   * Output only. The resource name of the partner.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The unique ID of the partner. Assigned by the system.
   *
   * @var string
   */
  public $partnerId;
  /**
   * Output only. The timestamp when the partner was last updated. Assigned by
   * the system.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Ad server related settings of the partner.
   *
   * @param PartnerAdServerConfig $adServerConfig
   */
  public function setAdServerConfig(PartnerAdServerConfig $adServerConfig)
  {
    $this->adServerConfig = $adServerConfig;
  }
  /**
   * @return PartnerAdServerConfig
   */
  public function getAdServerConfig()
  {
    return $this->adServerConfig;
  }
  /**
   * Billing related settings of the partner.
   *
   * @param PartnerBillingConfig $billingConfig
   */
  public function setBillingConfig(PartnerBillingConfig $billingConfig)
  {
    $this->billingConfig = $billingConfig;
  }
  /**
   * @return PartnerBillingConfig
   */
  public function getBillingConfig()
  {
    return $this->billingConfig;
  }
  /**
   * Settings that control how partner data may be accessed.
   *
   * @param PartnerDataAccessConfig $dataAccessConfig
   */
  public function setDataAccessConfig(PartnerDataAccessConfig $dataAccessConfig)
  {
    $this->dataAccessConfig = $dataAccessConfig;
  }
  /**
   * @return PartnerDataAccessConfig
   */
  public function getDataAccessConfig()
  {
    return $this->dataAccessConfig;
  }
  /**
   * The display name of the partner. Must be UTF-8 encoded with a maximum size
   * of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The status of the partner.
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
   * Settings that control which exchanges are enabled for the partner.
   *
   * @param ExchangeConfig $exchangeConfig
   */
  public function setExchangeConfig(ExchangeConfig $exchangeConfig)
  {
    $this->exchangeConfig = $exchangeConfig;
  }
  /**
   * @return ExchangeConfig
   */
  public function getExchangeConfig()
  {
    return $this->exchangeConfig;
  }
  /**
   * General settings of the partner.
   *
   * @param PartnerGeneralConfig $generalConfig
   */
  public function setGeneralConfig(PartnerGeneralConfig $generalConfig)
  {
    $this->generalConfig = $generalConfig;
  }
  /**
   * @return PartnerGeneralConfig
   */
  public function getGeneralConfig()
  {
    return $this->generalConfig;
  }
  /**
   * Output only. The resource name of the partner.
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
   * Output only. The unique ID of the partner. Assigned by the system.
   *
   * @param string $partnerId
   */
  public function setPartnerId($partnerId)
  {
    $this->partnerId = $partnerId;
  }
  /**
   * @return string
   */
  public function getPartnerId()
  {
    return $this->partnerId;
  }
  /**
   * Output only. The timestamp when the partner was last updated. Assigned by
   * the system.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Partner::class, 'Google_Service_DisplayVideo_Partner');
