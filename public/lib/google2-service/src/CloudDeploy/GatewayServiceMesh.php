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

namespace Google\Service\CloudDeploy;

class GatewayServiceMesh extends \Google\Model
{
  /**
   * Required. Name of the Kubernetes Deployment whose traffic is managed by the
   * specified HTTPRoute and Service.
   *
   * @var string
   */
  public $deployment;
  /**
   * Required. Name of the Gateway API HTTPRoute.
   *
   * @var string
   */
  public $httpRoute;
  /**
   * Optional. The label to use when selecting Pods for the Deployment and
   * Service resources. This label must already be present in both resources.
   *
   * @var string
   */
  public $podSelectorLabel;
  protected $routeDestinationsType = RouteDestinations::class;
  protected $routeDestinationsDataType = '';
  /**
   * Optional. The time to wait for route updates to propagate. The maximum
   * configurable time is 3 hours, in seconds format. If unspecified, there is
   * no wait time.
   *
   * @var string
   */
  public $routeUpdateWaitTime;
  /**
   * Required. Name of the Kubernetes Service.
   *
   * @var string
   */
  public $service;
  /**
   * Optional. The amount of time to migrate traffic back from the canary
   * Service to the original Service during the stable phase deployment. If
   * specified, must be between 15s and 3600s. If unspecified, there is no
   * cutback time.
   *
   * @var string
   */
  public $stableCutbackDuration;

  /**
   * Required. Name of the Kubernetes Deployment whose traffic is managed by the
   * specified HTTPRoute and Service.
   *
   * @param string $deployment
   */
  public function setDeployment($deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return string
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Required. Name of the Gateway API HTTPRoute.
   *
   * @param string $httpRoute
   */
  public function setHttpRoute($httpRoute)
  {
    $this->httpRoute = $httpRoute;
  }
  /**
   * @return string
   */
  public function getHttpRoute()
  {
    return $this->httpRoute;
  }
  /**
   * Optional. The label to use when selecting Pods for the Deployment and
   * Service resources. This label must already be present in both resources.
   *
   * @param string $podSelectorLabel
   */
  public function setPodSelectorLabel($podSelectorLabel)
  {
    $this->podSelectorLabel = $podSelectorLabel;
  }
  /**
   * @return string
   */
  public function getPodSelectorLabel()
  {
    return $this->podSelectorLabel;
  }
  /**
   * Optional. Route destinations allow configuring the Gateway API HTTPRoute to
   * be deployed to additional clusters. This option is available for multi-
   * cluster service mesh set ups that require the route to exist in the
   * clusters that call the service. If unspecified, the HTTPRoute will only be
   * deployed to the Target cluster.
   *
   * @param RouteDestinations $routeDestinations
   */
  public function setRouteDestinations(RouteDestinations $routeDestinations)
  {
    $this->routeDestinations = $routeDestinations;
  }
  /**
   * @return RouteDestinations
   */
  public function getRouteDestinations()
  {
    return $this->routeDestinations;
  }
  /**
   * Optional. The time to wait for route updates to propagate. The maximum
   * configurable time is 3 hours, in seconds format. If unspecified, there is
   * no wait time.
   *
   * @param string $routeUpdateWaitTime
   */
  public function setRouteUpdateWaitTime($routeUpdateWaitTime)
  {
    $this->routeUpdateWaitTime = $routeUpdateWaitTime;
  }
  /**
   * @return string
   */
  public function getRouteUpdateWaitTime()
  {
    return $this->routeUpdateWaitTime;
  }
  /**
   * Required. Name of the Kubernetes Service.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Optional. The amount of time to migrate traffic back from the canary
   * Service to the original Service during the stable phase deployment. If
   * specified, must be between 15s and 3600s. If unspecified, there is no
   * cutback time.
   *
   * @param string $stableCutbackDuration
   */
  public function setStableCutbackDuration($stableCutbackDuration)
  {
    $this->stableCutbackDuration = $stableCutbackDuration;
  }
  /**
   * @return string
   */
  public function getStableCutbackDuration()
  {
    return $this->stableCutbackDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GatewayServiceMesh::class, 'Google_Service_CloudDeploy_GatewayServiceMesh');
