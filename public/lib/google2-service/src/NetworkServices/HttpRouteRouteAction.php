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

class HttpRouteRouteAction extends \Google\Collection
{
  protected $collection_key = 'destinations';
  protected $corsPolicyType = HttpRouteCorsPolicy::class;
  protected $corsPolicyDataType = '';
  protected $destinationsType = HttpRouteDestination::class;
  protected $destinationsDataType = 'array';
  protected $directResponseType = HttpRouteHttpDirectResponse::class;
  protected $directResponseDataType = '';
  protected $faultInjectionPolicyType = HttpRouteFaultInjectionPolicy::class;
  protected $faultInjectionPolicyDataType = '';
  /**
   * Optional. Specifies the idle timeout for the selected route. The idle
   * timeout is defined as the period in which there are no bytes sent or
   * received on either the upstream or downstream connection. If not set, the
   * default idle timeout is 1 hour. If set to 0s, the timeout will be disabled.
   *
   * @var string
   */
  public $idleTimeout;
  protected $redirectType = HttpRouteRedirect::class;
  protected $redirectDataType = '';
  protected $requestHeaderModifierType = HttpRouteHeaderModifier::class;
  protected $requestHeaderModifierDataType = '';
  protected $requestMirrorPolicyType = HttpRouteRequestMirrorPolicy::class;
  protected $requestMirrorPolicyDataType = '';
  protected $responseHeaderModifierType = HttpRouteHeaderModifier::class;
  protected $responseHeaderModifierDataType = '';
  protected $retryPolicyType = HttpRouteRetryPolicy::class;
  protected $retryPolicyDataType = '';
  protected $statefulSessionAffinityType = HttpRouteStatefulSessionAffinityPolicy::class;
  protected $statefulSessionAffinityDataType = '';
  /**
   * Specifies the timeout for selected route. Timeout is computed from the time
   * the request has been fully processed (i.e. end of stream) up until the
   * response has been completely processed. Timeout includes all retries.
   *
   * @var string
   */
  public $timeout;
  protected $urlRewriteType = HttpRouteURLRewrite::class;
  protected $urlRewriteDataType = '';

