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

namespace Google\Service\AndroidPublisher;

class SubscriptionOffer extends \Google\Collection
{
  /**
   * Default value, should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The subscription offer is not and has never been available to users.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The subscription offer is available to new and existing users.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The subscription offer is not available to new users. Existing users retain
   * access.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $collection_key = 'regionalConfigs';
  /**
   * Required. Immutable. The ID of the base plan to which this offer is an
   * extension.
   *
   * @var string
   */
  public $basePlanId;
  /**
   * Required. Immutable. Unique ID of this subscription offer. Must be unique
   * within the base plan.
   *
   * @var string
   */
  public $offerId;
  protected $offerTagsType = OfferTag::class;
  protected $offerTagsDataType = 'array';
  protected $otherRegionsConfigType = OtherRegionsSubscriptionOfferConfig::class;
  protected $otherRegionsConfigDataType = '';
  /**
   * Required. Immutable. The package name of the app the parent subscription
   * belongs to.
   *
   * @var string
   */
  public $packageName;
  protected $phasesType = SubscriptionOfferPhase::class;
  protected $phasesDataType = 'array';
  /**
   * Required. Immutable. The ID of the parent subscription this offer belongs
   * to.
   *
   * @var string
   */
  public $productId;
  protected $regionalConfigsType = RegionalSubscriptionOfferConfig::class;
  protected $regionalConfigsDataType = 'array';
  /**
   * Output only. The current state of this offer. Can be changed using Activate
   * and Deactivate actions. NB: the base plan state supersedes this state, so
   * an active offer may not be available if the base plan is not active.
   *
   * @var string
   */
  public $state;
  protected $targetingType = SubscriptionOfferTargeting::class;
  protected $targetingDataType = '';

  /**
   * Required. Immutable. The ID of the base plan to which this offer is an
   * extension.
   *
   * @param string $basePlanId
   */
  public function setBasePlanId($basePlanId)
  {
    $this->basePlanId = $basePlanId;
  }
  /**
   * @return string
   */
  public function getBasePlanId()
  {
    return $this->basePlanId;
  }
  /**
   * Required. Immutable. Unique ID of this subscription offer. Must be unique
   * within the base plan.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * List of up to 20 custom tags specified for this offer, and returned to the
   * app through the billing library.
   *
   * @param OfferTag[] $offerTags
   */
  public function setOfferTags($offerTags)
  {
    $this->offerTags = $offerTags;
  }
  /**
   * @return OfferTag[]
   */
  public function getOfferTags()
  {
    return $this->offerTags;
  }
  /**
   * The configuration for any new locations Play may launch in the future.
   *
   * @param OtherRegionsSubscriptionOfferConfig $otherRegionsConfig
   */
  public function setOtherRegionsConfig(OtherRegionsSubscriptionOfferConfig $otherRegionsConfig)
  {
    $this->otherRegionsConfig = $otherRegionsConfig;
  }
  /**
   * @return OtherRegionsSubscriptionOfferConfig
   */
  public function getOtherRegionsConfig()
  {
    return $this->otherRegionsConfig;
  }
  /**
   * Required. Immutable. The package name of the app the parent subscription
   * belongs to.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * Required. The phases of this subscription offer. Must contain at least one
   * and at most two entries. Users will always receive all these phases in the
   * specified order.
   *
   * @param SubscriptionOfferPhase[] $phases
   */
  public function setPhases($phases)
  {
    $this->phases = $phases;
  }
  /**
   * @return SubscriptionOfferPhase[]
   */
  public function getPhases()
  {
    return $this->phases;
  }
  /**
   * Required. Immutable. The ID of the parent subscription this offer belongs
   * to.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Required. The region-specific configuration of this offer. Must contain at
   * least one entry.
   *
   * @param RegionalSubscriptionOfferConfig[] $regionalConfigs
   */
  public function setRegionalConfigs($regionalConfigs)
  {
    $this->regionalConfigs = $regionalConfigs;
  }
  /**
   * @return RegionalSubscriptionOfferConfig[]
   */
  public function getRegionalConfigs()
  {
    return $this->regionalConfigs;
  }
  /**
   * Output only. The current state of this offer. Can be changed using Activate
   * and Deactivate actions. NB: the base plan state supersedes this state, so
   * an active offer may not be available if the base plan is not active.
   *
   * Accepted values: STATE_UNSPECIFIED, DRAFT, ACTIVE, INACTIVE
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
   * The requirements that users need to fulfil to be eligible for this offer.
   * Represents the requirements that Play will evaluate to decide whether an
   * offer should be returned. Developers may further filter these offers
   * themselves.
   *
   * @param SubscriptionOfferTargeting $targeting
   */
  public function setTargeting(SubscriptionOfferTargeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return SubscriptionOfferTargeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionOffer::class, 'Google_Service_AndroidPublisher_SubscriptionOffer');
