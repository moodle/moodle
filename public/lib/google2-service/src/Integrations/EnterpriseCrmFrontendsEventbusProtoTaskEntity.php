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

namespace Google\Service\Integrations;

class EnterpriseCrmFrontendsEventbusProtoTaskEntity extends \Google\Model
{
  /**
   * Normal IP task
   */
  public const TASK_TYPE_TASK = 'TASK';
  /**
   * Task is of As-Is Template type
   */
  public const TASK_TYPE_ASIS_TEMPLATE = 'ASIS_TEMPLATE';
  /**
   * Task is of I/O template type with a different underlying task
   */
  public const TASK_TYPE_IO_TEMPLATE = 'IO_TEMPLATE';
  /**
   * True if the task has conflict with vpcsc
   *
   * @var bool
   */
  public $disabledForVpcSc;
  protected $metadataType = EnterpriseCrmEventbusProtoTaskMetadata::class;
  protected $metadataDataType = '';
  protected $paramSpecsType = EnterpriseCrmFrontendsEventbusProtoParamSpecsMessage::class;
  protected $paramSpecsDataType = '';
  protected $statsType = EnterpriseCrmEventbusStats::class;
  protected $statsDataType = '';
  /**
   * Defines the type of the task
   *
   * @var string
   */
  public $taskType;
  protected $uiConfigType = EnterpriseCrmEventbusProtoTaskUiConfig::class;
  protected $uiConfigDataType = '';

  /**
   * True if the task has conflict with vpcsc
   *
   * @param bool $disabledForVpcSc
   */
  public function setDisabledForVpcSc($disabledForVpcSc)
  {
    $this->disabledForVpcSc = $disabledForVpcSc;
  }
  /**
   * @return bool
   */
  public function getDisabledForVpcSc()
  {
    return $this->disabledForVpcSc;
  }
  /**
   * Metadata inclueds the task name, author and so on.
   *
   * @param EnterpriseCrmEventbusProtoTaskMetadata $metadata
   */
  public function setMetadata(EnterpriseCrmEventbusProtoTaskMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Declarations for inputs/outputs for a TypedTask. This is also associated
   * with the METADATA mask.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoParamSpecsMessage $paramSpecs
   */
  public function setParamSpecs(EnterpriseCrmFrontendsEventbusProtoParamSpecsMessage $paramSpecs)
  {
    $this->paramSpecs = $paramSpecs;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoParamSpecsMessage
   */
  public function getParamSpecs()
  {
    return $this->paramSpecs;
  }
  /**
   * Deprecated - statistics from the Monarch query.
   *
   * @deprecated
   * @param EnterpriseCrmEventbusStats $stats
   */
  public function setStats(EnterpriseCrmEventbusStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @deprecated
   * @return EnterpriseCrmEventbusStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * Defines the type of the task
   *
   * Accepted values: TASK, ASIS_TEMPLATE, IO_TEMPLATE
   *
   * @param self::TASK_TYPE_* $taskType
   */
  public function setTaskType($taskType)
  {
    $this->taskType = $taskType;
  }
  /**
   * @return self::TASK_TYPE_*
   */
  public function getTaskType()
  {
    return $this->taskType;
  }
  /**
   * UI configuration for this task Also associated with the METADATA mask.
   *
   * @param EnterpriseCrmEventbusProtoTaskUiConfig $uiConfig
   */
  public function setUiConfig(EnterpriseCrmEventbusProtoTaskUiConfig $uiConfig)
  {
    $this->uiConfig = $uiConfig;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskUiConfig
   */
  public function getUiConfig()
  {
    return $this->uiConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoTaskEntity::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoTaskEntity');
