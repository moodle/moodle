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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1SignDataRequest extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_UNSPECIFIED = 'SIGNATURE_ALGORITHM_UNSPECIFIED';
  /**
   * The server-side builds the PKCS#1 DigestInfo and sends a SHA256 hash of it
   * to the client. The client should sign using RSA with PKCS#1 v1.5 padding.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256 = 'SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256';
  /**
   * The server-side builds the PKCS#1 DigestInfo and sends it unhashed to the
   * client. The client is responsible for signing and hashing using the P-256
   * curve.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_ECDSA_SHA256 = 'SIGNATURE_ALGORITHM_ECDSA_SHA256';
  /**
   * Required. The data that the client was asked to sign.
   *
   * @var string
   */
  public $signData;
  /**
   * Required. The signature algorithm that the adapter expects the client and
   * backend components to use when processing `sign_data`.
   *
   * @var string
   */
  public $signatureAlgorithm;

  /**
   * Required. The data that the client was asked to sign.
   *
   * @param string $signData
   */
  public function setSignData($signData)
  {
    $this->signData = $signData;
  }
  /**
   * @return string
   */
  public function getSignData()
  {
    return $this->signData;
  }
  /**
   * Required. The signature algorithm that the adapter expects the client and
   * backend components to use when processing `sign_data`.
   *
   * Accepted values: SIGNATURE_ALGORITHM_UNSPECIFIED,
   * SIGNATURE_ALGORITHM_RSA_PKCS1_V1_5_SHA256, SIGNATURE_ALGORITHM_ECDSA_SHA256
   *
   * @param self::SIGNATURE_ALGORITHM_* $signatureAlgorithm
   */
  public function setSignatureAlgorithm($signatureAlgorithm)
  {
    $this->signatureAlgorithm = $signatureAlgorithm;
  }
  /**
   * @return self::SIGNATURE_ALGORITHM_*
   */
  public function getSignatureAlgorithm()
  {
    return $this->signatureAlgorithm;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1SignDataRequest::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1SignDataRequest');
