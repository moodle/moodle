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

class ConsistentHashLoadBalancerSettings extends \Google\Model
{
  protected $httpCookieType = ConsistentHashLoadBalancerSettingsHttpCookie::class;
  protected $httpCookieDataType = '';
  /**
   * The hash based on the value of the specified header field. This field is
   * applicable if the sessionAffinity is set toHEADER_FIELD.
   *
   * @var string
   */
  public $httpHeaderName;
  /**
   * The minimum number of virtual nodes to use for the hash ring. Defaults to
   * 1024. Larger ring sizes result in more granular load distributions. If the
   * number of hosts in the load balancing pool is larger than the ring size,
   * each host will be assigned a single virtual node.
   *
   * @var string
   */
  public $minimumRingSize;

  /**
   * Hash is based on HTTP Cookie. This field describes a HTTP cookie that will
   * be used as the hash key for the consistent hash load balancer. If the
   * cookie is not present, it will be generated. This field is applicable if
   * the sessionAffinity is set to HTTP_COOKIE.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param ConsistentHashLoadBalancerSettingsHttpCookie $httpCookie
   */
  public function setHttpCookie(ConsistentHashLoadBalancerSettingsHttpCookie $httpCookie)
  {
    $this->httpCookie = $httpCookie;
  }
  /**
   * @return ConsistentHashLoadBalancerSettingsHttpCookie
   */
  public function getHttpCookie()
  {
    return $this->httpCookie;
  }
  /**
   * The hash based on the value of the specified header field. This field is
   * applicable if the sessionAffinity is set toHEADER_FIELD.
   *
   * @param string $httpHeaderName
   */
  public function setHttpHeaderName($httpHeaderName)
  {
    $this->httpHeaderName = $httpHeaderName;
  }
  /**
   * @return string
   */
  public function getHttpHeaderName()
  {
    return $this->httpHeaderName;
  }
  /**
   * The minimum number of virtual nodes to use for the hash ring. Defaults to
   * 1024. Larger ring sizes result in more granular load distributions. If the
   * number of hosts in the load balancing pool is larger than the ring size,
   * each host will be assigned a single virtual node.
   *
   * @param string $minimumRingSize
   */
  public function setMinimumRingSize($minimumRingSize)
  {
    $this->minimumRingSize = $minimumRingSize;
  }
  /**
   * @return string
   */
  public function getMinimumRingSize()
  {
    return $this->minimumRingSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsistentHashLoadBalancerSettings::class, 'Google_Service_Compute_ConsistentHashLoadBalancerSettings');
