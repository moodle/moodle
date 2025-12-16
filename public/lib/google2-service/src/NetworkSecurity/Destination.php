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

namespace Google\Service\NetworkSecurity;

class Destination extends \Google\Collection
{
  protected $collection_key = 'ports';
  /**
   * Required. List of host names to match. Matched against the ":authority"
   * header in http requests. At least one host should match. Each host can be
   * an exact match, or a prefix match (example "mydomain.*") or a suffix match
   * (example "*.myorg.com") or a presence (any) match "*".
   *
   * @var string[]
   */
  public $hosts;
  protected $httpHeaderMatchType = HttpHeaderMatch::class;
  protected $httpHeaderMatchDataType = '';
  /**
   * Optional. A list of HTTP methods to match. At least one method should
   * match. Should not be set for gRPC services.
   *
   * @var string[]
   */
  public $methods;
  /**
   * Required. List of destination ports to match. At least one port should
   * match.
   *
   * @var string[]
   */
  public $ports;

  /**
   * Required. List of host names to match. Matched against the ":authority"
   * header in http requests. At least one host should match. Each host can be
   * an exact match, or a prefix match (example "mydomain.*") or a suffix match
   * (example "*.myorg.com") or a presence (any) match "*".
   *
   * @param string[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return string[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * Optional. Match against key:value pair in http header. Provides a flexible
   * match based on HTTP headers, for potentially advanced use cases. At least
   * one header should match. Avoid using header matches to make authorization
   * decisions unless there is a strong guarantee that requests arrive through a
   * trusted client or proxy.
   *
   * @param HttpHeaderMatch $httpHeaderMatch
   */
  public function setHttpHeaderMatch(HttpHeaderMatch $httpHeaderMatch)
  {
    $this->httpHeaderMatch = $httpHeaderMatch;
  }
  /**
   * @return HttpHeaderMatch
   */
  public function getHttpHeaderMatch()
  {
    return $this->httpHeaderMatch;
  }
  /**
   * Optional. A list of HTTP methods to match. At least one method should
   * match. Should not be set for gRPC services.
   *
   * @param string[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return string[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * Required. List of destination ports to match. At least one port should
   * match.
   *
   * @param string[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return string[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Destination::class, 'Google_Service_NetworkSecurity_Destination');
