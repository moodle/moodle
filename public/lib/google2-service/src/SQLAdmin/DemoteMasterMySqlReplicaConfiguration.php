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

namespace Google\Service\SQLAdmin;

class DemoteMasterMySqlReplicaConfiguration extends \Google\Model
{
  /**
   * PEM representation of the trusted CA's x509 certificate.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * PEM representation of the replica's x509 certificate.
   *
   * @var string
   */
  public $clientCertificate;
  /**
   * PEM representation of the replica's private key. The corresponding public
   * key is encoded in the client's certificate. The format of the replica's
   * private key can be either PKCS #1 or PKCS #8.
   *
   * @var string
   */
  public $clientKey;
  /**
   * This is always `sql#demoteMasterMysqlReplicaConfiguration`.
   *
   * @var string
   */
  public $kind;
  /**
   * The password for the replication connection.
   *
   * @var string
   */
  public $password;
  /**
   * The username for the replication connection.
   *
   * @var string
   */
  public $username;

  /**
   * PEM representation of the trusted CA's x509 certificate.
   *
   * @param string $caCertificate
   */
  public function setCaCertificate($caCertificate)
  {
    $this->caCertificate = $caCertificate;
  }
  /**
   * @return string
   */
  public function getCaCertificate()
  {
    return $this->caCertificate;
  }
  /**
   * PEM representation of the replica's x509 certificate.
   *
   * @param string $clientCertificate
   */
  public function setClientCertificate($clientCertificate)
  {
    $this->clientCertificate = $clientCertificate;
  }
  /**
   * @return string
   */
  public function getClientCertificate()
  {
    return $this->clientCertificate;
  }
  /**
   * PEM representation of the replica's private key. The corresponding public
   * key is encoded in the client's certificate. The format of the replica's
   * private key can be either PKCS #1 or PKCS #8.
   *
   * @param string $clientKey
   */
  public function setClientKey($clientKey)
  {
    $this->clientKey = $clientKey;
  }
  /**
   * @return string
   */
  public function getClientKey()
  {
    return $this->clientKey;
  }
  /**
   * This is always `sql#demoteMasterMysqlReplicaConfiguration`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The password for the replication connection.
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
   * The username for the replication connection.
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
class_alias(DemoteMasterMySqlReplicaConfiguration::class, 'Google_Service_SQLAdmin_DemoteMasterMySqlReplicaConfiguration');
