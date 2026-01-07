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

class PasswordOAuthFlow extends \Google\Model
{
  /**
   * The URL to be used for obtaining refresh tokens. This MUST be in the form
   * of a URL. The OAuth2 standard requires the use of TLS.
   *
   * @var string
   */
  public $refreshUrl;
  /**
   * The available scopes for the OAuth2 security scheme. A map between the
   * scope name and a short description for it. The map MAY be empty.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * The token URL to be used for this flow. This MUST be in the form of a URL.
   * The OAuth2 standard requires the use of TLS.
   *
   * @var string
   */
  public $tokenUrl;

  /**
   * The URL to be used for obtaining refresh tokens. This MUST be in the form
   * of a URL. The OAuth2 standard requires the use of TLS.
   *
   * @param string $refreshUrl
   */
  public function setRefreshUrl($refreshUrl)
  {
    $this->refreshUrl = $refreshUrl;
  }
  /**
   * @return string
   */
  public function getRefreshUrl()
  {
    return $this->refreshUrl;
  }
  /**
   * The available scopes for the OAuth2 security scheme. A map between the
   * scope name and a short description for it. The map MAY be empty.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * The token URL to be used for this flow. This MUST be in the form of a URL.
   * The OAuth2 standard requires the use of TLS.
   *
   * @param string $tokenUrl
   */
  public function setTokenUrl($tokenUrl)
  {
    $this->tokenUrl = $tokenUrl;
  }
  /**
   * @return string
   */
  public function getTokenUrl()
  {
    return $this->tokenUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PasswordOAuthFlow::class, 'Google_Service_WorkspaceEvents_PasswordOAuthFlow');
