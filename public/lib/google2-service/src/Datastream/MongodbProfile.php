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

class MongodbProfile extends \Google\Collection
{
  protected $collection_key = 'hostAddresses';
  /**
   * Optional. Specifies additional options for the MongoDB connection. The
   * options should be sent as key-value pairs, for example: `additional_options
   * = {"serverSelectionTimeoutMS": "10000", "directConnection": "true"}`. Keys
   * are case-sensitive and should match the official MongoDB connection string
   * options: https://www.mongodb.com/docs/manual/reference/connection-string-
   * options/ The server will not modify the values provided by the user.
   *
   * @var string[]
   */
  public $additionalOptions;
  protected $hostAddressesType = HostAddress::class;
  protected $hostAddressesDataType = 'array';
  /**
   * Optional. Password for the MongoDB connection. Mutually exclusive with the
   * `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. Name of the replica set. Only needed for self hosted replica set
   * type MongoDB cluster. For SRV connection format, this field must be empty.
   * For Standard connection format, this field must be specified.
   *
   * @var string
   */
  public $replicaSet;
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * SQLServer connection password. Mutually exclusive with the `password`
   * field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  protected $srvConnectionFormatType = SrvConnectionFormat::class;
  protected $srvConnectionFormatDataType = '';
  protected $sslConfigType = MongodbSslConfig::class;
  protected $sslConfigDataType = '';
  protected $standardConnectionFormatType = StandardConnectionFormat::class;
  protected $standardConnectionFormatDataType = '';
  /**
   * Required. Username for the MongoDB connection.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. Specifies additional options for the MongoDB connection. The
   * options should be sent as key-value pairs, for example: `additional_options
   * = {"serverSelectionTimeoutMS": "10000", "directConnection": "true"}`. Keys
   * are case-sensitive and should match the official MongoDB connection string
   * options: https://www.mongodb.com/docs/manual/reference/connection-string-
   * options/ The server will not modify the values provided by the user.
   *
   * @param string[] $additionalOptions
   */
  public function setAdditionalOptions($additionalOptions)
  {
    $this->additionalOptions = $additionalOptions;
  }
  /**
   * @return string[]
   */
  public function getAdditionalOptions()
  {
    return $this->additionalOptions;
  }
  /**
   * Required. List of host addresses for a MongoDB cluster. For SRV connection
   * format, this list must contain exactly one DNS host without a port. For
   * Standard connection format, this list must contain all the required hosts
   * in the cluster with their respective ports.
   *
   * @param HostAddress[] $hostAddresses
   */
  public function setHostAddresses($hostAddresses)
  {
    $this->hostAddresses = $hostAddresses;
  }
  /**
   * @return HostAddress[]
   */
  public function getHostAddresses()
  {
    return $this->hostAddresses;
  }
  /**
   * Optional. Password for the MongoDB connection. Mutually exclusive with the
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
   * Optional. Name of the replica set. Only needed for self hosted replica set
   * type MongoDB cluster. For SRV connection format, this field must be empty.
   * For Standard connection format, this field must be specified.
   *
   * @param string $replicaSet
   */
  public function setReplicaSet($replicaSet)
  {
    $this->replicaSet = $replicaSet;
  }
  /**
   * @return string
   */
  public function getReplicaSet()
  {
    return $this->replicaSet;
  }
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * SQLServer connection password. Mutually exclusive with the `password`
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
   * Srv connection format.
   *
   * @param SrvConnectionFormat $srvConnectionFormat
   */
  public function setSrvConnectionFormat(SrvConnectionFormat $srvConnectionFormat)
  {
    $this->srvConnectionFormat = $srvConnectionFormat;
  }
  /**
   * @return SrvConnectionFormat
   */
  public function getSrvConnectionFormat()
  {
    return $this->srvConnectionFormat;
  }
  /**
   * Optional. SSL configuration for the MongoDB connection.
   *
   * @param MongodbSslConfig $sslConfig
   */
  public function setSslConfig(MongodbSslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @return MongodbSslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
  /**
   * Standard connection format.
   *
   * @param StandardConnectionFormat $standardConnectionFormat
   */
  public function setStandardConnectionFormat(StandardConnectionFormat $standardConnectionFormat)
  {
    $this->standardConnectionFormat = $standardConnectionFormat;
  }
  /**
   * @return StandardConnectionFormat
   */
  public function getStandardConnectionFormat()
  {
    return $this->standardConnectionFormat;
  }
  /**
   * Required. Username for the MongoDB connection.
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
class_alias(MongodbProfile::class, 'Google_Service_Datastream_MongodbProfile');
