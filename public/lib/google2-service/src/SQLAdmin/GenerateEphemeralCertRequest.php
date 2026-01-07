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

namespace Google\Service\SQLAdmin;

class GenerateEphemeralCertRequest extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "accessToken" => "access_token",
        "publicKey" => "public_key",
  ];
  /**
   * Optional. Access token to include in the signed certificate.
   *
   * @var string
   */
  public $accessToken;
  /**
   * PEM encoded public key to include in the signed certificate.
   *
   * @var string
   */
  public $publicKey;
  /**
   * Optional. Optional snapshot read timestamp to trade freshness for
   * performance.
   *
   * @var string
   */
  public $readTime;
  /**
   * Optional. If set, it will contain the cert valid duration.
   *
   * @var string
   */
  public $validDuration;

  /**
   * Optional. Access token to include in the signed certificate.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * PEM encoded public key to include in the signed certificate.
   *
   * @param string $publicKey
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * Optional. Optional snapshot read timestamp to trade freshness for
   * performance.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * Optional. If set, it will contain the cert valid duration.
   *
   * @param string $validDuration
   */
  public function setValidDuration($validDuration)
  {
    $this->validDuration = $validDuration;
  }
  /**
   * @return string
   */
  public function getValidDuration()
  {
    return $this->validDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateEphemeralCertRequest::class, 'Google_Service_SQLAdmin_GenerateEphemeralCertRequest');
