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

class GenerateRandomBytesResponse extends \Google\Model
{
  /**
   * The generated data.
   *
   * @var string
   */
  public $data;
  /**
   * Integrity verification field. A CRC32C checksum of the returned
   * GenerateRandomBytesResponse.data. An integrity check of
   * GenerateRandomBytesResponse.data can be performed by computing the CRC32C
   * checksum of GenerateRandomBytesResponse.data and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
   *
   * @var string
   */
  public $dataCrc32c;

  /**
   * The generated data.
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
   * Integrity verification field. A CRC32C checksum of the returned
   * GenerateRandomBytesResponse.data. An integrity check of
   * GenerateRandomBytesResponse.data can be performed by computing the CRC32C
   * checksum of GenerateRandomBytesResponse.data and comparing your results to
   * this field. Discard the response in case of non-matching checksum values,
   * and perform a limited number of retries. A persistent mismatch may indicate
   * an issue in your computation of the CRC32C checksum. Note: This field is
   * defined as int64 for reasons of compatibility across different languages.
   * However, it is a non-negative integer, which will never exceed 2^32-1, and
   * can be safely downconverted to uint32 in languages that support this type.
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
class_alias(GenerateRandomBytesResponse::class, 'Google_Service_CloudKMS_GenerateRandomBytesResponse');
