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

class EnterpriseCrmFrontendsEventbusProtoParamSpecEntry extends \Google\Model
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
  /**
   * The FQCN of the Java object this represents. A string, for example, would
   * be "java.lang.String". If this is "java.lang.Object", the parameter can be
   * of any type.
   *
   * @var string
   */
  public $className;
  /**
   * If it is a collection of objects, this would be the FCQN of every
   * individual element in the collection. If this is "java.lang.Object", the
   * parameter is a collection of any type.
   *
   * @var string
   */
  public $collectionElementClassName;
  protected $configType = EnterpriseCrmEventbusProtoParamSpecEntryConfig::class;
  protected $configDataType = '';
  /**
   * The data type of the parameter.
   *
   * @var string
   */
  public $dataType;
  protected $defaultValueType = EnterpriseCrmFrontendsEventbusProtoParameterValueType::class;
  protected $defaultValueDataType = '';
  /**
   * If set, this entry is deprecated, so further use of this parameter should
   * be prohibited.
   *
   * @var bool
   */
  public $isDeprecated;
  /**
   * @var bool
   */
  public $isOutput;
  /**
   * If the data_type is JSON_VALUE, then this will define its schema.
   *
   * @var string
   */
  public $jsonSchema;
  /**
   * Key is used to retrieve the corresponding parameter value. This should be
   * unique for a given task. These parameters must be predefined in the
   * workflow definition.
   *
   * @var string
   */
  public $key;
  protected $protoDefType = EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition::class;
  protected $protoDefDataType = '';
  /**
   * If set, the user must provide an input value for this parameter.
   *
   * @var bool
   */
  public $required;
  protected $validationRuleType = EnterpriseCrmEventbusProtoParamSpecEntryValidationRule::class;
  protected $validationRuleDataType = '';

  /**
   * The FQCN of the Java object this represents. A string, for example, would
   * be "java.lang.String". If this is "java.lang.Object", the parameter can be
   * of any type.
   *
   * @param string $className
   */
  public function setClassName($className)
  {
    $this->className = $className;
  }
  /**
   * @return string
   */
  public function getClassName()
  {
    return $this->className;
  }
  /**
   * If it is a collection of objects, this would be the FCQN of every
   * individual element in the collection. If this is "java.lang.Object", the
   * parameter is a collection of any type.
   *
   * @param string $collectionElementClassName
   */
  public function setCollectionElementClassName($collectionElementClassName)
  {
    $this->collectionElementClassName = $collectionElementClassName;
  }
  /**
   * @return string
   */
  public function getCollectionElementClassName()
  {
    return $this->collectionElementClassName;
  }
  /**
   * Optional fields, such as help text and other useful info.
   *
   * @param EnterpriseCrmEventbusProtoParamSpecEntryConfig $config
   */
  public function setConfig(EnterpriseCrmEventbusProtoParamSpecEntryConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return EnterpriseCrmEventbusProtoParamSpecEntryConfig
   */
  public function getConfig()
  {
    return $this->config;
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
   * If set, this entry is deprecated, so further use of this parameter should
   * be prohibited.
   *
   * @param bool $isDeprecated
   */
  public function setIsDeprecated($isDeprecated)
  {
    $this->isDeprecated = $isDeprecated;
  }
  /**
   * @return bool
   */
  public function getIsDeprecated()
  {
    return $this->isDeprecated;
  }
  /**
   * @param bool $isOutput
   */
  public function setIsOutput($isOutput)
  {
    $this->isOutput = $isOutput;
  }
  /**
   * @return bool
   */
  public function getIsOutput()
  {
    return $this->isOutput;
  }
  /**
   * If the data_type is JSON_VALUE, then this will define its schema.
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
   * unique for a given task. These parameters must be predefined in the
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
   * Populated if this represents a proto or proto array.
   *
   * @param EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition $protoDef
   */
  public function setProtoDef(EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition $protoDef)
  {
    $this->protoDef = $protoDef;
  }
  /**
   * @return EnterpriseCrmEventbusProtoParamSpecEntryProtoDefinition
   */
  public function getProtoDef()
  {
    return $this->protoDef;
  }
  /**
   * If set, the user must provide an input value for this parameter.
   *
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
  /**
   * Rule used to validate inputs (individual values and collection elements)
   * for this parameter.
   *
   * @param EnterpriseCrmEventbusProtoParamSpecEntryValidationRule $validationRule
   */
  public function setValidationRule(EnterpriseCrmEventbusProtoParamSpecEntryValidationRule $validationRule)
  {
    $this->validationRule = $validationRule;
  }
  /**
   * @return EnterpriseCrmEventbusProtoParamSpecEntryValidationRule
   */
  public function getValidationRule()
  {
    return $this->validationRule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoParamSpecEntry::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoParamSpecEntry');
