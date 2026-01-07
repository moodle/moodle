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

namespace Google\Service\CloudKMS;

class DecryptRequest extends \Google\Model
{
  /**
   * Optional. Optional data that must match the data originally supplied in
   * EncryptRequest.additional_authenticated_data.
   *
   * @var string
   */
  public $additionalAuthenticatedData;
  /**
   * Optional. An optional CRC32C checksum of the
   * DecryptRequest.additional_authenticated_data. If specified,
   * KeyManagementService will verify the integrity of the received
   * DecryptRequest.additional_authenticated_data using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(DecryptRequest.additional_authenticated_data) is equal to
   * DecryptRequest.additional_authenticated_data_crc32c, and if so, perform a
   * limited number of retries. A persistent mismatch may indicate an issue in
   * your computation of the CRC32C checksum. Note: This field is defined as
   * int64 for reasons of compatibility across different languages. However, it
   * is a non-negative integer, which will never exceed 2^32-1, and can be
   * safely downconverted to uint32 in languages that support this type.
   *
   * @var string
   */
  public $additionalAuthenticatedDataCrc32c;
  /**
   * Required. The encrypted data originally returned in
   * EncryptResponse.ciphertext.
   *
   * @var string
   */
  public $ciphertext;
  /**
   * Optional. An optional CRC32C checksum of the DecryptRequest.ciphertext. If
   * specified, KeyManagementService will verify the integrity of the received
   * DecryptRequest.ciphertext using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(DecryptRequest.ciphertext) is equal to
   * DecryptRequest.ciphertext_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $ciphertextCrc32c;

  /**
   * Optional. Optional data that must match the data originally supplied in
   * EncryptRequest.additional_authenticated_data.
   *
   * @param string $additionalAuthenticatedData
   */
  public function setAdditionalAuthenticatedData($additionalAuthenticatedData)
  {
    $this->additionalAuthenticatedData = $additionalAuthenticatedData;
  }
  /**
   * @return string
   */
  public function getAdditionalAuthenticatedData()
  {
    return $this->additionalAuthenticatedData;
  }
  /**
   * Optional. An optional CRC32C checksum of the
   * DecryptRequest.additional_authenticated_data. If specified,
   * KeyManagementService will verify the integrity of the received
   * DecryptRequest.additional_authenticated_data using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(DecryptRequest.additional_authenticated_data) is equal to
   * DecryptRequest.additional_authenticated_data_crc32c, and if so, perform a
   * limited number of retries. A persistent mismatch may indicate an issue in
   * your computation of the CRC32C checksum. Note: This field is defined as
   * int64 for reasons of compatibility across different languages. However, it
   * is a non-negative integer, which will never exceed 2^32-1, and can be
   * safely downconverted to uint32 in languages that support this type.
   *
   * @param string $additionalAuthenticatedDataCrc32c
   */
  public function setAdditionalAuthenticatedDataCrc32c($additionalAuthenticatedDataCrc32c)
  {
    $this->additionalAuthenticatedDataCrc32c = $additionalAuthenticatedDataCrc32c;
  }
  /**
   * @return string
   */
  public function getAdditionalAuthenticatedDataCrc32c()
  {
    return $this->additionalAuthenticatedDataCrc32c;
  }
  /**
   * Required. The encrypted data originally returned in
   * EncryptResponse.ciphertext.
   *
   * @param string $ciphertext
   */
  public function setCiphertext($ciphertext)
  {
    $this->ciphertext = $ciphertext;
  }
  /**
   * @return string
   */
  public function getCiphertext()
  {
    return $this->ciphertext;
  }
  /**
   * Optional. An optional CRC32C checksum of the DecryptRequest.ciphertext. If
   * specified, KeyManagementService will verify the integrity of the received
   * DecryptRequest.ciphertext using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(DecryptRequest.ciphertext) is equal to
   * DecryptRequest.ciphertext_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $ciphertextCrc32c
   */
  public function setCiphertextCrc32c($ciphertextCrc32c)
  {
    $this->ciphertextCrc32c = $ciphertextCrc32c;
  }
  /**
   * @return string
   */
  public function getCiphertextCrc32c()
  {
    return $this->ciphertextCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DecryptRequest::class, 'Google_Service_CloudKMS_DecryptRequest');
