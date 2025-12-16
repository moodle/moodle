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

class RouterStatus extends \Google\Collection
{
  protected $collection_key = 'natStatus';
  protected $bestRoutesType = Route::class;
  protected $bestRoutesDataType = 'array';
  protected $bestRoutesForRouterType = Route::class;
  protected $bestRoutesForRouterDataType = 'array';
  protected $bgpPeerStatusType = RouterStatusBgpPeerStatus::class;
  protected $bgpPeerStatusDataType = 'array';
  protected $natStatusType = RouterStatusNatStatus::class;
  protected $natStatusDataType = 'array';
  /**
   * URI of the network to which this router belongs.
   *
   * @var string
   */
  public $network;

  /**
   * A list of the best dynamic routes for this Cloud Router's Virtual Private
   * Cloud (VPC) network in the same region as this Cloud Router.
   *
   * Lists all of the best routes per prefix that are programmed into this
   * region's VPC data plane.
   *
   * When global dynamic routing mode is turned on in the VPC network, this list
   * can include cross-region dynamic routes from Cloud Routers in other
   * regions.
   *
   * @param Route[] $bestRoutes
   */
  public function setBestRoutes($bestRoutes)
  {
    $this->bestRoutes = $bestRoutes;
  }
  /**
   * @return Route[]
   */
  public function getBestRoutes()
  {
    return $this->bestRoutes;
  }
  /**
   * A list of the best BGP routes learned by this Cloud Router.
   *
   * It is possible that routes listed might not be programmed into the data
   * plane, if the Google Cloud control plane finds a more optimal route for a
   * prefix than a route learned by this Cloud Router.
   *
   * @param Route[] $bestRoutesForRouter
   */
  public function setBestRoutesForRouter($bestRoutesForRouter)
  {
    $this->bestRoutesForRouter = $bestRoutesForRouter;
  }
  /**
   * @return Route[]
   */
  public function getBestRoutesForRouter()
  {
    return $this->bestRoutesForRouter;
  }
  /**
   * @param RouterStatusBgpPeerStatus[] $bgpPeerStatus
   */
  public function setBgpPeerStatus($bgpPeerStatus)
  {
    $this->bgpPeerStatus = $bgpPeerStatus;
  }
  /**
   * @return RouterStatusBgpPeerStatus[]
   */
  public function getBgpPeerStatus()
  {
    return $this->bgpPeerStatus;
  }
  /**
   * @param RouterStatusNatStatus[] $natStatus
   */
  public function setNatStatus($natStatus)
  {
    $this->natStatus = $natStatus;
  }
  /**
   * @return RouterStatusNatStatus[]
   */
  public function getNatStatus()
  {
    return $this->natStatus;
  }
  /**
   * URI of the network to which this router belongs.
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
class_alias(RouterStatus::class, 'Google_Service_Compute_RouterStatus');
