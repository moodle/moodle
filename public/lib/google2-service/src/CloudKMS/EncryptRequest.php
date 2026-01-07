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

class EncryptRequest extends \Google\Model
{
  /**
   * Optional. Optional data that, if specified, must also be provided during
   * decryption through DecryptRequest.additional_authenticated_data. The
   * maximum size depends on the key version's protection_level. For SOFTWARE,
   * EXTERNAL, and EXTERNAL_VPC keys the AAD must be no larger than 64KiB. For
   * HSM keys, the combined length of the plaintext and
   * additional_authenticated_data fields must be no larger than 8KiB.
   *
   * @var string
   */
  public $additionalAuthenticatedData;
  /**
   * Optional. An optional CRC32C checksum of the
   * EncryptRequest.additional_authenticated_data. If specified,
   * KeyManagementService will verify the integrity of the received
   * EncryptRequest.additional_authenticated_data using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(EncryptRequest.additional_authenticated_data) is equal to
   * EncryptRequest.additional_authenticated_data_crc32c, and if so, perform a
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
   * Required. The data to encrypt. Must be no larger than 64KiB. The maximum
   * size depends on the key version's protection_level. For SOFTWARE, EXTERNAL,
   * and EXTERNAL_VPC keys, the plaintext must be no larger than 64KiB. For HSM
   * keys, the combined length of the plaintext and
   * additional_authenticated_data fields must be no larger than 8KiB.
   *
   * @var string
   */
  public $plaintext;
  /**
   * Optional. An optional CRC32C checksum of the EncryptRequest.plaintext. If
   * specified, KeyManagementService will verify the integrity of the received
   * EncryptRequest.plaintext using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(EncryptRequest.plaintext) is equal to
   * EncryptRequest.plaintext_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $plaintextCrc32c;

  /**
   * Optional. Optional data that, if specified, must also be provided during
   * decryption through DecryptRequest.additional_authenticated_data. The
   * maximum size depends on the key version's protection_level. For SOFTWARE,
   * EXTERNAL, and EXTERNAL_VPC keys the AAD must be no larger than 64KiB. For
   * HSM keys, the combined length of the plaintext and
   * additional_authenticated_data fields must be no larger than 8KiB.
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
   * EncryptRequest.additional_authenticated_data. If specified,
   * KeyManagementService will verify the integrity of the received
   * EncryptRequest.additional_authenticated_data using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(EncryptRequest.additional_authenticated_data) is equal to
   * EncryptRequest.additional_authenticated_data_crc32c, and if so, perform a
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
   * Required. The data to encrypt. Must be no larger than 64KiB. The maximum
   * size depends on the key version's protection_level. For SOFTWARE, EXTERNAL,
   * and EXTERNAL_VPC keys, the plaintext must be no larger than 64KiB. For HSM
   * keys, the combined length of the plaintext and
   * additional_authenticated_data fields must be no larger than 8KiB.
   *
   * @param string $plaintext
   */
  public function setPlaintext($plaintext)
  {
    $this->plaintext = $plaintext;
  }
  /**
   * @return string
   */
  public function getPlaintext()
  {
    return $this->plaintext;
  }
  /**
   * Optional. An optional CRC32C checksum of the EncryptRequest.plaintext. If
   * specified, KeyManagementService will verify the integrity of the received
   * EncryptRequest.plaintext using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(EncryptRequest.plaintext) is equal to
   * EncryptRequest.plaintext_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $plaintextCrc32c
   */
  public function setPlaintextCrc32c($plaintextCrc32c)
  {
    $this->plaintextCrc32c = $plaintextCrc32c;
  }
  /**
   * @return string
   */
  public function getPlaintextCrc32c()
  {
    return $this->plaintextCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptRequest::class, 'Google_Service_CloudKMS_EncryptRequest');
