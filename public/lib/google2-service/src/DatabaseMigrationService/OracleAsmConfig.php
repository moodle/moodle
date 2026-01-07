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

class OracleAsmConfig extends \Google\Model
{
  /**
   * Required. ASM service name for the Oracle ASM connection.
   *
   * @var string
   */
  public $asmService;
  /**
   * Required. Hostname for the Oracle ASM connection.
   *
   * @var string
   */
  public $hostname;
  /**
   * Required. Input only. Password for the Oracle ASM connection.
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
   * Required. Port for the Oracle ASM connection.
   *
   * @var int
   */
  public $port;
  protected $sslType = SslConfig::class;
  protected $sslDataType = '';
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
   * Required. Input only. Password for the Oracle ASM connection.
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
   * Optional. SSL configuration for the Oracle connection.
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
class_alias(OracleAsmConfig::class, 'Google_Service_DatabaseMigrationService_OracleAsmConfig');
