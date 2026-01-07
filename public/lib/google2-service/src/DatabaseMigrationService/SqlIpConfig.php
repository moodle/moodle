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

namespace Google\Service\DatabaseMigrationService;

class SqlIpConfig extends \Google\Collection
{
  protected $collection_key = 'authorizedNetworks';
  /**
   * Optional. The name of the allocated IP address range for the private IP
   * Cloud SQL instance. This name refers to an already allocated IP range
   * address. If set, the instance IP address will be created in the allocated
   * range. Note that this IP address range can't be modified after the instance
   * is created. If you change the VPC when configuring connectivity settings
   * for the migration job, this field is not relevant.
   *
   * @var string
   */
  public $allocatedIpRange;
  protected $authorizedNetworksType = SqlAclEntry::class;
  protected $authorizedNetworksDataType = 'array';
  /**
   * Whether the instance should be assigned an IPv4 address or not.
   *
   * @var bool
   */
  public $enableIpv4;
  /**
   * The resource link for the VPC network from which the Cloud SQL instance is
   * accessible for private IP. For example,
   * `projects/myProject/global/networks/default`. This setting can be updated,
   * but it cannot be removed after it is set.
   *
   * @var string
   */
  public $privateNetwork;
  /**
   * Whether SSL connections over IP should be enforced or not.
   *
   * @var bool
   */
  public $requireSsl;

  /**
   * Optional. The name of the allocated IP address range for the private IP
   * Cloud SQL instance. This name refers to an already allocated IP range
   * address. If set, the instance IP address will be created in the allocated
   * range. Note that this IP address range can't be modified after the instance
   * is created. If you change the VPC when configuring connectivity settings
   * for the migration job, this field is not relevant.
   *
   * @param string $allocatedIpRange
   */
  public function setAllocatedIpRange($allocatedIpRange)
  {
    $this->allocatedIpRange = $allocatedIpRange;
  }
  /**
   * @return string
   */
  public function getAllocatedIpRange()
  {
    return $this->allocatedIpRange;
  }
  /**
   * The list of external networks that are allowed to connect to the instance
   * using the IP. See
   * https://en.wikipedia.org/wiki/CIDR_notation#CIDR_notation, also known as
   * 'slash' notation (e.g. `192.168.100.0/24`).
   *
   * @param SqlAclEntry[] $authorizedNetworks
   */
  public function setAuthorizedNetworks($authorizedNetworks)
  {
    $this->authorizedNetworks = $authorizedNetworks;
  }
  /**
   * @return SqlAclEntry[]
   */
  public function getAuthorizedNetworks()
  {
    return $this->authorizedNetworks;
  }
  /**
   * Whether the instance should be assigned an IPv4 address or not.
   *
   * @param bool $enableIpv4
   */
  public function setEnableIpv4($enableIpv4)
  {
    $this->enableIpv4 = $enableIpv4;
  }
  /**
   * @return bool
   */
  public function getEnableIpv4()
  {
    return $this->enableIpv4;
  }
  /**
   * The resource link for the VPC network from which the Cloud SQL instance is
   * accessible for private IP. For example,
   * `projects/myProject/global/networks/default`. This setting can be updated,
   * but it cannot be removed after it is set.
   *
   * @param string $privateNetwork
   */
  public function setPrivateNetwork($privateNetwork)
  {
    $this->privateNetwork = $privateNetwork;
  }
  /**
   * @return string
   */
  public function getPrivateNetwork()
  {
    return $this->privateNetwork;
  }
  /**
   * Whether SSL connections over IP should be enforced or not.
   *
   * @param bool $requireSsl
   */
  public function setRequireSsl($requireSsl)
  {
    $this->requireSsl = $requireSsl;
  }
  /**
   * @return bool
   */
  public function getRequireSsl()
  {
    return $this->requireSsl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlIpConfig::class, 'Google_Service_DatabaseMigrationService_SqlIpConfig');
