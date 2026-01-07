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

class OnPremisesConfiguration extends \Google\Collection
{
  /**
   * Unknown SSL option i.e. SSL option not specified by user.
   */
  public const SSL_OPTION_SSL_OPTION_UNSPECIFIED = 'SSL_OPTION_UNSPECIFIED';
  /**
   * SSL is not used for replica connection to the on-premises source.
   */
  public const SSL_OPTION_DISABLE = 'DISABLE';
  /**
   * SSL is required for replica connection to the on-premises source.
   */
  public const SSL_OPTION_REQUIRE = 'REQUIRE';
  /**
   * Verify CA is required for replica connection to the on-premises source.
   */
  public const SSL_OPTION_VERIFY_CA = 'VERIFY_CA';
  protected $collection_key = 'selectedObjects';
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
   * The dump file to create the Cloud SQL replica.
   *
   * @var string
   */
  public $dumpFilePath;
  /**
   * The host and port of the on-premises instance in host:port format
   *
   * @var string
   */
  public $hostPort;
  /**
   * This is always `sql#onPremisesConfiguration`.
   *
   * @var string
   */
  public $kind;
  /**
   * The password for connecting to on-premises instance.
   *
   * @var string
   */
  public $password;
  protected $selectedObjectsType = SelectedObjects::class;
  protected $selectedObjectsDataType = 'array';
  protected $sourceInstanceType = InstanceReference::class;
  protected $sourceInstanceDataType = '';
  /**
   * Optional. SSL option for replica connection to the on-premises source.
   *
   * @var string
   */
  public $sslOption;
  /**
   * The username for connecting to on-premises instance.
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
   * The dump file to create the Cloud SQL replica.
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
   * The host and port of the on-premises instance in host:port format
   *
   * @param string $hostPort
   */
  public function setHostPort($hostPort)
  {
    $this->hostPort = $hostPort;
  }
  /**
   * @return string
   */
  public function getHostPort()
  {
    return $this->hostPort;
  }
  /**
   * This is always `sql#onPremisesConfiguration`.
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
   * The password for connecting to on-premises instance.
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
   * Optional. A list of objects that the user selects for replication from an
   * external source instance.
   *
   * @param SelectedObjects[] $selectedObjects
   */
  public function setSelectedObjects($selectedObjects)
  {
    $this->selectedObjects = $selectedObjects;
  }
  /**
   * @return SelectedObjects[]
   */
  public function getSelectedObjects()
  {
    return $this->selectedObjects;
  }
  /**
   * The reference to Cloud SQL instance if the source is Cloud SQL.
   *
   * @param InstanceReference $sourceInstance
   */
  public function setSourceInstance(InstanceReference $sourceInstance)
  {
    $this->sourceInstance = $sourceInstance;
  }
  /**
   * @return InstanceReference
   */
  public function getSourceInstance()
  {
    return $this->sourceInstance;
  }
  /**
   * Optional. SSL option for replica connection to the on-premises source.
   *
   * Accepted values: SSL_OPTION_UNSPECIFIED, DISABLE, REQUIRE, VERIFY_CA
   *
   * @param self::SSL_OPTION_* $sslOption
   */
  public function setSslOption($sslOption)
  {
    $this->sslOption = $sslOption;
  }
  /**
   * @return self::SSL_OPTION_*
   */
  public function getSslOption()
  {
    return $this->sslOption;
  }
  /**
   * The username for connecting to on-premises instance.
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
class_alias(OnPremisesConfiguration::class, 'Google_Service_SQLAdmin_OnPremisesConfiguration');
