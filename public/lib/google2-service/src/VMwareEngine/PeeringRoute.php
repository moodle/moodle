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

namespace Google\Service\VMwareEngine;

class PeeringRoute extends \Google\Model
{
  /**
   * Unspecified exchanged routes direction. This is default.
   */
  public const DIRECTION_DIRECTION_UNSPECIFIED = 'DIRECTION_UNSPECIFIED';
  /**
   * Routes imported from the peer network.
   */
  public const DIRECTION_INCOMING = 'INCOMING';
  /**
   * Routes exported to the peer network.
   */
  public const DIRECTION_OUTGOING = 'OUTGOING';
  /**
   * Unspecified peering route type. This is the default value.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Dynamic routes in the peer network.
   */
  public const TYPE_DYNAMIC_PEERING_ROUTE = 'DYNAMIC_PEERING_ROUTE';
  /**
   * Static routes in the peer network.
   */
  public const TYPE_STATIC_PEERING_ROUTE = 'STATIC_PEERING_ROUTE';
  /**
   * Created, updated, and removed automatically by Google Cloud when subnets
   * are created, modified, or deleted in the peer network.
   */
  public const TYPE_SUBNET_PEERING_ROUTE = 'SUBNET_PEERING_ROUTE';
  /**
   * Output only. Destination range of the peering route in CIDR notation.
   *
   * @var string
   */
  public $destRange;
  /**
   * Output only. Direction of the routes exchanged with the peer network, from
   * the VMware Engine network perspective: * Routes of direction `INCOMING` are
   * imported from the peer network. * Routes of direction `OUTGOING` are
   * exported from the intranet VPC network of the VMware Engine network.
   *
   * @var string
   */
  public $direction;
  /**
   * Output only. True if the peering route has been imported from a peered VPC
   * network; false otherwise. The import happens if the field
   * `NetworkPeering.importCustomRoutes` is true for this network,
   * `NetworkPeering.exportCustomRoutes` is true for the peer VPC network, and
   * the import does not result in a route conflict.
   *
   * @var bool
   */
  public $imported;
  /**
   * Output only. Region containing the next hop of the peering route. This
   * field only applies to dynamic routes in the peer VPC network.
   *
   * @var string
   */
  public $nextHopRegion;
  /**
   * Output only. The priority of the peering route.
   *
   * @var string
   */
  public $priority;
  /**
   * Output only. Type of the route in the peer VPC network.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Destination range of the peering route in CIDR notation.
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
   * Output only. Direction of the routes exchanged with the peer network, from
   * the VMware Engine network perspective: * Routes of direction `INCOMING` are
   * imported from the peer network. * Routes of direction `OUTGOING` are
   * exported from the intranet VPC network of the VMware Engine network.
   *
   * Accepted values: DIRECTION_UNSPECIFIED, INCOMING, OUTGOING
   *
   * @param self::DIRECTION_* $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return self::DIRECTION_*
   */
  public function getDirection()
  {
    return $this->direction;
  }
  /**
   * Output only. True if the peering route has been imported from a peered VPC
   * network; false otherwise. The import happens if the field
   * `NetworkPeering.importCustomRoutes` is true for this network,
   * `NetworkPeering.exportCustomRoutes` is true for the peer VPC network, and
   * the import does not result in a route conflict.
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
   * Output only. Region containing the next hop of the peering route. This
   * field only applies to dynamic routes in the peer VPC network.
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
   * Output only. The priority of the peering route.
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
   * Output only. Type of the route in the peer VPC network.
   *
   * Accepted values: TYPE_UNSPECIFIED, DYNAMIC_PEERING_ROUTE,
   * STATIC_PEERING_ROUTE, SUBNET_PEERING_ROUTE
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
class_alias(PeeringRoute::class, 'Google_Service_VMwareEngine_PeeringRoute');
