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

class AsymmetricSignRequest extends \Google\Model
{
  /**
   * Optional. The data to sign. It can't be supplied if
   * AsymmetricSignRequest.digest is supplied.
   *
   * @var string
   */
  public $data;
  /**
   * Optional. An optional CRC32C checksum of the AsymmetricSignRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * AsymmetricSignRequest.data using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(AsymmetricSignRequest.data) is equal to
   * AsymmetricSignRequest.data_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $dataCrc32c;
  protected $digestType = Digest::class;
  protected $digestDataType = '';
  /**
   * Optional. An optional CRC32C checksum of the AsymmetricSignRequest.digest.
   * If specified, KeyManagementService will verify the integrity of the
   * received AsymmetricSignRequest.digest using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(AsymmetricSignRequest.digest) is equal to
   * AsymmetricSignRequest.digest_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $digestCrc32c;

  /**
   * Optional. The data to sign. It can't be supplied if
   * AsymmetricSignRequest.digest is supplied.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Optional. An optional CRC32C checksum of the AsymmetricSignRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * AsymmetricSignRequest.data using this checksum. KeyManagementService will
   * report an error if the checksum verification fails. If you receive a
   * checksum error, your client should verify that
   * CRC32C(AsymmetricSignRequest.data) is equal to
   * AsymmetricSignRequest.data_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $dataCrc32c
   */
  public function setDataCrc32c($dataCrc32c)
  {
    $this->dataCrc32c = $dataCrc32c;
  }
  /**
   * @return string
   */
  public function getDataCrc32c()
  {
    return $this->dataCrc32c;
  }
  /**
   * Optional. The digest of the data to sign. The digest must be produced with
   * the same digest algorithm as specified by the key version's algorithm. This
   * field may not be supplied if AsymmetricSignRequest.data is supplied.
   *
   * @param Digest $digest
   */
  public function setDigest(Digest $digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return Digest
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * Optional. An optional CRC32C checksum of the AsymmetricSignRequest.digest.
   * If specified, KeyManagementService will verify the integrity of the
   * received AsymmetricSignRequest.digest using this checksum.
   * KeyManagementService will report an error if the checksum verification
   * fails. If you receive a checksum error, your client should verify that
   * CRC32C(AsymmetricSignRequest.digest) is equal to
   * AsymmetricSignRequest.digest_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $digestCrc32c
   */
  public function setDigestCrc32c($digestCrc32c)
  {
    $this->digestCrc32c = $digestCrc32c;
  }
  /**
   * @return string
   */
  public function getDigestCrc32c()
  {
    return $this->digestCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AsymmetricSignRequest::class, 'Google_Service_CloudKMS_AsymmetricSignRequest');
