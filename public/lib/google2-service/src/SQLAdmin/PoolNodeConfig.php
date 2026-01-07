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

class PoolNodeConfig extends \Google\Collection
{
  /**
   * The state of the instance is unknown.
   */
  public const STATE_SQL_INSTANCE_STATE_UNSPECIFIED = 'SQL_INSTANCE_STATE_UNSPECIFIED';
  /**
   * The instance is running, or has been stopped by owner.
   */
  public const STATE_RUNNABLE = 'RUNNABLE';
  /**
   * The instance is not available, for example due to problems with billing.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The instance is being deleted.
   */
  public const STATE_PENDING_DELETE = 'PENDING_DELETE';
  /**
   * The instance is being created.
   */
  public const STATE_PENDING_CREATE = 'PENDING_CREATE';
  /**
   * The instance is down for maintenance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The creation of the instance failed or a fatal error occurred during
   * maintenance.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Deprecated
   *
   * @deprecated
   */
  public const STATE_ONLINE_MAINTENANCE = 'ONLINE_MAINTENANCE';
  /**
   * (Applicable to read pool nodes only.) The read pool node needs to be
   * repaired. The database might be unavailable.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  protected $collection_key = 'pscAutoConnections';
  /**
   * Output only. The DNS name of the read pool node.
   *
   * @var string
   */
  public $dnsName;
  protected $dnsNamesType = DnsNameMapping::class;
  protected $dnsNamesDataType = 'array';
  /**
   * Output only. The zone of the read pool node.
   *
   * @var string
   */
  public $gceZone;
  protected $ipAddressesType = IpMapping::class;
  protected $ipAddressesDataType = 'array';
  /**
   * Output only. The name of the read pool node, to be used for retrieving
   * metrics and logs.
   *
   * @var string
   */
  public $name;
  protected $pscAutoConnectionsType = PscAutoConnectionConfig::class;
  protected $pscAutoConnectionsDataType = 'array';
  /**
   * Output only. The Private Service Connect (PSC) service attachment of the
   * read pool node.
   *
   * @var string
   */
  public $pscServiceAttachmentLink;
  /**
   * Output only. The current state of the read pool node.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The DNS name of the read pool node.
   *
   * @param string $dnsName
   */
  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }
  /**
   * @return string
   */
  public function getDnsName()
  {
    return $this->dnsName;
  }
  /**
   * Output only. The list of DNS names used by this read pool node.
   *
   * @param DnsNameMapping[] $dnsNames
   */
  public function setDnsNames($dnsNames)
  {
    $this->dnsNames = $dnsNames;
  }
  /**
   * @return DnsNameMapping[]
   */
  public function getDnsNames()
  {
    return $this->dnsNames;
  }
  /**
   * Output only. The zone of the read pool node.
   *
   * @param string $gceZone
   */
  public function setGceZone($gceZone)
  {
    $this->gceZone = $gceZone;
  }
  /**
   * @return string
   */
  public function getGceZone()
  {
    return $this->gceZone;
  }
  /**
   * Output only. Mappings containing IP addresses that can be used to connect
   * to the read pool node.
   *
   * @param IpMapping[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return IpMapping[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * Output only. The name of the read pool node, to be used for retrieving
   * metrics and logs.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The list of settings for requested automatically-setup Private
   * Service Connect (PSC) consumer endpoints that can be used to connect to
   * this read pool node.
   *
   * @param PscAutoConnectionConfig[] $pscAutoConnections
   */
  public function setPscAutoConnections($pscAutoConnections)
  {
    $this->pscAutoConnections = $pscAutoConnections;
  }
  /**
   * @return PscAutoConnectionConfig[]
   */
  public function getPscAutoConnections()
  {
    return $this->pscAutoConnections;
  }
  /**
   * Output only. The Private Service Connect (PSC) service attachment of the
   * read pool node.
   *
   * @param string $pscServiceAttachmentLink
   */
  public function setPscServiceAttachmentLink($pscServiceAttachmentLink)
  {
    $this->pscServiceAttachmentLink = $pscServiceAttachmentLink;
  }
  /**
   * @return string
   */
  public function getPscServiceAttachmentLink()
  {
    return $this->pscServiceAttachmentLink;
  }
  /**
   * Output only. The current state of the read pool node.
   *
   * Accepted values: SQL_INSTANCE_STATE_UNSPECIFIED, RUNNABLE, SUSPENDED,
   * PENDING_DELETE, PENDING_CREATE, MAINTENANCE, FAILED, ONLINE_MAINTENANCE,
   * REPAIRING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PoolNodeConfig::class, 'Google_Service_SQLAdmin_PoolNodeConfig');
