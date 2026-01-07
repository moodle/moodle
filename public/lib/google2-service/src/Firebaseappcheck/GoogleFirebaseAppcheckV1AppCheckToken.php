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

namespace Google\Service\Firebaseappcheck;

class GoogleFirebaseAppcheckV1AppCheckToken extends \Google\Model
{
  /**
   * The App Check token. App Check tokens are signed
   * [JWTs](https://tools.ietf.org/html/rfc7519) containing claims that identify
   * the attested app and GCP project. This token is used to access Google
   * services protected by App Check. These tokens can also be [verified by your
   * own custom backends](https://firebase.google.com/docs/app-check/custom-
   * resource-backend) using the Firebase Admin SDK or third-party libraries.
   *
   * @var string
   */
  public $token;
  /**
   * The duration from the time this token is minted until its expiration. This
   * field is intended to ease client-side token management, since the client
   * may have clock skew, but is still able to accurately measure a duration.
   *
   * @var string
   */
  public $ttl;

  /**
   * The App Check token. App Check tokens are signed
   * [JWTs](https://tools.ietf.org/html/rfc7519) containing claims that identify
   * the attested app and GCP project. This token is used to access Google
   * services protected by App Check. These tokens can also be [verified by your
   * own custom backends](https://firebase.google.com/docs/app-check/custom-
   * resource-backend) using the Firebase Admin SDK or third-party libraries.
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
   * The duration from the time this token is minted until its expiration. This
   * field is intended to ease client-side token management, since the client
   * may have clock skew, but is still able to accurately measure a duration.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1AppCheckToken::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1AppCheckToken');
