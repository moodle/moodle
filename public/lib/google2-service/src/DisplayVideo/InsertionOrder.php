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

class InsertionOrder extends \Google\Collection
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
  /**
   * Insertion order type is not specified or is unknown.
   */
  public const INSERTION_ORDER_TYPE_INSERTION_ORDER_TYPE_UNSPECIFIED = 'INSERTION_ORDER_TYPE_UNSPECIFIED';
  /**
   * Real-time bidding.
   */
  public const INSERTION_ORDER_TYPE_RTB = 'RTB';
  /**
   * Over-the-top.
   */
  public const INSERTION_ORDER_TYPE_OVER_THE_TOP = 'OVER_THE_TOP';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const OPTIMIZATION_OBJECTIVE_OPTIMIZATION_OBJECTIVE_UNSPECIFIED = 'OPTIMIZATION_OBJECTIVE_UNSPECIFIED';
  /**
   * Prioritize impressions that increase sales and conversions.
   */
  public const OPTIMIZATION_OBJECTIVE_CONVERSION = 'CONVERSION';
  /**
   * Prioritize impressions that increase website traffic, apps, app stores.
   */
  public const OPTIMIZATION_OBJECTIVE_CLICK = 'CLICK';
  /**
   * Prioritize impressions of specific quality.
   */
  public const OPTIMIZATION_OBJECTIVE_BRAND_AWARENESS = 'BRAND_AWARENESS';
  /**
   * Objective is defined by the assigned custom bidding algorithm.
   */
  public const OPTIMIZATION_OBJECTIVE_CUSTOM = 'CUSTOM';
  /**
   * Objective is not defined. Any KPI or bidding strategy can be used.
   */
  public const OPTIMIZATION_OBJECTIVE_NO_OBJECTIVE = 'NO_OBJECTIVE';
  /**
   * Reservation type value is not specified or is unknown in this version.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_UNSPECIFIED = 'RESERVATION_TYPE_UNSPECIFIED';
  /**
   * Not created through a guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_NOT_GUARANTEED = 'RESERVATION_TYPE_NOT_GUARANTEED';
  /**
   * Created through a programmatic guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED = 'RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED';
  /**
   * Created through a tag guaranteed inventory source.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_TAG_GUARANTEED = 'RESERVATION_TYPE_TAG_GUARANTEED';
  /**
   * Created through a Petra inventory source. Only applicable to YouTube and
   * Partners line items.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_PETRA_VIRAL = 'RESERVATION_TYPE_PETRA_VIRAL';
  /**
   * Created with an instant quote. Only applicable to YouTube and partners line
   * items.
   */
  public const RESERVATION_TYPE_RESERVATION_TYPE_INSTANT_RESERVE = 'RESERVATION_TYPE_INSTANT_RESERVE';
  protected $collection_key = 'partnerCosts';
  /**
   * Output only. The unique ID of the advertiser the insertion order belongs
   * to.
   *
   * @var string
   */
  public $advertiserId;
  protected $bidStrategyType = BiddingStrategy::class;
  protected $bidStrategyDataType = '';
  protected $budgetType = InsertionOrderBudget::class;
  protected $budgetDataType = '';
  /**
   * Required. Immutable. The unique ID of the campaign that the insertion order
   * belongs to.
   *
   * @var string
   */
  public $campaignId;
  /**
   * Required. The display name of the insertion order. Must be UTF-8 encoded
   * with a maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Controls whether or not the insertion order can spend its budget
   * and bid on inventory. * For CreateInsertionOrder method, only
   * `ENTITY_STATUS_DRAFT` is allowed. To activate an insertion order, use
   * UpdateInsertionOrder method and update the status to `ENTITY_STATUS_ACTIVE`
   * after creation. * An insertion order cannot be changed back to
   * `ENTITY_STATUS_DRAFT` status from any other status. * An insertion order
   * cannot be set to `ENTITY_STATUS_ACTIVE` if its parent campaign is not
   * active.
   *
   * @var string
   */
  public $entityStatus;
  protected $frequencyCapType = FrequencyCap::class;
  protected $frequencyCapDataType = '';
  /**
   * Output only. The unique ID of the insertion order. Assigned by the system.
   *
   * @var string
   */
  public $insertionOrderId;
  /**
   * Optional. The type of insertion order. If this field is unspecified in
   * creation, the value defaults to `RTB`.
   *
   * @var string
   */
  public $insertionOrderType;
  protected $integrationDetailsType = IntegrationDetails::class;
  protected $integrationDetailsDataType = '';
  protected $kpiType = Kpi::class;
  protected $kpiDataType = '';
  /**
   * Output only. The resource name of the insertion order.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Required. The optimization objective of the insertion order.
   *
   * @var string
   */
  public $optimizationObjective;
  protected $pacingType = Pacing::class;
  protected $pacingDataType = '';
  protected $partnerCostsType = PartnerCost::class;
  protected $partnerCostsDataType = 'array';
  /**
   * Output only. The reservation type of the insertion order.
   *
   * @var string
   */
  public $reservationType;
  /**
   * Output only. The timestamp when the insertion order was last updated.
   * Assigned by the system.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The unique ID of the advertiser the insertion order belongs
   * to.
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
   * Optional. The bidding strategy of the insertion order. By default,
   * fixed_bid is set.
   *
   * @param BiddingStrategy $bidStrategy
   */
  public function setBidStrategy(BiddingStrategy $bidStrategy)
  {
    $this->bidStrategy = $bidStrategy;
  }
  /**
   * @return BiddingStrategy
   */
  public function getBidStrategy()
  {
    return $this->bidStrategy;
  }
  /**
   * Required. The budget allocation settings of the insertion order.
   *
   * @param InsertionOrderBudget $budget
   */
  public function setBudget(InsertionOrderBudget $budget)
  {
    $this->budget = $budget;
  }
  /**
   * @return InsertionOrderBudget
   */
  public function getBudget()
  {
    return $this->budget;
  }
  /**
   * Required. Immutable. The unique ID of the campaign that the insertion order
   * belongs to.
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
   * Required. The display name of the insertion order. Must be UTF-8 encoded
   * with a maximum size of 240 bytes.
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
   * Required. Controls whether or not the insertion order can spend its budget
   * and bid on inventory. * For CreateInsertionOrder method, only
   * `ENTITY_STATUS_DRAFT` is allowed. To activate an insertion order, use
   * UpdateInsertionOrder method and update the status to `ENTITY_STATUS_ACTIVE`
   * after creation. * An insertion order cannot be changed back to
   * `ENTITY_STATUS_DRAFT` status from any other status. * An insertion order
   * cannot be set to `ENTITY_STATUS_ACTIVE` if its parent campaign is not
   * active.
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
   * Required. The frequency capping setting of the insertion order.
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
   * Output only. The unique ID of the insertion order. Assigned by the system.
   *
   * @param string $insertionOrderId
   */
  public function setInsertionOrderId($insertionOrderId)
  {
    $this->insertionOrderId = $insertionOrderId;
  }
  /**
   * @return string
   */
  public function getInsertionOrderId()
  {
    return $this->insertionOrderId;
  }
  /**
   * Optional. The type of insertion order. If this field is unspecified in
   * creation, the value defaults to `RTB`.
   *
   * Accepted values: INSERTION_ORDER_TYPE_UNSPECIFIED, RTB, OVER_THE_TOP
   *
   * @param self::INSERTION_ORDER_TYPE_* $insertionOrderType
   */
  public function setInsertionOrderType($insertionOrderType)
  {
    $this->insertionOrderType = $insertionOrderType;
  }
  /**
   * @return self::INSERTION_ORDER_TYPE_*
   */
  public function getInsertionOrderType()
  {
    return $this->insertionOrderType;
  }
  /**
   * Optional. Additional integration details of the insertion order.
   *
   * @param IntegrationDetails $integrationDetails
   */
  public function setIntegrationDetails(IntegrationDetails $integrationDetails)
  {
    $this->integrationDetails = $integrationDetails;
  }
  /**
   * @return IntegrationDetails
   */
  public function getIntegrationDetails()
  {
    return $this->integrationDetails;
  }
  /**
   * Required. The key performance indicator (KPI) of the insertion order. This
   * is represented as referred to as the "Goal" in the Display & Video 360
   * interface.
   *
   * @param Kpi $kpi
   */
  public function setKpi(Kpi $kpi)
  {
    $this->kpi = $kpi;
  }
  /**
   * @return Kpi
   */
  public function getKpi()
  {
    return $this->kpi;
  }
  /**
   * Output only. The resource name of the insertion order.
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
   * Optional. Required. The optimization objective of the insertion order.
   *
   * Accepted values: OPTIMIZATION_OBJECTIVE_UNSPECIFIED, CONVERSION, CLICK,
   * BRAND_AWARENESS, CUSTOM, NO_OBJECTIVE
   *
   * @param self::OPTIMIZATION_OBJECTIVE_* $optimizationObjective
   */
  public function setOptimizationObjective($optimizationObjective)
  {
    $this->optimizationObjective = $optimizationObjective;
  }
  /**
   * @return self::OPTIMIZATION_OBJECTIVE_*
   */
  public function getOptimizationObjective()
  {
    return $this->optimizationObjective;
  }
  /**
   * Required. The budget spending speed setting of the insertion order.
   * pacing_type `PACING_TYPE_ASAP` is not compatible with pacing_period
   * `PACING_PERIOD_FLIGHT`.
   *
   * @param Pacing $pacing
   */
  public function setPacing(Pacing $pacing)
  {
    $this->pacing = $pacing;
  }
  /**
   * @return Pacing
   */
  public function getPacing()
  {
    return $this->pacing;
  }
  /**
   * Optional. The partner costs associated with the insertion order. If absent
   * or empty in CreateInsertionOrder method, the newly created insertion order
   * will inherit partner costs from the partner settings.
   *
   * @param PartnerCost[] $partnerCosts
   */
  public function setPartnerCosts($partnerCosts)
  {
    $this->partnerCosts = $partnerCosts;
  }
  /**
   * @return PartnerCost[]
   */
  public function getPartnerCosts()
  {
    return $this->partnerCosts;
  }
  /**
   * Output only. The reservation type of the insertion order.
   *
   * Accepted values: RESERVATION_TYPE_UNSPECIFIED,
   * RESERVATION_TYPE_NOT_GUARANTEED, RESERVATION_TYPE_PROGRAMMATIC_GUARANTEED,
   * RESERVATION_TYPE_TAG_GUARANTEED, RESERVATION_TYPE_PETRA_VIRAL,
   * RESERVATION_TYPE_INSTANT_RESERVE
   *
   * @param self::RESERVATION_TYPE_* $reservationType
   */
  public function setReservationType($reservationType)
  {
    $this->reservationType = $reservationType;
  }
  /**
   * @return self::RESERVATION_TYPE_*
   */
  public function getReservationType()
  {
    return $this->reservationType;
  }
  /**
   * Output only. The timestamp when the insertion order was last updated.
   * Assigned by the system.
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
class_alias(InsertionOrder::class, 'Google_Service_DisplayVideo_InsertionOrder');
