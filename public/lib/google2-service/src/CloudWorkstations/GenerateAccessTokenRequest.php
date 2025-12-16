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

namespace Google\Service\CloudWorkstations;

class GenerateAccessTokenRequest extends \Google\Model
{
  /**
   * Desired expiration time of the access token. This value must be at most 24
   * hours in the future. If a value is not specified, the token's expiration
   * time will be set to a default value of 1 hour in the future.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Optional. Port for which the access token should be generated. If
   * specified, the generated access token grants access only to the specified
   * port of the workstation. If specified, values must be within the range [1 -
   * 65535]. If not specified, the generated access token grants access to all
   * ports of the workstation.
   *
   * @var int
   */
  public $port;
  /**
   * Desired lifetime duration of the access token. This value must be at most
   * 24 hours. If a value is not specified, the token's lifetime will be set to
   * a default value of 1 hour.
   *
   * @var string
   */
  public $ttl;

  /**
   * Desired expiration time of the access token. This value must be at most 24
   * hours in the future. If a value is not specified, the token's expiration
   * time will be set to a default value of 1 hour in the future.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Optional. Port for which the access token should be generated. If
   * specified, the generated access token grants access only to the specified
   * port of the workstation. If specified, values must be within the range [1 -
   * 65535]. If not specified, the generated access token grants access to all
   * ports of the workstation.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Desired lifetime duration of the access token. This value must be at most
   * 24 hours. If a value is not specified, the token's lifetime will be set to
   * a default value of 1 hour.
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
class_alias(GenerateAccessTokenRequest::class, 'Google_Service_CloudWorkstations_GenerateAccessTokenRequest');
