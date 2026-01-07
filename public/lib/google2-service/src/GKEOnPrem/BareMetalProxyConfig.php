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

namespace Google\Service\GKEOnPrem;

class BareMetalProxyConfig extends \Google\Collection
{
  protected $collection_key = 'noProxy';
  /**
   * A list of IPs, hostnames, and domains that should skip the proxy. Examples:
   * ["127.0.0.1", "example.com", ".corp", "localhost"].
   *
   * @var string[]
   */
  public $noProxy;
  /**
   * Required. Specifies the address of your proxy server. Examples:
   * `http://domain` Do not provide credentials in the format
   * `http://(username:password@)domain` these will be rejected by the server.
   *
   * @var string
   */
  public $uri;

  /**
   * A list of IPs, hostnames, and domains that should skip the proxy. Examples:
   * ["127.0.0.1", "example.com", ".corp", "localhost"].
   *
   * @param string[] $noProxy
   */
  public function setNoProxy($noProxy)
  {
    $this->noProxy = $noProxy;
  }
  /**
   * @return string[]
   */
  public function getNoProxy()
  {
    return $this->noProxy;
  }
  /**
   * Required. Specifies the address of your proxy server. Examples:
   * `http://domain` Do not provide credentials in the format
   * `http://(username:password@)domain` these will be rejected by the server.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalProxyConfig::class, 'Google_Service_GKEOnPrem_BareMetalProxyConfig');
