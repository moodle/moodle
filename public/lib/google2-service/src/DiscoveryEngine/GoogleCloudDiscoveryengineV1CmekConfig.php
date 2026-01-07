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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1CmekConfig extends \Google\Collection
{
  /**
   * The NotebookLM state is unknown.
   */
  public const NOTEBOOKLM_STATE_NOTEBOOK_LM_STATE_UNSPECIFIED = 'NOTEBOOK_LM_STATE_UNSPECIFIED';
  /**
   * The NotebookLM is not ready.
   */
  public const NOTEBOOKLM_STATE_NOTEBOOK_LM_NOT_READY = 'NOTEBOOK_LM_NOT_READY';
  /**
   * The NotebookLM is ready to be used.
   */
  public const NOTEBOOKLM_STATE_NOTEBOOK_LM_READY = 'NOTEBOOK_LM_READY';
  /**
   * The NotebookLM is not enabled.
   */
  public const NOTEBOOKLM_STATE_NOTEBOOK_LM_NOT_ENABLED = 'NOTEBOOK_LM_NOT_ENABLED';
  /**
   * The CmekConfig state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The CmekConfig is creating.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The CmekConfig can be used with DataStores.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The CmekConfig is unavailable, most likely due to the KMS Key being
   * revoked.
   */
  public const STATE_KEY_ISSUE = 'KEY_ISSUE';
  /**
   * The CmekConfig is deleting.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The CmekConfig deletion process failed.
   */
  public const STATE_DELETE_FAILED = 'DELETE_FAILED';
  /**
   * The CmekConfig is not usable, most likely due to some internal issue.
   */
  public const STATE_UNUSABLE = 'UNUSABLE';
  /**
   * The KMS key version is being rotated.
   */
  public const STATE_ACTIVE_ROTATING = 'ACTIVE_ROTATING';
  /**
   * The KMS key is soft deleted. Some cleanup policy will eventually be
   * applied.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The KMS key is expired, meaning the key has been disabled for 30+ days. The
   * customer can call DeleteCmekConfig to change the state to DELETED.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  protected $collection_key = 'singleRegionKeys';
  /**
   * Output only. The default CmekConfig for the Customer.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Required. KMS key resource name which will be used to encrypt resources `pr
   * ojects/{project}/locations/{location}/keyRings/{keyRing}/cryptoKeys/{keyId}
   * `.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Output only. KMS key version resource name which will be used to encrypt
   * resources `/cryptoKeyVersions/{keyVersion}`.
   *
   * @var string
   */
  public $kmsKeyVersion;
  /**
   * Output only. The timestamp of the last key rotation.
   *
   * @var string
   */
  public $lastRotationTimestampMicros;
  /**
   * Required. The name of the CmekConfig of the form
   * `projects/{project}/locations/{location}/cmekConfig` or
   * `projects/{project}/locations/{location}/cmekConfigs/{cmek_config}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Whether the NotebookLM Corpus is ready to be used.
   *
   * @var string
   */
  public $notebooklmState;
  protected $singleRegionKeysType = GoogleCloudDiscoveryengineV1SingleRegionKey::class;
  protected $singleRegionKeysDataType = 'array';
  /**
   * Output only. The states of the CmekConfig.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The default CmekConfig for the Customer.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Required. KMS key resource name which will be used to encrypt resources `pr
   * ojects/{project}/locations/{location}/keyRings/{keyRing}/cryptoKeys/{keyId}
   * `.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Output only. KMS key version resource name which will be used to encrypt
   * resources `/cryptoKeyVersions/{keyVersion}`.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
  /**
   * Output only. The timestamp of the last key rotation.
   *
   * @param string $lastRotationTimestampMicros
   */
  public function setLastRotationTimestampMicros($lastRotationTimestampMicros)
  {
    $this->lastRotationTimestampMicros = $lastRotationTimestampMicros;
  }
  /**
   * @return string
   */
  public function getLastRotationTimestampMicros()
  {
    return $this->lastRotationTimestampMicros;
  }
  /**
   * Required. The name of the CmekConfig of the form
   * `projects/{project}/locations/{location}/cmekConfig` or
   * `projects/{project}/locations/{location}/cmekConfigs/{cmek_config}`.
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
   * Output only. Whether the NotebookLM Corpus is ready to be used.
   *
   * Accepted values: NOTEBOOK_LM_STATE_UNSPECIFIED, NOTEBOOK_LM_NOT_READY,
   * NOTEBOOK_LM_READY, NOTEBOOK_LM_NOT_ENABLED
   *
   * @param self::NOTEBOOKLM_STATE_* $notebooklmState
   */
  public function setNotebooklmState($notebooklmState)
  {
    $this->notebooklmState = $notebooklmState;
  }
  /**
   * @return self::NOTEBOOKLM_STATE_*
   */
  public function getNotebooklmState()
  {
    return $this->notebooklmState;
  }
  /**
   * Optional. Single-regional CMEKs that are required for some VAIS features.
   *
   * @param GoogleCloudDiscoveryengineV1SingleRegionKey[] $singleRegionKeys
   */
  public function setSingleRegionKeys($singleRegionKeys)
  {
    $this->singleRegionKeys = $singleRegionKeys;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1SingleRegionKey[]
   */
  public function getSingleRegionKeys()
  {
    return $this->singleRegionKeys;
  }
  /**
   * Output only. The states of the CmekConfig.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, KEY_ISSUE, DELETING,
   * DELETE_FAILED, UNUSABLE, ACTIVE_ROTATING, DELETED, EXPIRED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CmekConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CmekConfig');
