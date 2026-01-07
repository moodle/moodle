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

namespace Google\Service\AndroidManagement;

class ProxyInfo extends \Google\Collection
{
  protected $collection_key = 'excludedHosts';
  /**
   * For a direct proxy, the hosts for which the proxy is bypassed. The host
   * names may contain wildcards such as *.example.com.
   *
   * @var string[]
   */
  public $excludedHosts;
  /**
   * The host of the direct proxy.
   *
   * @var string
   */
  public $host;
  /**
   * The URI of the PAC script used to configure the proxy.
   *
   * @var string
   */
  public $pacUri;
  /**
   * The port of the direct proxy.
   *
   * @var int
   */
  public $port;

  /**
   * For a direct proxy, the hosts for which the proxy is bypassed. The host
   * names may contain wildcards such as *.example.com.
   *
   * @param string[] $excludedHosts
   */
  public function setExcludedHosts($excludedHosts)
  {
    $this->excludedHosts = $excludedHosts;
  }
  /**
   * @return string[]
   */
  public function getExcludedHosts()
  {
    return $this->excludedHosts;
  }
  /**
   * The host of the direct proxy.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * The URI of the PAC script used to configure the proxy.
   *
   * @param string $pacUri
   */
  public function setPacUri($pacUri)
  {
    $this->pacUri = $pacUri;
  }
  /**
   * @return string
   */
  public function getPacUri()
  {
    return $this->pacUri;
  }
  /**
   * The port of the direct proxy.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProxyInfo::class, 'Google_Service_AndroidManagement_ProxyInfo');
