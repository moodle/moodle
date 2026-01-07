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

class MacVerifyRequest extends \Google\Model
{
  /**
   * Required. The data used previously as a MacSignRequest.data to generate the
   * MAC tag.
   *
   * @var string
   */
  public $data;
  /**
   * Optional. An optional CRC32C checksum of the MacVerifyRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacVerifyRequest.data using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacVerifyRequest.data) is
   * equal to MacVerifyRequest.data_crc32c, and if so, perform a limited number
   * of retries. A persistent mismatch may indicate an issue in your computation
   * of the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $dataCrc32c;
  /**
   * Required. The signature to verify.
   *
   * @var string
   */
  public $mac;
  /**
   * Optional. An optional CRC32C checksum of the MacVerifyRequest.mac. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacVerifyRequest.mac using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacVerifyRequest.mac) is equal
   * to MacVerifyRequest.mac_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $macCrc32c;

  /**
   * Required. The data used previously as a MacSignRequest.data to generate the
   * MAC tag.
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
   * Optional. An optional CRC32C checksum of the MacVerifyRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacVerifyRequest.data using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacVerifyRequest.data) is
   * equal to MacVerifyRequest.data_crc32c, and if so, perform a limited number
   * of retries. A persistent mismatch may indicate an issue in your computation
   * of the CRC32C checksum. Note: This field is defined as int64 for reasons of
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
   * Required. The signature to verify.
   *
   * @param string $mac
   */
  public function setMac($mac)
  {
    $this->mac = $mac;
  }
  /**
   * @return string
   */
  public function getMac()
  {
    return $this->mac;
  }
  /**
   * Optional. An optional CRC32C checksum of the MacVerifyRequest.mac. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacVerifyRequest.mac using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacVerifyRequest.mac) is equal
   * to MacVerifyRequest.mac_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @param string $macCrc32c
   */
  public function setMacCrc32c($macCrc32c)
  {
    $this->macCrc32c = $macCrc32c;
  }
  /**
   * @return string
   */
  public function getMacCrc32c()
  {
    return $this->macCrc32c;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MacVerifyRequest::class, 'Google_Service_CloudKMS_MacVerifyRequest');
