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

namespace Google\Service\AuthorizedBuyersMarketplace;

class DeliveryControl extends \Google\Collection
{
  /**
   * A placeholder for an unspecified companion delivery type.
   */
  public const COMPANION_DELIVERY_TYPE_COMPANION_DELIVERY_TYPE_UNSPECIFIED = 'COMPANION_DELIVERY_TYPE_UNSPECIFIED';
  /**
   * Companions are not required to serve a creative set. The creative set can
   * serve an inventory that has zero or more matching companions.
   */
  public const COMPANION_DELIVERY_TYPE_DELIVERY_OPTIONAL = 'DELIVERY_OPTIONAL';
  /**
   * At least one companion must be served in order for the creative set to be
   * used.
   */
  public const COMPANION_DELIVERY_TYPE_DELIVERY_AT_LEAST_ONE = 'DELIVERY_AT_LEAST_ONE';
  /**
   * All companions in the set must be served in order for the creative set to
   * be used. This can still serve to inventory that has more companions than
   * can be filled.
   */
  public const COMPANION_DELIVERY_TYPE_DELIVERY_ALL = 'DELIVERY_ALL';
  /**
   * Creatives are displayed roughly the same number of times over the duration
   * of the deal.
   */
  public const CREATIVE_ROTATION_TYPE_CREATIVE_ROTATION_TYPE_UNSPECIFIED = 'CREATIVE_ROTATION_TYPE_UNSPECIFIED';
  /**
   * Creatives are displayed roughly the same number of times over the duration
   * of the deal.
   */
  public const CREATIVE_ROTATION_TYPE_ROTATION_EVEN = 'ROTATION_EVEN';
  /**
   * Creatives are served roughly proportionally to their performance.
   */
  public const CREATIVE_ROTATION_TYPE_ROTATION_OPTIMIZED = 'ROTATION_OPTIMIZED';
  /**
   * Creatives are served roughly proportionally to their weights.
   */
  public const CREATIVE_ROTATION_TYPE_ROTATION_MANUAL = 'ROTATION_MANUAL';
  /**
   * Creatives are served exactly in sequential order, also known as
   * Storyboarding.
   */
  public const CREATIVE_ROTATION_TYPE_ROTATION_SEQUENTIAL = 'ROTATION_SEQUENTIAL';
  /**
   * A placeholder for an undefined delivery rate type.
   */
  public const DELIVERY_RATE_TYPE_DELIVERY_RATE_TYPE_UNSPECIFIED = 'DELIVERY_RATE_TYPE_UNSPECIFIED';
  /**
   * Impressions are served uniformly over the life of the deal.
   */
  public const DELIVERY_RATE_TYPE_EVENLY = 'EVENLY';
  /**
   * Impressions are served front-loaded.
   */
  public const DELIVERY_RATE_TYPE_FRONT_LOADED = 'FRONT_LOADED';
  /**
   * Impressions are served as fast as possible.
   */
  public const DELIVERY_RATE_TYPE_AS_FAST_AS_POSSIBLE = 'AS_FAST_AS_POSSIBLE';
  /**
   * A placeholder for an unspecified roadblocking type.
   */
  public const ROADBLOCKING_TYPE_ROADBLOCKING_TYPE_UNSPECIFIED = 'ROADBLOCKING_TYPE_UNSPECIFIED';
  /**
   * Only one creative from a deal can serve per ad request.
   * https://support.google.com/admanager/answer/177277.
   */
  public const ROADBLOCKING_TYPE_ONLY_ONE = 'ONLY_ONE';
  /**
   * Any number of creatives from a deal can serve together per ad request.
   */
  public const ROADBLOCKING_TYPE_ONE_OR_MORE = 'ONE_OR_MORE';
  /**
   * As many creatives from a deal as can fit on a page will serve. This could
   * mean anywhere from one to all of a deal's creatives given the size
   * constraints of ad slots on a page.
   */
  public const ROADBLOCKING_TYPE_AS_MANY_AS_POSSIBLE = 'AS_MANY_AS_POSSIBLE';
  /**
   * All or none of the creatives from a deal will serve.
   */
  public const ROADBLOCKING_TYPE_ALL_ROADBLOCK = 'ALL_ROADBLOCK';
  /**
   * A main/companion creative set roadblocking type.
   */
  public const ROADBLOCKING_TYPE_CREATIVE_SET = 'CREATIVE_SET';
  protected $collection_key = 'frequencyCap';
  /**
   * Output only. Specifies roadblocking in a main companion lineitem.
   *
   * @var string
   */
  public $companionDeliveryType;
  /**
   * Output only. Specifies strategy to use for selecting a creative when
   * multiple creatives of the same size are available.
   *
   * @var string
   */
  public $creativeRotationType;
  /**
   * Output only. Specifies how the impression delivery will be paced.
   *
   * @var string
   */
  public $deliveryRateType;
  protected $frequencyCapType = FrequencyCap::class;
  protected $frequencyCapDataType = 'array';
  /**
   * Output only. Specifies the roadblocking type in display creatives.
   *
   * @var string
   */
  public $roadblockingType;

