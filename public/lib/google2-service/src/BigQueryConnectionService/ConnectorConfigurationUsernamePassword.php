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

namespace Google\Service\BigQueryConnectionService;

class ConnectorConfigurationUsernamePassword extends \Google\Model
{
  protected $passwordType = ConnectorConfigurationSecret::class;
  protected $passwordDataType = '';
  /**
   * Required. Username.
   *
   * @var string
   */
  public $username;

  /**
   * Required. Password.
   *
   * @param ConnectorConfigurationSecret $password
   */
  public function setPassword(ConnectorConfigurationSecret $password)
  {
    $this->password = $password;
  }
  /**
   * @return ConnectorConfigurationSecret
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Required. Username.
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
class_alias(ConnectorConfigurationUsernamePassword::class, 'Google_Service_BigQueryConnectionService_ConnectorConfigurationUsernamePassword');
