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

namespace Google\Service\Monitoring;

class UptimeCheckIp extends \Google\Model
{
  /**
   * Default value if no region is specified. Will result in Uptime checks
   * running from all regions.
   */
  public const REGION_REGION_UNSPECIFIED = 'REGION_UNSPECIFIED';
  /**
   * Allows checks to run from locations within the United States of America.
   */
  public const REGION_USA = 'USA';
  /**
   * Allows checks to run from locations within the continent of Europe.
   */
  public const REGION_EUROPE = 'EUROPE';
  /**
   * Allows checks to run from locations within the continent of South America.
   */
  public const REGION_SOUTH_AMERICA = 'SOUTH_AMERICA';
  /**
   * Allows checks to run from locations within the Asia Pacific area (ex:
   * Singapore).
   */
  public const REGION_ASIA_PACIFIC = 'ASIA_PACIFIC';
  /**
   * Allows checks to run from locations within the western United States of
   * America
   */
  public const REGION_USA_OREGON = 'USA_OREGON';
  /**
   * Allows checks to run from locations within the central United States of
   * America
   */
  public const REGION_USA_IOWA = 'USA_IOWA';
  /**
   * Allows checks to run from locations within the eastern United States of
   * America
   */
  public const REGION_USA_VIRGINIA = 'USA_VIRGINIA';
  /**
   * The IP address from which the Uptime check originates. This is a fully
   * specified IP address (not an IP address range). Most IP addresses, as of
   * this publication, are in IPv4 format; however, one should not rely on the
   * IP addresses being in IPv4 format indefinitely, and should support
   * interpreting this field in either IPv4 or IPv6 format.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * A more specific location within the region that typically encodes a
   * particular city/town/metro (and its containing state/province or country)
   * within the broader umbrella region category.
   *
   * @var string
   */
  public $location;
  /**
   * A broad region category in which the IP address is located.
   *
   * @var string
   */
  public $region;

  /**
   * The IP address from which the Uptime check originates. This is a fully
   * specified IP address (not an IP address range). Most IP addresses, as of
   * this publication, are in IPv4 format; however, one should not rely on the
   * IP addresses being in IPv4 format indefinitely, and should support
   * interpreting this field in either IPv4 or IPv6 format.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * A more specific location within the region that typically encodes a
   * particular city/town/metro (and its containing state/province or country)
   * within the broader umbrella region category.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * A broad region category in which the IP address is located.
   *
   * Accepted values: REGION_UNSPECIFIED, USA, EUROPE, SOUTH_AMERICA,
   * ASIA_PACIFIC, USA_OREGON, USA_IOWA, USA_VIRGINIA
   *
   * @param self::REGION_* $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return self::REGION_*
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UptimeCheckIp::class, 'Google_Service_Monitoring_UptimeCheckIp');
