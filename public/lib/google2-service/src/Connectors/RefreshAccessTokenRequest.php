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

namespace Google\Service\Connectors;

class RefreshAccessTokenRequest extends \Google\Model
{
  /**
   * Optional. Refresh Token String. If the Refresh Token is not provided, the
   * runtime will read the data from the secret manager.
   *
   * @var string
   */
  public $refreshToken;

  /**
   * Optional. Refresh Token String. If the Refresh Token is not provided, the
   * runtime will read the data from the secret manager.
   *
   * @param string $refreshToken
   */
  public function setRefreshToken($refreshToken)
  {
    $this->refreshToken = $refreshToken;
  }
  /**
   * @return string
   */
  public function getRefreshToken()
  {
    return $this->refreshToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RefreshAccessTokenRequest::class, 'Google_Service_Connectors_RefreshAccessTokenRequest');
