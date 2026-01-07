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

namespace Google\Service\Networkconnectivity;

class Filter extends \Google\Model
{
  /**
   * Default value.
   */
  public const PROTOCOL_VERSION_PROTOCOL_VERSION_UNSPECIFIED = 'PROTOCOL_VERSION_UNSPECIFIED';
  /**
   * The PBR is for IPv4 internet protocol traffic.
   */
  public const PROTOCOL_VERSION_IPV4 = 'IPV4';
  /**
   * The PBR is for IPv6 internet protocol traffic.
   */
  public const PROTOCOL_VERSION_IPV6 = 'IPV6';
  /**
   * Optional. The destination IP range of outgoing packets that this policy-
   * based route applies to. Default is "0.0.0.0/0" if protocol version is IPv4
   * and "::/0" if protocol version is IPv6.
   *
   * @var string
   */
  public $destRange;
  /**
   * Optional. The IP protocol that this policy-based route applies to. Valid
   * values are 'TCP', 'UDP', and 'ALL'. Default is 'ALL'.
   *
   * @var string
   */
  public $ipProtocol;
  /**
   * Required. Internet protocol versions this policy-based route applies to.
   * IPV4 and IPV6 is supported.
   *
   * @var string
   */
  public $protocolVersion;
  /**
   * Optional. The source IP range of outgoing packets that this policy-based
   * route applies to. Default is "0.0.0.0/0" if protocol version is IPv4 and
   * "::/0" if protocol version is IPv6.
   *
   * @var string
   */
  public $srcRange;

  /**
   * Optional. The destination IP range of outgoing packets that this policy-
   * based route applies to. Default is "0.0.0.0/0" if protocol version is IPv4
   * and "::/0" if protocol version is IPv6.
   *
   * @param string $destRange
   */
  public function setDestRange($destRange)
  {
    $this->destRange = $destRange;
  }
  /**
   * @return string
   */
  public function getDestRange()
  {
    return $this->destRange;
  }
  /**
   * Optional. The IP protocol that this policy-based route applies to. Valid
   * values are 'TCP', 'UDP', and 'ALL'. Default is 'ALL'.
   *
   * @param string $ipProtocol
   */
  public function setIpProtocol($ipProtocol)
  {
    $this->ipProtocol = $ipProtocol;
  }
  /**
   * @return string
   */
  public function getIpProtocol()
  {
    return $this->ipProtocol;
  }
  /**
   * Required. Internet protocol versions this policy-based route applies to.
   * IPV4 and IPV6 is supported.
   *
   * Accepted values: PROTOCOL_VERSION_UNSPECIFIED, IPV4, IPV6
   *
   * @param self::PROTOCOL_VERSION_* $protocolVersion
   */
  public function setProtocolVersion($protocolVersion)
  {
    $this->protocolVersion = $protocolVersion;
  }
  /**
   * @return self::PROTOCOL_VERSION_*
   */
  public function getProtocolVersion()
  {
    return $this->protocolVersion;
  }
  /**
   * Optional. The source IP range of outgoing packets that this policy-based
   * route applies to. Default is "0.0.0.0/0" if protocol version is IPv4 and
   * "::/0" if protocol version is IPv6.
   *
   * @param string $srcRange
   */
  public function setSrcRange($srcRange)
  {
    $this->srcRange = $srcRange;
  }
  /**
   * @return string
   */
  public function getSrcRange()
  {
    return $this->srcRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Filter::class, 'Google_Service_Networkconnectivity_Filter');
