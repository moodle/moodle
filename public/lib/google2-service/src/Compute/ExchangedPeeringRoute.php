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

class ExchangedPeeringRoute extends \Google\Model
{
  /**
   * For routes exported from local network.
   */
  public const TYPE_DYNAMIC_PEERING_ROUTE = 'DYNAMIC_PEERING_ROUTE';
  /**
   * The peering route.
   */
  public const TYPE_STATIC_PEERING_ROUTE = 'STATIC_PEERING_ROUTE';
  /**
   * The peering route corresponding to subnetwork range.
   */
  public const TYPE_SUBNET_PEERING_ROUTE = 'SUBNET_PEERING_ROUTE';
  /**
   * The destination range of the route.
   *
   * @var string
   */
  public $destRange;
  /**
   * True if the peering route has been imported from a peer. The actual import
   * happens if the field networkPeering.importCustomRoutes is true for this
   * network, and networkPeering.exportCustomRoutes is true for the peer
   * network, and the import does not result in a route conflict.
   *
   * @var bool
   */
  public $imported;
  /**
   * The region of peering route next hop, only applies to dynamic routes.
   *
   * @var string
   */
  public $nextHopRegion;
  /**
   * The priority of the peering route.
   *
   * @var string
   */
  public $priority;
  /**
   * The type of the peering route.
   *
   * @var string
   */
  public $type;

  /**
   * The destination range of the route.
   *
   * @param string $destRange
   */
  public function setDestRange($destRange)
  {
    $this->destRange = $destRange;
  }
  /**
   * @return string
   */
  public function getDestRange()
  {
    return $this->destRange;
  }
  /**
   * True if the peering route has been imported from a peer. The actual import
   * happens if the field networkPeering.importCustomRoutes is true for this
   * network, and networkPeering.exportCustomRoutes is true for the peer
   * network, and the import does not result in a route conflict.
   *
   * @param bool $imported
   */
  public function setImported($imported)
  {
    $this->imported = $imported;
  }
  /**
   * @return bool
   */
  public function getImported()
  {
    return $this->imported;
  }
  /**
   * The region of peering route next hop, only applies to dynamic routes.
   *
   * @param string $nextHopRegion
   */
  public function setNextHopRegion($nextHopRegion)
  {
    $this->nextHopRegion = $nextHopRegion;
  }
  /**
   * @return string
   */
  public function getNextHopRegion()
  {
    return $this->nextHopRegion;
  }
  /**
   * The priority of the peering route.
   *
   * @param string $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return string
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * The type of the peering route.
   *
   * Accepted values: DYNAMIC_PEERING_ROUTE, STATIC_PEERING_ROUTE,
   * SUBNET_PEERING_ROUTE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExchangedPeeringRoute::class, 'Google_Service_Compute_ExchangedPeeringRoute');
