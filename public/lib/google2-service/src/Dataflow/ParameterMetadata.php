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

namespace Google\Service\Dataflow;

class ParameterMetadata extends \Google\Collection
{
  /**
   * Default input type.
   */
  public const PARAM_TYPE_DEFAULT = 'DEFAULT';
  /**
   * The parameter specifies generic text input.
   */
  public const PARAM_TYPE_TEXT = 'TEXT';
  /**
   * The parameter specifies a Cloud Storage Bucket to read from.
   */
  public const PARAM_TYPE_GCS_READ_BUCKET = 'GCS_READ_BUCKET';
  /**
   * The parameter specifies a Cloud Storage Bucket to write to.
   */
  public const PARAM_TYPE_GCS_WRITE_BUCKET = 'GCS_WRITE_BUCKET';
  /**
   * The parameter specifies a Cloud Storage file path to read from.
   */
  public const PARAM_TYPE_GCS_READ_FILE = 'GCS_READ_FILE';
  /**
   * The parameter specifies a Cloud Storage file path to write to.
   */
  public const PARAM_TYPE_GCS_WRITE_FILE = 'GCS_WRITE_FILE';
  /**
   * The parameter specifies a Cloud Storage folder path to read from.
   */
  public const PARAM_TYPE_GCS_READ_FOLDER = 'GCS_READ_FOLDER';
  /**
   * The parameter specifies a Cloud Storage folder to write to.
   */
  public const PARAM_TYPE_GCS_WRITE_FOLDER = 'GCS_WRITE_FOLDER';
  /**
   * The parameter specifies a Pub/Sub Topic.
   */
  public const PARAM_TYPE_PUBSUB_TOPIC = 'PUBSUB_TOPIC';
  /**
   * The parameter specifies a Pub/Sub Subscription.
   */
  public const PARAM_TYPE_PUBSUB_SUBSCRIPTION = 'PUBSUB_SUBSCRIPTION';
  /**
   * The parameter specifies a BigQuery table.
   */
  public const PARAM_TYPE_BIGQUERY_TABLE = 'BIGQUERY_TABLE';
  /**
   * The parameter specifies a JavaScript UDF in Cloud Storage.
   */
  public const PARAM_TYPE_JAVASCRIPT_UDF_FILE = 'JAVASCRIPT_UDF_FILE';
  /**
   * The parameter specifies a Service Account email.
   */
  public const PARAM_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * The parameter specifies a Machine Type.
   */
  public const PARAM_TYPE_MACHINE_TYPE = 'MACHINE_TYPE';
  /**
   * The parameter specifies a KMS Key name.
   */
  public const PARAM_TYPE_KMS_KEY_NAME = 'KMS_KEY_NAME';
  /**
   * The parameter specifies a Worker Region.
   */
  public const PARAM_TYPE_WORKER_REGION = 'WORKER_REGION';
  /**
   * The parameter specifies a Worker Zone.
   */
  public const PARAM_TYPE_WORKER_ZONE = 'WORKER_ZONE';
  /**
   * The parameter specifies a boolean input.
   */
  public const PARAM_TYPE_BOOLEAN = 'BOOLEAN';
  /**
   * The parameter specifies an enum input.
   */
  public const PARAM_TYPE_ENUM = 'ENUM';
  /**
   * The parameter specifies a number input.
   */
  public const PARAM_TYPE_NUMBER = 'NUMBER';
  /**
   * Deprecated. Please use KAFKA_READ_TOPIC instead.
   *
   * @deprecated
   */
  public const PARAM_TYPE_KAFKA_TOPIC = 'KAFKA_TOPIC';
  /**
   * The parameter specifies the fully-qualified name of an Apache Kafka topic.
   * This can be either a Google Managed Kafka topic or a non-managed Kafka
   * topic.
   */
  public const PARAM_TYPE_KAFKA_READ_TOPIC = 'KAFKA_READ_TOPIC';
  /**
   * The parameter specifies the fully-qualified name of an Apache Kafka topic.
   * This can be an existing Google Managed Kafka topic, the name for a new
   * Google Managed Kafka topic, or an existing non-managed Kafka topic.
   */
  public const PARAM_TYPE_KAFKA_WRITE_TOPIC = 'KAFKA_WRITE_TOPIC';
  protected $collection_key = 'regexes';
  /**
   * Optional. Additional metadata for describing this parameter.
   *
   * @var string[]
   */
  public $customMetadata;
  /**
   * Optional. The default values will pre-populate the parameter with the given
   * value from the proto. If default_value is left empty, the parameter will be
   * populated with a default of the relevant type, e.g. false for a boolean.
   *
   * @var string
   */
  public $defaultValue;
  protected $enumOptionsType = ParameterMetadataEnumOption::class;
  protected $enumOptionsDataType = 'array';
  /**
   * Optional. Specifies a group name for this parameter to be rendered under.
   * Group header text will be rendered exactly as specified in this field. Only
   * considered when parent_name is NOT provided.
   *
   * @var string
   */
  public $groupName;
  /**
   * Required. The help text to display for the parameter.
   *
   * @var string
   */
  public $helpText;
  /**
   * Optional. Whether the parameter should be hidden in the UI.
   *
   * @var bool
   */
  public $hiddenUi;
  /**
   * Optional. Whether the parameter is optional. Defaults to false.
   *
   * @var bool
   */
  public $isOptional;
  /**
   * Required. The label to display for the parameter.
   *
   * @var string
   */
  public $label;
  /**
   * Required. The name of the parameter.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The type of the parameter. Used for selecting input picker.
   *
   * @var string
   */
  public $paramType;
  /**
   * Optional. Specifies the name of the parent parameter. Used in conjunction
   * with 'parent_trigger_values' to make this parameter conditional (will only
   * be rendered conditionally). Should be mappable to a ParameterMetadata.name
   * field.
   *
   * @var string
   */
  public $parentName;
  /**
   * Optional. The value(s) of the 'parent_name' parameter which will trigger
   * this parameter to be shown. If left empty, ANY non-empty value in
   * parent_name will trigger this parameter to be shown. Only considered when
   * this parameter is conditional (when 'parent_name' has been provided).
   *
   * @var string[]
   */
  public $parentTriggerValues;
  /**
   * Optional. Regexes that the parameter must match.
   *
   * @var string[]
   */
  public $regexes;

