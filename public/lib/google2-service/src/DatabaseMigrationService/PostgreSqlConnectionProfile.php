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

class PostgreSqlConnectionProfile extends \Google\Model
{
  public const NETWORK_ARCHITECTURE_NETWORK_ARCHITECTURE_UNSPECIFIED = 'NETWORK_ARCHITECTURE_UNSPECIFIED';
  /**
   * Instance is in Cloud SQL's old producer network architecture.
   */
  public const NETWORK_ARCHITECTURE_NETWORK_ARCHITECTURE_OLD_CSQL_PRODUCER = 'NETWORK_ARCHITECTURE_OLD_CSQL_PRODUCER';
  /**
   * Instance is in Cloud SQL's new producer network architecture.
   */
  public const NETWORK_ARCHITECTURE_NETWORK_ARCHITECTURE_NEW_CSQL_PRODUCER = 'NETWORK_ARCHITECTURE_NEW_CSQL_PRODUCER';
  /**
   * Optional. If the destination is an AlloyDB database, use this field to
   * provide the AlloyDB cluster ID.
   *
   * @var string
   */
  public $alloydbClusterId;
  /**
   * If the source is a Cloud SQL database, use this field to provide the Cloud
   * SQL instance ID of the source.
   *
   * @var string
   */
  public $cloudSqlId;
  /**
   * Optional. The name of the specific database within the host.
   *
   * @var string
   */
  public $database;
  /**
   * Required. The IP or hostname of the source PostgreSQL database.
   *
   * @var string
   */
  public $host;
  /**
   * Output only. If the source is a Cloud SQL database, this field indicates
   * the network architecture it's associated with.
   *
   * @var string
   */
  public $networkArchitecture;
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
   * Output only. Indicates If this connection profile password is stored.
   *
   * @var bool
   */
  public $passwordSet;
  /**
   * Required. The network port of the source PostgreSQL database.
   *
   * @var int
   */
  public $port;
  protected $privateServiceConnectConnectivityType = PrivateServiceConnectConnectivity::class;
  protected $privateServiceConnectConnectivityDataType = '';
  protected $sslType = SslConfig::class;
  protected $sslDataType = '';
  protected $staticIpConnectivityType = StaticIpConnectivity::class;
  protected $staticIpConnectivityDataType = '';
  /**
   * Required. The username that Database Migration Service will use to connect
   * to the database. The value is encrypted when stored in Database Migration
   * Service.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. If the destination is an AlloyDB database, use this field to
   * provide the AlloyDB cluster ID.
   *
   * @param string $alloydbClusterId
   */
  public function setAlloydbClusterId($alloydbClusterId)
  {
    $this->alloydbClusterId = $alloydbClusterId;
  }
  /**
   * @return string
   */
  public function getAlloydbClusterId()
  {
    return $this->alloydbClusterId;
  }
  /**
   * If the source is a Cloud SQL database, use this field to provide the Cloud
   * SQL instance ID of the source.
   *
   * @param string $cloudSqlId
   */
  public function setCloudSqlId($cloudSqlId)
  {
    $this->cloudSqlId = $cloudSqlId;
  }
  /**
   * @return string
   */
  public function getCloudSqlId()
  {
    return $this->cloudSqlId;
  }
  /**
   * Optional. The name of the specific database within the host.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Required. The IP or hostname of the source PostgreSQL database.
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
   * Output only. If the source is a Cloud SQL database, this field indicates
   * the network architecture it's associated with.
   *
   * Accepted values: NETWORK_ARCHITECTURE_UNSPECIFIED,
   * NETWORK_ARCHITECTURE_OLD_CSQL_PRODUCER,
   * NETWORK_ARCHITECTURE_NEW_CSQL_PRODUCER
   *
   * @param self::NETWORK_ARCHITECTURE_* $networkArchitecture
   */
  public function setNetworkArchitecture($networkArchitecture)
  {
    $this->networkArchitecture = $networkArchitecture;
  }
  /**
   * @return self::NETWORK_ARCHITECTURE_*
   */
  public function getNetworkArchitecture()
  {
    return $this->networkArchitecture;
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
   * Output only. Indicates If this connection profile password is stored.
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
   * Required. The network port of the source PostgreSQL database.
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
   * Private service connect connectivity.
   *
   * @param PrivateServiceConnectConnectivity $privateServiceConnectConnectivity
   */
  public function setPrivateServiceConnectConnectivity(PrivateServiceConnectConnectivity $privateServiceConnectConnectivity)
  {
    $this->privateServiceConnectConnectivity = $privateServiceConnectConnectivity;
  }
  /**
   * @return PrivateServiceConnectConnectivity
   */
  public function getPrivateServiceConnectConnectivity()
  {
    return $this->privateServiceConnectConnectivity;
  }
  /**
   * SSL configuration for the destination to connect to the source database.
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
   * Static ip connectivity data (default, no additional details needed).
   *
   * @param StaticIpConnectivity $staticIpConnectivity
   */
  public function setStaticIpConnectivity(StaticIpConnectivity $staticIpConnectivity)
  {
    $this->staticIpConnectivity = $staticIpConnectivity;
  }
  /**
   * @return StaticIpConnectivity
   */
  public function getStaticIpConnectivity()
  {
    return $this->staticIpConnectivity;
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
class_alias(PostgreSqlConnectionProfile::class, 'Google_Service_DatabaseMigrationService_PostgreSqlConnectionProfile');
