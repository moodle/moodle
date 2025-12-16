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

namespace Google\Service\SecretManager;

class SecretPayload extends \Google\Model
{
  /**
   * The secret data. Must be no larger than 64KiB.
   *
   * @var string
   */
  public $data;
  /**
   * Optional. If specified, SecretManagerService will verify the integrity of
   * the received data on SecretManagerService.AddSecretVersion calls using the
   * crc32c checksum and store it to include in future
   * SecretManagerService.AccessSecretVersion responses. If a checksum is not
   * provided in the SecretManagerService.AddSecretVersion request, the
   * SecretManagerService will generate and store one for you. The CRC32C value
   * is encoded as a Int64 for compatibility, and can be safely downconverted to
   * uint32 in languages that support this type.
   * https://cloud.google.com/apis/design/design_patterns#integer_types
   *
   * @var string
   */
  public $dataCrc32c;

  /**
   * The secret data. Must be no larger than 64KiB.
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
   * Optional. If specified, SecretManagerService will verify the integrity of
   * the received data on SecretManagerService.AddSecretVersion calls using the
   * crc32c checksum and store it to include in future
   * SecretManagerService.AccessSecretVersion responses. If a checksum is not
   * provided in the SecretManagerService.AddSecretVersion request, the
   * SecretManagerService will generate and store one for you. The CRC32C value
   * is encoded as a Int64 for compatibility, and can be safely downconverted to
   * uint32 in languages that support this type.
   * https://cloud.google.com/apis/design/design_patterns#integer_types
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
class_alias(SecretPayload::class, 'Google_Service_SecretManager_SecretPayload');
