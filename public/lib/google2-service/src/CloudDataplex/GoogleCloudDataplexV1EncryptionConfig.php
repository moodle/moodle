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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1EncryptionConfig extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const ENCRYPTION_STATE_ENCRYPTION_STATE_UNSPECIFIED = 'ENCRYPTION_STATE_UNSPECIFIED';
  /**
   * The encryption state of the database when the EncryptionConfig is created
   * or updated. If the encryption fails, it is retried indefinitely and the
   * state is shown as ENCRYPTING.
   */
  public const ENCRYPTION_STATE_ENCRYPTING = 'ENCRYPTING';
  /**
   * The encryption of data has completed successfully.
   */
  public const ENCRYPTION_STATE_COMPLETED = 'COMPLETED';
  /**
   * The encryption of data has failed. The state is set to FAILED when the
   * encryption fails due to reasons like permission issues, invalid key etc.
   */
  public const ENCRYPTION_STATE_FAILED = 'FAILED';
  /**
   * Output only. The time when the Encryption configuration was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Represent the state of CMEK opt-in for metastore.
   *
   * @var bool
   */
  public $enableMetastoreEncryption;
  /**
   * Output only. The state of encryption of the databases.
   *
   * @var string
   */
  public $encryptionState;
  /**
   * Etag of the EncryptionConfig. This is a strong etag.
   *
   * @var string
   */
  public $etag;
  protected $failureDetailsType = GoogleCloudDataplexV1EncryptionConfigFailureDetails::class;
  protected $failureDetailsDataType = '';
  /**
   * Optional. If a key is chosen, it means that the customer is using CMEK. If
   * a key is not chosen, it means that the customer is using Google managed
   * encryption.
   *
   * @var string
   */
  public $key;
  /**
   * Identifier. The resource name of the EncryptionConfig. Format: organization
   * s/{organization}/locations/{location}/encryptionConfigs/{encryption_config}
   * Global location is not supported.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the Encryption configuration was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the Encryption configuration was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Represent the state of CMEK opt-in for metastore.
   *
   * @param bool $enableMetastoreEncryption
   */
  public function setEnableMetastoreEncryption($enableMetastoreEncryption)
  {
    $this->enableMetastoreEncryption = $enableMetastoreEncryption;
  }
  /**
   * @return bool
   */
  public function getEnableMetastoreEncryption()
  {
    return $this->enableMetastoreEncryption;
  }
  /**
   * Output only. The state of encryption of the databases.
   *
   * Accepted values: ENCRYPTION_STATE_UNSPECIFIED, ENCRYPTING, COMPLETED,
   * FAILED
   *
   * @param self::ENCRYPTION_STATE_* $encryptionState
   */
  public function setEncryptionState($encryptionState)
  {
    $this->encryptionState = $encryptionState;
  }
  /**
   * @return self::ENCRYPTION_STATE_*
   */
  public function getEncryptionState()
  {
    return $this->encryptionState;
  }
  /**
   * Etag of the EncryptionConfig. This is a strong etag.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Details of the failure if anything related to Cmek db fails.
   *
   * @param GoogleCloudDataplexV1EncryptionConfigFailureDetails $failureDetails
   */
  public function setFailureDetails(GoogleCloudDataplexV1EncryptionConfigFailureDetails $failureDetails)
  {
    $this->failureDetails = $failureDetails;
  }
  /**
   * @return GoogleCloudDataplexV1EncryptionConfigFailureDetails
   */
  public function getFailureDetails()
  {
    return $this->failureDetails;
  }
  /**
   * Optional. If a key is chosen, it means that the customer is using CMEK. If
   * a key is not chosen, it means that the customer is using Google managed
   * encryption.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Identifier. The resource name of the EncryptionConfig. Format: organization
   * s/{organization}/locations/{location}/encryptionConfigs/{encryption_config}
   * Global location is not supported.
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
   * Output only. The time when the Encryption configuration was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1EncryptionConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EncryptionConfig');
