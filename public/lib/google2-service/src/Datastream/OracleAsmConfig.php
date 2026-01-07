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

class OracleAsmConfig extends \Google\Model
{
  /**
   * Required. ASM service name for the Oracle ASM connection.
   *
   * @var string
   */
  public $asmService;
  /**
   * Optional. Connection string attributes
   *
   * @var string[]
   */
  public $connectionAttributes;
  /**
   * Required. Hostname for the Oracle ASM connection.
   *
   * @var string
   */
  public $hostname;
  protected $oracleSslConfigType = OracleSslConfig::class;
  protected $oracleSslConfigDataType = '';
  /**
   * Optional. Password for the Oracle ASM connection. Mutually exclusive with
   * the `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Required. Port for the Oracle ASM connection.
   *
   * @var int
   */
  public $port;
  /**
   * Optional. A reference to a Secret Manager resource name storing the Oracle
   * ASM connection password. Mutually exclusive with the `password` field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  /**
   * Required. Username for the Oracle ASM connection.
   *
   * @var string
   */
  public $username;

  /**
   * Required. ASM service name for the Oracle ASM connection.
   *
   * @param string $asmService
   */
  public function setAsmService($asmService)
  {
    $this->asmService = $asmService;
  }
  /**
   * @return string
   */
  public function getAsmService()
  {
    return $this->asmService;
  }
  /**
   * Optional. Connection string attributes
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
   * Required. Hostname for the Oracle ASM connection.
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
   * Optional. Password for the Oracle ASM connection. Mutually exclusive with
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
   * Required. Port for the Oracle ASM connection.
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
   * ASM connection password. Mutually exclusive with the `password` field.
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
   * Required. Username for the Oracle ASM connection.
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
class_alias(OracleAsmConfig::class, 'Google_Service_Datastream_OracleAsmConfig');
