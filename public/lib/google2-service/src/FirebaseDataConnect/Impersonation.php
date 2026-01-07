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

namespace Google\Service\FirebaseDataConnect;

class Impersonation extends \Google\Model
{
  /**
   * Evaluate the auth policy with a customized JWT auth token. Should follow
   * the Firebase Auth token format.
   * https://firebase.google.com/docs/rules/rules-and-auth For example: a
   * verified user may have auth_claims of {"sub": , "email_verified": true}
   *
   * @var array[]
   */
  public $authClaims;
  /**
   * Optional. If set, include debug details in GraphQL error extensions.
   *
   * @var bool
   */
  public $includeDebugDetails;
  /**
   * Evaluate the auth policy as an unauthenticated request. Can only be set to
   * true.
   *
   * @var bool
   */
  public $unauthenticated;

  /**
   * Evaluate the auth policy with a customized JWT auth token. Should follow
   * the Firebase Auth token format.
   * https://firebase.google.com/docs/rules/rules-and-auth For example: a
   * verified user may have auth_claims of {"sub": , "email_verified": true}
   *
   * @param array[] $authClaims
   */
  public function setAuthClaims($authClaims)
  {
    $this->authClaims = $authClaims;
  }
  /**
   * @return array[]
   */
  public function getAuthClaims()
  {
    return $this->authClaims;
  }
  /**
   * Optional. If set, include debug details in GraphQL error extensions.
   *
   * @param bool $includeDebugDetails
   */
  public function setIncludeDebugDetails($includeDebugDetails)
  {
    $this->includeDebugDetails = $includeDebugDetails;
  }
  /**
   * @return bool
   */
  public function getIncludeDebugDetails()
  {
    return $this->includeDebugDetails;
  }
  /**
   * Evaluate the auth policy as an unauthenticated request. Can only be set to
   * true.
   *
   * @param bool $unauthenticated
   */
  public function setUnauthenticated($unauthenticated)
  {
    $this->unauthenticated = $unauthenticated;
  }
  /**
   * @return bool
   */
  public function getUnauthenticated()
  {
    return $this->unauthenticated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Impersonation::class, 'Google_Service_FirebaseDataConnect_Impersonation');
