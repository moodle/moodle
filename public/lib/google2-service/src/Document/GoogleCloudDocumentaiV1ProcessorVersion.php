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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessorVersion extends \Google\Model
{
  /**
   * The processor version has unspecified model type.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * The processor version has generative model type.
   */
  public const MODEL_TYPE_MODEL_TYPE_GENERATIVE = 'MODEL_TYPE_GENERATIVE';
  /**
   * The processor version has custom model type.
   */
  public const MODEL_TYPE_MODEL_TYPE_CUSTOM = 'MODEL_TYPE_CUSTOM';
  /**
   * The processor version is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The processor version is deployed and can be used for processing.
   */
  public const STATE_DEPLOYED = 'DEPLOYED';
  /**
   * The processor version is being deployed.
   */
  public const STATE_DEPLOYING = 'DEPLOYING';
  /**
   * The processor version is not deployed and cannot be used for processing.
   */
  public const STATE_UNDEPLOYED = 'UNDEPLOYED';
  /**
   * The processor version is being undeployed.
   */
  public const STATE_UNDEPLOYING = 'UNDEPLOYING';
  /**
   * The processor version is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The processor version is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The processor version failed and is in an indeterminate state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The processor version is being imported.
   */
  public const STATE_IMPORTING = 'IMPORTING';
  /**
   * Output only. The time the processor version was created.
   *
   * @var string
   */
  public $createTime;
  protected $deprecationInfoType = GoogleCloudDocumentaiV1ProcessorVersionDeprecationInfo::class;
  protected $deprecationInfoDataType = '';
  /**
   * The display name of the processor version.
   *
   * @var string
   */
  public $displayName;
  protected $documentSchemaType = GoogleCloudDocumentaiV1DocumentSchema::class;
  protected $documentSchemaDataType = '';
  protected $genAiModelInfoType = GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo::class;
  protected $genAiModelInfoDataType = '';
  /**
   * Output only. Denotes that this `ProcessorVersion` is managed by Google.
   *
   * @var bool
   */
  public $googleManaged;
  /**
   * Output only. The KMS key name used for encryption.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. The KMS key version with which data is encrypted.
   *
   * @var string
   */
  public $kmsKeyVersionName;
  protected $latestEvaluationType = GoogleCloudDocumentaiV1EvaluationReference::class;
  protected $latestEvaluationDataType = '';
  /**
   * Output only. The model type of this processor version.
   *
   * @var string
   */
  public $modelType;
  /**
   * Identifier. The resource name of the processor version. Format: `projects/{
   * project}/locations/{location}/processors/{processor}/processorVersions/{pro
   * cessor_version}`
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
   * Output only. The state of the processor version.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The time the processor version was created.
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
   * Output only. If set, information about the eventual deprecation of this
   * version.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersionDeprecationInfo $deprecationInfo
   */
  public function setDeprecationInfo(GoogleCloudDocumentaiV1ProcessorVersionDeprecationInfo $deprecationInfo)
  {
    $this->deprecationInfo = $deprecationInfo;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersionDeprecationInfo
   */
  public function getDeprecationInfo()
  {
    return $this->deprecationInfo;
  }
  /**
   * The display name of the processor version.
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
   * Output only. The schema of the processor version. Describes the output.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchema $documentSchema
   */
  public function setDocumentSchema(GoogleCloudDocumentaiV1DocumentSchema $documentSchema)
  {
    $this->documentSchema = $documentSchema;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchema
   */
  public function getDocumentSchema()
  {
    return $this->documentSchema;
  }
  /**
   * Output only. Information about Generative AI model-based processor
   * versions.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo $genAiModelInfo
   */
  public function setGenAiModelInfo(GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo $genAiModelInfo)
  {
    $this->genAiModelInfo = $genAiModelInfo;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersionGenAiModelInfo
   */
  public function getGenAiModelInfo()
  {
    return $this->genAiModelInfo;
  }
  /**
   * Output only. Denotes that this `ProcessorVersion` is managed by Google.
   *
   * @param bool $googleManaged
   */
  public function setGoogleManaged($googleManaged)
  {
    $this->googleManaged = $googleManaged;
  }
  /**
   * @return bool
   */
  public function getGoogleManaged()
  {
    return $this->googleManaged;
  }
  /**
   * Output only. The KMS key name used for encryption.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Output only. The KMS key version with which data is encrypted.
   *
   * @param string $kmsKeyVersionName
   */
  public function setKmsKeyVersionName($kmsKeyVersionName)
  {
    $this->kmsKeyVersionName = $kmsKeyVersionName;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersionName()
  {
    return $this->kmsKeyVersionName;
  }
  /**
   * Output only. The most recently invoked evaluation for the processor
   * version.
   *
   * @param GoogleCloudDocumentaiV1EvaluationReference $latestEvaluation
   */
  public function setLatestEvaluation(GoogleCloudDocumentaiV1EvaluationReference $latestEvaluation)
  {
    $this->latestEvaluation = $latestEvaluation;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationReference
   */
  public function getLatestEvaluation()
  {
    return $this->latestEvaluation;
  }
  /**
   * Output only. The model type of this processor version.
   *
   * Accepted values: MODEL_TYPE_UNSPECIFIED, MODEL_TYPE_GENERATIVE,
   * MODEL_TYPE_CUSTOM
   *
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
  /**
   * Identifier. The resource name of the processor version. Format: `projects/{
   * project}/locations/{location}/processors/{processor}/processorVersions/{pro
   * cessor_version}`
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
   * Output only. The state of the processor version.
   *
   * Accepted values: STATE_UNSPECIFIED, DEPLOYED, DEPLOYING, UNDEPLOYED,
   * UNDEPLOYING, CREATING, DELETING, FAILED, IMPORTING
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
class_alias(GoogleCloudDocumentaiV1ProcessorVersion::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessorVersion');
