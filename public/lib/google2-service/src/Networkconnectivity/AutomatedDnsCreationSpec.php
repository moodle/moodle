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

class AutomatedDnsCreationSpec extends \Google\Model
{
  /**
   * Required. The DNS suffix to use for the DNS record. Must end with a dot.
   * This should be a valid DNS domain name as per RFC 1035. Each label (between
   * dots) can contain letters, digits, and hyphens, and must not start or end
   * with a hyphen. Example: "my-service.example.com.", "internal."
   *
   * @var string
   */
  public $dnsSuffix;
  /**
   * Required. The hostname (the first label of the FQDN) to use for the DNS
   * record. This should be a valid DNS label as per RFC 1035. Generally, this
   * means the hostname can contain letters, digits, and hyphens, and must not
   * start or end with a hyphen. Example: "my-instance", "db-1"
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. The Time To Live for the DNS record, in seconds. If not provided,
   * a default of 30 seconds will be used.
   *
   * @var string
   */
  public $ttl;

  /**
   * Required. The DNS suffix to use for the DNS record. Must end with a dot.
   * This should be a valid DNS domain name as per RFC 1035. Each label (between
   * dots) can contain letters, digits, and hyphens, and must not start or end
   * with a hyphen. Example: "my-service.example.com.", "internal."
   *
   * @param string $dnsSuffix
   */
  public function setDnsSuffix($dnsSuffix)
  {
    $this->dnsSuffix = $dnsSuffix;
  }
  /**
   * @return string
   */
  public function getDnsSuffix()
  {
    return $this->dnsSuffix;
  }
  /**
   * Required. The hostname (the first label of the FQDN) to use for the DNS
   * record. This should be a valid DNS label as per RFC 1035. Generally, this
   * means the hostname can contain letters, digits, and hyphens, and must not
   * start or end with a hyphen. Example: "my-instance", "db-1"
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
   * Optional. The Time To Live for the DNS record, in seconds. If not provided,
   * a default of 30 seconds will be used.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomatedDnsCreationSpec::class, 'Google_Service_Networkconnectivity_AutomatedDnsCreationSpec');
