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

class WeightedBackendService extends \Google\Model
{
  /**
   * The full or partial URL to the default BackendService resource. Before
   * forwarding the request to backendService, the load balancer applies any
   * relevant headerActions specified as part of thisbackendServiceWeight.
   *
   * @var string
   */
  public $backendService;
  protected $headerActionType = HttpHeaderAction::class;
  protected $headerActionDataType = '';
  /**
   * Specifies the fraction of traffic sent to a backend service, computed
   * asweight / (sum of all weightedBackendService weights in routeAction).
   *
   * The selection of a backend service is determined only for new traffic. Once
   * a user's request has been directed to a backend service, subsequent
   * requests are sent to the same backend service as determined by the backend
   * service's session affinity policy. Don't configure session affinity if
   * you're using weighted traffic splitting. If you do, the weighted traffic
   * splitting configuration takes precedence.
   *
   * The value must be from 0 to 1000.
   *
   * @var string
   */
  public $weight;

  /**
   * The full or partial URL to the default BackendService resource. Before
   * forwarding the request to backendService, the load balancer applies any
   * relevant headerActions specified as part of thisbackendServiceWeight.
   *
   * @param string $backendService
   */
  public function setBackendService($backendService)
  {
    $this->backendService = $backendService;
  }
  /**
   * @return string
   */
  public function getBackendService()
  {
    return $this->backendService;
  }
  /**
   * Specifies changes to request and response headers that need to take effect
   * for the selected backendService.
   *
   * headerAction specified here take effect beforeheaderAction in the enclosing
   * HttpRouteRule,PathMatcher and UrlMap.
   *
   * headerAction is not supported for load balancers that have their
   * loadBalancingScheme set to EXTERNAL.
   *
   * Not supported when the URL map is bound to a target gRPC proxy that has
   * validateForProxyless field set to true.
   *
   * @param HttpHeaderAction $headerAction
   */
  public function setHeaderAction(HttpHeaderAction $headerAction)
  {
    $this->headerAction = $headerAction;
  }
  /**
   * @return HttpHeaderAction
   */
  public function getHeaderAction()
  {
    return $this->headerAction;
  }
  /**
   * Specifies the fraction of traffic sent to a backend service, computed
   * asweight / (sum of all weightedBackendService weights in routeAction).
   *
   * The selection of a backend service is determined only for new traffic. Once
   * a user's request has been directed to a backend service, subsequent
   * requests are sent to the same backend service as determined by the backend
   * service's session affinity policy. Don't configure session affinity if
   * you're using weighted traffic splitting. If you do, the weighted traffic
   * splitting configuration takes precedence.
   *
   * The value must be from 0 to 1000.
   *
   * @param string $weight
   */
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  /**
   * @return string
   */
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WeightedBackendService::class, 'Google_Service_Compute_WeightedBackendService');
