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

namespace Google\Service\CloudAlloyDBAdmin;

class GenerateClientCertificateRequest extends \Google\Model
{
  /**
   * @var string
   */
  public $certDuration;
  /**
   * @var string
   */
  public $publicKey;
  /**
   * @var string
   */
  public $requestId;
  /**
   * @var bool
   */
  public $useMetadataExchange;

  /**
   * @param string
   */
  public function setCertDuration($certDuration)
  {
    $this->certDuration = $certDuration;
  }
  /**
   * @return string
   */
  public function getCertDuration()
  {
    return $this->certDuration;
  }
  /**
   * @param string
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
   * @param string
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * @param bool
   */
  public function setUseMetadataExchange($useMetadataExchange)
  {
    $this->useMetadataExchange = $useMetadataExchange;
  }
  /**
   * @return bool
   */
  public function getUseMetadataExchange()
  {
    return $this->useMetadataExchange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateClientCertificateRequest::class, 'Google_Service_CloudAlloyDBAdmin_GenerateClientCertificateRequest');
