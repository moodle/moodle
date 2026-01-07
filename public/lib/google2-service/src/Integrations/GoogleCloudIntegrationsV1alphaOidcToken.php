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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaOidcToken extends \Google\Model
{
  /**
   * Audience to be used when generating OIDC token. The audience claim
   * identifies the recipients that the JWT is intended for.
   *
   * @var string
   */
  public $audience;
  /**
   * The service account email to be used as the identity for the token.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * ID token obtained for the service account
   *
   * @var string
   */
  public $token;
  /**
   * The approximate time until the token retrieved is valid.
   *
   * @var string
   */
  public $tokenExpireTime;

  /**
   * Audience to be used when generating OIDC token. The audience claim
   * identifies the recipients that the JWT is intended for.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * The service account email to be used as the identity for the token.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * ID token obtained for the service account
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * The approximate time until the token retrieved is valid.
   *
   * @param string $tokenExpireTime
   */
  public function setTokenExpireTime($tokenExpireTime)
  {
    $this->tokenExpireTime = $tokenExpireTime;
  }
  /**
   * @return string
   */
  public function getTokenExpireTime()
  {
    return $this->tokenExpireTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaOidcToken::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaOidcToken');
