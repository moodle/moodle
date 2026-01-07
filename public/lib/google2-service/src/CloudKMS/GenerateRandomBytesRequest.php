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

class GenerateRandomBytesRequest extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PROTECTION_LEVEL_PROTECTION_LEVEL_UNSPECIFIED = 'PROTECTION_LEVEL_UNSPECIFIED';
  /**
   * Crypto operations are performed in software.
   */
  public const PROTECTION_LEVEL_SOFTWARE = 'SOFTWARE';
  /**
   * Crypto operations are performed in a Hardware Security Module.
   */
  public const PROTECTION_LEVEL_HSM = 'HSM';
  /**
   * Crypto operations are performed by an external key manager.
   */
  public const PROTECTION_LEVEL_EXTERNAL = 'EXTERNAL';
  /**
   * Crypto operations are performed in an EKM-over-VPC backend.
   */
  public const PROTECTION_LEVEL_EXTERNAL_VPC = 'EXTERNAL_VPC';
  /**
   * Crypto operations are performed in a single-tenant HSM.
   */
  public const PROTECTION_LEVEL_HSM_SINGLE_TENANT = 'HSM_SINGLE_TENANT';
  /**
   * The length in bytes of the amount of randomness to retrieve. Minimum 8
   * bytes, maximum 1024 bytes.
   *
   * @var int
   */
  public $lengthBytes;
  /**
   * The ProtectionLevel to use when generating the random data. Currently, only
   * HSM protection level is supported.
   *
   * @var string
   */
  public $protectionLevel;

  /**
   * The length in bytes of the amount of randomness to retrieve. Minimum 8
   * bytes, maximum 1024 bytes.
   *
   * @param int $lengthBytes
   */
  public function setLengthBytes($lengthBytes)
  {
    $this->lengthBytes = $lengthBytes;
  }
  /**
   * @return int
   */
  public function getLengthBytes()
  {
    return $this->lengthBytes;
  }
  /**
   * The ProtectionLevel to use when generating the random data. Currently, only
   * HSM protection level is supported.
   *
   * Accepted values: PROTECTION_LEVEL_UNSPECIFIED, SOFTWARE, HSM, EXTERNAL,
   * EXTERNAL_VPC, HSM_SINGLE_TENANT
   *
   * @param self::PROTECTION_LEVEL_* $protectionLevel
   */
  public function setProtectionLevel($protectionLevel)
  {
    $this->protectionLevel = $protectionLevel;
  }
  /**
   * @return self::PROTECTION_LEVEL_*
   */
  public function getProtectionLevel()
  {
    return $this->protectionLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateRandomBytesRequest::class, 'Google_Service_CloudKMS_GenerateRandomBytesRequest');
