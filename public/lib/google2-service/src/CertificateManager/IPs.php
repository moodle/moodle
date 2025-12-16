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

namespace Google\Service\CertificateManager;

class IPs extends \Google\Collection
{
  protected $collection_key = 'servingOnAltPorts';
  /**
   * Output only. The list of IP addresses resolved from the domain's A/AAAA
   * records. Can contain both ipv4 and ipv6 addresses.
   *
   * @var string[]
   */
  public $resolved;
  /**
   * Output only. The list of IP addresses, where the certificate is attached
   * and port 443 is open.
   *
   * @var string[]
   */
  public $serving;
  /**
   * Output only. The list of IP addresses, where the certificate is attached,
   * but port 443 is not open.
   *
   * @var string[]
   */
  public $servingOnAltPorts;

  /**
   * Output only. The list of IP addresses resolved from the domain's A/AAAA
   * records. Can contain both ipv4 and ipv6 addresses.
   *
   * @param string[] $resolved
   */
  public function setResolved($resolved)
  {
    $this->resolved = $resolved;
  }
  /**
   * @return string[]
   */
  public function getResolved()
  {
    return $this->resolved;
  }
  /**
   * Output only. The list of IP addresses, where the certificate is attached
   * and port 443 is open.
   *
   * @param string[] $serving
   */
  public function setServing($serving)
  {
    $this->serving = $serving;
  }
  /**
   * @return string[]
   */
  public function getServing()
  {
    return $this->serving;
  }
  /**
   * Output only. The list of IP addresses, where the certificate is attached,
   * but port 443 is not open.
   *
   * @param string[] $servingOnAltPorts
   */
  public function setServingOnAltPorts($servingOnAltPorts)
  {
    $this->servingOnAltPorts = $servingOnAltPorts;
  }
  /**
   * @return string[]
   */
  public function getServingOnAltPorts()
  {
    return $this->servingOnAltPorts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IPs::class, 'Google_Service_CertificateManager_IPs');
