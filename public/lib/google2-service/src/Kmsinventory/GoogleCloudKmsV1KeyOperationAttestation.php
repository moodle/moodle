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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsV1KeyOperationAttestation extends \Google\Model
{
  /**
   * Not specified.
   */
  public const FORMAT_ATTESTATION_FORMAT_UNSPECIFIED = 'ATTESTATION_FORMAT_UNSPECIFIED';
  /**
   * Cavium HSM attestation compressed with gzip. Note that this format is
   * defined by Cavium and subject to change at any time. See
   * https://www.marvell.com/products/security-solutions/nitrox-hs-
   * adapters/software-key-attestation.html.
   */
  public const FORMAT_CAVIUM_V1_COMPRESSED = 'CAVIUM_V1_COMPRESSED';
  /**
   * Cavium HSM attestation V2 compressed with gzip. This is a new format
   * introduced in Cavium's version 3.2-08.
   */
  public const FORMAT_CAVIUM_V2_COMPRESSED = 'CAVIUM_V2_COMPRESSED';
  protected $certChainsType = GoogleCloudKmsV1KeyOperationAttestationCertificateChains::class;
  protected $certChainsDataType = '';
  /**
   * Output only. The attestation data provided by the HSM when the key
   * operation was performed.
   *
   * @var string
   */
  public $content;
  /**
   * Output only. The format of the attestation data.
   *
   * @var string
   */
  public $format;

  /**
   * Output only. The certificate chains needed to validate the attestation
   *
   * @param GoogleCloudKmsV1KeyOperationAttestationCertificateChains $certChains
   */
  public function setCertChains(GoogleCloudKmsV1KeyOperationAttestationCertificateChains $certChains)
  {
    $this->certChains = $certChains;
  }
  /**
   * @return GoogleCloudKmsV1KeyOperationAttestationCertificateChains
   */
  public function getCertChains()
  {
    return $this->certChains;
  }
  /**
   * Output only. The attestation data provided by the HSM when the key
   * operation was performed.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. The format of the attestation data.
   *
   * Accepted values: ATTESTATION_FORMAT_UNSPECIFIED, CAVIUM_V1_COMPRESSED,
   * CAVIUM_V2_COMPRESSED
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsV1KeyOperationAttestation::class, 'Google_Service_Kmsinventory_GoogleCloudKmsV1KeyOperationAttestation');
