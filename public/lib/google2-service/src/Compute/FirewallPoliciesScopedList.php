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

class FirewallPoliciesScopedList extends \Google\Collection
{
  protected $collection_key = 'firewallPolicies';
  protected $firewallPoliciesType = FirewallPolicy::class;
  protected $firewallPoliciesDataType = 'array';
  protected $warningType = FirewallPoliciesScopedListWarning::class;
  protected $warningDataType = '';

  /**
   * A list of firewall policies contained in this scope.
   *
   * @param FirewallPolicy[] $firewallPolicies
   */
  public function setFirewallPolicies($firewallPolicies)
  {
    $this->firewallPolicies = $firewallPolicies;
  }
  /**
   * @return FirewallPolicy[]
   */
  public function getFirewallPolicies()
  {
    return $this->firewallPolicies;
  }
  /**
   * Informational warning which replaces the list of firewall policies when the
   * list is empty.
   *
   * @param FirewallPoliciesScopedListWarning $warning
   */
  public function setWarning(FirewallPoliciesScopedListWarning $warning)
  {
    $this->warning = $warning;
  }
  /**
   * @return FirewallPoliciesScopedListWarning
   */
  public function getWarning()
  {
    return $this->warning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPoliciesScopedList::class, 'Google_Service_Compute_FirewallPoliciesScopedList');
