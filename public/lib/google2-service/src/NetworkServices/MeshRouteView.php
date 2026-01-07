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

namespace Google\Service\NetworkServices;

class MeshRouteView extends \Google\Model
{
  /**
   * Output only. Identifier. Full path name of the MeshRouteView resource.
   * Format: projects/{project_number}/locations/{location}/meshes/{mesh}/routeV
   * iews/{route_view}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The resource id for the route.
   *
   * @var string
   */
  public $routeId;
  /**
   * Output only. Location where the route exists.
   *
   * @var string
   */
  public $routeLocation;
  /**
   * Output only. Project number where the route exists.
   *
   * @var string
   */
  public $routeProjectNumber;
  /**
   * Output only. Type of the route: HttpRoute,GrpcRoute,TcpRoute, or TlsRoute
   *
   * @var string
   */
  public $routeType;

  /**
   * Output only. Identifier. Full path name of the MeshRouteView resource.
   * Format: projects/{project_number}/locations/{location}/meshes/{mesh}/routeV
   * iews/{route_view}
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
   * Output only. The resource id for the route.
   *
   * @param string $routeId
   */
  public function setRouteId($routeId)
  {
    $this->routeId = $routeId;
  }
  /**
   * @return string
   */
  public function getRouteId()
  {
    return $this->routeId;
  }
  /**
   * Output only. Location where the route exists.
   *
   * @param string $routeLocation
   */
  public function setRouteLocation($routeLocation)
  {
    $this->routeLocation = $routeLocation;
  }
  /**
   * @return string
   */
  public function getRouteLocation()
  {
    return $this->routeLocation;
  }
  /**
   * Output only. Project number where the route exists.
   *
   * @param string $routeProjectNumber
   */
  public function setRouteProjectNumber($routeProjectNumber)
  {
    $this->routeProjectNumber = $routeProjectNumber;
  }
  /**
   * @return string
   */
  public function getRouteProjectNumber()
  {
    return $this->routeProjectNumber;
  }
  /**
   * Output only. Type of the route: HttpRoute,GrpcRoute,TcpRoute, or TlsRoute
   *
   * @param string $routeType
   */
  public function setRouteType($routeType)
  {
    $this->routeType = $routeType;
  }
  /**
   * @return string
   */
  public function getRouteType()
  {
    return $this->routeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeshRouteView::class, 'Google_Service_NetworkServices_MeshRouteView');
