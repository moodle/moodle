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

class Campaign extends \Google\Collection
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
  protected $collection_key = 'campaignBudgets';
  /**
   * Output only. The unique ID of the advertiser the campaign belongs to.
   *
   * @var string
   */
  public $advertiserId;
  protected $campaignBudgetsType = CampaignBudget::class;
  protected $campaignBudgetsDataType = 'array';
  protected $campaignFlightType = CampaignFlight::class;
  protected $campaignFlightDataType = '';
  protected $campaignGoalType = CampaignGoal::class;
  protected $campaignGoalDataType = '';
  /**
   * Output only. The unique ID of the campaign. Assigned by the system.
   *
   * @var string
   */
  public $campaignId;
  /**
   * Required. The display name of the campaign. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Controls whether or not the insertion orders under this campaign
   * can spend their budgets and bid on inventory. * Accepted values are
   * `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_ARCHIVED`, and
   * `ENTITY_STATUS_PAUSED`. * For CreateCampaign method,
   * `ENTITY_STATUS_ARCHIVED` is not allowed.
   *
   * @var string
   */
  public $entityStatus;
  protected $frequencyCapType = FrequencyCap::class;
  protected $frequencyCapDataType = '';
  /**
   * Output only. The resource name of the campaign.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when the campaign was last updated. Assigned by
   * the system.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The unique ID of the advertiser the campaign belongs to.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * The list of budgets available to this campaign. If this field is not set,
   * the campaign uses an unlimited budget.
   *
   * @param CampaignBudget[] $campaignBudgets
   */
  public function setCampaignBudgets($campaignBudgets)
  {
    $this->campaignBudgets = $campaignBudgets;
  }
  /**
   * @return CampaignBudget[]
   */
  public function getCampaignBudgets()
  {
    return $this->campaignBudgets;
  }
  /**
   * Required. The planned spend and duration of the campaign.
   *
   * @param CampaignFlight $campaignFlight
   */
  public function setCampaignFlight(CampaignFlight $campaignFlight)
  {
    $this->campaignFlight = $campaignFlight;
  }
  /**
   * @return CampaignFlight
   */
  public function getCampaignFlight()
  {
    return $this->campaignFlight;
  }
  /**
   * Required. The goal of the campaign.
   *
   * @param CampaignGoal $campaignGoal
   */
  public function setCampaignGoal(CampaignGoal $campaignGoal)
  {
    $this->campaignGoal = $campaignGoal;
  }
  /**
   * @return CampaignGoal
   */
  public function getCampaignGoal()
  {
    return $this->campaignGoal;
  }
  /**
   * Output only. The unique ID of the campaign. Assigned by the system.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * Required. The display name of the campaign. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
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
   * Required. Controls whether or not the insertion orders under this campaign
   * can spend their budgets and bid on inventory. * Accepted values are
   * `ENTITY_STATUS_ACTIVE`, `ENTITY_STATUS_ARCHIVED`, and
   * `ENTITY_STATUS_PAUSED`. * For CreateCampaign method,
   * `ENTITY_STATUS_ARCHIVED` is not allowed.
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
   * Required. The frequency cap setting of the campaign. *Warning*: On
   * **February 28, 2025**, frequency cap time periods greater than 30 days will
   * no longer be accepted. [Read more about this announced change](/display-
   * video/api/deprecations#features.lifetime_frequency_cap)
   *
   * @param FrequencyCap $frequencyCap
   */
  public function setFrequencyCap(FrequencyCap $frequencyCap)
  {
    $this->frequencyCap = $frequencyCap;
  }
  /**
   * @return FrequencyCap
   */
  public function getFrequencyCap()
  {
    return $this->frequencyCap;
  }
  /**
   * Output only. The resource name of the campaign.
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
   * Output only. The timestamp when the campaign was last updated. Assigned by
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
class_alias(Campaign::class, 'Google_Service_DisplayVideo_Campaign');
