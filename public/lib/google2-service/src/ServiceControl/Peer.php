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

namespace Google\Service\ServiceControl;

class Peer extends \Google\Model
{
  /**
   * The IP address of the peer.
   *
   * @var string
   */
  public $ip;
  /**
   * The labels associated with the peer.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The network port of the peer.
   *
   * @var string
   */
  public $port;
  /**
   * The identity of this peer. Similar to `Request.auth.principal`, but
   * relative to the peer instead of the request. For example, the identity
   * associated with a load balancer that forwarded the request.
   *
   * @var string
   */
  public $principal;
  /**
   * The CLDR country/region code associated with the above IP address. If the
   * IP address is private, the `region_code` should reflect the physical
   * location where this peer is running.
   *
   * @var string
   */
  public $regionCode;

  /**
   * The IP address of the peer.
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }
  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }
  /**
   * The labels associated with the peer.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The network port of the peer.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * The identity of this peer. Similar to `Request.auth.principal`, but
   * relative to the peer instead of the request. For example, the identity
   * associated with a load balancer that forwarded the request.
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
  /**
   * The CLDR country/region code associated with the above IP address. If the
   * IP address is private, the `region_code` should reflect the physical
   * location where this peer is running.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Peer::class, 'Google_Service_ServiceControl_Peer');
