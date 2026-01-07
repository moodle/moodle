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

class UserCredentials extends \Google\Model
{
  /**
   * Optional. Password for the Salesforce connection. Mutually exclusive with
   * the `secret_manager_stored_password` field.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce connection's password. Mutually exclusive with the `password`
   * field.
   *
   * @var string
   */
  public $secretManagerStoredPassword;
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce connection's security token. Mutually exclusive with the
   * `security_token` field.
   *
   * @var string
   */
  public $secretManagerStoredSecurityToken;
  /**
   * Optional. Security token for the Salesforce connection. Mutually exclusive
   * with the `secret_manager_stored_security_token` field.
   *
   * @var string
   */
  public $securityToken;
  /**
   * Required. Username for the Salesforce connection.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. Password for the Salesforce connection. Mutually exclusive with
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
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce connection's password. Mutually exclusive with the `password`
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
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce connection's security token. Mutually exclusive with the
   * `security_token` field.
   *
   * @param string $secretManagerStoredSecurityToken
   */
  public function setSecretManagerStoredSecurityToken($secretManagerStoredSecurityToken)
  {
    $this->secretManagerStoredSecurityToken = $secretManagerStoredSecurityToken;
  }
  /**
   * @return string
   */
  public function getSecretManagerStoredSecurityToken()
  {
    return $this->secretManagerStoredSecurityToken;
  }
  /**
   * Optional. Security token for the Salesforce connection. Mutually exclusive
   * with the `secret_manager_stored_security_token` field.
   *
   * @param string $securityToken
   */
  public function setSecurityToken($securityToken)
  {
    $this->securityToken = $securityToken;
  }
  /**
   * @return string
   */
  public function getSecurityToken()
  {
    return $this->securityToken;
  }
  /**
   * Required. Username for the Salesforce connection.
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
class_alias(UserCredentials::class, 'Google_Service_Datastream_UserCredentials');
