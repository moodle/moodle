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

class SqlServerConnectionProfile extends \Google\Model
{
  protected $backupsType = SqlServerBackups::class;
  protected $backupsDataType = '';
  /**
   * If the source is a Cloud SQL database, use this field to provide the Cloud
   * SQL instance ID of the source.
   *
   * @var string
   */
  public $cloudSqlId;
  /**
   * Optional. The project id of the Cloud SQL instance. If not provided, the
   * project id of the connection profile will be used.
   *
   * @var string
   */
  public $cloudSqlProjectId;
  /**
   * Required. The name of the specific database within the host.
   *
   * @var string
   */
  public $database;
  /**
   * Optional. The Database Mirroring (DBM) port of the source SQL Server
   * instance.
   *
   * @var int
   */
  public $dbmPort;
  protected $forwardSshConnectivityType = ForwardSshTunnelConnectivity::class;
  protected $forwardSshConnectivityDataType = '';
  /**
   * Required. The IP or hostname of the source SQL Server database.
   *
   * @var string
   */
  public $host;
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
   * Required. The network port of the source SQL Server database.
   *
   * @var int
   */
  public $port;
  protected $privateConnectivityType = PrivateConnectivity::class;
  protected $privateConnectivityDataType = '';
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
   * The backup details in Cloud Storage for homogeneous migration to Cloud SQL
   * for SQL Server.
   *
   * @param SqlServerBackups $backups
   */
  public function setBackups(SqlServerBackups $backups)
  {
    $this->backups = $backups;
  }
  /**
   * @return SqlServerBackups
   */
  public function getBackups()
  {
    return $this->backups;
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
   * Optional. The project id of the Cloud SQL instance. If not provided, the
   * project id of the connection profile will be used.
   *
   * @param string $cloudSqlProjectId
   */
  public function setCloudSqlProjectId($cloudSqlProjectId)
  {
    $this->cloudSqlProjectId = $cloudSqlProjectId;
  }
  /**
   * @return string
   */
  public function getCloudSqlProjectId()
  {
    return $this->cloudSqlProjectId;
  }
  /**
   * Required. The name of the specific database within the host.
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
   * Optional. The Database Mirroring (DBM) port of the source SQL Server
   * instance.
   *
   * @param int $dbmPort
   */
  public function setDbmPort($dbmPort)
  {
    $this->dbmPort = $dbmPort;
  }
  /**
   * @return int
   */
  public function getDbmPort()
  {
    return $this->dbmPort;
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
   * Required. The IP or hostname of the source SQL Server database.
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
   * Required. The network port of the source SQL Server database.
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
   * Private Service Connect connectivity.
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
   * Static IP connectivity data (default, no additional details needed).
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
class_alias(SqlServerConnectionProfile::class, 'Google_Service_DatabaseMigrationService_SqlServerConnectionProfile');
