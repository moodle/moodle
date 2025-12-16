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

class OracleProfile extends \Google\Model
{
  /**
   * Connection string attributes
   *
   * @var string[]
   */
  public $connectionAttributes;
  /**
   * Required. Database for the Oracle connection.
   *
   * @var string
   */
  public $databaseService;
  /**
   * Required. Hostname for the Oracle connection.
   *
   * @var string
   */
  public $hostname;
  protected $oracleAsmConfigType = OracleAsmConfig::class;
  protected $oracleAsmConfigDataType = '';
  protected $oracleSslConfigType = OracleSslConfig::class;
  protected $oracleSslConfigDataType = '';
  /**
   * Optional. Password for the Oracle connection. Mutually exclusive with the
   * `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Port for the Oracle connection, default value is 1521.
   *
   * @var int
   */
  public $port;
  /**
   * Optional. A reference to a Secret Manager resource name storing the Oracle
   * connection password. Mutually exclusive with the `password` field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  /**
   * Required. Username for the Oracle connection.
   *
   * @var string
   */
  public $username;

  /**
   * Connection string attributes
   *
   * @param string[] $connectionAttributes
   */
  public function setConnectionAttributes($connectionAttributes)
  {
    $this->connectionAttributes = $connectionAttributes;
  }
  /**
   * @return string[]
   */
  public function getConnectionAttributes()
  {
    return $this->connectionAttributes;
  }
  /**
   * Required. Database for the Oracle connection.
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
   * Required. Hostname for the Oracle connection.
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
   * Optional. SSL configuration for the Oracle connection.
   *
   * @param OracleSslConfig $oracleSslConfig
   */
  public function setOracleSslConfig(OracleSslConfig $oracleSslConfig)
  {
    $this->oracleSslConfig = $oracleSslConfig;
  }
  /**
   * @return OracleSslConfig
   */
  public function getOracleSslConfig()
  {
    return $this->oracleSslConfig;
  }
  /**
   * Optional. Password for the Oracle connection. Mutually exclusive with the
   * `secret_manager_stored_password` field.
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
   * Port for the Oracle connection, default value is 1521.
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
   * Optional. A reference to a Secret Manager resource name storing the Oracle
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
   * Required. Username for the Oracle connection.
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
class_alias(OracleProfile::class, 'Google_Service_Datastream_OracleProfile');
