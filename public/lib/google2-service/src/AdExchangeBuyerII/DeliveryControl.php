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

namespace Google\Service\AdExchangeBuyerII;

class DeliveryControl extends \Google\Collection
{
  /**
   * A placeholder for an undefined creative blocking level.
   */
  public const CREATIVE_BLOCKING_LEVEL_CREATIVE_BLOCKING_LEVEL_UNSPECIFIED = 'CREATIVE_BLOCKING_LEVEL_UNSPECIFIED';
  /**
   * Publisher blocking rules will be applied.
   */
  public const CREATIVE_BLOCKING_LEVEL_PUBLISHER_BLOCKING_RULES = 'PUBLISHER_BLOCKING_RULES';
  /**
   * The Ad Exchange policy blocking rules will be applied.
   */
  public const CREATIVE_BLOCKING_LEVEL_ADX_POLICY_BLOCKING_ONLY = 'ADX_POLICY_BLOCKING_ONLY';
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
  protected $collection_key = 'frequencyCaps';
  /**
   * Output only. Specified the creative blocking levels to be applied.
   *
   * @var string
   */
  public $creativeBlockingLevel;
  /**
   * Output only. Specifies how the impression delivery will be paced.
   *
   * @var string
   */
  public $deliveryRateType;
  protected $frequencyCapsType = FrequencyCap::class;
  protected $frequencyCapsDataType = 'array';

  /**
   * Output only. Specified the creative blocking levels to be applied.
   *
   * Accepted values: CREATIVE_BLOCKING_LEVEL_UNSPECIFIED,
   * PUBLISHER_BLOCKING_RULES, ADX_POLICY_BLOCKING_ONLY
   *
   * @param self::CREATIVE_BLOCKING_LEVEL_* $creativeBlockingLevel
   */
  public function setCreativeBlockingLevel($creativeBlockingLevel)
  {
    $this->creativeBlockingLevel = $creativeBlockingLevel;
  }
  /**
   * @return self::CREATIVE_BLOCKING_LEVEL_*
   */
  public function getCreativeBlockingLevel()
  {
    return $this->creativeBlockingLevel;
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
   * Output only. Specifies any frequency caps.
   *
   * @param FrequencyCap[] $frequencyCaps
   */
  public function setFrequencyCaps($frequencyCaps)
  {
    $this->frequencyCaps = $frequencyCaps;
  }
  /**
   * @return FrequencyCap[]
   */
  public function getFrequencyCaps()
  {
    return $this->frequencyCaps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliveryControl::class, 'Google_Service_AdExchangeBuyerII_DeliveryControl');
