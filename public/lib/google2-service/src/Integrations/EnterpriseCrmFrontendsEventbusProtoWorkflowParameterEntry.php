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

class EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry extends \Google\Collection
{
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const DATA_TYPE_STRING_VALUE = 'STRING_VALUE';
  public const DATA_TYPE_INT_VALUE = 'INT_VALUE';
  public const DATA_TYPE_DOUBLE_VALUE = 'DOUBLE_VALUE';
  public const DATA_TYPE_BOOLEAN_VALUE = 'BOOLEAN_VALUE';
  public const DATA_TYPE_PROTO_VALUE = 'PROTO_VALUE';
  public const DATA_TYPE_SERIALIZED_OBJECT_VALUE = 'SERIALIZED_OBJECT_VALUE';
  public const DATA_TYPE_STRING_ARRAY = 'STRING_ARRAY';
  public const DATA_TYPE_INT_ARRAY = 'INT_ARRAY';
  public const DATA_TYPE_DOUBLE_ARRAY = 'DOUBLE_ARRAY';
  public const DATA_TYPE_PROTO_ARRAY = 'PROTO_ARRAY';
  public const DATA_TYPE_PROTO_ENUM = 'PROTO_ENUM';
  public const DATA_TYPE_BOOLEAN_ARRAY = 'BOOLEAN_ARRAY';
  public const DATA_TYPE_PROTO_ENUM_ARRAY = 'PROTO_ENUM_ARRAY';
  /**
   * BYTES and BYTES_ARRAY data types are not allowed for top-level params.
   * They're only meant to support protobufs with BYTES (sub)fields.
   */
  public const DATA_TYPE_BYTES = 'BYTES';
  public const DATA_TYPE_BYTES_ARRAY = 'BYTES_ARRAY';
  public const DATA_TYPE_NON_SERIALIZABLE_OBJECT = 'NON_SERIALIZABLE_OBJECT';
  public const DATA_TYPE_JSON_VALUE = 'JSON_VALUE';
  public const IN_OUT_TYPE_IN_OUT_TYPE_UNSPECIFIED = 'IN_OUT_TYPE_UNSPECIFIED';
  /**
   * Input parameters for the workflow. EventBus validates that these parameters
   * exist in the workflows before execution.
   */
  public const IN_OUT_TYPE_IN = 'IN';
  /**
   * Output Parameters for the workflow. EventBus will only return the workflow
   * parameters tagged with OUT in the response back.
   */
  public const IN_OUT_TYPE_OUT = 'OUT';
  /**
   * Input or Output Parameters. These can be used as both input and output.
   * EventBus will validate for the existence of these parameters before
   * execution and will also return this parameter back in the response.
   */
  public const IN_OUT_TYPE_IN_OUT = 'IN_OUT';
  protected $collection_key = 'children';
  protected $attributesType = EnterpriseCrmEventbusProtoAttributes::class;
  protected $attributesDataType = '';
  protected $childrenType = EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry::class;
  protected $childrenDataType = 'array';
  /**
   * Indicates whether this variable contains large data and need to be uploaded
   * to Cloud Storage.
   *
   * @var bool
   */
  public $containsLargeData;
  /**
   * The data type of the parameter.
   *
   * @var string
   */
  public $dataType;
  protected $defaultValueType = EnterpriseCrmFrontendsEventbusProtoParameterValueType::class;
  protected $defaultValueDataType = '';
  /**
   * Optional. The description about the parameter
   *
   * @var string
   */
  public $description;
  /**
   * Specifies the input/output type for the parameter.
   *
   * @var string
   */
  public $inOutType;
  /**
   * Whether this parameter is a transient parameter.
   *
   * @var bool
   */
  public $isTransient;
  /**
   * This schema will be used to validate runtime JSON-typed values of this
   * parameter.
   *
   * @var string
   */
  public $jsonSchema;
  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * workflow definition.
   *
   * @var string
   */
  public $key;
  /**
   * The name (without prefix) to be displayed in the UI for this parameter.
   * E.g. if the key is "foo.bar.myName", then the name would be "myName".
   *
   * @var string
   */
  public $name;
  protected $producedByType = EnterpriseCrmEventbusProtoNodeIdentifier::class;
  protected $producedByDataType = '';
  /**
   * @var string
   */
  public $producer;
  /**
   * The name of the protobuf type if the parameter has a protobuf data type.
   *
   * @var string
   */
  public $protoDefName;
  /**
   * If the data type is of type proto or proto array, this field needs to be
   * populated with the fully qualified proto name. This message, for example,
   * would be "enterprise.crm.frontends.eventbus.proto.WorkflowParameterEntry".
   *
   * @var string
   */
  public $protoDefPath;
  /**
   * @var bool
   */
  public $required;

