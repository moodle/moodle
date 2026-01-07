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

class MacSignRequest extends \Google\Model
{
  /**
   * Required. The data to sign. The MAC tag is computed over this data field
   * based on the specific algorithm.
   *
   * @var string
   */
  public $data;
  /**
   * Optional. An optional CRC32C checksum of the MacSignRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacSignRequest.data using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacSignRequest.data) is equal
   * to MacSignRequest.data_crc32c, and if so, perform a limited number of
   * retries. A persistent mismatch may indicate an issue in your computation of
   * the CRC32C checksum. Note: This field is defined as int64 for reasons of
   * compatibility across different languages. However, it is a non-negative
   * integer, which will never exceed 2^32-1, and can be safely downconverted to
   * uint32 in languages that support this type.
   *
   * @var string
   */
  public $dataCrc32c;

  /**
   * Required. The data to sign. The MAC tag is computed over this data field
   * based on the specific algorithm.
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
   * Optional. An optional CRC32C checksum of the MacSignRequest.data. If
   * specified, KeyManagementService will verify the integrity of the received
   * MacSignRequest.data using this checksum. KeyManagementService will report
   * an error if the checksum verification fails. If you receive a checksum
   * error, your client should verify that CRC32C(MacSignRequest.data) is equal
   * to MacSignRequest.data_crc32c, and if so, perform a limited number of
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MacSignRequest::class, 'Google_Service_CloudKMS_MacSignRequest');
