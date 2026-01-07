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

class PathMatcher extends \Google\Collection
{
  protected $collection_key = 'routeRules';
  protected $defaultCustomErrorResponsePolicyType = CustomErrorResponsePolicy::class;
  protected $defaultCustomErrorResponsePolicyDataType = '';
  protected $defaultRouteActionType = HttpRouteAction::class;
  protected $defaultRouteActionDataType = '';
  /**
   * The full or partial URL to the BackendService resource. This URL is used if
   * none of the pathRules orrouteRules defined by this PathMatcher are matched.
   * For example, the following are all valid URLs to a BackendService resource:
   * - https://www.googleapis.com/compute/v1/projects/project/global/backendServ
   * ices/backendService      -
   * compute/v1/projects/project/global/backendServices/backendService      -
   * global/backendServices/backendService
   *
   * If defaultRouteAction is also specified, advanced routing actions, such as
   * URL rewrites, take effect before sending the request to the backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * Authorization requires one or more of the following Google IAM permissions
   * on the specified resource default_service:                -
   * compute.backendBuckets.use       - compute.backendServices.use
   *
   * @var string
   */
  public $defaultService;
  protected $defaultUrlRedirectType = HttpRedirectAction::class;
  protected $defaultUrlRedirectDataType = '';
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  protected $headerActionType = HttpHeaderAction::class;
  protected $headerActionDataType = '';
  /**
   * The name to which this PathMatcher is referred by theHostRule.
   *
   * @var string
   */
  public $name;
  protected $pathRulesType = PathRule::class;
  protected $pathRulesDataType = 'array';
  protected $routeRulesType = HttpRouteRule::class;
  protected $routeRulesDataType = 'array';

