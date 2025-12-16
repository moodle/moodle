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

namespace Google\Service\SecurityCommandCenter\Resource;

use Google\Service\SecurityCommandCenter\Simulation;

/**
 * The "simulations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $simulations = $securitycenterService->organizations_simulations;
 *  </code>
 */
class OrganizationsSimulations extends \Google\Service\Resource
{
  /**
   * Get the simulation by name or the latest simulation for the given
   * organization. (simulations.get)
   *
   * @param string $name Required. The organization name or simulation name of
   * this simulation Valid format:
   * `organizations/{organization}/simulations/latest`
   * `organizations/{organization}/simulations/{simulation}`
   * @param array $optParams Optional parameters.
   * @return Simulation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Simulation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSimulations::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsSimulations');
