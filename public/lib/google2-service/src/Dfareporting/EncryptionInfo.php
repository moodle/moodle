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

namespace Google\Service\Dfareporting;

class EncryptionInfo extends \Google\Model
{
  public const ENCRYPTION_ENTITY_TYPE_ENCRYPTION_ENTITY_TYPE_UNKNOWN = 'ENCRYPTION_ENTITY_TYPE_UNKNOWN';
  public const ENCRYPTION_ENTITY_TYPE_DCM_ACCOUNT = 'DCM_ACCOUNT';
  public const ENCRYPTION_ENTITY_TYPE_DCM_ADVERTISER = 'DCM_ADVERTISER';
  public const ENCRYPTION_ENTITY_TYPE_DBM_PARTNER = 'DBM_PARTNER';
  public const ENCRYPTION_ENTITY_TYPE_DBM_ADVERTISER = 'DBM_ADVERTISER';
  public const ENCRYPTION_ENTITY_TYPE_ADWORDS_CUSTOMER = 'ADWORDS_CUSTOMER';
  public const ENCRYPTION_ENTITY_TYPE_DFP_NETWORK_CODE = 'DFP_NETWORK_CODE';
  public const ENCRYPTION_SOURCE_ENCRYPTION_SCOPE_UNKNOWN = 'ENCRYPTION_SCOPE_UNKNOWN';
  public const ENCRYPTION_SOURCE_AD_SERVING = 'AD_SERVING';
  public const ENCRYPTION_SOURCE_DATA_TRANSFER = 'DATA_TRANSFER';
  /**
   * The encryption entity ID. This should match the encryption configuration
   * for ad serving or Data Transfer.
   *
   * @var string
   */
  public $encryptionEntityId;
  /**
   * The encryption entity type. This should match the encryption configuration
   * for ad serving or Data Transfer.
   *
   * @var string
   */
  public $encryptionEntityType;
  /**
   * Describes whether the encrypted cookie was received from ad serving (the %m
   * macro) or from Data Transfer.
   *
   * @var string
   */
  public $encryptionSource;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#encryptionInfo".
   *
   * @var string
   */
  public $kind;

  /**
   * The encryption entity ID. This should match the encryption configuration
   * for ad serving or Data Transfer.
   *
   * @param string $encryptionEntityId
   */
  public function setEncryptionEntityId($encryptionEntityId)
  {
    $this->encryptionEntityId = $encryptionEntityId;
  }
  /**
   * @return string
   */
  public function getEncryptionEntityId()
  {
    return $this->encryptionEntityId;
  }
  /**
   * The encryption entity type. This should match the encryption configuration
   * for ad serving or Data Transfer.
   *
   * Accepted values: ENCRYPTION_ENTITY_TYPE_UNKNOWN, DCM_ACCOUNT,
   * DCM_ADVERTISER, DBM_PARTNER, DBM_ADVERTISER, ADWORDS_CUSTOMER,
   * DFP_NETWORK_CODE
   *
   * @param self::ENCRYPTION_ENTITY_TYPE_* $encryptionEntityType
   */
  public function setEncryptionEntityType($encryptionEntityType)
  {
    $this->encryptionEntityType = $encryptionEntityType;
  }
  /**
   * @return self::ENCRYPTION_ENTITY_TYPE_*
   */
  public function getEncryptionEntityType()
  {
    return $this->encryptionEntityType;
  }
  /**
   * Describes whether the encrypted cookie was received from ad serving (the %m
   * macro) or from Data Transfer.
   *
   * Accepted values: ENCRYPTION_SCOPE_UNKNOWN, AD_SERVING, DATA_TRANSFER
   *
   * @param self::ENCRYPTION_SOURCE_* $encryptionSource
   */
  public function setEncryptionSource($encryptionSource)
  {
    $this->encryptionSource = $encryptionSource;
  }
  /**
   * @return self::ENCRYPTION_SOURCE_*
   */
  public function getEncryptionSource()
  {
    return $this->encryptionSource;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#encryptionInfo".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionInfo::class, 'Google_Service_Dfareporting_EncryptionInfo');
