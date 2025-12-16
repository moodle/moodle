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

class InstanceGroupManagerInstanceFlexibilityPolicyInstanceSelection extends \Google\Collection
{
  protected $collection_key = 'machineTypes';
  /**
   * Full machine-type names, e.g. "n1-standard-16".
   *
   * @var string[]
   */
  public $machineTypes;
  /**
   * Preference of this instance selection. Lower number means higher
   * preference. MIG will first try to create a VM based on the machine-type
   * with lowest rank and fallback to next rank based on availability. Machine
   * types and instance selections with the same rank have the same preference.
   *
   * @var int
   */
  public $rank;

  /**
   * Full machine-type names, e.g. "n1-standard-16".
   *
   * @param string[] $machineTypes
   */
  public function setMachineTypes($machineTypes)
  {
    $this->machineTypes = $machineTypes;
  }
  /**
   * @return string[]
   */
  public function getMachineTypes()
  {
    return $this->machineTypes;
  }
  /**
   * Preference of this instance selection. Lower number means higher
   * preference. MIG will first try to create a VM based on the machine-type
   * with lowest rank and fallback to next rank based on availability. Machine
   * types and instance selections with the same rank have the same preference.
   *
   * @param int $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return int
   */
  public function getRank()
  {
    return $this->rank;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerInstanceFlexibilityPolicyInstanceSelection::class, 'Google_Service_Compute_InstanceGroupManagerInstanceFlexibilityPolicyInstanceSelection');