  /**
   * The specification for allowing client side cross-origin requests.
   *
   * @param HttpRouteCorsPolicy $corsPolicy
   */
  public function setCorsPolicy(HttpRouteCorsPolicy $corsPolicy)
  {
    $this->corsPolicy = $corsPolicy;
  }
  /**
   * @return HttpRouteCorsPolicy
   */
  public function getCorsPolicy()
  {
    return $this->corsPolicy;
  }
  /**
   * The destination to which traffic should be forwarded.
   *
   * @param HttpRouteDestination[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return HttpRouteDestination[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Optional. Static HTTP Response object to be returned regardless of the
   * request.
   *
   * @param HttpRouteHttpDirectResponse $directResponse
   */
  public function setDirectResponse(HttpRouteHttpDirectResponse $directResponse)
  {
    $this->directResponse = $directResponse;
  }
  /**
   * @return HttpRouteHttpDirectResponse
   */
  public function getDirectResponse()
  {
    return $this->directResponse;
  }
  /**
   * The specification for fault injection introduced into traffic to test the
   * resiliency of clients to backend service failure. As part of fault
   * injection, when clients send requests to a backend service, delays can be
   * introduced on a percentage of requests before sending those requests to the
   * backend service. Similarly requests from clients can be aborted for a
   * percentage of requests. timeout and retry_policy will be ignored by clients
   * that are configured with a fault_injection_policy
   *
   * @param HttpRouteFaultInjectionPolicy $faultInjectionPolicy
   */
  public function setFaultInjectionPolicy(HttpRouteFaultInjectionPolicy $faultInjectionPolicy)
  {
    $this->faultInjectionPolicy = $faultInjectionPolicy;
  }
  /**
   * @return HttpRouteFaultInjectionPolicy
   */
  public function getFaultInjectionPolicy()
  {
    return $this->faultInjectionPolicy;
  }
  /**
   * Optional. Specifies the idle timeout for the selected route. The idle
   * timeout is defined as the period in which there are no bytes sent or
   * received on either the upstream or downstream connection. If not set, the
   * default idle timeout is 1 hour. If set to 0s, the timeout will be disabled.
   *
   * @param string $idleTimeout
   */
  public function setIdleTimeout($idleTimeout)
  {
    $this->idleTimeout = $idleTimeout;
  }
  /**
   * @return string
   */
  public function getIdleTimeout()
  {
    return $this->idleTimeout;
  }
  /**
   * If set, the request is directed as configured by this field.
   *
   * @param HttpRouteRedirect $redirect
   */
  public function setRedirect(HttpRouteRedirect $redirect)
  {
    $this->redirect = $redirect;
  }
  /**
   * @return HttpRouteRedirect
   */
  public function getRedirect()
  {
    return $this->redirect;
  }
  /**
   * The specification for modifying the headers of a matching request prior to
   * delivery of the request to the destination. If HeaderModifiers are set on
   * both the Destination and the RouteAction, they will be merged. Conflicts
   * between the two will not be resolved on the configuration.
   *
   * @param HttpRouteHeaderModifier $requestHeaderModifier
   */
  public function setRequestHeaderModifier(HttpRouteHeaderModifier $requestHeaderModifier)
  {
    $this->requestHeaderModifier = $requestHeaderModifier;
  }
  /**
   * @return HttpRouteHeaderModifier
   */
  public function getRequestHeaderModifier()
  {
    return $this->requestHeaderModifier;
  }
  /**
   * Specifies the policy on how requests intended for the routes destination
   * are shadowed to a separate mirrored destination. Proxy will not wait for
   * the shadow destination to respond before returning the response. Prior to
   * sending traffic to the shadow service, the host/authority header is
   * suffixed with -shadow.
   *
   * @param HttpRouteRequestMirrorPolicy $requestMirrorPolicy
   */
  public function setRequestMirrorPolicy(HttpRouteRequestMirrorPolicy $requestMirrorPolicy)
  {
    $this->requestMirrorPolicy = $requestMirrorPolicy;
  }
  /**
   * @return HttpRouteRequestMirrorPolicy
   */
  public function getRequestMirrorPolicy()
  {
    return $this->requestMirrorPolicy;
  }
  /**
   * The specification for modifying the headers of a response prior to sending
   * the response back to the client. If HeaderModifiers are set on both the
   * Destination and the RouteAction, they will be merged. Conflicts between the
   * two will not be resolved on the configuration.
   *
   * @param HttpRouteHeaderModifier $responseHeaderModifier
   */
  public function setResponseHeaderModifier(HttpRouteHeaderModifier $responseHeaderModifier)
  {
    $this->responseHeaderModifier = $responseHeaderModifier;
  }
  /**
   * @return HttpRouteHeaderModifier
   */
  public function getResponseHeaderModifier()
  {
    return $this->responseHeaderModifier;
  }
  /**
   * Specifies the retry policy associated with this route.
   *
   * @param HttpRouteRetryPolicy $retryPolicy
   */
  public function setRetryPolicy(HttpRouteRetryPolicy $retryPolicy)
  {
    $this->retryPolicy = $retryPolicy;
  }
  /**
   * @return HttpRouteRetryPolicy
   */
  public function getRetryPolicy()
  {
    return $this->retryPolicy;
  }
  /**
   * Optional. Specifies cookie-based stateful session affinity.
   *
   * @param HttpRouteStatefulSessionAffinityPolicy $statefulSessionAffinity
   */
  public function setStatefulSessionAffinity(HttpRouteStatefulSessionAffinityPolicy $statefulSessionAffinity)
  {
    $this->statefulSessionAffinity = $statefulSessionAffinity;
  }
  /**
   * @return HttpRouteStatefulSessionAffinityPolicy
   */
  public function getStatefulSessionAffinity()
  {
    return $this->statefulSessionAffinity;
  }
  /**
   * Specifies the timeout for selected route. Timeout is computed from the time
   * the request has been fully processed (i.e. end of stream) up until the
   * response has been completely processed. Timeout includes all retries.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * The specification for rewrite URL before forwarding requests to the
   * destination.
   *
   * @param HttpRouteURLRewrite $urlRewrite
   */
  public function setUrlRewrite(HttpRouteURLRewrite $urlRewrite)
  {
    $this->urlRewrite = $urlRewrite;
  }
  /**
   * @return HttpRouteURLRewrite
   */
  public function getUrlRewrite()
  {
    return $this->urlRewrite;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteRouteAction::class, 'Google_Service_NetworkServices_HttpRouteRouteAction');
