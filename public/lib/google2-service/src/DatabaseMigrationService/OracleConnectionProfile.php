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

class OracleConnectionProfile extends \Google\Model
{
  /**
   * Required. Database service for the Oracle connection.
   *
   * @var string
   */
  public $databaseService;
  protected $forwardSshConnectivityType = ForwardSshTunnelConnectivity::class;
  protected $forwardSshConnectivityDataType = '';
  /**
   * Required. The IP or hostname of the source Oracle database.
   *
   * @var string
   */
  public $host;
  protected $oracleAsmConfigType = OracleAsmConfig::class;
  protected $oracleAsmConfigDataType = '';
  /**
   * Required. Input only. The password for the user that Database Migration
   * Service will be using to connect to the database. This field is not
   * returned on request, and the value is encrypted when stored in Database
   * Migration Service.
   *
   * @var string
   */
  public $password;
  /**
   * Output only. Indicates whether a new password is included in the request.
   *
   * @var bool
   */
  public $passwordSet;
  /**
   * Required. The network port of the source Oracle database.
   *
   * @var int
   */
  public $port;
  protected $privateConnectivityType = PrivateConnectivity::class;
  protected $privateConnectivityDataType = '';
  protected $sslType = SslConfig::class;
  protected $sslDataType = '';
  protected $staticServiceIpConnectivityType = StaticServiceIpConnectivity::class;
  protected $staticServiceIpConnectivityDataType = '';
  /**
   * Required. The username that Database Migration Service will use to connect
   * to the database. The value is encrypted when stored in Database Migration
   * Service.
   *
   * @var string
   */
  public $username;

  /**
   * Required. Database service for the Oracle connection.
   *
   * @param string $databaseService
   */
  public function setDatabaseService($databaseService)
  {
    $this->databaseService = $databaseService;
  }
  /**
   * @return string
   */
  public function getDatabaseService()
  {
    return $this->databaseService;
  }
  /**
   * Forward SSH tunnel connectivity.
   *
   * @param ForwardSshTunnelConnectivity $forwardSshConnectivity
   */
  public function setForwardSshConnectivity(ForwardSshTunnelConnectivity $forwardSshConnectivity)
  {
    $this->forwardSshConnectivity = $forwardSshConnectivity;
  }
  /**
   * @return ForwardSshTunnelConnectivity
   */
  public function getForwardSshConnectivity()
  {
    return $this->forwardSshConnectivity;
  }
  /**
   * Required. The IP or hostname of the source Oracle database.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Optional. Configuration for Oracle ASM connection.
   *
   * @param OracleAsmConfig $oracleAsmConfig
   */
  public function setOracleAsmConfig(OracleAsmConfig $oracleAsmConfig)
  {
    $this->oracleAsmConfig = $oracleAsmConfig;
  }
  /**
   * @return OracleAsmConfig
   */
  public function getOracleAsmConfig()
  {
    return $this->oracleAsmConfig;
  }
  /**
   * Required. Input only. The password for the user that Database Migration
   * Service will be using to connect to the database. This field is not
   * returned on request, and the value is encrypted when stored in Database
   * Migration Service.
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
   * Output only. Indicates whether a new password is included in the request.
   *
   * @param bool $passwordSet
   */
  public function setPasswordSet($passwordSet)
  {
    $this->passwordSet = $passwordSet;
  }
  /**
   * @return bool
   */
  public function getPasswordSet()
  {
    return $this->passwordSet;
  }
  /**
   * Required. The network port of the source Oracle database.
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
   * Private connectivity.
   *
   * @param PrivateConnectivity $privateConnectivity
   */
  public function setPrivateConnectivity(PrivateConnectivity $privateConnectivity)
  {
    $this->privateConnectivity = $privateConnectivity;
  }
  /**
   * @return PrivateConnectivity
   */
  public function getPrivateConnectivity()
  {
    return $this->privateConnectivity;
  }
  /**
   * SSL configuration for the connection to the source Oracle database. * Only
   * `SERVER_ONLY` configuration is supported for Oracle SSL. * SSL is supported
   * for Oracle versions 12 and above.
   *
   * @param SslConfig $ssl
   */
  public function setSsl(SslConfig $ssl)
  {
    $this->ssl = $ssl;
  }
  /**
   * @return SslConfig
   */
  public function getSsl()
  {
    return $this->ssl;
  }
  /**
   * Static Service IP connectivity.
   *
   * @param StaticServiceIpConnectivity $staticServiceIpConnectivity
   */
  public function setStaticServiceIpConnectivity(StaticServiceIpConnectivity $staticServiceIpConnectivity)
  {
    $this->staticServiceIpConnectivity = $staticServiceIpConnectivity;
  }
  /**
   * @return StaticServiceIpConnectivity
   */
  public function getStaticServiceIpConnectivity()
  {
    return $this->staticServiceIpConnectivity;
  }
  /**
   * Required. The username that Database Migration Service will use to connect
   * to the database. The value is encrypted when stored in Database Migration
   * Service.
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
class_alias(OracleConnectionProfile::class, 'Google_Service_DatabaseMigrationService_OracleConnectionProfile');
