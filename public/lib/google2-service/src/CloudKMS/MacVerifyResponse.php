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

class MacVerifyResponse extends \Google\Model
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
   * The resource name of the CryptoKeyVersion used for verification. Check this
   * field to verify that the intended resource was used for verification.
   *
   * @var string
   */
  public $name;
  /**
   * The ProtectionLevel of the CryptoKeyVersion used for verification.
   *
   * @var string
   */
  public $protectionLevel;
  /**
   * This field indicates whether or not the verification operation for
   * MacVerifyRequest.mac over MacVerifyRequest.data was successful.
   *
   * @var bool
   */
  public $success;
  /**
   * Integrity verification field. A flag indicating whether
   * MacVerifyRequest.data_crc32c was received by KeyManagementService and used
   * for the integrity verification of the data. A false value of this field
   * indicates either that MacVerifyRequest.data_crc32c was left unset or that
   * it was not delivered to KeyManagementService. If you've set
   * MacVerifyRequest.data_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedDataCrc32c;
  /**
   * Integrity verification field. A flag indicating whether
   * MacVerifyRequest.mac_crc32c was received by KeyManagementService and used
   * for the integrity verification of the data. A false value of this field
   * indicates either that MacVerifyRequest.mac_crc32c was left unset or that it
   * was not delivered to KeyManagementService. If you've set
   * MacVerifyRequest.mac_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedMacCrc32c;
  /**
   * Integrity verification field. This value is used for the integrity
   * verification of [MacVerifyResponse.success]. If the value of this field
   * contradicts the value of [MacVerifyResponse.success], discard the response
   * and perform a limited number of retries.
   *
   * @var bool
   */
  public $verifiedSuccessIntegrity;

  /**
   * The resource name of the CryptoKeyVersion used for verification. Check this
   * field to verify that the intended resource was used for verification.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The ProtectionLevel of the CryptoKeyVersion used for verification.
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
  /**
   * This field indicates whether or not the verification operation for
   * MacVerifyRequest.mac over MacVerifyRequest.data was successful.
   *
   * @param bool $success
   */
  public function setSuccess($success)
  {
    $this->success = $success;
  }
  /**
   * @return bool
   */
  public function getSuccess()
  {
    return $this->success;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * MacVerifyRequest.data_crc32c was received by KeyManagementService and used
   * for the integrity verification of the data. A false value of this field
   * indicates either that MacVerifyRequest.data_crc32c was left unset or that
   * it was not delivered to KeyManagementService. If you've set
   * MacVerifyRequest.data_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @param bool $verifiedDataCrc32c
   */
  public function setVerifiedDataCrc32c($verifiedDataCrc32c)
  {
    $this->verifiedDataCrc32c = $verifiedDataCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedDataCrc32c()
  {
    return $this->verifiedDataCrc32c;
  }
  /**
   * Integrity verification field. A flag indicating whether
   * MacVerifyRequest.mac_crc32c was received by KeyManagementService and used
   * for the integrity verification of the data. A false value of this field
   * indicates either that MacVerifyRequest.mac_crc32c was left unset or that it
   * was not delivered to KeyManagementService. If you've set
   * MacVerifyRequest.mac_crc32c but this field is still false, discard the
   * response and perform a limited number of retries.
   *
   * @param bool $verifiedMacCrc32c
   */
  public function setVerifiedMacCrc32c($verifiedMacCrc32c)
  {
    $this->verifiedMacCrc32c = $verifiedMacCrc32c;
  }
  /**
   * @return bool
   */
  public function getVerifiedMacCrc32c()
  {
    return $this->verifiedMacCrc32c;
  }
  /**
   * Integrity verification field. This value is used for the integrity
   * verification of [MacVerifyResponse.success]. If the value of this field
   * contradicts the value of [MacVerifyResponse.success], discard the response
   * and perform a limited number of retries.
   *
   * @param bool $verifiedSuccessIntegrity
   */
  public function setVerifiedSuccessIntegrity($verifiedSuccessIntegrity)
  {
    $this->verifiedSuccessIntegrity = $verifiedSuccessIntegrity;
  }
  /**
   * @return bool
   */
  public function getVerifiedSuccessIntegrity()
  {
    return $this->verifiedSuccessIntegrity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MacVerifyResponse::class, 'Google_Service_CloudKMS_MacVerifyResponse');
