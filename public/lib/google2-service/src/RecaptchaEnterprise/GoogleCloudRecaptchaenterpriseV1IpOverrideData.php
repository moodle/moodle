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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1IpOverrideData extends \Google\Model
{
  /**
   * Default override type that indicates this enum hasn't been specified.
   */
  public const OVERRIDE_TYPE_OVERRIDE_TYPE_UNSPECIFIED = 'OVERRIDE_TYPE_UNSPECIFIED';
  /**
   * Allowlist the IP address; i.e. give a `risk_analysis.score` of 0.9 for all
   * valid assessments.
   */
  public const OVERRIDE_TYPE_ALLOW = 'ALLOW';
  /**
   * Required. The IP address to override (can be IPv4, IPv6 or CIDR). The IP
   * override must be a valid IPv4 or IPv6 address, or a CIDR range. The IP
   * override must be a public IP address. Example of IPv4: 168.192.5.6 Example
   * of IPv6: 2001:0000:130F:0000:0000:09C0:876A:130B Example of IPv4 with CIDR:
   * 168.192.5.0/24 Example of IPv6 with CIDR: 2001:0DB8:1234::/48
   *
   * @var string
   */
  public $ip;
  /**
   * Required. Describes the type of IP override.
   *
   * @var string
   */
  public $overrideType;

  /**
   * Required. The IP address to override (can be IPv4, IPv6 or CIDR). The IP
   * override must be a valid IPv4 or IPv6 address, or a CIDR range. The IP
   * override must be a public IP address. Example of IPv4: 168.192.5.6 Example
   * of IPv6: 2001:0000:130F:0000:0000:09C0:876A:130B Example of IPv4 with CIDR:
   * 168.192.5.0/24 Example of IPv6 with CIDR: 2001:0DB8:1234::/48
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
   * Required. Describes the type of IP override.
   *
   * Accepted values: OVERRIDE_TYPE_UNSPECIFIED, ALLOW
   *
   * @param self::OVERRIDE_TYPE_* $overrideType
   */
  public function setOverrideType($overrideType)
  {
    $this->overrideType = $overrideType;
  }
  /**
   * @return self::OVERRIDE_TYPE_*
   */
  public function getOverrideType()
  {
    return $this->overrideType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1IpOverrideData::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1IpOverrideData');