  /**
   * defaultCustomErrorResponsePolicy specifies how the Load Balancer returns
   * error responses when BackendServiceorBackendBucket responds with an error.
   *
   * This policy takes effect at the PathMatcher level and applies only when no
   * policy has been defined for the error code at lower levels likeRouteRule
   * and PathRule within thisPathMatcher. If an error code does not have a
   * policy defined in defaultCustomErrorResponsePolicy, then a policy defined
   * for the error code in UrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * For example, consider a UrlMap with the following configuration:
   * - UrlMap.defaultCustomErrorResponsePolicy is configured      with policies
   * for 5xx and 4xx errors      - A RouteRule for /coming_soon/ is configured
   * for the      error code 404.
   *
   * If the request is for www.myotherdomain.com and a404 is encountered, the
   * policy underUrlMap.defaultCustomErrorResponsePolicy takes effect. If a404
   * response is encountered for the requestwww.example.com/current_events/, the
   * pathMatcher's policy takes effect. If however, the request
   * forwww.example.com/coming_soon/ encounters a 404, the policy in
   * RouteRule.customErrorResponsePolicy takes effect. If any of the requests in
   * this example encounter a 500 error code, the policy
   * atUrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * When used in conjunction withpathMatcher.defaultRouteAction.retryPolicy,
   * retries take precedence. Only once all retries are exhausted,
   * thedefaultCustomErrorResponsePolicy is applied. While attempting a retry,
   * if load balancer is successful in reaching the service, the
   * defaultCustomErrorResponsePolicy is ignored and the response from the
   * service is returned to the client.
   *
   * defaultCustomErrorResponsePolicy is supported only for global external
   * Application Load Balancers.
   *
   * @param CustomErrorResponsePolicy $defaultCustomErrorResponsePolicy
   */
  public function setDefaultCustomErrorResponsePolicy(CustomErrorResponsePolicy $defaultCustomErrorResponsePolicy)
  {
    $this->defaultCustomErrorResponsePolicy = $defaultCustomErrorResponsePolicy;
  }
  /**
   * @return CustomErrorResponsePolicy
   */
  public function getDefaultCustomErrorResponsePolicy()
  {
    return $this->defaultCustomErrorResponsePolicy;
  }
  /**
   * defaultRouteAction takes effect when none of the pathRules or routeRules
   * match. The load balancer performs advanced routing actions, such as URL
   * rewrites and header transformations, before forwarding the request to the
   * selected backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * URL maps for classic Application Load Balancers only support the urlRewrite
   * action within a path matcher'sdefaultRouteAction.
   *
   * @param HttpRouteAction $defaultRouteAction
   */
  public function setDefaultRouteAction(HttpRouteAction $defaultRouteAction)
  {
    $this->defaultRouteAction = $defaultRouteAction;
  }
  /**
   * @return HttpRouteAction
   */
  public function getDefaultRouteAction()
  {
    return $this->defaultRouteAction;
  }
  /**
   * The full or partial URL to the BackendService resource. This URL is used if
   * none of the pathRules orrouteRules defined by this PathMatcher are matched.
   * For example, the following are all valid URLs to a BackendService resource:
   * - https://www.googleapis.com/compute/v1/projects/project/global/backendServ
   * ices/backendService      -
   * compute/v1/projects/project/global/backendServices/backendService      -
   * global/backendServices/backendService
   *
   * If defaultRouteAction is also specified, advanced routing actions, such as
   * URL rewrites, take effect before sending the request to the backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * Authorization requires one or more of the following Google IAM permissions
   * on the specified resource default_service:                -
   * compute.backendBuckets.use       - compute.backendServices.use
   *
   * @param string $defaultService
   */
  public function setDefaultService($defaultService)
  {
    $this->defaultService = $defaultService;
  }
  /**
   * @return string
   */
  public function getDefaultService()
  {
    return $this->defaultService;
  }
  /**
   * When none of the specified pathRules orrouteRules match, the request is
   * redirected to a URL specified by defaultUrlRedirect.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * Not supported when the URL map is bound to a target gRPC proxy.
   *
   * @param HttpRedirectAction $defaultUrlRedirect
   */
  public function setDefaultUrlRedirect(HttpRedirectAction $defaultUrlRedirect)
  {
    $this->defaultUrlRedirect = $defaultUrlRedirect;
  }
  /**
   * @return HttpRedirectAction
   */
  public function getDefaultUrlRedirect()
  {
    return $this->defaultUrlRedirect;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Specifies changes to request and response headers that need to take effect
   * for the selected backend service.
   *
   * HeaderAction specified here are applied after the matchingHttpRouteRule
   * HeaderAction and before theHeaderAction in the UrlMap
   *
   * HeaderAction is not supported for load balancers that have their
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
   * The name to which this PathMatcher is referred by theHostRule.
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
   * The list of path rules. Use this list instead of routeRules when routing
   * based on simple path matching is all that's required. A path rule can only
   * include a wildcard character (*) after a forward slash character ("/").
   *
   * The order by which path rules are specified does not matter. Matches are
   * always done on the longest-path-first basis.
   *
   * For example: a pathRule with a path /a/b/c will match before /a/b
   * irrespective of the order in which those paths appear in this list.
   *
   * Within a given pathMatcher, only one ofpathRules or routeRules must be set.
   *
   * @param PathRule[] $pathRules
   */
  public function setPathRules($pathRules)
  {
    $this->pathRules = $pathRules;
  }
  /**
   * @return PathRule[]
   */
  public function getPathRules()
  {
    return $this->pathRules;
  }
  /**
   * The list of HTTP route rules. Use this list instead ofpathRules when
   * advanced route matching and routing actions are desired. routeRules are
   * evaluated in order of priority, from the lowest to highest number.
   *
   * Within a given pathMatcher, you can set only one ofpathRules or routeRules.
   *
   * @param HttpRouteRule[] $routeRules
   */
  public function setRouteRules($routeRules)
  {
    $this->routeRules = $routeRules;
  }
  /**
   * @return HttpRouteRule[]
   */
  public function getRouteRules()
  {
    return $this->routeRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PathMatcher::class, 'Google_Service_Compute_PathMatcher');
