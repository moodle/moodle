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

class GoogleCloudAiplatformV1ReasoningEngine extends \Google\Model
{
  protected $contextSpecType = GoogleCloudAiplatformV1ReasoningEngineContextSpec::class;
  protected $contextSpecDataType = '';
  /**
   * Output only. Timestamp when this ReasoningEngine was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the ReasoningEngine.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the ReasoningEngine.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Labels for the ReasoningEngine.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the ReasoningEngine. Format: `projects/{pr
   * oject}/locations/{location}/reasoningEngines/{reasoning_engine}`
   *
   * @var string
   */
  public $name;
  protected $specType = GoogleCloudAiplatformV1ReasoningEngineSpec::class;
  protected $specDataType = '';
  /**
   * Output only. Timestamp when this ReasoningEngine was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Configuration for how Agent Engine sub-resources should manage
   * context.
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineContextSpec $contextSpec
   */
  public function setContextSpec(GoogleCloudAiplatformV1ReasoningEngineContextSpec $contextSpec)
  {
    $this->contextSpec = $contextSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineContextSpec
   */
  public function getContextSpec()
  {
    return $this->contextSpec;
  }
  /**
   * Output only. Timestamp when this ReasoningEngine was created.
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
   * Optional. The description of the ReasoningEngine.
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
   * Required. The display name of the ReasoningEngine.
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
   * Customer-managed encryption key spec for a ReasoningEngine. If set, this
   * ReasoningEngine and all sub-resources of this ReasoningEngine will be
   * secured by this key.
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
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
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
   * Labels for the ReasoningEngine.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the ReasoningEngine. Format: `projects/{pr
   * oject}/locations/{location}/reasoningEngines/{reasoning_engine}`
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
   * Optional. Configurations of the ReasoningEngine
   *
   * @param GoogleCloudAiplatformV1ReasoningEngineSpec $spec
   */
  public function setSpec(GoogleCloudAiplatformV1ReasoningEngineSpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return GoogleCloudAiplatformV1ReasoningEngineSpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * Output only. Timestamp when this ReasoningEngine was most recently updated.
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
class_alias(GoogleCloudAiplatformV1ReasoningEngine::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReasoningEngine');
