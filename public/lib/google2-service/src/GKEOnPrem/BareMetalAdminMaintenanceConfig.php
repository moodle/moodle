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

namespace Google\Service\GKEOnPrem;

class BareMetalAdminMaintenanceConfig extends \Google\Collection
{
  protected $collection_key = 'maintenanceAddressCidrBlocks';
  /**
   * Required. All IPv4 address from these ranges will be placed into
   * maintenance mode. Nodes in maintenance mode will be cordoned and drained.
   * When both of these are true, the "baremetal.cluster.gke.io/maintenance"
   * annotation will be set on the node resource.
   *
   * @var string[]
   */
  public $maintenanceAddressCidrBlocks;

  /**
   * Required. All IPv4 address from these ranges will be placed into
   * maintenance mode. Nodes in maintenance mode will be cordoned and drained.
   * When both of these are true, the "baremetal.cluster.gke.io/maintenance"
   * annotation will be set on the node resource.
   *
   * @param string[] $maintenanceAddressCidrBlocks
   */
  public function setMaintenanceAddressCidrBlocks($maintenanceAddressCidrBlocks)
  {
    $this->maintenanceAddressCidrBlocks = $maintenanceAddressCidrBlocks;
  }
  /**
   * @return string[]
   */
  public function getMaintenanceAddressCidrBlocks()
  {
    return $this->maintenanceAddressCidrBlocks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminMaintenanceConfig::class, 'Google_Service_GKEOnPrem_BareMetalAdminMaintenanceConfig');
