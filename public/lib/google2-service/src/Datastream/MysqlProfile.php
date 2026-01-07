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

namespace Google\Service\Datastream;

class MysqlProfile extends \Google\Model
{
  /**
   * Required. Hostname for the MySQL connection.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. Input only. Password for the MySQL connection. Mutually exclusive
   * with the `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Port for the MySQL connection, default value is 3306.
   *
   * @var int
   */
  public $port;
  /**
   * Optional. A reference to a Secret Manager resource name storing the MySQL
   * connection password. Mutually exclusive with the `password` field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  protected $sslConfigType = MysqlSslConfig::class;
  protected $sslConfigDataType = '';
  /**
   * Required. Username for the MySQL connection.
   *
   * @var string
   */
  public $username;

  /**
   * Required. Hostname for the MySQL connection.
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
   * Optional. Input only. Password for the MySQL connection. Mutually exclusive
   * with the `secret_manager_stored_password` field.
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
   * Port for the MySQL connection, default value is 3306.
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
   * Optional. A reference to a Secret Manager resource name storing the MySQL
   * connection password. Mutually exclusive with the `password` field.
   *
   * @param string $secretManagerStoredPassword
   */
  public function setSecretManagerStoredPassword($secretManagerStoredPassword)
  {
    $this->secretManagerStoredPassword = $secretManagerStoredPassword;
  }
  /**
   * @return string
   */
  public function getSecretManagerStoredPassword()
  {
    return $this->secretManagerStoredPassword;
  }
  /**
   * SSL configuration for the MySQL connection.
   *
   * @param MysqlSslConfig $sslConfig
   */
  public function setSslConfig(MysqlSslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @return MysqlSslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
  /**
   * Required. Username for the MySQL connection.
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
class_alias(MysqlProfile::class, 'Google_Service_Datastream_MysqlProfile');
