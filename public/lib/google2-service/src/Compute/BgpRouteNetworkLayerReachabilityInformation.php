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

class BgpRouteNetworkLayerReachabilityInformation extends \Google\Model
{
  /**
   * If the BGP session supports multiple paths (RFC 7911), the path identifier
   * for this route.
   *
   * @var string
   */
  public $pathId;
  /**
   * Human readable CIDR notation for a prefix. E.g. 10.42.0.0/16.
   *
   * @var string
   */
  public $prefix;

  /**
   * If the BGP session supports multiple paths (RFC 7911), the path identifier
   * for this route.
   *
   * @param string $pathId
   */
  public function setPathId($pathId)
  {
    $this->pathId = $pathId;
  }
  /**
   * @return string
   */
  public function getPathId()
  {
    return $this->pathId;
  }
  /**
   * Human readable CIDR notation for a prefix. E.g. 10.42.0.0/16.
   *
   * @param string $prefix
   */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }
  /**
   * @return string
   */
  public function getPrefix()
  {
    return $this->prefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BgpRouteNetworkLayerReachabilityInformation::class, 'Google_Service_Compute_BgpRouteNetworkLayerReachabilityInformation');
