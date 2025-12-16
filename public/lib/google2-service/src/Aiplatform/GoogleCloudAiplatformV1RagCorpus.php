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

class GoogleCloudAiplatformV1RagCorpus extends \Google\Model
{
  protected $corpusStatusType = GoogleCloudAiplatformV1CorpusStatus::class;
  protected $corpusStatusDataType = '';
  /**
   * Output only. Timestamp when this RagCorpus was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the RagCorpus.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the RagCorpus. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Output only. The resource name of the RagCorpus.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Timestamp when this RagCorpus was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $vectorDbConfigType = GoogleCloudAiplatformV1RagVectorDbConfig::class;
  protected $vectorDbConfigDataType = '';
  protected $vertexAiSearchConfigType = GoogleCloudAiplatformV1VertexAiSearchConfig::class;
  protected $vertexAiSearchConfigDataType = '';

  /**
   * Output only. RagCorpus state.
   *
   * @param GoogleCloudAiplatformV1CorpusStatus $corpusStatus
   */
  public function setCorpusStatus(GoogleCloudAiplatformV1CorpusStatus $corpusStatus)
  {
    $this->corpusStatus = $corpusStatus;
  }
  /**
   * @return GoogleCloudAiplatformV1CorpusStatus
   */
  public function getCorpusStatus()
  {
    return $this->corpusStatus;
  }
  /**
   * Output only. Timestamp when this RagCorpus was created.
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
   * Optional. The description of the RagCorpus.
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
   * Required. The display name of the RagCorpus. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Immutable. The CMEK key name used to encrypt at-rest data related
   * to this Corpus. Only applicable to RagManagedDb option for Vector DB. This
   * field can only be set at corpus creation time, and cannot be updated or
   * deleted.
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
   * Output only. The resource name of the RagCorpus.
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Timestamp when this RagCorpus was last updated.
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
  /**
   * Optional. Immutable. The config for the Vector DBs.
   *
   * @param GoogleCloudAiplatformV1RagVectorDbConfig $vectorDbConfig
   */
  public function setVectorDbConfig(GoogleCloudAiplatformV1RagVectorDbConfig $vectorDbConfig)
  {
    $this->vectorDbConfig = $vectorDbConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1RagVectorDbConfig
   */
  public function getVectorDbConfig()
  {
    return $this->vectorDbConfig;
  }
  /**
   * Optional. Immutable. The config for the Vertex AI Search.
   *
   * @param GoogleCloudAiplatformV1VertexAiSearchConfig $vertexAiSearchConfig
   */
  public function setVertexAiSearchConfig(GoogleCloudAiplatformV1VertexAiSearchConfig $vertexAiSearchConfig)
  {
    $this->vertexAiSearchConfig = $vertexAiSearchConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1VertexAiSearchConfig
   */
  public function getVertexAiSearchConfig()
  {
    return $this->vertexAiSearchConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagCorpus::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagCorpus');
