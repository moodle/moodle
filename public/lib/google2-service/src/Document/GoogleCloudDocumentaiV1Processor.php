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

class GoogleCloudDocumentaiV1Processor extends \Google\Collection
{
  /**
   * The processor is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The processor is enabled, i.e., has an enabled version which can currently
   * serve processing requests and all the feature dependencies have been
   * successfully initialized.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The processor is disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The processor is being enabled, will become `ENABLED` if successful.
   */
  public const STATE_ENABLING = 'ENABLING';
  /**
   * The processor is being disabled, will become `DISABLED` if successful.
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * The processor is being created, will become either `ENABLED` (for
   * successful creation) or `FAILED` (for failed ones). Once a processor is in
   * this state, it can then be used for document processing, but the feature
   * dependencies of the processor might not be fully created yet.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The processor failed during creation or initialization of feature
   * dependencies. The user should delete the processor and recreate one as all
   * the functionalities of the processor are disabled.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The processor is being deleted, will be removed if successful.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'processorVersionAliases';
  /**
   * Optional. SchemaVersion used by the Processor. It is the same as
   * Processor's DatasetSchema.schema_version Format is `projects/{project}/loca
   * tions/{location}/schemas/{schema}/schemaVersions/{schema_version}
   *
   * @var string
   */
  public $activeSchemaVersion;
  /**
   * Output only. The time the processor was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The default processor version.
   *
   * @var string
   */
  public $defaultProcessorVersion;
  /**
   * The display name of the processor.
   *
   * @var string
   */
  public $displayName;
  /**
   * The [KMS key](https://cloud.google.com/security-key-management) used for
   * encryption and decryption in CMEK scenarios.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. Immutable. The resource name of the processor. Format:
   * `projects/{project}/locations/{location}/processors/{processor}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. The http endpoint that can be called to invoke
   * processing.
   *
   * @var string
   */
  public $processEndpoint;
  protected $processorVersionAliasesType = GoogleCloudDocumentaiV1ProcessorVersionAlias::class;
  protected $processorVersionAliasesDataType = 'array';
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
   * Output only. The state of the processor.
   *
   * @var string
   */
  public $state;
  /**
   * The processor type, such as: `OCR_PROCESSOR`, `INVOICE_PROCESSOR`. To get a
   * list of processor types, see FetchProcessorTypes.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. SchemaVersion used by the Processor. It is the same as
   * Processor's DatasetSchema.schema_version Format is `projects/{project}/loca
   * tions/{location}/schemas/{schema}/schemaVersions/{schema_version}
   *
   * @param string $activeSchemaVersion
   */
  public function setActiveSchemaVersion($activeSchemaVersion)
  {
    $this->activeSchemaVersion = $activeSchemaVersion;
  }
  /**
   * @return string
   */
  public function getActiveSchemaVersion()
  {
    return $this->activeSchemaVersion;
  }
  /**
   * Output only. The time the processor was created.
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
   * The default processor version.
   *
   * @param string $defaultProcessorVersion
   */
  public function setDefaultProcessorVersion($defaultProcessorVersion)
  {
    $this->defaultProcessorVersion = $defaultProcessorVersion;
  }
  /**
   * @return string
   */
  public function getDefaultProcessorVersion()
  {
    return $this->defaultProcessorVersion;
  }
  /**
   * The display name of the processor.
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
   * The [KMS key](https://cloud.google.com/security-key-management) used for
   * encryption and decryption in CMEK scenarios.
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
   * Output only. Immutable. The resource name of the processor. Format:
   * `projects/{project}/locations/{location}/processors/{processor}`
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
   * Output only. Immutable. The http endpoint that can be called to invoke
   * processing.
   *
   * @param string $processEndpoint
   */
  public function setProcessEndpoint($processEndpoint)
  {
    $this->processEndpoint = $processEndpoint;
  }
  /**
   * @return string
   */
  public function getProcessEndpoint()
  {
    return $this->processEndpoint;
  }
  /**
   * Output only. The processor version aliases.
   *
   * @param GoogleCloudDocumentaiV1ProcessorVersionAlias[] $processorVersionAliases
   */
  public function setProcessorVersionAliases($processorVersionAliases)
  {
    $this->processorVersionAliases = $processorVersionAliases;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessorVersionAlias[]
   */
  public function getProcessorVersionAliases()
  {
    return $this->processorVersionAliases;
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
   * Output only. The state of the processor.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, ENABLING, DISABLING,
   * CREATING, FAILED, DELETING
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
  /**
   * The processor type, such as: `OCR_PROCESSOR`, `INVOICE_PROCESSOR`. To get a
   * list of processor types, see FetchProcessorTypes.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1Processor::class, 'Google_Service_Document_GoogleCloudDocumentaiV1Processor');
