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

class PathRule extends \Google\Collection
{
  protected $collection_key = 'paths';
  protected $customErrorResponsePolicyType = CustomErrorResponsePolicy::class;
  protected $customErrorResponsePolicyDataType = '';
  /**
   * The list of path patterns to match. Each must start with / and the only
   * place a * is allowed is at the end following a /.  The string fed to the
   * path matcher does not include any text after the first ? or #, and those
   * chars are not allowed here.
   *
   * @var string[]
   */
  public $paths;
  protected $routeActionType = HttpRouteAction::class;
  protected $routeActionDataType = '';
  /**
   * The full or partial URL of the backend service resource to which traffic is
   * directed if this rule is matched. If routeAction is also specified,
   * advanced routing actions, such as URL rewrites, take effect before sending
   * the request to the backend.
   *
   * Only one of urlRedirect, service orrouteAction.weightedBackendService can
   * be set.
   *
   * @var string
   */
  public $service;
  protected $urlRedirectType = HttpRedirectAction::class;
  protected $urlRedirectDataType = '';

  /**
   * customErrorResponsePolicy specifies how the Load Balancer returns error
   * responses when BackendServiceorBackendBucket responds with an error.
   *
   * If a policy for an error code is not configured for the PathRule, a policy
   * for the error code configured
   * inpathMatcher.defaultCustomErrorResponsePolicy is applied. If one is not
   * specified inpathMatcher.defaultCustomErrorResponsePolicy, the policy
   * configured in UrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * For example, consider a UrlMap with the following configuration:
   * - UrlMap.defaultCustomErrorResponsePolicy are configured      with policies
   * for 5xx and 4xx errors      - A PathRule for /coming_soon/ is configured
   * for the error      code 404.
   *
   * If the request is for www.myotherdomain.com and a404 is encountered, the
   * policy underUrlMap.defaultCustomErrorResponsePolicy takes effect. If a404
   * response is encountered for the requestwww.example.com/current_events/, the
   * pathMatcher's policy takes effect. If however, the request
   * forwww.example.com/coming_soon/ encounters a 404, the policy in
   * PathRule.customErrorResponsePolicy takes effect. If any of the requests in
   * this example encounter a 500 error code, the policy
   * atUrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * customErrorResponsePolicy is supported only for global external Application
   * Load Balancers.
   *
   * @param CustomErrorResponsePolicy $customErrorResponsePolicy
   */
  public function setCustomErrorResponsePolicy(CustomErrorResponsePolicy $customErrorResponsePolicy)
  {
    $this->customErrorResponsePolicy = $customErrorResponsePolicy;
  }
  /**
   * @return CustomErrorResponsePolicy
   */
  public function getCustomErrorResponsePolicy()
  {
    return $this->customErrorResponsePolicy;
  }
  /**
   * The list of path patterns to match. Each must start with / and the only
   * place a * is allowed is at the end following a /.  The string fed to the
   * path matcher does not include any text after the first ? or #, and those
   * chars are not allowed here.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
  /**
   * In response to a matching path, the load balancer performs advanced routing
   * actions, such as URL rewrites and header transformations, before forwarding
   * the request to the selected backend.
   *
   * Only one of urlRedirect, service orrouteAction.weightedBackendService can
   * be set.
   *
   * URL maps for classic Application Load Balancers only support the urlRewrite
   * action within a path rule'srouteAction.
   *
   * @param HttpRouteAction $routeAction
   */
  public function setRouteAction(HttpRouteAction $routeAction)
  {
    $this->routeAction = $routeAction;
  }
  /**
   * @return HttpRouteAction
   */
  public function getRouteAction()
  {
    return $this->routeAction;
  }
  /**
   * The full or partial URL of the backend service resource to which traffic is
   * directed if this rule is matched. If routeAction is also specified,
   * advanced routing actions, such as URL rewrites, take effect before sending
   * the request to the backend.
   *
   * Only one of urlRedirect, service orrouteAction.weightedBackendService can
   * be set.
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
   * When a path pattern is matched, the request is redirected to a URL
   * specified by urlRedirect.
   *
   * Only one of urlRedirect, service orrouteAction.weightedBackendService can
   * be set.
   *
   * Not supported when the URL map is bound to a target gRPC proxy.
   *
   * @param HttpRedirectAction $urlRedirect
   */
  public function setUrlRedirect(HttpRedirectAction $urlRedirect)
  {
    $this->urlRedirect = $urlRedirect;
  }
  /**
   * @return HttpRedirectAction
   */
  public function getUrlRedirect()
  {
    return $this->urlRedirect;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PathRule::class, 'Google_Service_Compute_PathRule');
