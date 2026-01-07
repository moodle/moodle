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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MetadataStore extends \Google\Model
{
  /**
   * Output only. Timestamp when this MetadataStore was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataplexConfigType = GoogleCloudAiplatformV1MetadataStoreDataplexConfig::class;
  protected $dataplexConfigDataType = '';
  /**
   * Description of the MetadataStore.
   *
   * @var string
   */
  public $description;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. The resource name of the MetadataStore instance.
   *
   * @var string
   */
  public $name;
  protected $stateType = GoogleCloudAiplatformV1MetadataStoreMetadataStoreState::class;
  protected $stateDataType = '';
  /**
   * Output only. Timestamp when this MetadataStore was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this MetadataStore was created.
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
   * Optional. Dataplex integration settings.
   *
   * @param GoogleCloudAiplatformV1MetadataStoreDataplexConfig $dataplexConfig
   */
  public function setDataplexConfig(GoogleCloudAiplatformV1MetadataStoreDataplexConfig $dataplexConfig)
  {
    $this->dataplexConfig = $dataplexConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1MetadataStoreDataplexConfig
   */
  public function getDataplexConfig()
  {
    return $this->dataplexConfig;
  }
  /**
   * Description of the MetadataStore.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Customer-managed encryption key spec for a Metadata Store. If set, this
   * Metadata Store and all sub-resources of this Metadata Store are secured
   * using this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Output only. The resource name of the MetadataStore instance.
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
   * Output only. State information of the MetadataStore.
   *
   * @param GoogleCloudAiplatformV1MetadataStoreMetadataStoreState $state
   */
  public function setState(GoogleCloudAiplatformV1MetadataStoreMetadataStoreState $state)
  {
    $this->state = $state;
  }
  /**
   * @return GoogleCloudAiplatformV1MetadataStoreMetadataStoreState
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Timestamp when this MetadataStore was last updated.
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
class_alias(GoogleCloudAiplatformV1MetadataStore::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MetadataStore');
