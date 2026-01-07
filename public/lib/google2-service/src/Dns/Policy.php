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

class Policy extends \Google\Collection
{
  protected $collection_key = 'networks';
  protected $alternativeNameServerConfigType = PolicyAlternativeNameServerConfig::class;
  protected $alternativeNameServerConfigDataType = '';
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the policy's function.
   *
   * @var string
   */
  public $description;
  protected $dns64ConfigType = PolicyDns64Config::class;
  protected $dns64ConfigDataType = '';
  /**
   * Allows networks bound to this policy to receive DNS queries sent by VMs or
   * applications over VPN connections. When enabled, a virtual IP address is
   * allocated from each of the subnetworks that are bound to this policy.
   *
   * @var bool
   */
  public $enableInboundForwarding;
  /**
   * Controls whether logging is enabled for the networks bound to this policy.
   * Defaults to no logging if not set.
   *
   * @var bool
   */
  public $enableLogging;
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @var string
   */
  public $id;
  /**
   * @var string
   */
  public $kind;
  /**
   * User-assigned name for this policy.
   *
   * @var string
   */
  public $name;
  protected $networksType = PolicyNetwork::class;
  protected $networksDataType = 'array';

  /**
   * Sets an alternative name server for the associated networks. When
   * specified, all DNS queries are forwarded to a name server that you choose.
   * Names such as .internal are not available when an alternative name server
   * is specified.
   *
   * @param PolicyAlternativeNameServerConfig $alternativeNameServerConfig
   */
  public function setAlternativeNameServerConfig(PolicyAlternativeNameServerConfig $alternativeNameServerConfig)
  {
    $this->alternativeNameServerConfig = $alternativeNameServerConfig;
  }
  /**
   * @return PolicyAlternativeNameServerConfig
   */
  public function getAlternativeNameServerConfig()
  {
    return $this->alternativeNameServerConfig;
  }
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the policy's function.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Configurations related to DNS64 for this policy.
   *
   * @param PolicyDns64Config $dns64Config
   */
  public function setDns64Config(PolicyDns64Config $dns64Config)
  {
    $this->dns64Config = $dns64Config;
  }
  /**
   * @return PolicyDns64Config
   */
  public function getDns64Config()
  {
    return $this->dns64Config;
  }
  /**
   * Allows networks bound to this policy to receive DNS queries sent by VMs or
   * applications over VPN connections. When enabled, a virtual IP address is
   * allocated from each of the subnetworks that are bound to this policy.
   *
   * @param bool $enableInboundForwarding
   */
  public function setEnableInboundForwarding($enableInboundForwarding)
  {
    $this->enableInboundForwarding = $enableInboundForwarding;
  }
  /**
   * @return bool
   */
  public function getEnableInboundForwarding()
  {
    return $this->enableInboundForwarding;
  }
  /**
   * Controls whether logging is enabled for the networks bound to this policy.
   * Defaults to no logging if not set.
   *
   * @param bool $enableLogging
   */
  public function setEnableLogging($enableLogging)
  {
    $this->enableLogging = $enableLogging;
  }
  /**
   * @return bool
   */
  public function getEnableLogging()
  {
    return $this->enableLogging;
  }
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
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
  /**
   * User-assigned name for this policy.
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
   * List of network names specifying networks to which this policy is applied.
   *
   * @param PolicyNetwork[] $networks
   */
  public function setNetworks($networks)
  {
    $this->networks = $networks;
  }
  /**
   * @return PolicyNetwork[]
   */
  public function getNetworks()
  {
    return $this->networks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policy::class, 'Google_Service_Dns_Policy');
