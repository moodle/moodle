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

namespace Google\Service\Backupdr;

class FetchAccessTokenResponse extends \Google\Model
{
  /**
   * The token is valid until this time.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The location in bucket that can be used for reading.
   *
   * @var string
   */
  public $readLocation;
  /**
   * The downscoped token that was created.
   *
   * @var string
   */
  public $token;
  /**
   * The location in bucket that can be used for writing.
   *
   * @var string
   */
  public $writeLocation;

  /**
   * The token is valid until this time.
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
   * The location in bucket that can be used for reading.
   *
   * @param string $readLocation
   */
  public function setReadLocation($readLocation)
  {
    $this->readLocation = $readLocation;
  }
  /**
   * @return string
   */
  public function getReadLocation()
  {
    return $this->readLocation;
  }
  /**
   * The downscoped token that was created.
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
   * The location in bucket that can be used for writing.
   *
   * @param string $writeLocation
   */
  public function setWriteLocation($writeLocation)
  {
    $this->writeLocation = $writeLocation;
  }
  /**
   * @return string
   */
  public function getWriteLocation()
  {
    return $this->writeLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchAccessTokenResponse::class, 'Google_Service_Backupdr_FetchAccessTokenResponse');
