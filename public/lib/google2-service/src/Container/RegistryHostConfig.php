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

namespace Google\Service\Container;

class RegistryHostConfig extends \Google\Collection
{
  protected $collection_key = 'hosts';
  protected $hostsType = HostConfig::class;
  protected $hostsDataType = 'array';
  /**
   * Defines the host name of the registry server, which will be used to create
   * configuration file as /etc/containerd/hosts.d//hosts.toml. It supports
   * fully qualified domain names (FQDN) and IP addresses: Specifying port is
   * supported. Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @var string
   */
  public $server;

  /**
   * HostConfig configures a list of host-specific configurations for the
   * server. Each server can have at most 10 host configurations.
   *
   * @param HostConfig[] $hosts
   */
  public function setHosts($hosts)
  {
    $this->hosts = $hosts;
  }
  /**
   * @return HostConfig[]
   */
  public function getHosts()
  {
    return $this->hosts;
  }
  /**
   * Defines the host name of the registry server, which will be used to create
   * configuration file as /etc/containerd/hosts.d//hosts.toml. It supports
   * fully qualified domain names (FQDN) and IP addresses: Specifying port is
   * supported. Wildcards are NOT supported. Examples: - my.customdomain.com -
   * 10.0.1.2:5000
   *
   * @param string $server
   */
  public function setServer($server)
  {
    $this->server = $server;
  }
  /**
   * @return string
   */
  public function getServer()
  {
    return $this->server;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegistryHostConfig::class, 'Google_Service_Container_RegistryHostConfig');
