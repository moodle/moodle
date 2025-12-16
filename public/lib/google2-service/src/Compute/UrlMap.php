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

class UrlMap extends \Google\Collection
{
  protected $collection_key = 'tests';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $defaultCustomErrorResponsePolicyType = CustomErrorResponsePolicy::class;
  protected $defaultCustomErrorResponsePolicyDataType = '';
  protected $defaultRouteActionType = HttpRouteAction::class;
  protected $defaultRouteActionDataType = '';
  /**
   * The full or partial URL of the defaultService resource to which traffic is
   * directed if none of the hostRules match. If defaultRouteAction is also
   * specified, advanced routing actions, such as URL rewrites, take effect
   * before sending the request to the backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * defaultService has no effect when the URL map is bound to a target gRPC
   * proxy that has the validateForProxyless field set to true.
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
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field is ignored when
   * inserting a UrlMap. An up-to-date fingerprint must be provided in order to
   * update the UrlMap, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a UrlMap.
   *
   * @var string
   */
  public $fingerprint;
  protected $headerActionType = HttpHeaderAction::class;
  protected $headerActionDataType = '';
  protected $hostRulesType = HostRule::class;
  protected $hostRulesDataType = 'array';
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#urlMaps for
   * url maps.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  protected $pathMatchersType = PathMatcher::class;
  protected $pathMatchersDataType = 'array';
  /**
   * Output only. [Output Only] URL of the region where the regional URL map
   * resides. This field is not applicable to global URL maps. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $testsType = UrlMapTest::class;
  protected $testsDataType = 'array';

  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * defaultCustomErrorResponsePolicy specifies how the Load Balancer returns
   * error responses when BackendServiceorBackendBucket responds with an error.
   *
   * This policy takes effect at the load balancer level and applies only when
   * no policy has been defined for the error code at lower levels like
   * PathMatcher, RouteRule and PathRule within this UrlMap.
   *
   * For example, consider a UrlMap with the following configuration:
   * - defaultCustomErrorResponsePolicy containing policies for      responding
   * to 5xx and 4xx errors      - A PathMatcher configured for *.example.com has
   * defaultCustomErrorResponsePolicy for 4xx.
   *
   * If a request for http://www.example.com/ encounters a404, the policy
   * inpathMatcher.defaultCustomErrorResponsePolicy will be enforced. When the
   * request for http://www.example.com/ encounters a502, the policy
   * inUrlMap.defaultCustomErrorResponsePolicy will be enforced. When a request
   * that does not match any host in *.example.com such as
   * http://www.myotherexample.com/, encounters a404,
   * UrlMap.defaultCustomErrorResponsePolicy takes effect.
   *
   * When used in conjunction withdefaultRouteAction.retryPolicy, retries take
   * precedence. Only once all retries are exhausted,
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
   * defaultRouteAction takes effect when none of the hostRules match. The load
   * balancer performs advanced routing actions, such as URL rewrites and header
   * transformations, before forwarding the request to the selected backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   *  URL maps for classic Application Load Balancers only support the
   * urlRewrite action within defaultRouteAction.
   *
   * defaultRouteAction has no effect when the URL map is bound to a target gRPC
   * proxy that has the validateForProxyless field set to true.
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
   * The full or partial URL of the defaultService resource to which traffic is
   * directed if none of the hostRules match. If defaultRouteAction is also
   * specified, advanced routing actions, such as URL rewrites, take effect
   * before sending the request to the backend.
   *
   * Only one of defaultUrlRedirect, defaultService or
   * defaultRouteAction.weightedBackendService can be set.
   *
   * defaultService has no effect when the URL map is bound to a target gRPC
   * proxy that has the validateForProxyless field set to true.
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
   * When none of the specified hostRules match, the request is redirected to a
   * URL specified by defaultUrlRedirect.
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field is ignored when
   * inserting a UrlMap. An up-to-date fingerprint must be provided in order to
   * update the UrlMap, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a UrlMap.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Specifies changes to request and response headers that need to take effect
   * for the selected backendService.
   *
   * The headerAction specified here take effect afterheaderAction specified
   * under pathMatcher.
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
   * The list of host rules to use against the URL.
   *
   * @param HostRule[] $hostRules
   */
  public function setHostRules($hostRules)
  {
    $this->hostRules = $hostRules;
  }
  /**
   * @return HostRule[]
   */
  public function getHostRules()
  {
    return $this->hostRules;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#urlMaps for
   * url maps.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * The list of named PathMatchers to use against the URL.
   *
   * @param PathMatcher[] $pathMatchers
   */
  public function setPathMatchers($pathMatchers)
  {
    $this->pathMatchers = $pathMatchers;
  }
  /**
   * @return PathMatcher[]
   */
  public function getPathMatchers()
  {
    return $this->pathMatchers;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional URL map
   * resides. This field is not applicable to global URL maps. You must specify
   * this field as part of the HTTP request URL. It is not settable as a field
   * in the request body.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The list of expected URL mapping tests. Request to update theUrlMap
   * succeeds only if all test cases pass. You can specify a maximum of 100
   * tests per UrlMap.
   *
   * Not supported when the URL map is bound to a target gRPC proxy that has
   * validateForProxyless field set to true.
   *
   * @param UrlMapTest[] $tests
   */
  public function setTests($tests)
  {
    $this->tests = $tests;
  }
  /**
   * @return UrlMapTest[]
   */
  public function getTests()
  {
    return $this->tests;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlMap::class, 'Google_Service_Compute_UrlMap');
