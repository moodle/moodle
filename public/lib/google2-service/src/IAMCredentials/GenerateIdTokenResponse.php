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

namespace Google\Service\IAMCredentials;

class GenerateIdTokenResponse extends \Google\Model
{
  /**
   * The OpenId Connect ID token. The token is a JSON Web Token (JWT) that
   * contains a payload with claims. See the [JSON Web Token
   * spec](https://tools.ietf.org/html/rfc7519) for more information. Here is an
   * example of a decoded JWT payload: ``` { "iss":
   * "https://accounts.google.com", "iat": 1496953245, "exp": 1496953245, "aud":
   * "https://www.example.com", "sub": "107517467455664443765", "azp":
   * "107517467455664443765", "email": "my-iam-account@my-
   * project.iam.gserviceaccount.com", "email_verified": true, "google": {
   * "organization_number": 123456 } } ```
   *
   * @var string
   */
  public $token;

  /**
   * The OpenId Connect ID token. The token is a JSON Web Token (JWT) that
   * contains a payload with claims. See the [JSON Web Token
   * spec](https://tools.ietf.org/html/rfc7519) for more information. Here is an
   * example of a decoded JWT payload: ``` { "iss":
   * "https://accounts.google.com", "iat": 1496953245, "exp": 1496953245, "aud":
   * "https://www.example.com", "sub": "107517467455664443765", "azp":
   * "107517467455664443765", "email": "my-iam-account@my-
   * project.iam.gserviceaccount.com", "email_verified": true, "google": {
   * "organization_number": 123456 } } ```
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateIdTokenResponse::class, 'Google_Service_IAMCredentials_GenerateIdTokenResponse');
