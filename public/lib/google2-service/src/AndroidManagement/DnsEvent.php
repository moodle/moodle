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

class DnsEvent extends \Google\Collection
{
  protected $collection_key = 'ipAddresses';
  /**
   * The hostname that was looked up.
   *
   * @var string
   */
  public $hostname;
  /**
   * The (possibly truncated) list of the IP addresses returned for DNS lookup
   * (max 10 IPv4 or IPv6 addresses).
   *
   * @var string[]
   */
  public $ipAddresses;
  /**
   * The package name of the UID that performed the DNS lookup.
   *
   * @var string
   */
  public $packageName;
  /**
   * The number of IP addresses returned from the DNS lookup event. May be
   * higher than the amount of ip_addresses if there were too many addresses to
   * log.
   *
   * @var string
   */
  public $totalIpAddressesReturned;

  /**
   * The hostname that was looked up.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * The (possibly truncated) list of the IP addresses returned for DNS lookup
   * (max 10 IPv4 or IPv6 addresses).
   *
   * @param string[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return string[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * The package name of the UID that performed the DNS lookup.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * The number of IP addresses returned from the DNS lookup event. May be
   * higher than the amount of ip_addresses if there were too many addresses to
   * log.
   *
   * @param string $totalIpAddressesReturned
   */
  public function setTotalIpAddressesReturned($totalIpAddressesReturned)
  {
    $this->totalIpAddressesReturned = $totalIpAddressesReturned;
  }
  /**
   * @return string
   */
  public function getTotalIpAddressesReturned()
  {
    return $this->totalIpAddressesReturned;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsEvent::class, 'Google_Service_AndroidManagement_DnsEvent');
