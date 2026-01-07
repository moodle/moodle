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

namespace Google\Service\FirebaseAppHosting;

class DnsUpdates extends \Google\Collection
{
  protected $collection_key = 'discovered';
  /**
   * Output only. The last time App Hosting checked your custom domain's DNS
   * records.
   *
   * @var string
   */
  public $checkTime;
  protected $desiredType = DnsRecordSet::class;
  protected $desiredDataType = 'array';
  protected $discoveredType = DnsRecordSet::class;
  protected $discoveredDataType = 'array';
  /**
   * Output only. The domain name the DNS updates pertain to.
   *
   * @var string
   */
  public $domainName;

  /**
   * Output only. The last time App Hosting checked your custom domain's DNS
   * records.
   *
   * @param string $checkTime
   */
  public function setCheckTime($checkTime)
  {
    $this->checkTime = $checkTime;
  }
  /**
   * @return string
   */
  public function getCheckTime()
  {
    return $this->checkTime;
  }
  /**
   * Output only. The set of DNS records App Hosting needs in order to be able
   * to serve secure content on the domain.
   *
   * @param DnsRecordSet[] $desired
   */
  public function setDesired($desired)
  {
    $this->desired = $desired;
  }
  /**
   * @return DnsRecordSet[]
   */
  public function getDesired()
  {
    return $this->desired;
  }
  /**
   * Output only. The set of DNS records App Hosting discovered when inspecting
   * a domain.
   *
   * @param DnsRecordSet[] $discovered
   */
  public function setDiscovered($discovered)
  {
    $this->discovered = $discovered;
  }
  /**
   * @return DnsRecordSet[]
   */
  public function getDiscovered()
  {
    return $this->discovered;
  }
  /**
   * Output only. The domain name the DNS updates pertain to.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsUpdates::class, 'Google_Service_FirebaseAppHosting_DnsUpdates');
