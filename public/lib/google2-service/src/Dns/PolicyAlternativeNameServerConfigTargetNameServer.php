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

namespace Google\Service\Dns;

class PolicyAlternativeNameServerConfigTargetNameServer extends \Google\Model
{
  /**
   * Cloud DNS makes forwarding decision based on IP address ranges; that is,
   * RFC1918 addresses forward to the target through the VPC and non-RFC1918
   * addresses forward to the target through the internet
   */
  public const FORWARDING_PATH_default = 'default';
  /**
   * Cloud DNS always forwards to this target through the VPC.
   */
  public const FORWARDING_PATH_private = 'private';
  /**
   * Forwarding path for this TargetNameServer. If unset or set to DEFAULT,
   * Cloud DNS makes forwarding decisions based on address ranges; that is,
   * RFC1918 addresses go to the VPC network, non-RFC1918 addresses go to the
   * internet. When set to PRIVATE, Cloud DNS always sends queries through the
   * VPC network for this target.
   *
   * @var string
   */
  public $forwardingPath;
  /**
   * IPv4 address to forward queries to.
   *
   * @var string
   */
  public $ipv4Address;
  /**
   * IPv6 address to forward to. Does not accept both fields (ipv4 & ipv6) being
   * populated. Public preview as of November 2022.
   *
   * @var string
   */
  public $ipv6Address;
  /**
   * @var string
   */
  public $kind;

  /**
   * Forwarding path for this TargetNameServer. If unset or set to DEFAULT,
   * Cloud DNS makes forwarding decisions based on address ranges; that is,
   * RFC1918 addresses go to the VPC network, non-RFC1918 addresses go to the
   * internet. When set to PRIVATE, Cloud DNS always sends queries through the
   * VPC network for this target.
   *
   * Accepted values: default, private
   *
   * @param self::FORWARDING_PATH_* $forwardingPath
   */
  public function setForwardingPath($forwardingPath)
  {
    $this->forwardingPath = $forwardingPath;
  }
  /**
   * @return self::FORWARDING_PATH_*
   */
  public function getForwardingPath()
  {
    return $this->forwardingPath;
  }
  /**
   * IPv4 address to forward queries to.
   *
   * @param string $ipv4Address
   */
  public function setIpv4Address($ipv4Address)
  {
    $this->ipv4Address = $ipv4Address;
  }
  /**
   * @return string
   */
  public function getIpv4Address()
  {
    return $this->ipv4Address;
  }
  /**
   * IPv6 address to forward to. Does not accept both fields (ipv4 & ipv6) being
   * populated. Public preview as of November 2022.
   *
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyAlternativeNameServerConfigTargetNameServer::class, 'Google_Service_Dns_PolicyAlternativeNameServerConfigTargetNameServer');