  /**
   * Output only. Specifies roadblocking in a main companion lineitem.
   *
   * Accepted values: COMPANION_DELIVERY_TYPE_UNSPECIFIED, DELIVERY_OPTIONAL,
   * DELIVERY_AT_LEAST_ONE, DELIVERY_ALL
   *
   * @param self::COMPANION_DELIVERY_TYPE_* $companionDeliveryType
   */
  public function setCompanionDeliveryType($companionDeliveryType)
  {
    $this->companionDeliveryType = $companionDeliveryType;
  }
  /**
   * @return self::COMPANION_DELIVERY_TYPE_*
   */
  public function getCompanionDeliveryType()
  {
    return $this->companionDeliveryType;
  }
  /**
   * Output only. Specifies strategy to use for selecting a creative when
   * multiple creatives of the same size are available.
   *
   * Accepted values: CREATIVE_ROTATION_TYPE_UNSPECIFIED, ROTATION_EVEN,
   * ROTATION_OPTIMIZED, ROTATION_MANUAL, ROTATION_SEQUENTIAL
   *
   * @param self::CREATIVE_ROTATION_TYPE_* $creativeRotationType
   */
  public function setCreativeRotationType($creativeRotationType)
  {
    $this->creativeRotationType = $creativeRotationType;
  }
  /**
   * @return self::CREATIVE_ROTATION_TYPE_*
   */
  public function getCreativeRotationType()
  {
    return $this->creativeRotationType;
  }
  /**
   * Output only. Specifies how the impression delivery will be paced.
   *
   * Accepted values: DELIVERY_RATE_TYPE_UNSPECIFIED, EVENLY, FRONT_LOADED,
   * AS_FAST_AS_POSSIBLE
   *
   * @param self::DELIVERY_RATE_TYPE_* $deliveryRateType
   */
  public function setDeliveryRateType($deliveryRateType)
  {
    $this->deliveryRateType = $deliveryRateType;
  }
  /**
   * @return self::DELIVERY_RATE_TYPE_*
   */
  public function getDeliveryRateType()
  {
    return $this->deliveryRateType;
  }
  /**
   * Output only. Specifies any frequency caps. Cannot be filtered within
   * ListDealsRequest.
   *
   * @param FrequencyCap[] $frequencyCap
   */
  public function setFrequencyCap($frequencyCap)
  {
    $this->frequencyCap = $frequencyCap;
  }
  /**
   * @return FrequencyCap[]
   */
  public function getFrequencyCap()
  {
    return $this->frequencyCap;
  }
  /**
   * Output only. Specifies the roadblocking type in display creatives.
   *
   * Accepted values: ROADBLOCKING_TYPE_UNSPECIFIED, ONLY_ONE, ONE_OR_MORE,
   * AS_MANY_AS_POSSIBLE, ALL_ROADBLOCK, CREATIVE_SET
   *
   * @param self::ROADBLOCKING_TYPE_* $roadblockingType
   */
  public function setRoadblockingType($roadblockingType)
  {
    $this->roadblockingType = $roadblockingType;
  }
  /**
   * @return self::ROADBLOCKING_TYPE_*
   */
  public function getRoadblockingType()
  {
    return $this->roadblockingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryControl::class, 'Google_Service_AuthorizedBuyersMarketplace_DeliveryControl');
