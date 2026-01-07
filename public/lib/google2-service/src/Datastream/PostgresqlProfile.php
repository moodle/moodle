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

class PostgresqlProfile extends \Google\Model
{
  /**
   * Required. Database for the PostgreSQL connection.
   *
   * @var string
   */
  public $database;
  /**
   * Required. Hostname for the PostgreSQL connection.
   *
   * @var string
   */
  public $hostname;
  /**
   * Optional. Password for the PostgreSQL connection. Mutually exclusive with
   * the `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Port for the PostgreSQL connection, default value is 5432.
   *
   * @var int
   */
  public $port;
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * PostgreSQL connection password. Mutually exclusive with the `password`
   * field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  protected $sslConfigType = PostgresqlSslConfig::class;
  protected $sslConfigDataType = '';
  /**
   * Required. Username for the PostgreSQL connection.
   *
   * @var string
   */
  public $username;

  /**
   * Required. Database for the PostgreSQL connection.
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
   * Required. Hostname for the PostgreSQL connection.
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
   * Optional. Password for the PostgreSQL connection. Mutually exclusive with
   * the `secret_manager_stored_password` field.
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
   * Port for the PostgreSQL connection, default value is 5432.
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
   * Optional. A reference to a Secret Manager resource name storing the
   * PostgreSQL connection password. Mutually exclusive with the `password`
   * field.
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
   * Optional. SSL configuration for the PostgreSQL connection. In case
   * PostgresqlSslConfig is not set, the connection will use the default SSL
   * mode, which is `prefer` (i.e. this mode will only use encryption if enabled
   * from database side, otherwise will use unencrypted communication)
   *
   * @param PostgresqlSslConfig $sslConfig
   */
  public function setSslConfig(PostgresqlSslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @return PostgresqlSslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
  /**
   * Required. Username for the PostgreSQL connection.
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
class_alias(PostgresqlProfile::class, 'Google_Service_Datastream_PostgresqlProfile');