  /**
   * Metadata information about the parameters.
   *
   * @param EnterpriseCrmEventbusProtoAttributes $attributes
   */
  public function setAttributes(EnterpriseCrmEventbusProtoAttributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return EnterpriseCrmEventbusProtoAttributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Child parameters nested within this parameter. This field only applies to
   * protobuf parameters
   *
   * @param EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry[] $children
   */
  public function setChildren($children)
  {
    $this->children = $children;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry[]
   */
  public function getChildren()
  {
    return $this->children;
  }
  /**
   * Indicates whether this variable contains large data and need to be uploaded
   * to Cloud Storage.
   *
   * @param bool $containsLargeData
   */
  public function setContainsLargeData($containsLargeData)
  {
    $this->containsLargeData = $containsLargeData;
  }
  /**
   * @return bool
   */
  public function getContainsLargeData()
  {
    return $this->containsLargeData;
  }
  /**
   * The data type of the parameter.
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, STRING_VALUE, INT_VALUE,
   * DOUBLE_VALUE, BOOLEAN_VALUE, PROTO_VALUE, SERIALIZED_OBJECT_VALUE,
   * STRING_ARRAY, INT_ARRAY, DOUBLE_ARRAY, PROTO_ARRAY, PROTO_ENUM,
   * BOOLEAN_ARRAY, PROTO_ENUM_ARRAY, BYTES, BYTES_ARRAY,
   * NON_SERIALIZABLE_OBJECT, JSON_VALUE
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Default values for the defined keys. Each value can either be string, int,
   * double or any proto message or a serialized object.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoParameterValueType $defaultValue
   */
  public function setDefaultValue(EnterpriseCrmFrontendsEventbusProtoParameterValueType $defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoParameterValueType
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Optional. The description about the parameter
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
   * Specifies the input/output type for the parameter.
   *
   * Accepted values: IN_OUT_TYPE_UNSPECIFIED, IN, OUT, IN_OUT
   *
   * @param self::IN_OUT_TYPE_* $inOutType
   */
  public function setInOutType($inOutType)
  {
    $this->inOutType = $inOutType;
  }
  /**
   * @return self::IN_OUT_TYPE_*
   */
  public function getInOutType()
  {
    return $this->inOutType;
  }
  /**
   * Whether this parameter is a transient parameter.
   *
   * @param bool $isTransient
   */
  public function setIsTransient($isTransient)
  {
    $this->isTransient = $isTransient;
  }
  /**
   * @return bool
   */
  public function getIsTransient()
  {
    return $this->isTransient;
  }
  /**
   * This schema will be used to validate runtime JSON-typed values of this
   * parameter.
   *
   * @param string $jsonSchema
   */
  public function setJsonSchema($jsonSchema)
  {
    $this->jsonSchema = $jsonSchema;
  }
  /**
   * @return string
   */
  public function getJsonSchema()
  {
    return $this->jsonSchema;
  }
  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given fired event. These parameters must be predefined in the
   * workflow definition.
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
   * The name (without prefix) to be displayed in the UI for this parameter.
   * E.g. if the key is "foo.bar.myName", then the name would be "myName".
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
   * The identifier of the node (TaskConfig/TriggerConfig) this parameter was
   * produced by, if it is a transient param or a copy of an input param.
   *
   * @param EnterpriseCrmEventbusProtoNodeIdentifier $producedBy
   */
  public function setProducedBy(EnterpriseCrmEventbusProtoNodeIdentifier $producedBy)
  {
    $this->producedBy = $producedBy;
  }
  /**
   * @return EnterpriseCrmEventbusProtoNodeIdentifier
   */
  public function getProducedBy()
  {
    return $this->producedBy;
  }
  /**
   * @param string $producer
   */
  public function setProducer($producer)
  {
    $this->producer = $producer;
  }
  /**
   * @return string
   */
  public function getProducer()
  {
    return $this->producer;
  }
  /**
   * The name of the protobuf type if the parameter has a protobuf data type.
   *
   * @param string $protoDefName
   */
  public function setProtoDefName($protoDefName)
  {
    $this->protoDefName = $protoDefName;
  }
  /**
   * @return string
   */
  public function getProtoDefName()
  {
    return $this->protoDefName;
  }
  /**
   * If the data type is of type proto or proto array, this field needs to be
   * populated with the fully qualified proto name. This message, for example,
   * would be "enterprise.crm.frontends.eventbus.proto.WorkflowParameterEntry".
   *
   * @param string $protoDefPath
   */
  public function setProtoDefPath($protoDefPath)
  {
    $this->protoDefPath = $protoDefPath;
  }
  /**
   * @return string
   */
  public function getProtoDefPath()
  {
    return $this->protoDefPath;
  }
  /**
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoWorkflowParameterEntry');
