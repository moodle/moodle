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

class VmwareAdminProxy extends \Google\Model
{
  /**
   * A comma-separated list of IP addresses, IP address ranges, host names, and
   * domain names that should not go through the proxy server. When Google
   * Distributed Cloud sends a request to one of these addresses, hosts, or
   * domains, the request is sent directly.
   *
   * @var string
   */
  public $noProxy;
  /**
   * The HTTP address of proxy server.
   *
   * @var string
   */
  public $url;

  /**
   * A comma-separated list of IP addresses, IP address ranges, host names, and
   * domain names that should not go through the proxy server. When Google
   * Distributed Cloud sends a request to one of these addresses, hosts, or
   * domains, the request is sent directly.
   *
   * @param string $noProxy
   */
  public function setNoProxy($noProxy)
  {
    $this->noProxy = $noProxy;
  }
  /**
   * @return string
   */
  public function getNoProxy()
  {
    return $this->noProxy;
  }
  /**
   * The HTTP address of proxy server.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminProxy::class, 'Google_Service_GKEOnPrem_VmwareAdminProxy');
