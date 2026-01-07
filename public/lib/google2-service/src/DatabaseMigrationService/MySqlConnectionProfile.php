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

class MySqlConnectionProfile extends \Google\Model
{
  /**
   * If the source is a Cloud SQL database, use this field to provide the Cloud
   * SQL instance ID of the source.
   *
   * @var string
   */
  public $cloudSqlId;
  /**
   * Required. The IP or hostname of the source MySQL database.
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
   * Output only. Indicates If this connection profile password is stored.
   *
   * @var bool
   */
  public $passwordSet;
  /**
   * Required. The network port of the source MySQL database.
   *
   * @var int
   */
  public $port;
  protected $sslType = SslConfig::class;
  protected $sslDataType = '';
  /**
   * Required. The username that Database Migration Service will use to connect
   * to the database. The value is encrypted when stored in Database Migration
   * Service.
   *
   * @var string
   */
  public $username;

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
   * Required. The IP or hostname of the source MySQL database.
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
   * Required. The network port of the source MySQL database.
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
class_alias(MySqlConnectionProfile::class, 'Google_Service_DatabaseMigrationService_MySqlConnectionProfile');
