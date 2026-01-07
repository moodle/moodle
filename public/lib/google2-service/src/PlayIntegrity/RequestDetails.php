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

namespace Google\Service\PlayIntegrity;

class RequestDetails extends \Google\Model
{
  /**
   * Nonce that was provided in the request (which is base64 web-safe no-wrap).
   *
   * @var string
   */
  public $nonce;
  /**
   * Request hash that was provided in the request.
   *
   * @var string
   */
  public $requestHash;
  /**
   * Required. Application package name this attestation was requested for.
   * Note: This field makes no guarantees or promises on the caller integrity.
   * For details on application integrity, check application_integrity.
   *
   * @var string
   */
  public $requestPackageName;
  /**
   * Required. Timestamp, in milliseconds, of the integrity application request.
   *
   * @var string
   */
  public $timestampMillis;

  /**
   * Nonce that was provided in the request (which is base64 web-safe no-wrap).
   *
   * @param string $nonce
   */
  public function setNonce($nonce)
  {
    $this->nonce = $nonce;
  }
  /**
   * @return string
   */
  public function getNonce()
  {
    return $this->nonce;
  }
  /**
   * Request hash that was provided in the request.
   *
   * @param string $requestHash
   */
  public function setRequestHash($requestHash)
  {
    $this->requestHash = $requestHash;
  }
  /**
   * @return string
   */
  public function getRequestHash()
  {
    return $this->requestHash;
  }
  /**
   * Required. Application package name this attestation was requested for.
   * Note: This field makes no guarantees or promises on the caller integrity.
   * For details on application integrity, check application_integrity.
   *
   * @param string $requestPackageName
   */
  public function setRequestPackageName($requestPackageName)
  {
    $this->requestPackageName = $requestPackageName;
  }
  /**
   * @return string
   */
  public function getRequestPackageName()
  {
    return $this->requestPackageName;
  }
  /**
   * Required. Timestamp, in milliseconds, of the integrity application request.
   *
   * @param string $timestampMillis
   */
  public function setTimestampMillis($timestampMillis)
  {
    $this->timestampMillis = $timestampMillis;
  }
  /**
   * @return string
   */
  public function getTimestampMillis()
  {
    return $this->timestampMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestDetails::class, 'Google_Service_PlayIntegrity_RequestDetails');
