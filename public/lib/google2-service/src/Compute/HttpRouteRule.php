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

class HttpRouteRule extends \Google\Collection
{
  protected $collection_key = 'matchRules';
  protected $customErrorResponsePolicyType = CustomErrorResponsePolicy::class;
  protected $customErrorResponsePolicyDataType = '';
  /**
   * The short description conveying the intent of this routeRule.
   *
   * The description can have a maximum length of 1024 characters.
   *
   * @var string
   */
  public $description;
  protected $headerActionType = HttpHeaderAction::class;
  protected $headerActionDataType = '';
  protected $matchRulesType = HttpRouteRuleMatch::class;
  protected $matchRulesDataType = 'array';
  /**
   * For routeRules within a given pathMatcher, priority determines the order in
   * which a load balancer interpretsrouteRules. RouteRules are evaluated in
   * order of priority, from the lowest to highest number. The priority of a
   * rule decreases as its number increases (1, 2, 3, N+1). The first rule that
   * matches the request is applied.
   *
   * You cannot configure two or more routeRules with the same priority.
   * Priority for each rule must be set to a number from 0 to 2147483647
   * inclusive.
   *
   * Priority numbers can have gaps, which enable you to add or remove rules in
   * the future without affecting the rest of the rules. For example, 1, 2, 3,
   * 4, 5, 9, 12, 16 is a valid series of priority numbers to which you could
   * add rules numbered from 6 to 8, 10 to 11, and 13 to 15 in the future
   * without any impact on existing rules.
   *
   * @var int
   */
  public $priority;
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
   * If a policy for an error code is not configured for the RouteRule, a policy
   * for the error code configured
   * inpathMatcher.defaultCustomErrorResponsePolicy is applied. If one is not
   * specified inpathMatcher.defaultCustomErrorResponsePolicy, the policy
   * configured in UrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * For example, consider a UrlMap with the following configuration:
   * - UrlMap.defaultCustomErrorResponsePolicy are configured      with policies
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
   * When used in conjunction withrouteRules.routeAction.retryPolicy, retries
   * take precedence. Only once all retries are exhausted,
   * thecustomErrorResponsePolicy is applied. While attempting a retry, if load
   * balancer is successful in reaching the service, the
   * customErrorResponsePolicy is ignored and the response from the service is
   * returned to the client.
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
   * The short description conveying the intent of this routeRule.
   *
   * The description can have a maximum length of 1024 characters.
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
   * for the selected backendService.
   *
   * The headerAction value specified here is applied before the matching
   * pathMatchers[].headerAction and afterpathMatchers[].routeRules[].routeActio
   * n.weightedBackendService.backendServiceWeightAction[].headerAction
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
   * The list of criteria for matching attributes of a request to thisrouteRule.
   * This list has OR semantics: the request matches this routeRule when any of
   * thematchRules are satisfied. However predicates within a given matchRule
   * have AND semantics. All predicates within a matchRule must match for the
   * request to match the rule.
   *
   * @param HttpRouteRuleMatch[] $matchRules
   */
  public function setMatchRules($matchRules)
  {
    $this->matchRules = $matchRules;
  }
  /**
   * @return HttpRouteRuleMatch[]
   */
  public function getMatchRules()
  {
    return $this->matchRules;
  }
  /**
   * For routeRules within a given pathMatcher, priority determines the order in
   * which a load balancer interpretsrouteRules. RouteRules are evaluated in
   * order of priority, from the lowest to highest number. The priority of a
   * rule decreases as its number increases (1, 2, 3, N+1). The first rule that
   * matches the request is applied.
   *
   * You cannot configure two or more routeRules with the same priority.
   * Priority for each rule must be set to a number from 0 to 2147483647
   * inclusive.
   *
   * Priority numbers can have gaps, which enable you to add or remove rules in
   * the future without affecting the rest of the rules. For example, 1, 2, 3,
   * 4, 5, 9, 12, 16 is a valid series of priority numbers to which you could
   * add rules numbered from 6 to 8, 10 to 11, and 13 to 15 in the future
   * without any impact on existing rules.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * In response to a matching matchRule, the load balancer performs advanced
   * routing actions, such as URL rewrites and header transformations, before
   * forwarding the request to the selected backend.
   *
   * Only one of urlRedirect, service orrouteAction.weightedBackendService can
   * be set.
   *
   * URL maps for classic Application Load Balancers only support the urlRewrite
   * action within a route rule'srouteAction.
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
   * When this rule is matched, the request is redirected to a URL specified by
   * urlRedirect.
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
class_alias(HttpRouteRule::class, 'Google_Service_Compute_HttpRouteRule');
