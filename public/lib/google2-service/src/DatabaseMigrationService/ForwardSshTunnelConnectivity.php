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

class ForwardSshTunnelConnectivity extends \Google\Model
{
  /**
   * Required. Hostname for the SSH tunnel.
   *
   * @var string
   */
  public $hostname;
  /**
   * Input only. SSH password.
   *
   * @var string
   */
  public $password;
  /**
   * Port for the SSH tunnel, default value is 22.
   *
   * @var int
   */
  public $port;
  /**
   * Input only. SSH private key.
   *
   * @var string
   */
  public $privateKey;
  /**
   * Required. Username for the SSH tunnel.
   *
   * @var string
   */
  public $username;

  /**
   * Required. Hostname for the SSH tunnel.
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
   * Input only. SSH password.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Port for the SSH tunnel, default value is 22.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Input only. SSH private key.
   *
   * @param string $privateKey
   */
  public function setPrivateKey($privateKey)
  {
    $this->privateKey = $privateKey;
  }
  /**
   * @return string
   */
  public function getPrivateKey()
  {
    return $this->privateKey;
  }
  /**
   * Required. Username for the SSH tunnel.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForwardSshTunnelConnectivity::class, 'Google_Service_DatabaseMigrationService_ForwardSshTunnelConnectivity');
