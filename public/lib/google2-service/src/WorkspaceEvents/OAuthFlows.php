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

namespace Google\Service\WorkspaceEvents;

class OAuthFlows extends \Google\Model
{
  protected $authorizationCodeType = AuthorizationCodeOAuthFlow::class;
  protected $authorizationCodeDataType = '';
  protected $clientCredentialsType = ClientCredentialsOAuthFlow::class;
  protected $clientCredentialsDataType = '';
  protected $implicitType = ImplicitOAuthFlow::class;
  protected $implicitDataType = '';
  protected $passwordType = PasswordOAuthFlow::class;
  protected $passwordDataType = '';

  /**
   * @param AuthorizationCodeOAuthFlow $authorizationCode
   */
  public function setAuthorizationCode(AuthorizationCodeOAuthFlow $authorizationCode)
  {
    $this->authorizationCode = $authorizationCode;
  }
  /**
   * @return AuthorizationCodeOAuthFlow
   */
  public function getAuthorizationCode()
  {
    return $this->authorizationCode;
  }
  /**
   * @param ClientCredentialsOAuthFlow $clientCredentials
   */
  public function setClientCredentials(ClientCredentialsOAuthFlow $clientCredentials)
  {
    $this->clientCredentials = $clientCredentials;
  }
  /**
   * @return ClientCredentialsOAuthFlow
   */
  public function getClientCredentials()
  {
    return $this->clientCredentials;
  }
  /**
   * @param ImplicitOAuthFlow $implicit
   */
  public function setImplicit(ImplicitOAuthFlow $implicit)
  {
    $this->implicit = $implicit;
  }
  /**
   * @return ImplicitOAuthFlow
   */
  public function getImplicit()
  {
    return $this->implicit;
  }
  /**
   * @param PasswordOAuthFlow $password
   */
  public function setPassword(PasswordOAuthFlow $password)
  {
    $this->password = $password;
  }
  /**
   * @return PasswordOAuthFlow
   */
  public function getPassword()
  {
    return $this->password;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OAuthFlows::class, 'Google_Service_WorkspaceEvents_OAuthFlows');
