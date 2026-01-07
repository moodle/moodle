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

namespace Google\Service\Compute;

class InterconnectApplicationAwareInterconnect extends \Google\Collection
{
  protected $collection_key = 'shapeAveragePercentages';
  protected $bandwidthPercentagePolicyType = InterconnectApplicationAwareInterconnectBandwidthPercentagePolicy::class;
  protected $bandwidthPercentagePolicyDataType = '';
  /**
   * Description for the application awareness profile on this Cloud
   * Interconnect.
   *
   * @var string
   */
  public $profileDescription;
  protected $shapeAveragePercentagesType = InterconnectApplicationAwareInterconnectBandwidthPercentage::class;
  protected $shapeAveragePercentagesDataType = 'array';
  protected $strictPriorityPolicyType = InterconnectApplicationAwareInterconnectStrictPriorityPolicy::class;
  protected $strictPriorityPolicyDataType = '';

  /**
   * @param InterconnectApplicationAwareInterconnectBandwidthPercentagePolicy $bandwidthPercentagePolicy
   */
  public function setBandwidthPercentagePolicy(InterconnectApplicationAwareInterconnectBandwidthPercentagePolicy $bandwidthPercentagePolicy)
  {
    $this->bandwidthPercentagePolicy = $bandwidthPercentagePolicy;
  }
  /**
   * @return InterconnectApplicationAwareInterconnectBandwidthPercentagePolicy
   */
  public function getBandwidthPercentagePolicy()
  {
    return $this->bandwidthPercentagePolicy;
  }
  /**
   * Description for the application awareness profile on this Cloud
   * Interconnect.
   *
   * @param string $profileDescription
   */
  public function setProfileDescription($profileDescription)
  {
    $this->profileDescription = $profileDescription;
  }
  /**
   * @return string
   */
  public function getProfileDescription()
  {
    return $this->profileDescription;
  }
  /**
   * Optional field to specify a list of shape average percentages to be applied
   * in conjunction with StrictPriorityPolicy or BandwidthPercentagePolicy.
   *
   * @param InterconnectApplicationAwareInterconnectBandwidthPercentage[] $shapeAveragePercentages
   */
  public function setShapeAveragePercentages($shapeAveragePercentages)
  {
    $this->shapeAveragePercentages = $shapeAveragePercentages;
  }
  /**
   * @return InterconnectApplicationAwareInterconnectBandwidthPercentage[]
   */
  public function getShapeAveragePercentages()
  {
    return $this->shapeAveragePercentages;
  }
  /**
   * @param InterconnectApplicationAwareInterconnectStrictPriorityPolicy $strictPriorityPolicy
   */
  public function setStrictPriorityPolicy(InterconnectApplicationAwareInterconnectStrictPriorityPolicy $strictPriorityPolicy)
  {
    $this->strictPriorityPolicy = $strictPriorityPolicy;
  }
  /**
   * @return InterconnectApplicationAwareInterconnectStrictPriorityPolicy
   */
  public function getStrictPriorityPolicy()
  {
    return $this->strictPriorityPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectApplicationAwareInterconnect::class, 'Google_Service_Compute_InterconnectApplicationAwareInterconnect');
