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

class SubscriptionOfferPhase extends \Google\Collection
{
  protected $collection_key = 'regionalConfigs';
  /**
   * Required. The duration of a single recurrence of this phase. Specified in
   * ISO 8601 format.
   *
   * @var string
   */
  public $duration;
  protected $otherRegionsConfigType = OtherRegionsSubscriptionOfferPhaseConfig::class;
  protected $otherRegionsConfigDataType = '';
  /**
   * Required. The number of times this phase repeats. If this offer phase is
   * not free, each recurrence charges the user the price of this offer phase.
   *
   * @var int
   */
  public $recurrenceCount;
  protected $regionalConfigsType = RegionalSubscriptionOfferPhaseConfig::class;
  protected $regionalConfigsDataType = 'array';

  /**
   * Required. The duration of a single recurrence of this phase. Specified in
   * ISO 8601 format.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Pricing information for any new locations Play may launch in.
   *
   * @param OtherRegionsSubscriptionOfferPhaseConfig $otherRegionsConfig
   */
  public function setOtherRegionsConfig(OtherRegionsSubscriptionOfferPhaseConfig $otherRegionsConfig)
  {
    $this->otherRegionsConfig = $otherRegionsConfig;
  }
  /**
   * @return OtherRegionsSubscriptionOfferPhaseConfig
   */
  public function getOtherRegionsConfig()
  {
    return $this->otherRegionsConfig;
  }
  /**
   * Required. The number of times this phase repeats. If this offer phase is
   * not free, each recurrence charges the user the price of this offer phase.
   *
   * @param int $recurrenceCount
   */
  public function setRecurrenceCount($recurrenceCount)
  {
    $this->recurrenceCount = $recurrenceCount;
  }
  /**
   * @return int
   */
  public function getRecurrenceCount()
  {
    return $this->recurrenceCount;
  }
  /**
   * Required. The region-specific configuration of this offer phase. This list
   * must contain exactly one entry for each region for which the subscription
   * offer has a regional config.
   *
   * @param RegionalSubscriptionOfferPhaseConfig[] $regionalConfigs
   */
  public function setRegionalConfigs($regionalConfigs)
  {
    $this->regionalConfigs = $regionalConfigs;
  }
  /**
   * @return RegionalSubscriptionOfferPhaseConfig[]
   */
  public function getRegionalConfigs()
  {
    return $this->regionalConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionOfferPhase::class, 'Google_Service_AndroidPublisher_SubscriptionOfferPhase');