  /**
   * Optional. Additional metadata for describing this parameter.
   *
   * @param string[] $customMetadata
   */
  public function setCustomMetadata($customMetadata)
  {
    $this->customMetadata = $customMetadata;
  }
  /**
   * @return string[]
   */
  public function getCustomMetadata()
  {
    return $this->customMetadata;
  }
  /**
   * Optional. The default values will pre-populate the parameter with the given
   * value from the proto. If default_value is left empty, the parameter will be
   * populated with a default of the relevant type, e.g. false for a boolean.
   *
   * @param string $defaultValue
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return string
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Optional. The options shown when ENUM ParameterType is specified.
   *
   * @param ParameterMetadataEnumOption[] $enumOptions
   */
  public function setEnumOptions($enumOptions)
  {
    $this->enumOptions = $enumOptions;
  }
  /**
   * @return ParameterMetadataEnumOption[]
   */
  public function getEnumOptions()
  {
    return $this->enumOptions;
  }
  /**
   * Optional. Specifies a group name for this parameter to be rendered under.
   * Group header text will be rendered exactly as specified in this field. Only
   * considered when parent_name is NOT provided.
   *
   * @param string $groupName
   */
  public function setGroupName($groupName)
  {
    $this->groupName = $groupName;
  }
  /**
   * @return string
   */
  public function getGroupName()
  {
    return $this->groupName;
  }
  /**
   * Required. The help text to display for the parameter.
   *
   * @param string $helpText
   */
  public function setHelpText($helpText)
  {
    $this->helpText = $helpText;
  }
  /**
   * @return string
   */
  public function getHelpText()
  {
    return $this->helpText;
  }
  /**
   * Optional. Whether the parameter should be hidden in the UI.
   *
   * @param bool $hiddenUi
   */
  public function setHiddenUi($hiddenUi)
  {
    $this->hiddenUi = $hiddenUi;
  }
  /**
   * @return bool
   */
  public function getHiddenUi()
  {
    return $this->hiddenUi;
  }
  /**
   * Optional. Whether the parameter is optional. Defaults to false.
   *
   * @param bool $isOptional
   */
  public function setIsOptional($isOptional)
  {
    $this->isOptional = $isOptional;
  }
  /**
   * @return bool
   */
  public function getIsOptional()
  {
    return $this->isOptional;
  }
  /**
   * Required. The label to display for the parameter.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Required. The name of the parameter.
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
   * Optional. The type of the parameter. Used for selecting input picker.
   *
   * Accepted values: DEFAULT, TEXT, GCS_READ_BUCKET, GCS_WRITE_BUCKET,
   * GCS_READ_FILE, GCS_WRITE_FILE, GCS_READ_FOLDER, GCS_WRITE_FOLDER,
   * PUBSUB_TOPIC, PUBSUB_SUBSCRIPTION, BIGQUERY_TABLE, JAVASCRIPT_UDF_FILE,
   * SERVICE_ACCOUNT, MACHINE_TYPE, KMS_KEY_NAME, WORKER_REGION, WORKER_ZONE,
   * BOOLEAN, ENUM, NUMBER, KAFKA_TOPIC, KAFKA_READ_TOPIC, KAFKA_WRITE_TOPIC
   *
   * @param self::PARAM_TYPE_* $paramType
   */
  public function setParamType($paramType)
  {
    $this->paramType = $paramType;
  }
  /**
   * @return self::PARAM_TYPE_*
   */
  public function getParamType()
  {
    return $this->paramType;
  }
  /**
   * Optional. Specifies the name of the parent parameter. Used in conjunction
   * with 'parent_trigger_values' to make this parameter conditional (will only
   * be rendered conditionally). Should be mappable to a ParameterMetadata.name
   * field.
   *
   * @param string $parentName
   */
  public function setParentName($parentName)
  {
    $this->parentName = $parentName;
  }
  /**
   * @return string
   */
  public function getParentName()
  {
    return $this->parentName;
  }
  /**
   * Optional. The value(s) of the 'parent_name' parameter which will trigger
   * this parameter to be shown. If left empty, ANY non-empty value in
   * parent_name will trigger this parameter to be shown. Only considered when
   * this parameter is conditional (when 'parent_name' has been provided).
   *
   * @param string[] $parentTriggerValues
   */
  public function setParentTriggerValues($parentTriggerValues)
  {
    $this->parentTriggerValues = $parentTriggerValues;
  }
  /**
   * @return string[]
   */
  public function getParentTriggerValues()
  {
    return $this->parentTriggerValues;
  }
  /**
   * Optional. Regexes that the parameter must match.
   *
   * @param string[] $regexes
   */
  public function setRegexes($regexes)
  {
    $this->regexes = $regexes;
  }
  /**
   * @return string[]
   */
  public function getRegexes()
  {
    return $this->regexes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParameterMetadata::class, 'Google_Service_Dataflow_ParameterMetadata');
