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

namespace Google\Service\SQLAdmin;

class IpMapping extends \Google\Model
{
  /**
   * This is an unknown IP address type.
   */
  public const TYPE_SQL_IP_ADDRESS_TYPE_UNSPECIFIED = 'SQL_IP_ADDRESS_TYPE_UNSPECIFIED';
  /**
   * IP address the customer is supposed to connect to. Usually this is the load
   * balancer's IP address
   */
  public const TYPE_PRIMARY = 'PRIMARY';
  /**
   * Source IP address of the connection a read replica establishes to its
   * external primary instance. This IP address can be allowlisted by the
   * customer in case it has a firewall that filters incoming connection to its
   * on premises primary instance.
   */
  public const TYPE_OUTGOING = 'OUTGOING';
  /**
   * Private IP used when using private IPs and network peering.
   */
  public const TYPE_PRIVATE = 'PRIVATE';
  /**
   * V1 IP of a migrated instance. We want the user to decommission this IP as
   * soon as the migration is complete. Note: V1 instances with V1 ip addresses
   * will be counted as PRIMARY.
   */
  public const TYPE_MIGRATED_1ST_GEN = 'MIGRATED_1ST_GEN';
  /**
   * The IP address assigned.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The due time for this IP to be retired in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`. This field is only available when the IP is
   * scheduled to be retired.
   *
   * @var string
   */
  public $timeToRetire;
  /**
   * The type of this IP address. A `PRIMARY` address is a public address that
   * can accept incoming connections. A `PRIVATE` address is a private address
   * that can accept incoming connections. An `OUTGOING` address is the source
   * address of connections originating from the instance, if supported.
   *
   * @var string
   */
  public $type;

  /**
   * The IP address assigned.
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
   * The due time for this IP to be retired in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`. This field is only available when the IP is
   * scheduled to be retired.
   *
   * @param string $timeToRetire
   */
  public function setTimeToRetire($timeToRetire)
  {
    $this->timeToRetire = $timeToRetire;
  }
  /**
   * @return string
   */
  public function getTimeToRetire()
  {
    return $this->timeToRetire;
  }
  /**
   * The type of this IP address. A `PRIMARY` address is a public address that
   * can accept incoming connections. A `PRIVATE` address is a private address
   * that can accept incoming connections. An `OUTGOING` address is the source
   * address of connections originating from the instance, if supported.
   *
   * Accepted values: SQL_IP_ADDRESS_TYPE_UNSPECIFIED, PRIMARY, OUTGOING,
   * PRIVATE, MIGRATED_1ST_GEN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IpMapping::class, 'Google_Service_SQLAdmin_IpMapping');
