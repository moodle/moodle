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

namespace Google\Service\NetworkSecurity;

class FirewallEndpointAssociationReference extends \Google\Model
{
  /**
   * Output only. The resource name of the FirewallEndpointAssociation. Format:
   * projects/{project}/locations/{location}/firewallEndpointAssociations/{id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The VPC network associated. Format:
   * projects/{project}/global/networks/{name}.
   *
   * @var string
   */
  public $network;

  /**
   * Output only. The resource name of the FirewallEndpointAssociation. Format:
   * projects/{project}/locations/{location}/firewallEndpointAssociations/{id}
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
   * Output only. The VPC network associated. Format:
   * projects/{project}/global/networks/{name}.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallEndpointAssociationReference::class, 'Google_Service_NetworkSecurity_FirewallEndpointAssociationReference');
