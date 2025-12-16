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

class GoogleFirebaseAppcheckV1DebugToken extends \Google\Model
{
  /**
   * Required. A human readable display name used to identify this debug token.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The relative resource name of the debug token, in the format: ```
   * projects/{project_number}/apps/{app_id}/debugTokens/{debug_token_id} ```
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. Immutable. The secret token itself. Must be provided
   * during creation, and must be a UUID4, case insensitive. This field is
   * immutable once set, and cannot be provided during an UpdateDebugToken
   * request. You can, however, delete this debug token using DeleteDebugToken
   * to revoke it. For security reasons, this field will never be populated in
   * any response.
   *
   * @var string
   */
  public $token;
  /**
   * Output only. Timestamp when this debug token was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. A human readable display name used to identify this debug token.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. The relative resource name of the debug token, in the format: ```
   * projects/{project_number}/apps/{app_id}/debugTokens/{debug_token_id} ```
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Input only. Immutable. The secret token itself. Must be provided
   * during creation, and must be a UUID4, case insensitive. This field is
   * immutable once set, and cannot be provided during an UpdateDebugToken
   * request. You can, however, delete this debug token using DeleteDebugToken
   * to revoke it. For security reasons, this field will never be populated in
   * any response.
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
   * Output only. Timestamp when this debug token was most recently updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppcheckV1DebugToken::class, 'Google_Service_Firebaseappcheck_GoogleFirebaseAppcheckV1DebugToken');
