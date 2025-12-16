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

class MySqlReplicaConfiguration extends \Google\Model
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
   * key is encoded in the client's certificate.
   *
   * @var string
   */
  public $clientKey;
  /**
   * Seconds to wait between connect retries. MySQL's default is 60 seconds.
   *
   * @var int
   */
  public $connectRetryInterval;
  /**
   * Path to a SQL dump file in Google Cloud Storage from which the replica
   * instance is to be created. The URI is in the form gs://bucketName/fileName.
   * Compressed gzip files (.gz) are also supported. Dumps have the binlog co-
   * ordinates from which replication begins. This can be accomplished by
   * setting --master-data to 1 when using mysqldump.
   *
   * @var string
   */
  public $dumpFilePath;
  /**
   * This is always `sql#mysqlReplicaConfiguration`.
   *
   * @var string
   */
  public $kind;
  /**
   * Interval in milliseconds between replication heartbeats.
   *
   * @var string
   */
  public $masterHeartbeatPeriod;
  /**
   * The password for the replication connection.
   *
   * @var string
   */
  public $password;
  /**
   * A list of permissible ciphers to use for SSL encryption.
   *
   * @var string
   */
  public $sslCipher;
  /**
   * The username for the replication connection.
   *
   * @var string
   */
  public $username;
  /**
   * Whether or not to check the primary instance's Common Name value in the
   * certificate that it sends during the SSL handshake.
   *
   * @var bool
   */
  public $verifyServerCertificate;

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
   * key is encoded in the client's certificate.
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
   * Seconds to wait between connect retries. MySQL's default is 60 seconds.
   *
   * @param int $connectRetryInterval
   */
  public function setConnectRetryInterval($connectRetryInterval)
  {
    $this->connectRetryInterval = $connectRetryInterval;
  }
  /**
   * @return int
   */
  public function getConnectRetryInterval()
  {
    return $this->connectRetryInterval;
  }
  /**
   * Path to a SQL dump file in Google Cloud Storage from which the replica
   * instance is to be created. The URI is in the form gs://bucketName/fileName.
   * Compressed gzip files (.gz) are also supported. Dumps have the binlog co-
   * ordinates from which replication begins. This can be accomplished by
   * setting --master-data to 1 when using mysqldump.
   *
   * @param string $dumpFilePath
   */
  public function setDumpFilePath($dumpFilePath)
  {
    $this->dumpFilePath = $dumpFilePath;
  }
  /**
   * @return string
   */
  public function getDumpFilePath()
  {
    return $this->dumpFilePath;
  }
  /**
   * This is always `sql#mysqlReplicaConfiguration`.
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
   * Interval in milliseconds between replication heartbeats.
   *
   * @param string $masterHeartbeatPeriod
   */
  public function setMasterHeartbeatPeriod($masterHeartbeatPeriod)
  {
    $this->masterHeartbeatPeriod = $masterHeartbeatPeriod;
  }
  /**
   * @return string
   */
  public function getMasterHeartbeatPeriod()
  {
    return $this->masterHeartbeatPeriod;
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
   * A list of permissible ciphers to use for SSL encryption.
   *
   * @param string $sslCipher
   */
  public function setSslCipher($sslCipher)
  {
    $this->sslCipher = $sslCipher;
  }
  /**
   * @return string
   */
  public function getSslCipher()
  {
    return $this->sslCipher;
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
  /**
   * Whether or not to check the primary instance's Common Name value in the
   * certificate that it sends during the SSL handshake.
   *
   * @param bool $verifyServerCertificate
   */
  public function setVerifyServerCertificate($verifyServerCertificate)
  {
    $this->verifyServerCertificate = $verifyServerCertificate;
  }
  /**
   * @return bool
   */
  public function getVerifyServerCertificate()
  {
    return $this->verifyServerCertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MySqlReplicaConfiguration::class, 'Google_Service_SQLAdmin_MySqlReplicaConfiguration');
