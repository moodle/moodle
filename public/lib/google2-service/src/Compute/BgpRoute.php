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

class BgpRoute extends \Google\Collection
{
  public const ORIGIN_BGP_ORIGIN_EGP = 'BGP_ORIGIN_EGP';
  public const ORIGIN_BGP_ORIGIN_IGP = 'BGP_ORIGIN_IGP';
  public const ORIGIN_BGP_ORIGIN_INCOMPLETE = 'BGP_ORIGIN_INCOMPLETE';
  protected $collection_key = 'communities';
  protected $asPathsType = BgpRouteAsPath::class;
  protected $asPathsDataType = 'array';
  /**
   * Output only. [Output only] BGP communities in human-readable A:B format.
   *
   * @var string[]
   */
  public $communities;
  protected $destinationType = BgpRouteNetworkLayerReachabilityInformation::class;
  protected $destinationDataType = '';
  /**
   * Output only. [Output only] BGP multi-exit discriminator
   *
   * @var string
   */
  public $med;
  /**
   * Output only. [Output only] BGP origin (EGP, IGP or INCOMPLETE)
   *
   * @var string
   */
  public $origin;

  /**
   * Output only. [Output only] AS-PATH for the route
   *
   * @param BgpRouteAsPath[] $asPaths
   */
  public function setAsPaths($asPaths)
  {
    $this->asPaths = $asPaths;
  }
  /**
   * @return BgpRouteAsPath[]
   */
  public function getAsPaths()
  {
    return $this->asPaths;
  }
  /**
   * Output only. [Output only] BGP communities in human-readable A:B format.
   *
   * @param string[] $communities
   */
  public function setCommunities($communities)
  {
    $this->communities = $communities;
  }
  /**
   * @return string[]
   */
  public function getCommunities()
  {
    return $this->communities;
  }
  /**
   * Output only. [Output only] Destination IP range for the route, in human-
   * readable CIDR format
   *
   * @param BgpRouteNetworkLayerReachabilityInformation $destination
   */
  public function setDestination(BgpRouteNetworkLayerReachabilityInformation $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return BgpRouteNetworkLayerReachabilityInformation
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Output only. [Output only] BGP multi-exit discriminator
   *
   * @param string $med
   */
  public function setMed($med)
  {
    $this->med = $med;
  }
  /**
   * @return string
   */
  public function getMed()
  {
    return $this->med;
  }
  /**
   * Output only. [Output only] BGP origin (EGP, IGP or INCOMPLETE)
   *
   * Accepted values: BGP_ORIGIN_EGP, BGP_ORIGIN_IGP, BGP_ORIGIN_INCOMPLETE
   *
   * @param self::ORIGIN_* $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return self::ORIGIN_*
   */
  public function getOrigin()
  {
    return $this->origin;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BgpRoute::class, 'Google_Service_Compute_BgpRoute');
