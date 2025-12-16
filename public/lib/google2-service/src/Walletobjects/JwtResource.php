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

namespace Google\Service\Walletobjects;

class JwtResource extends \Google\Model
{
  /**
   * A string representing a JWT of the format described at
   * https://developers.google.com/wallet/reference/rest/v1/Jwt
   *
   * @var string
   */
  public $jwt;

  /**
   * A string representing a JWT of the format described at
   * https://developers.google.com/wallet/reference/rest/v1/Jwt
   *
   * @param string $jwt
   */
  public function setJwt($jwt)
  {
    $this->jwt = $jwt;
  }
  /**
   * @return string
   */
  public function getJwt()
  {
    return $this->jwt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JwtResource::class, 'Google_Service_Walletobjects_JwtResource');
